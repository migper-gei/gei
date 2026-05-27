<?php
date_default_timezone_set('Europe/Lisbon');
/**
 * Backup completo da base de dados MySQL — GEI
 * ─────────────────────────────────────────────
 * • Exporta estrutura + dados de todas as tabelas
 * • Suporta campos BLOB (imgavaria, video, logotipo) → exportados em hex (0x...)
 * • Compressão gzip automática
 * • Rotação automática (mantém os últimos N backups)
 * • Protegido por sessão (apenas administradores)
 * • Permite download direto via browser
 */

// ─────────────────────────────────────────────
//  SESSÃO SEGURA
// ─────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}


// ─────────────────────────────────────────────
//  BD — obtida da sessão (igual ao config.php)
// ─────────────────────────────────────────────
if (!isset($_SESSION['nobd']) || !isset($_SESSION['serverbd'])
    || empty($_SESSION['nobd']) || empty($_SESSION['serverbd'])) {
    header('Location: ' . (defined('SVRURL') ? SVRURL : '/') . 'i');
    exit();
}

include_once __DIR__ . '/config_serverbd.php';   // define DB_USERNAME e DB_PASSWORD

define('DB_HOST',    $_SESSION['serverbd']);
define('DB_USER',    DB_USERNAME);
define('DB_PASS',    DB_PASSWORD);
define('DB_NAME',    $_SESSION['nobd']);
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');






define('BACKUP_DIR',      __DIR__ . '/backups/');
define('ZIP_BACKUP',      false);
define('MAX_BACKUPS_DEF', 10);       // valor por defeito se não configurado na BD
define('AUTO_DOWNLOAD',   isset($_GET['download']));


// ─────────────────────────────────────────────
//  LIGAÇÃO À BASE DE DADOS
// ─────────────────────────────────────────────
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($db->connect_error) {
    die("Erro de ligação à base de dados: " . $db->connect_error);
}
$db->set_charset(DB_CHARSET);

// Ler MAX_BACKUPS da tabela settings (campo backup_retencao), com fallback para o valor por defeito
$_max_backups = MAX_BACKUPS_DEF;
if (isset($db)) {
    $stmt_ret = $db->prepare("SELECT backup_retencao FROM settings LIMIT 1");
    if ($stmt_ret) {
        $stmt_ret->execute();
        $row_ret = $stmt_ret->get_result()->fetch_assoc();
        $stmt_ret->close();
        if (!empty($row_ret['backup_retencao']) && (int)$row_ret['backup_retencao'] > 0) {
            $_max_backups = (int)$row_ret['backup_retencao'];
        }
    }
}
define('MAX_BACKUPS', $_max_backups);

// ─────────────────────────────────────────────
//  FUNÇÕES AUXILIARES
// ─────────────────────────────────────────────

/** Escapa um valor para INSERT, tratando NULLs e BLOBs */
function escape_value(mysqli $conn, mixed $value, bool $is_blob): string {
    if ($value === null) return 'NULL';
    if ($is_blob) {
        if ($value === '' || strlen($value) === 0) return 'NULL';
        return '0x' . bin2hex($value);
    }
    return "'" . mysqli_real_escape_string($conn, (string)$value) . "'";
}

/** Devolve array [campo => ['type' => ..., 'is_blob' => bool]] para uma tabela */
function get_column_types(mysqli $conn, string $table): array {
    $blob_types = ['blob', 'tinyblob', 'mediumblob', 'longblob', 'binary', 'varbinary'];
    $result     = mysqli_query($conn, "SHOW COLUMNS FROM `{$table}`");
    $columns    = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $base_type = strtolower(preg_replace('/\(.*\)/', '', $row['Type']));
        $columns[$row['Field']] = [
            'type'    => $base_type,
            'is_blob' => in_array($base_type, $blob_types, true),
        ];
    }
    return $columns;
}

/** Remove backups mais antigos se exceder o limite */
function rotate_backups(string $dir, int $max): void {
    if ($max <= 0) return;
    $files = glob($dir . 'backup_*.sql*');
    if (!$files) return;
    usort($files, function($a, $b) { return filemtime($a) - filemtime($b); });
    while (count($files) > $max) {
        $oldest = array_shift($files);
        unlink($oldest);
    }
}

/** Formata bytes em KB/MB legível */
function fmt_size(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    return round($bytes / 1024, 1) . ' KB';
}

/** Cria a pasta de backup e garante que tem um .htaccess a bloquear acesso HTTP */
function prepare_backup_dir(string $dir): void {
    if (!is_dir($dir) && !mkdir($dir, 0750, true)) {
        throw new RuntimeException("Não foi possível criar o directório {$dir}");
    }
    $htaccess = $dir . '.htaccess';
    $rules    = "# Bloqueia todo o acesso HTTP direto a esta pasta\n"
              . "Order Allow,Deny\n"
              . "Deny from all\n"
              . "<IfModule mod_authz_core.c>\n"
              . "    Require all denied\n"
              . "</IfModule>\n";

    // Escrever sempre — garante que versões antigas (sem mod_authz_core) são atualizadas
    if (!file_exists($htaccess) || file_get_contents($htaccess) !== $rules) {
        file_put_contents($htaccess, $rules);
    }
}

// ─────────────────────────────────────────────
//  DOWNLOAD DIRETO (antes de qualquer output HTML)
// ─────────────────────────────────────────────
if (AUTO_DOWNLOAD) {
    // Só administradores (tipo 1) podem fazer download
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
        http_response_code(403);
        die('Acesso negado.');
    }

    ob_start();
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    prepare_backup_dir(BACKUP_DIR);

    $filename = 'backup_' . DB_NAME . '_' . date('Ymd_His') . '.sql';
    $filepath = BACKUP_DIR . $filename;

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$conn) { ob_end_clean(); die('Erro de ligação: ' . mysqli_connect_error()); }
    mysqli_set_charset($conn, DB_CHARSET);

    $fh = fopen($filepath, 'w');
    if (!$fh) { ob_end_clean(); die('Erro ao criar ficheiro de backup.'); }

    fwrite($fh, "-- ============================================================\n");
    fwrite($fh, "-- Backup  : " . DB_NAME . "\n");
    fwrite($fh, "-- Data    : " . date('Y-m-d H:i:s') . "\n");
    fwrite($fh, "-- Host    : " . DB_HOST . "\n");
    fwrite($fh, "-- Charset : " . DB_CHARSET . "\n");
    fwrite($fh, "-- ============================================================\n\n");
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
    fwrite($fh, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
    fwrite($fh, "SET NAMES " . DB_CHARSET . ";\n\n");

    $tables = [
        'escolas',
        'logotipo',
        'salas',
        'equipamento',
        'avarias_reparacoes',
        'chat_message',
        'equip_requisitado',
        'manutencao',
        'outro_equipamento',
        'periodos',
        'requisicao',
        'settings',
        'tarefas',
        'tipos_equipamento',
        'tipos_manutencao',
        'utilizadores',
    ];

    $total_rows = 0;
    foreach ($tables as $table) {
        fwrite($fh, "\n-- ------------------------------------------------------------\n");
        fwrite($fh, "-- Tabela: `{$table}`\n");
        fwrite($fh, "-- ------------------------------------------------------------\n");
        fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");

        $create_result = mysqli_query($conn, "SHOW CREATE TABLE `{$table}`");
        $create_row    = mysqli_fetch_row($create_result);
        fwrite($fh, $create_row[1] . ";\n\n");

        $col_types    = get_column_types($conn, $table);
        $col_names    = array_keys($col_types);
        $cols_escaped = implode(', ', array_map(function($c) { return "`{$c}`"; }, $col_names));

        $data_result = mysqli_query($conn, "SELECT * FROM `{$table}`", MYSQLI_USE_RESULT);
        $row_count   = 0;
        while ($row = mysqli_fetch_assoc($data_result)) {
            $values = [];
            foreach ($col_names as $col) {
                $values[] = escape_value($conn, $row[$col], $col_types[$col]['is_blob']);
            }
            fwrite($fh, "INSERT INTO `{$table}` ({$cols_escaped}) VALUES (" . implode(', ', $values) . ");\n");
            $row_count++;
        }
        mysqli_free_result($data_result);
        fwrite($fh, "\n");
        $total_rows += $row_count;
    }

    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
    fwrite($fh, "-- Fim do backup — " . $total_rows . " linhas exportadas\n");
    fclose($fh);
    mysqli_close($conn);

    $final_path = $filepath;
    $final_name = $filename;

    if (ZIP_BACKUP && class_exists("ZipArchive")) {
        $zip_path = $filepath . ".zip";
        $zip      = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) === true) {
            $zip->addFile($filepath, $filename);
            $zip->close();
            unlink($filepath);
            $final_path = $zip_path;
            $final_name = $filename . ".zip";
        }
    }

    rotate_backups(BACKUP_DIR, MAX_BACKUPS);

    // ── Auditar download manual ───────────────────────────────────────────────
    require_once __DIR__ . '/gei_audit.php';
    $detalhe_dl = 'ficheiro=' . $final_name . ' | tamanho=' . fmt_size(filesize($final_path));
    gei_audit($db, 'backup_download', 'backup', 0, $detalhe_dl);

    ob_end_clean();
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $final_name . '"');
    header('Content-Length: ' . filesize($final_path));
    header('Pragma: no-cache');
    header('Cache-Control: must-revalidate');
    readfile($final_path);
    exit;
}

// ─────────────────────────────────────────────
//  ESTADO DO BACKUP (executado via POST ou agendamento)
// ─────────────────────────────────────────────
$backup_log     = [];
$backup_success = false;
$final_name     = '';
$final_path     = '';
// ─────────────────────────────────────────────
//  GRAVAR CONFIGURAÇÃO DE AGENDAMENTO + RETENÇÃO
// ─────────────────────────────────────────────
$retencao_msg   = '';
$agendamento_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['tipo']) && $_SESSION['tipo'] == 1) {

    // ── Retenção (compatibilidade com campo antigo) ──────────────────────────
    if (isset($_POST['backup_retencao'])) {
        $nova_retencao = (int)$_POST['backup_retencao'];
        if ($nova_retencao >= 1 && $nova_retencao <= 50) {
            $col_check = $db->query("SHOW COLUMNS FROM settings LIKE 'backup_retencao'");
            if ($col_check && $col_check->num_rows === 0) {
                $db->query("ALTER TABLE settings ADD COLUMN backup_retencao INT DEFAULT 10");
            }
            $stmt_upd = $db->prepare("UPDATE settings SET backup_retencao=?");
            $stmt_upd->bind_param("i", $nova_retencao);
            $stmt_upd->execute();
            $stmt_upd->close();
            $retencao_msg = "✓ Retenção atualizada para {$nova_retencao} backup(s).";
            rotate_backups(BACKUP_DIR, $nova_retencao);
        } else {
            $retencao_msg = "✗ Valor inválido (1–50).";
        }
    }

    // ── Guardar configuração de agendamento ──────────────────────────────────
    if (isset($_POST['acao']) && $_POST['acao'] === 'guardar_agendamento') {
        $ag_ativo       = isset($_POST['ag_ativo']) ? 1 : 0;
        $ag_freq        = in_array($_POST['ag_frequencia'] ?? '', ['diario','semanal','mensal']) ? $_POST['ag_frequencia'] : 'diario';
        $ag_hora        = preg_match('/^\d{2}:\d{2}$/', $_POST['ag_hora'] ?? '') ? $_POST['ag_hora'] . ':00' : '02:00:00';
        $ag_dia_semana  = (int)($_POST['ag_dia_semana'] ?? 1);
        $ag_dia_mes     = max(1, min(28, (int)($_POST['ag_dia_mes'] ?? 1)));
        $ag_destino     = in_array($_POST['ag_destino'] ?? '', ['local','email','ftp','email_ftp']) ? $_POST['ag_destino'] : 'local';
        $ag_email       = trim($_POST['ag_email_destino'] ?? '');
        $ag_ftp_host    = trim($_POST['ag_ftp_host'] ?? '');
        $ag_ftp_port    = max(1, min(65535, (int)($_POST['ag_ftp_port'] ?? 21)));
        $ag_ftp_user    = trim($_POST['ag_ftp_user'] ?? '');
        $ag_ftp_pass_raw = trim($_POST['ag_ftp_pass'] ?? '');   // password em plain-text (cifrada antes de guardar)
        $ag_ftp_dir     = trim($_POST['ag_ftp_dir'] ?? '/backups/');
        $ag_s3_bucket   = trim($_POST['ag_s3_bucket'] ?? '');
        $ag_s3_region   = trim($_POST['ag_s3_region'] ?? 'eu-west-1');
        $ag_s3_prefix   = trim($_POST['ag_s3_prefix'] ?? 'gei-backups/');
        $ag_retencao    = max(1, min(50, (int)($_POST['ag_retencao'] ?? 10)));
        $ag_gzip        = isset($_POST['ag_gzip']) ? 1 : 0;

        // Cifrar ftp_pass com AES_ENCRYPT (mesmo padrão da password SMTP)
        $_smtpKey_ag = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';
        if (!empty($ag_ftp_pass_raw) && !empty($_smtpKey_ag)) {
            // Usar AES_ENCRYPT via query paramétrica — devolve o valor cifrado
            $stmt_enc = $db->prepare("SELECT AES_ENCRYPT(?, ?) AS enc");
            $stmt_enc->bind_param('ss', $ag_ftp_pass_raw, $_smtpKey_ag);
            $stmt_enc->execute();
            $row_enc = $stmt_enc->get_result()->fetch_assoc();
            $stmt_enc->close();
            $ag_ftp_pass = $row_enc['enc'];   // valor binário cifrado
        } elseif (empty($ag_ftp_pass_raw)) {
            // Campo deixado em branco → manter o valor existente na BD (não substituir)
            $stmt_cur = $db->query("SELECT ftp_pass FROM backup_agendamento LIMIT 1");
            $ag_ftp_pass = ($stmt_cur && $row_cur = $stmt_cur->fetch_assoc()) ? $row_cur['ftp_pass'] : null;
        } else {
            // SMTP_KEY não definida — guardar null e avisar
            $ag_ftp_pass = null;
            $agendamento_msg = "✗ Variável SMTP_KEY não definida — password FTP não guardada.";
        }

        // Verificar se a tabela backup_agendamento existe
        $tbl_check = $db->query("SHOW TABLES LIKE 'backup_agendamento'");
        if (!$tbl_check || $tbl_check->num_rows === 0) {
            $agendamento_msg = "✗ Tabela backup_agendamento não existe. Execute o ficheiro backup_migration.sql primeiro.";
        } else {
            $existing = $db->query("SELECT id FROM backup_agendamento LIMIT 1");
            if ($existing && $existing->num_rows > 0) {
                // UPDATE
                $stmt_ag = $db->prepare(
                    "UPDATE backup_agendamento SET ativo=?,frequencia=?,hora=?,dia_semana=?,dia_mes=?,
                     destino=?,email_destino=?,ftp_host=?,ftp_port=?,ftp_user=?,ftp_pass=?,ftp_dir=?,
                     retencao=?,gzip=? LIMIT 1"
                );
                $stmt_ag->bind_param("issiisssisssii",
                    $ag_ativo,$ag_freq,$ag_hora,$ag_dia_semana,$ag_dia_mes,
                    $ag_destino,$ag_email,$ag_ftp_host,$ag_ftp_port,$ag_ftp_user,$ag_ftp_pass,$ag_ftp_dir,
                    $ag_retencao,$ag_gzip
                );
            } else {
                // INSERT
                $stmt_ag = $db->prepare(
                    "INSERT INTO backup_agendamento (ativo,frequencia,hora,dia_semana,dia_mes,
                     destino,email_destino,ftp_host,ftp_port,ftp_user,ftp_pass,ftp_dir,
                     retencao,gzip)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                );
                $stmt_ag->bind_param("issiisssisssii",
                    $ag_ativo,$ag_freq,$ag_hora,$ag_dia_semana,$ag_dia_mes,
                    $ag_destino,$ag_email,$ag_ftp_host,$ag_ftp_port,$ag_ftp_user,$ag_ftp_pass,$ag_ftp_dir,
                    $ag_retencao,$ag_gzip
                );
            }
            $stmt_ag->execute();
            $stmt_ag->close();
            $agendamento_msg = "✓ Configuração de agendamento guardada.";
        }
    }
    // ── Executar backup agendado manualmente ────────────────────────────────────
    if (isset($_POST["acao"]) && $_POST["acao"] === "executar_cron") {
        $cron_log  = [];
        $erros_cron = [];
        try {
            // Ler configuração de destino da BD
            $cfg = [];
            $r_cfg = $db->query("SELECT * FROM backup_agendamento LIMIT 1");
            if ($r_cfg && $row_cfg = $r_cfg->fetch_assoc()) {
                $cfg = $row_cfg;
            }
            $destino_cfg = $cfg['destino'] ?? 'local';

            prepare_backup_dir(BACKUP_DIR);
            $filename = 'backup_' . DB_NAME . '_' . date('Ymd_His') . '.sql';
            $filepath = BACKUP_DIR . $filename;
            $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if (!$conn) throw new RuntimeException('Erro de ligação: ' . mysqli_connect_error());
            mysqli_set_charset($conn, DB_CHARSET);
            $fh = fopen($filepath, 'w');
            if (!$fh) throw new RuntimeException('Não foi possível criar o ficheiro.');
            fwrite($fh, '-- Backup: ' . DB_NAME . ' | ' . date('Y-m-d H:i:s') . "\n");
            fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\nSET NAMES " . DB_CHARSET . ";\n\n");
            $tables_cron = ['escolas','logotipo','salas','equipamento','avarias_reparacoes',
                'chat_message','equip_requisitado','manutencao','outro_equipamento','periodos',
                'requisicao','settings','tarefas','tipos_equipamento','tipos_manutencao',
                'utilizadores','backup_agendamento','backup_historico'];
            $total_rows_cron = 0;
            foreach ($tables_cron as $tbl_c) {
                $chk = mysqli_query($conn, "SHOW TABLES LIKE '" . $tbl_c . "'");
                if (!$chk || mysqli_num_rows($chk) === 0) continue;
                fwrite($fh, "DROP TABLE IF EXISTS `{$tbl_c}`;\n");
                $cr = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE `{$tbl_c}`"));
                fwrite($fh, $cr[1] . ";\n\n");
                $col_types_c = get_column_types($conn, $tbl_c);
                $col_names_c = array_keys($col_types_c);
                $cols_esc_c  = implode(', ', array_map(function($c) { return "`{$c}`"; }, $col_names_c));
                $data_c = mysqli_query($conn, "SELECT * FROM `{$tbl_c}`", MYSQLI_USE_RESULT);
                $rc = 0;
                while ($row_c = mysqli_fetch_assoc($data_c)) {
                    $vals_c = [];
                    foreach ($col_names_c as $col_c) {
                        $vals_c[] = escape_value($conn, $row_c[$col_c], $col_types_c[$col_c]['is_blob']);
                    }
                    fwrite($fh, "INSERT INTO `{$tbl_c}` ({$cols_esc_c}) VALUES (" . implode(', ', $vals_c) . ");\n");
                    $rc++;
                }
                mysqli_free_result($data_c);
                fwrite($fh, "\n");
                $total_rows_cron += $rc;
                $cron_log[] = '-> ' . $tbl_c . ': ' . $rc . ' linha(s)';
            }
            fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($fh);
            mysqli_close($conn);
            unset($conn); // garantir que $db não é afetado

            $final_cron_path = $filepath;
            $final_cron_name = $filename;
            if (!empty($cfg['gzip']) && class_exists('ZipArchive')) {
                $zip_path_c = $filepath . '.zip';
                $zip_c = new ZipArchive();
                if ($zip_c->open($zip_path_c, ZipArchive::CREATE) === true) {
                    $zip_c->addFile($filepath, $filename);
                    $zip_c->close();
                    unlink($filepath);
                    $final_cron_path = $zip_path_c;
                    $final_cron_name = $filename . '.zip';
                }
            }
            $fsize_cron    = filesize($final_cron_path);
            $email_cron_ok = false;

            // ── ENVIO POR EMAIL ──────────────────────────────────────────────
            $cron_log[] = '── Destino configurado: ' . $destino_cfg . ' | Email: ' . ($cfg['email_destino'] ?? '(vazio)');

            if (in_array($destino_cfg, ['email', 'email_ftp'], true) && !empty($cfg['email_destino'])) {

                // Usar o mesmo padrão de includes que o resto da aplicação
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

                $mail_bkp = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    $mail_bkp->CharSet = 'UTF-8';
                    $mail_bkp->isSMTP();

                    // ── Carregar configurações SMTP directamente da BD ────────
                    $_smtpKey_bkp = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';
                    if (empty($_smtpKey_bkp)) {
                        throw new PHPMailer\PHPMailer\Exception('Variável SMTP_KEY não definida no servidor.');
                    }
                    $stmt_smtp_bkp = $db->prepare(
                        "SELECT email_user, nome_app, AES_DECRYPT(pass, ?) AS pass_dec,
                                email_smtp, email_smtpport
                         FROM settings LIMIT 1"
                    );
                    $stmt_smtp_bkp->bind_param('s', $_smtpKey_bkp);
                    $stmt_smtp_bkp->execute();
                    $row_smtp_bkp = $stmt_smtp_bkp->get_result()->fetch_assoc();
                    $stmt_smtp_bkp->close();

                    if (empty($row_smtp_bkp) || empty($row_smtp_bkp['email_smtp'])) {
                        throw new PHPMailer\PHPMailer\Exception('Configurações SMTP não encontradas na tabela settings.');
                    }

                    $mail_bkp->Host        = $row_smtp_bkp['email_smtp'];
                    $mail_bkp->Port        = (int)$row_smtp_bkp['email_smtpport'];
                    $mail_bkp->SMTPAuth    = true;
                    $mail_bkp->Username    = $row_smtp_bkp['email_user'];
                    $mail_bkp->Password    = $row_smtp_bkp['pass_dec'];
                    $mail_bkp->SMTPSecure  = ((int)$row_smtp_bkp['email_smtpport'] === 465)
                                                ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                                                : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail_bkp->SMTPAutoTLS = true;
                    $mail_bkp->Timeout     = 15;
                    $mail_bkp->From        = $row_smtp_bkp['email_user'];
                    $mail_bkp->FromName    = $row_smtp_bkp['nome_app'] ?? 'GEI';
                    $mail_bkp->Sender      = $row_smtp_bkp['email_user'];
                    $mail_bkp->addReplyTo($row_smtp_bkp['email_user'], $row_smtp_bkp['nome_app'] ?? 'GEI');

                    // Destinatários
                    $addrs_validos = 0;
                    foreach (array_map('trim', explode(',', $cfg['email_destino'])) as $addr) {
                        if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                            $mail_bkp->addAddress($addr);
                            $addrs_validos++;
                        }
                    }
                    if ($addrs_validos === 0) {
                        throw new PHPMailer\PHPMailer\Exception('Nenhum endereço de email válido em: ' . $cfg['email_destino']);
                    }

                    $mail_bkp->isHTML(true);
                    $mail_bkp->Subject = '[GEI] Backup BD ' . DB_NAME . ' — ' . date('d/m/Y H:i');
                    $mail_bkp->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
  <div style="background-color:#003366;padding:20px 24px;">
    <h2 style="color:#ffffff;margin:0;font-size:18px;">&#128190; Backup Automático — GEI</h2>
  </div>
  <div style="padding:24px;background-color:#f9f9f9;">
    <p style="font-size:14px;color:#333;">O backup agendado da base de dados foi concluído com sucesso.</p>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Base de dados</td><td style="padding:6px 10px;color:#222;">' . htmlspecialchars(DB_NAME, ENT_QUOTES, 'UTF-8') . '</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Ficheiro</td><td style="padding:6px 10px;color:#222;font-family:monospace;">' . htmlspecialchars($final_cron_name, ENT_QUOTES, 'UTF-8') . '</td></tr>
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;">Tamanho</td><td style="padding:6px 10px;color:#222;">' . fmt_size($fsize_cron) . '</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Data/hora</td><td style="padding:6px 10px;color:#222;">' . date('d/m/Y H:i:s') . '</td></tr>
    </table>
    <p style="font-size:12px;color:#888;margin-top:16px;">O ficheiro de backup está em anexo. Guarde-o num local seguro.</p>
  </div>
  <div style="background-color:#eeeeee;padding:12px 24px;text-align:center;font-size:12px;color:#888;">Este email foi gerado automaticamente. Por favor não responda.</div>
</div>';
                    $mail_bkp->AltBody = "Backup GEI concluído: {$final_cron_name} (" . fmt_size($fsize_cron) . ") — " . date('d/m/Y H:i:s');

                    if ($fsize_cron <= 20 * 1024 * 1024) {
                        $mail_bkp->addAttachment($final_cron_path, $final_cron_name);
                    } else {
                        $mail_bkp->Body .= '<p style="color:#e74c3c;font-size:12px;padding:0 24px;">&#9888; Ficheiro demasiado grande para anexo (' . fmt_size($fsize_cron) . ').</p>';
                    }

                    $mail_bkp->send();
                    $email_cron_ok = true;
                    $cron_log[] = '✉ Email enviado com sucesso para ' . $cfg['email_destino'];

                } catch (PHPMailer\PHPMailer\Exception $e) {
                    $erros_cron[] = 'Email falhou: ' . $mail_bkp->ErrorInfo;
                    $cron_log[]   = '✗ Email ERRO: ' . $mail_bkp->ErrorInfo;
                }

                // Se destino for apenas 'email' e envio bem sucedido, apagar ficheiro local
                if ($email_cron_ok && $destino_cfg === 'email' && file_exists($final_cron_path)) {
                    unlink($final_cron_path);
                    $cron_log[] = '🗑 Ficheiro local removido (destino=email)';
                }

            } elseif (in_array($destino_cfg, ['email', 'email_ftp'], true)) {
                // Destino requer email mas o endereço não está configurado
                $erros_cron[] = 'Email: endereço de destino não configurado na tabela backup_agendamento.';
                $cron_log[]   = '✗ Email ERRO: campo email_destino está vazio — configure o endereço de email no agendamento.';
            } else {
                $cron_log[] = '── Email não aplicável (destino=' . $destino_cfg . ')';
            }

            // ── ENVIO POR FTP ─────────────────────────────────────────────────
            $ftp_cron_ok = false;
            if (in_array($destino_cfg, ['ftp', 'email_ftp'], true) && !empty($cfg['ftp_host'])) {
                $ftp_host = $cfg['ftp_host'];
                $ftp_port = !empty($cfg['ftp_port']) ? (int)$cfg['ftp_port'] : 21;
                $ftp_user = $cfg['ftp_user'] ?? '';
                $ftp_dir  = rtrim($cfg['ftp_dir'] ?? '/', '/') . '/';

                // Desencriptar ftp_pass com AES_DECRYPT (mesmo padrão da password SMTP)
                $_smtpKey_ftp = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';
                if (empty($_smtpKey_ftp)) {
                    throw new RuntimeException('Variável SMTP_KEY não definida — não é possível desencriptar a password FTP.');
                }
                $stmt_dec = $db->prepare("SELECT AES_DECRYPT(?, ?) AS pass_dec");
                $stmt_dec->bind_param('ss', $cfg['ftp_pass'], $_smtpKey_ftp);
                $stmt_dec->execute();
                $row_dec  = $stmt_dec->get_result()->fetch_assoc();
                $stmt_dec->close();
                $ftp_pass = $row_dec['pass_dec'] ?? '';

                $cron_log[] = '── A ligar ao FTP: ' . $ftp_host . ':' . $ftp_port;

                if (!function_exists('ftp_connect')) {
                    $erros_cron[] = 'FTP: extensão ftp não está disponível neste servidor PHP.';
                    $cron_log[]   = '✗ FTP ERRO: extensão ftp não disponível.';
                } else {
                    $ftp_conn = @ftp_connect($ftp_host, $ftp_port, 15);
                    if (!$ftp_conn) {
                        $erros_cron[] = 'FTP: não foi possível ligar a ' . $ftp_host . ':' . $ftp_port;
                        $cron_log[]   = '✗ FTP ERRO: falha na ligação a ' . $ftp_host;
                    } elseif (!@ftp_login($ftp_conn, $ftp_user, $ftp_pass)) {
                        $erros_cron[] = 'FTP: autenticação falhou para o utilizador ' . $ftp_user;
                        $cron_log[]   = '✗ FTP ERRO: autenticação falhou';
                        ftp_close($ftp_conn);
                    } else {
                        ftp_pasv($ftp_conn, true); // modo passivo (recomendado)

                        // Criar directório remoto se não existir
                        $remote_dir_parts = array_filter(explode('/', $ftp_dir));
                        $current_path = '';
                        foreach ($remote_dir_parts as $part) {
                            $current_path .= '/' . $part;
                            if (!@ftp_chdir($ftp_conn, $current_path)) {
                                @ftp_mkdir($ftp_conn, $current_path);
                            }
                        }
                        @ftp_chdir($ftp_conn, $ftp_dir);

                        $remote_file = $ftp_dir . $final_cron_name;
                        $fh_ftp = fopen($final_cron_path, 'r');
                        if ($fh_ftp && ftp_fput($ftp_conn, $remote_file, $fh_ftp, FTP_BINARY)) {
                            $ftp_cron_ok = true;
                            $cron_log[]  = '✓ FTP: ficheiro enviado para ' . $ftp_host . $remote_file;
                        } else {
                            $erros_cron[] = 'FTP: falha ao enviar ' . $final_cron_name . ' para ' . $ftp_host . $remote_file;
                            $cron_log[]   = '✗ FTP ERRO: falha ao enviar o ficheiro';
                        }
                        if ($fh_ftp) fclose($fh_ftp);
                        ftp_close($ftp_conn);

                        // Se destino for apenas 'ftp', apagar ficheiro local após envio bem sucedido
                        if ($ftp_cron_ok && $destino_cfg === 'ftp' && file_exists($final_cron_path)) {
                            unlink($final_cron_path);
                            $cron_log[] = '🗑 Ficheiro local removido (destino=ftp)';
                        }

                        // Se destino for 'email_ftp', apagar ficheiro local após ambos os envios bem sucedidos
                        if ($ftp_cron_ok && $email_cron_ok && $destino_cfg === 'email_ftp' && file_exists($final_cron_path)) {
                            unlink($final_cron_path);
                            $cron_log[] = '🗑 Ficheiro local removido (destino=email_ftp)';
                        }
                    }
                }
            } else {
                $cron_log[] = '── FTP não configurado (destino=' . $destino_cfg . ')';
            }

            // ── Rotação de backups locais (só quando o ficheiro fica em disco) ──
            if ($destino_cfg === 'local') {
                rotate_backups(BACKUP_DIR, (int)($cfg['retencao'] ?? MAX_BACKUPS));
            }

            // ── Destino realizado para histórico ─────────────────────────────
            $destino_realizado = 'local';
            if ($email_cron_ok && $ftp_cron_ok)          $destino_realizado = 'email+ftp';
            elseif ($email_cron_ok)                       $destino_realizado = 'email';
            elseif ($ftp_cron_ok)                         $destino_realizado = 'ftp';

            // ── Registar no histórico ─────────────────────────────────────────
            $hist_tbl_chk = $db->query("SHOW TABLES LIKE 'backup_historico'");
            if ($hist_tbl_chk && $hist_tbl_chk->num_rows > 0) {
                $notas_cron = empty($erros_cron) ? null : implode(' | ', $erros_cron);
                $stmt_h = $db->prepare('INSERT INTO backup_historico (ficheiro,tamanho_bytes,destino,status,notas,criado_em) VALUES (?,?,?,\'ok\',?,NOW())');
                $stmt_h->bind_param('siss', $final_cron_name, $fsize_cron, $destino_realizado, $notas_cron);
                $stmt_h->execute();
                $stmt_h->close();
            }

            array_unshift($cron_log, '✓ Backup concluído: ' . $final_cron_name . ' (' . fmt_size($fsize_cron) . ')');

            // ── Auditar execução de backup ────────────────────────────────────
            require_once __DIR__ . '/gei_audit.php';
            $detalhe_cron = 'ficheiro=' . $final_cron_name
                          . ' | tamanho=' . fmt_size($fsize_cron)
                          . ' | destino=' . $destino_realizado
                          . (empty($erros_cron) ? '' : ' | erros=' . implode('; ', $erros_cron));
            gei_audit($db, 'backup_executar', 'backup', 0, $detalhe_cron);

            $agendamento_msg = 'cron_output:' . implode("\n", $cron_log);

        } catch (RuntimeException $e) {
            $agendamento_msg = 'cron_output:✗ Erro: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php include ("head.php"); ?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

      <?php include ("header.php"); ?>

      <?php include("sessao_timeout.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Cópia de segurança</li>
                  </ol>
               </nav>
                  <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                     <div class="welcome-section">
                        <?php include("msg_bemvindo.php"); ?>
                     </div>

                     <br>

                     <?php
                     // ── Painel de informação sobre backups existentes ──
                     $backup_files  = glob(BACKUP_DIR . 'backup_*.sql*') ?: [];
                     $backup_count  = count($backup_files);
                     $backup_bytes  = array_sum(array_map('filesize', $backup_files));

                     // Ordenar do mais recente para o mais antigo
                     usort($backup_files, function($a, $b) { return filemtime($b) - filemtime($a); });

                     $pct_usado = MAX_BACKUPS > 0 ? min(100, round($backup_count / MAX_BACKUPS * 100)) : 0;
                     $bar_color = $pct_usado >= 90 ? '#e74c3c' : ($pct_usado >= 70 ? '#e67e22' : '#1cc88a');
                     ?>

                     <!-- Card de estado dos backups -->
                     <div style="background:#fff;border:1px solid #e3e8f4;border-radius:10px;padding:20px 24px;margin-bottom:20px;box-shadow:0 2px 8px rgba(75,108,183,.08);">
                         <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                             <div style="display:flex;align-items:center;gap:8px;">
                                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12H2"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/><line x1="6" y1="16" x2="6.01" y2="16"/><line x1="10" y1="16" x2="10.01" y2="16"/></svg>
                                 <strong style="color:#182848;font-size:.95rem;">Backups existentes</strong>
                             </div>
                             <span style="font-size:.8rem;color:#7b88a0;">Pasta: <code style="background:#f4f6fb;padding:2px 6px;border-radius:4px;">backups/</code></span>
                         </div>

                         <!-- Barra de ocupação -->
                         <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                             <div style="flex:1;background:#eef1f8;border-radius:99px;height:8px;overflow:hidden;">
                                 <div style="width:<?php echo $pct_usado; ?>%;height:100%;background:<?php echo $bar_color; ?>;border-radius:99px;transition:width .4s;"></div>
                             </div>
                             <span style="font-size:.82rem;font-weight:700;color:<?php echo $bar_color; ?>;white-space:nowrap;">
                                 <?php echo $backup_count; ?> / <?php echo MAX_BACKUPS; ?>
                             </span>
                         </div>

                         <!-- Métricas -->
                         <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;">
                             <div style="text-align:center;min-width:80px;">
                                 <div style="font-size:1.4rem;font-weight:800;color:#182848;"><?php echo $backup_count; ?></div>
                                 <div style="font-size:.72rem;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;">Ficheiros</div>
                             </div>
                             <div style="text-align:center;min-width:80px;">
                                 <div style="font-size:1.4rem;font-weight:800;color:#182848;"><?php echo fmt_size($backup_bytes); ?></div>
                                 <div style="font-size:.72rem;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;">Espaço total</div>
                             </div>
                             <div style="text-align:center;min-width:80px;">
                                 <div style="font-size:1.4rem;font-weight:800;color:#182848;"><?php echo MAX_BACKUPS; ?></div>
                                 <div style="font-size:.72rem;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;">Retenção máx.</div>
                             </div>
                         </div>

                         <?php if ($backup_count > 0): ?>
                         <!-- Lista dos últimos backups -->
                         <div style="border-top:1px solid #eef1f8;padding-top:12px;margin-bottom:14px;">
                             <div style="font-size:.75rem;font-weight:700;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">Últimos backups</div>
                             <?php foreach (array_slice($backup_files, 0, 5) as $i => $bf): ?>
                             <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px dashed #eef1f8;font-size:.8rem;<?php echo $i===0?'color:#182848;font-weight:600':'color:#5a6370'; ?>">
                                 <span>
                                     <?php echo $i === 0 ? '⭐ ' : ''; ?>
                                     <?php echo htmlspecialchars(basename($bf), ENT_QUOTES, 'UTF-8'); ?>
                                 </span>
                                 <span style="color:#7b88a0;white-space:nowrap;margin-left:12px;">
                                     <?php echo fmt_size(filesize($bf)); ?> &nbsp;·&nbsp;
                                     <?php echo date('d/m/Y H:i', filemtime($bf)); ?>
                                 </span>
                             </div>
                             <?php endforeach; ?>
                             <?php if ($backup_count > 5): ?>
                             <div style="font-size:.75rem;color:#7b88a0;margin-top:6px;">+ <?php echo $backup_count - 5; ?> ficheiro(s) mais antigo(s)</div>
                             <?php endif; ?>
                         </div>
                         <?php endif; ?>

                         <!-- Formulário de retenção (compacto, mantido por compatibilidade) -->
                         <form method="post" action="<?php echo SVRURL ?>backup.php" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;border-top:1px solid #eef1f8;padding-top:14px;">
                             <label style="font-size:.8rem;font-weight:700;color:#1e2a45;white-space:nowrap;">Manter os últimos</label>
                             <input type="number" name="backup_retencao" value="<?php echo MAX_BACKUPS; ?>" min="1" max="50" required
                                 style="width:70px;padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.85rem;font-weight:700;color:#182848;text-align:center;">
                             <span style="font-size:.8rem;color:#7b88a0;">backups locais (1–50)</span>
                             <button type="submit" style="padding:5px 14px;border-radius:6px;background:#4b6cb7;color:#fff;border:none;font-size:.8rem;font-weight:700;cursor:pointer;">Guardar</button>
                             <?php if ($retencao_msg): ?>
                             <span style="font-size:.8rem;font-weight:600;color:<?php echo (str_starts_with($retencao_msg,'✓'))?'#1cc88a':'#e74c3c'; ?>;">
                                 <?php echo htmlspecialchars($retencao_msg, ENT_QUOTES, 'UTF-8'); ?>
                             </span>
                             <?php endif; ?>
                         </form>
                     </div>


                     <br>

                     <!-- ═══════════════════════════════════════════════════════
                          PAINEL: AGENDAMENTO AUTOMÁTICO (cron)
                     ══════════════════════════════════════════════════════════ -->
                     <?php
                     // Ler configuração atual de agendamento
                     $ag = [];
                     $tbl_ag = $db->query("SHOW TABLES LIKE 'backup_agendamento'");
                     if ($tbl_ag && $tbl_ag->num_rows > 0) {
                         $r_ag = $db->query("SELECT * FROM backup_agendamento LIMIT 1");
                         if ($r_ag) $ag = $r_ag->fetch_assoc() ?: [];
                     }
                     $ag_exists = !empty($ag);
                     // Defaults
                     $ag['ativo']         = $ag['ativo']         ?? 1;
                     $ag['frequencia']    = $ag['frequencia']    ?? 'diario';
                     $ag['hora']          = isset($ag['hora'])    ? substr($ag['hora'], 0, 5) : '02:00';
                     $ag['dia_semana']    = $ag['dia_semana']    ?? 1;
                     $ag['dia_mes']       = $ag['dia_mes']       ?? 1;
                     $ag['destino']       = $ag['destino']       ?? 'local';
                     $ag['email_destino'] = $ag['email_destino'] ?? '';
                     $ag['ftp_host']      = $ag['ftp_host']      ?? '';
                     $ag['ftp_port']      = $ag['ftp_port']      ?? 21;
                     $ag['ftp_user']      = $ag['ftp_user']      ?? '';
                     $ag['ftp_dir']       = $ag['ftp_dir']       ?? '/backups/';
                     $ag['s3_bucket']     = $ag['s3_bucket']     ?? '';
                     $ag['s3_region']     = $ag['s3_region']     ?? 'eu-west-1';
                     $ag['s3_prefix']     = $ag['s3_prefix']     ?? 'gei-backups/';
                     $ag['retencao']      = $ag['retencao']      ?? 10;
                     $ag['gzip']          = $ag['gzip']          ?? 1;

                     // Buscar emails definidos na tabela backup_agendamento
                     $emails_agendamento = [];
                     $r_emails_ag = $db->query("SELECT email_destino FROM backup_agendamento WHERE email_destino IS NOT NULL AND email_destino != ''");
                     if ($r_emails_ag) {
                         while ($row_email_ag = $r_emails_ag->fetch_assoc()) {
                             foreach (array_map('trim', explode(',', $row_email_ag['email_destino'])) as $addr) {
                                 if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL) && !in_array($addr, $emails_agendamento, true)) {
                                     $emails_agendamento[] = $addr;
                                 }
                             }
                         }
                     }

                     $freq_label = ['diario'=>'Diário (00h–23h59)','semanal'=>'Semanal','mensal'=>'Mensal'];
                     $dias_semana = ['0'=>'Domingo','1'=>'Segunda','2'=>'Terça','3'=>'Quarta','4'=>'Quinta','5'=>'Sexta','6'=>'Sábado'];

                     // Gerar linha cron sugerida
                     [$h_cron,$m_cron] = explode(':', $ag['hora'] . ':00');
                     $cron_linha = match($ag['frequencia']) {
                         'semanal' => "{$m_cron} {$h_cron} * * {$ag['dia_semana']}",
                         'mensal'  => "{$m_cron} {$h_cron} {$ag['dia_mes']} * *",
                         default   => "{$m_cron} {$h_cron} * * *",
                     };
                     $cron_cmd = "/usr/bin/php " . __DIR__ . "/backup_cron.php >> /var/log/gei_backup.log 2>&1";
                     ?>

                     <div style="background:#fff;border:1px solid #e3e8f4;border-radius:10px;padding:20px 24px;margin-bottom:20px;box-shadow:0 2px 8px rgba(75,108,183,.08);">
                         <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                             <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                             <strong style="color:#182848;font-size:.95rem;">Agendamento Automático</strong>
                             
                             <!--
                             <?php if (!$ag_exists): ?>
                             <span style="font-size:.72rem;background:#fff3cd;color:#856404;padding:2px 8px;border-radius:99px;margin-left:4px;">Execute backup_migration.sql primeiro</span>
                             <?php endif; ?>
                             -->

                         </div>

                         <?php if ($agendamento_msg): ?>
                         <div style="padding:8px 14px;border-radius:6px;margin-bottom:12px;font-size:.82rem;font-weight:700;background:<?php echo str_starts_with($agendamento_msg,'✓')?'#d4edda':'#f8d7da'; ?>;color:<?php echo str_starts_with($agendamento_msg,'✓')?'#155724':'#721c24'; ?>;">
                             <?php echo htmlspecialchars($agendamento_msg, ENT_QUOTES, 'UTF-8'); ?>
                         </div>
                         <?php endif; ?>

                         <!-- Linha cron sugerida 
                         <div style="background:#f4f6fb;border-radius:7px;padding:10px 14px;margin-bottom:16px;font-size:.78rem;">
                             <div style="font-weight:700;color:#4b6cb7;margin-bottom:4px;">📋 Linha crontab sugerida (edite com <code>crontab -e</code>):</div>
                             <code style="font-size:.82rem;color:#182848;word-break:break-all;"><?php echo htmlspecialchars("{$cron_linha} {$cron_cmd}", ENT_QUOTES, 'UTF-8'); ?></code>
                         </div>-->

                         <form method="post" action="<?php echo SVRURL ?>backup.php">
                             <input type="hidden" name="acao" value="guardar_agendamento">

                             <div style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:14px;">
                                 <!-- Ativo -->
                                 <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;font-weight:700;color:#1e2a45;cursor:pointer;">
                                     <input type="checkbox" name="ag_ativo" value="1" <?php echo $ag['ativo']?'checked':''; ?> style="accent-color:#4b6cb7;">
                                     Ativo
                                 </label>

                                 <!-- Frequência -->
                                 <div>
                                     <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:3px;">Frequência</label>
                                     <select name="ag_frequencia" id="ag_frequencia" onchange="geiAgendUpdate()" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;">
                                         <?php foreach(['diario'=>'Diário','semanal'=>'Semanal','mensal'=>'Mensal'] as $v=>$l): ?>
                                         <option value="<?php echo $v; ?>" <?php echo $ag['frequencia']===$v?'selected':''; ?>><?php echo $l; ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>

                                 <!-- Hora -->
                                 <div>
                                     <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:3px;">Hora</label>
                                     <input type="time" name="ag_hora" value="<?php echo htmlspecialchars($ag['hora']); ?>"
                                         style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;">
                                 </div>

                                 <!-- Dia da semana (visível só se semanal) -->
                                 <div id="ag_dia_sem_wrap" style="display:<?php echo $ag['frequencia']==='semanal'?'block':'none'; ?>">
                                     <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:3px;">Dia da semana</label>
                                     <select name="ag_dia_semana" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;">
                                         <?php foreach($dias_semana as $v=>$l): ?>
                                         <option value="<?php echo $v; ?>" <?php echo (string)$ag['dia_semana']===$v?'selected':''; ?>><?php echo $l; ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>

                                 <!-- Dia do mês (visível só se mensal) -->
                                 <div id="ag_dia_mes_wrap" style="display:<?php echo $ag['frequencia']==='mensal'?'block':'none'; ?>">
                                     <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:3px;">Dia do mês</label>
                                     <input type="number" name="ag_dia_mes" value="<?php echo (int)$ag['dia_mes']; ?>" min="1" max="28"
                                         style="width:65px;padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;text-align:center;">
                                 </div>

                                 <!-- Zip -->
                                 <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;font-weight:700;color:#1e2a45;cursor:pointer;margin-top:18px;">
                                     <input type="checkbox" name="ag_gzip" value="1" <?php echo $ag['gzip']?'checked':''; ?> style="accent-color:#4b6cb7;">
                                     Comprimir (.zip)
                                 </label>

                                 <!-- Retenção -->
                                 <div>
                                     <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:3px;">Manter localmente</label>
                                     <div style="display:flex;align-items:center;gap:6px;">
                                         <input type="number" name="ag_retencao" value="<?php echo (int)$ag['retencao']; ?>" min="1" max="50"
                                             style="width:65px;padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;text-align:center;">
                                         <span style="font-size:.78rem;color:#7b88a0;">backups</span>
                                     </div>
                                 </div>
                             </div>

                             <!-- Destino -->
                             <div style="margin-bottom:12px;">
                                 <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:6px;">Destino do backup</label>
                                 <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                     <?php
                                     $destinos = ['local'=>'💾 Apenas local','email'=>'📧 Email','ftp'=>'📁 FTP','email_ftp'=>'📧 + 📁 Email + FTP'];
                                     foreach($destinos as $dv=>$dl): ?>
                                     <label style="display:flex;align-items:center;gap:5px;font-size:.8rem;cursor:pointer;padding:5px 10px;border:1.5px solid <?php echo $ag['destino']===$dv?'#4b6cb7':'#c7d4f0'; ?>;border-radius:6px;background:<?php echo $ag['destino']===$dv?'#eef2ff':'#fff'; ?>;">
                                         <input type="radio" name="ag_destino" value="<?php echo $dv; ?>" <?php echo $ag['destino']===$dv?'checked':''; ?> onchange="geiAgendUpdate()" style="accent-color:#4b6cb7;">
                                         <?php echo $dl; ?>
                                     </label>
                                     <?php endforeach; ?>
                                 </div>
                             </div>

                             <!-- Email destino -->
                             <div id="ag_email_wrap" style="display:<?php echo in_array($ag['destino'],['email','email_ftp'])?'block':'none'; ?>;margin-bottom:12px;">
                                 <label style="font-size:.78rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:4px;">Email(s) de destino <span style="font-weight:400;">(separados por vírgula)</span></label>
                                 <input type="text" name="ag_email_destino" id="ag_email_destino_input" value="<?php echo htmlspecialchars($ag['email_destino'], ENT_QUOTES, 'UTF-8'); ?>"
                                     placeholder="admin@escola.pt, ti@escola.pt"
                                     style="width:100%;max-width:440px;padding:6px 10px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;color:#182848;">
                                 <?php if (!empty($emails_agendamento)): ?>
                                 <div style="margin-top:7px;">
                                     <span style="font-size:.72rem;color:#7b88a0;font-weight:600;">Emails configurados:</span>
                                     <div style="display:flex;flex-wrap:wrap;gap:5px;margin-top:4px;">
                                         <?php foreach($emails_agendamento as $ea): ?>
                                         <button type="button"
                                             onclick="geiAddEmail(<?php echo htmlspecialchars(json_encode($ea), ENT_QUOTES, 'UTF-8'); ?>)"
                                             title="Adicionar <?php echo htmlspecialchars($ea, ENT_QUOTES, 'UTF-8'); ?>"
                                             style="padding:3px 10px;border-radius:99px;border:1.5px solid #c7d4f0;background:#f4f6fb;color:#4b6cb7;font-size:.72rem;cursor:pointer;font-weight:600;white-space:nowrap;">
                                             + <?php echo htmlspecialchars($ea, ENT_QUOTES, 'UTF-8'); ?>
                                         </button>
                                         <?php endforeach; ?>
                                     </div>
                                 </div>
                                 <?php endif; ?>
                             </div>

                             <!-- FTP -->
                             <div id="ag_ftp_wrap" style="display:<?php echo in_array($ag['destino'],['ftp','email_ftp'])?'block':'none'; ?>;margin-bottom:12px;">
                                 <div style="font-size:.78rem;font-weight:700;color:#7b88a0;margin-bottom:6px;">Configuração FTP</div>
                                 <div style="display:flex;flex-wrap:wrap;gap:10px;">
                                     <div><label style="font-size:.72rem;color:#7b88a0;display:block;">Host</label><input type="text" name="ag_ftp_host" value="<?php echo htmlspecialchars($ag['ftp_host'], ENT_QUOTES,'UTF-8'); ?>" placeholder="ftp.exemplo.pt" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;width:180px;"></div>
                                     <div><label style="font-size:.72rem;color:#7b88a0;display:block;">Porta</label><input type="number" name="ag_ftp_port" value="<?php echo (int)$ag['ftp_port']; ?>" min="1" max="65535" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;width:75px;"></div>
                                     <div><label style="font-size:.72rem;color:#7b88a0;display:block;">Utilizador</label><input type="text" name="ag_ftp_user" value="<?php echo htmlspecialchars($ag['ftp_user'], ENT_QUOTES,'UTF-8'); ?>" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;width:140px;"></div>
                                     <div><label style="font-size:.72rem;color:#7b88a0;display:block;">Password</label><input type="password" name="ag_ftp_pass" placeholder="password" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;width:160px;"></div>
                                     <div><label style="font-size:.72rem;color:#7b88a0;display:block;">Pasta remota</label><input type="text" name="ag_ftp_dir" value="<?php echo htmlspecialchars($ag['ftp_dir'], ENT_QUOTES,'UTF-8'); ?>" style="padding:5px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.82rem;width:140px;"></div>
                                 </div>
                                 <?php if (!empty($ag['ftp_host'])): ?>
                                 <div style="margin-top:8px;padding:7px 12px;background:#f4f6fb;border-radius:7px;border:1px solid #e3e8f4;font-size:.75rem;color:#5a6370;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                     <span style="font-weight:700;color:#4b6cb7;">📁 Config. atual:</span>
                                     <span><strong>Host:</strong> <?php echo htmlspecialchars($ag['ftp_host'], ENT_QUOTES, 'UTF-8'); ?></span>
                                     <span style="color:#c7d4f0;">|</span>
                                     <span><strong>Porta:</strong> <?php echo (int)$ag['ftp_port']; ?></span>
                                     <span style="color:#c7d4f0;">|</span>
                                     <span><strong>Utilizador:</strong> <?php echo htmlspecialchars($ag['ftp_user'], ENT_QUOTES, 'UTF-8'); ?></span>
                                     <span style="color:#c7d4f0;">|</span>
                                     <span><strong>Pasta:</strong> <?php echo htmlspecialchars($ag['ftp_dir'], ENT_QUOTES, 'UTF-8'); ?></span>
                                     <span style="color:#c7d4f0;">|</span>
                                     <span><strong>Password:</strong> <span style="letter-spacing:2px;">••••••</span> <em style="font-style:normal;color:#7b88a0;"></em></span>
                                 </div>
                                 <?php else: ?>
                                 <div style="margin-top:8px;padding:6px 12px;background:#fff8e1;border-radius:7px;border:1px solid #ffe082;font-size:.75rem;color:#856404;">
                                     ⚠️ Nenhuma configuração FTP guardada ainda. Preencha os campos acima.
                                 </div>
                                 <?php endif; ?>
                             </div>


                             <button type="submit" style="margin-top:8px;padding:7px 20px;border-radius:7px;background:#4b6cb7;color:#fff;border:none;font-size:.85rem;font-weight:700;cursor:pointer;">
                                 💾 Guardar configuração de agendamento
                             </button>
                        </form>

                         <!-- Botão executar agora -->
                         <form method="post" action="<?php echo SVRURL ?>backup.php" style="margin-top:14px;border-top:1px solid #eef1f8;padding-top:14px;">
                             <input type="hidden" name="acao" value="executar_cron">
                             <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                                 <button type="submit" style="padding:8px 22px;border-radius:7px;background:#1cc88a;color:#fff;border:none;font-size:.85rem;font-weight:700;cursor:pointer;">
                                     ▶ Executar Backup Agora
                                 </button>
                                 <span style="font-size:.78rem;color:#7b88a0;">Executa o backup imediatamente, independente do agendamento</span>
                             </div>
                         </form>

                         <?php
                         if (!empty($agendamento_msg) && str_starts_with($agendamento_msg, "cron_output:")):
                             $cron_out = trim(substr($agendamento_msg, 12));
                         ?>
                         <div style="margin-top:14px;background:#1e1e1e;border-radius:6px;padding:1em 1.2em;font-family:'Courier New',monospace;font-size:.82em;line-height:1.8;color:#d4d4d4;max-height:300px;overflow-y:auto;">
                             <?php foreach(explode("\n", $cron_out) as $line): ?>
                                 <div><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></div>
                             <?php endforeach; ?>
                         </div>
                         <?php endif; ?>

                     </div>

                     <!-- ═══════════════════════════════════════════════════════
                          PAINEL: HISTÓRICO DE BACKUPS
                     ══════════════════════════════════════════════════════════ -->
                     <?php
                     $hist_rows = [];
                     $hist_tbl = $db->query("SHOW TABLES LIKE 'backup_historico'");
                     if ($hist_tbl && $hist_tbl->num_rows > 0) {
                         $r_hist = $db->query("SELECT * FROM backup_historico ORDER BY criado_em DESC LIMIT 30");
                         if ($r_hist) {
                             while ($h = $r_hist->fetch_assoc()) $hist_rows[] = $h;
                         }
                     }
                     ?>

                     <div style="background:#fff;border:1px solid #e3e8f4;border-radius:10px;padding:20px 24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(75,108,183,.08);">
                         <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                             <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
                             <strong style="color:#182848;font-size:.95rem;">Histórico de Backups</strong>
                             <span style="font-size:.72rem;color:#7b88a0;margin-left:4px;">(últimos 30)</span>
                         </div>

                         <?php if (empty($hist_rows)): ?>
                         <p style="font-size:.82rem;color:#7b88a0;margin:0;">Nenhum backup registado ainda. Execute o primeiro backup agendado (cron) para ver o histórico aqui.</p>
                         <?php else: ?>
                         <div style="overflow-x:auto;">
                             <table style="width:100%;border-collapse:collapse;font-size:.8rem;">
                                 <thead>
                                     <tr style="border-bottom:2px solid #e3e8f4;">
                                         <th style="text-align:left;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Data/Hora</th>
                                         <th style="text-align:left;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Ficheiro</th>
                                         <th style="text-align:center;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Tamanho</th>
                                         <th style="text-align:center;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Destino</th>
                                         <th style="text-align:center;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Estado</th>
                                         <th style="text-align:left;padding:6px 10px;color:#7b88a0;font-weight:700;text-transform:uppercase;font-size:.7rem;letter-spacing:.4px;">Notas</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                 <?php foreach($hist_rows as $i => $h):
                                     $st_color = match($h['status']) { 'ok'=>'#1cc88a','erro'=>'#e74c3c', default=>'#e67e22' };
                                     $st_bg    = match($h['status']) { 'ok'=>'#d4f6ea','erro'=>'#fde8e8', default=>'#fef3e2' };
                                 ?>
                                 <tr style="border-bottom:1px solid #eef1f8;background:<?php echo $i%2===0?'#fff':'#f9fafc'; ?>;">
                                     <td style="padding:7px 10px;color:#5a6370;white-space:nowrap;"><?php echo date('d/m/Y H:i', strtotime($h['criado_em'])); ?></td>
                                     <td style="padding:7px 10px;color:#182848;font-family:monospace;font-size:.75rem;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($h['ficheiro'],ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($h['ficheiro'],ENT_QUOTES,'UTF-8'); ?></td>
                                     <td style="padding:7px 10px;color:#5a6370;text-align:center;white-space:nowrap;"><?php echo fmt_size((int)$h['tamanho_bytes']); ?></td>
                                     <td style="padding:7px 10px;text-align:center;white-space:nowrap;"><span style="font-size:.72rem;background:#eef2ff;color:#4b6cb7;padding:2px 7px;border-radius:99px;font-weight:700;"><?php echo htmlspecialchars($h['destino'],ENT_QUOTES,'UTF-8'); ?></span></td>
                                     <td style="padding:7px 10px;text-align:center;white-space:nowrap;"><span style="font-size:.72rem;background:<?php echo $st_bg; ?>;color:<?php echo $st_color; ?>;padding:2px 8px;border-radius:99px;font-weight:700;"><?php echo $h['status']; ?></span></td>
                                     <td style="padding:7px 10px;color:#7b88a0;font-size:.75rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($h['notas']??'',ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($h['notas']??'—',ENT_QUOTES,'UTF-8'); ?></td>
                                 </tr>
                                 <?php endforeach; ?>
                                 </tbody>
                             </table>
                         </div>
                         <?php endif; ?>
                     </div>

                     <a href="<?php echo SVRURL ?>configura">
                        <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
                     </a>

                     <br>

                     <?php include ("jquery_bootstrap.php"); ?>

                     <script>
                     function geiAddEmail(email) {
                         var input = document.getElementById('ag_email_destino_input');
                         var current = input.value.trim();
                         var emails = current ? current.split(',').map(function(e){ return e.trim(); }) : [];
                         if (emails.indexOf(email) === -1) {
                             emails.push(email);
                             input.value = emails.join(', ');
                         }
                         // Feedback visual no botão
                         document.querySelectorAll('#ag_email_wrap button[type=button]').forEach(function(btn){
                             if (btn.getAttribute('title') === 'Adicionar ' + email) {
                                 btn.style.background = '#d4edda';
                                 btn.style.borderColor = '#1cc88a';
                                 btn.style.color = '#155724';
                                 setTimeout(function(){ btn.style.background=''; btn.style.borderColor=''; btn.style.color=''; }, 1200);
                             }
                         });
                     }
                     function geiAgendUpdate() {
                         var freq    = document.querySelector('[name=ag_frequencia]').value;
                         var destino = document.querySelector('[name=ag_destino]:checked')?.value || 'local';
                         document.getElementById('ag_dia_sem_wrap').style.display = freq==='semanal' ? 'block' : 'none';
                         document.getElementById('ag_dia_mes_wrap').style.display = freq==='mensal'  ? 'block' : 'none';
                         document.getElementById('ag_email_wrap').style.display   = ['email','email_ftp'].includes(destino) ? 'block' : 'none';
                         document.getElementById('ag_ftp_wrap').style.display     = ['ftp','email_ftp'].includes(destino)              ? 'block' : 'none';
                         // Actualizar label dos radio buttons
                         document.querySelectorAll('[name=ag_destino]').forEach(function(r) {
                             var lbl = r.closest('label');
                             lbl.style.borderColor = r.checked ? '#4b6cb7' : '#c7d4f0';
                             lbl.style.background  = r.checked ? '#eef2ff' : '#fff';
                         });
                     }
                     // Inicializar no load
                     document.addEventListener('DOMContentLoaded', geiAgendUpdate);
                     </script>

                     <br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
