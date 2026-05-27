<?php
/**
 * backup_cron.php — GEI
 * ─────────────────────────────────────────────────────────────────────────────
 * Script CLI para agendamento via cron.
 * Executa backup SQL completo, comprime, envia por email e/ou FTP, e regista
 * o histórico na tabela backup_historico.
 *
 * USO:
 *   php /caminho/para/backup_cron.php
 *
 * EXEMPLOS CRON:
 *   # Todos os dias às 02:00
 *   0 2 * * * /usr/bin/php /var/www/html/gei/backup_cron.php >> /var/log/gei_backup.log 2>&1
 *
 *   # Toda a segunda-feira às 03:00 (semanal)
 *   0 3 * * 1 /usr/bin/php /var/www/html/gei/backup_cron.php >> /var/log/gei_backup.log 2>&1
 *
 * REQUISITOS:
 *   - .env com: DB_USER, DB_PASS, DB_HOST, DB_NAME, SMTP_KEY
 *   - Tabela settings com colunas de email SMTP
 *   - Tabela backup_agendamento (criada pelo migration SQL)
 *   - Tabela backup_historico   (criada pelo migration SQL)
 * ─────────────────────────────────────────────────────────────────────────────
 */

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

// ── Só pode correr em CLI ────────────────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comandos (cron).' . PHP_EOL);
}

define('GEI_ROOT',   __DIR__);
define('BACKUP_DIR', GEI_ROOT . '/backups/');
define('LOG_PREFIX', '[' . date('Y-m-d H:i:s') . '] ');

// ── Carregar .env ────────────────────────────────────────────────────────────
$envFile = GEI_ROOT . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        if (!isset($_ENV[trim($k)]) && getenv(trim($k)) === false) {
            $_ENV[trim($k)] = trim($v, " '\"");
        }
    }
}

function env(string $key, string $default = ''): string {
    $v = $_ENV[$key] ?? getenv($key);
    return ($v === false) ? $default : (string)$v;
}

// ── Configuração de BD ───────────────────────────────────────────────────────
$DB_HOST    = env('DB_HOST', 'localhost');
$DB_USER    = env('DB_USER');
$DB_PASS    = env('DB_PASS');
$DB_PORT    = (int)env('DB_PORT', '3306');
$DB_CHARSET = 'utf8mb4';
$SMTP_KEY   = env('SMTP_KEY');

// DB_NAME: primeiro tenta argv[1] (passado pelo backup_check.php via sessão),
// depois fallback para variável de ambiente
$DB_NAME = (!empty($argv[1])) ? trim($argv[1]) : env('DB_NAME');

if (empty($DB_USER) || empty($DB_NAME)) {
    log_msg('ERRO', 'Variáveis de ambiente DB_USER e/ou DB_NAME não definidas.');
    exit(1);
}

// ── Funções de log ───────────────────────────────────────────────────────────
function log_msg(string $level, string $msg): void {
    echo LOG_PREFIX . "[{$level}] {$msg}" . PHP_EOL;
}

// ── Ligação à BD de configuração ─────────────────────────────────────────────
$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if (!$db) {
    log_msg('ERRO', 'Falha na ligação à BD: ' . mysqli_connect_error());
    exit(1);
}
mysqli_set_charset($db, $DB_CHARSET);

// ── Ler configuração de agendamento e destino ─────────────────────────────────
$cfg = [];
$r = mysqli_query($db, "SELECT * FROM backup_agendamento LIMIT 1");
if ($r && $row = mysqli_fetch_assoc($r)) {
    $cfg = $row;
} else {
    log_msg('AVISO', 'Tabela backup_agendamento não encontrada ou vazia — a usar padrões.');
    $cfg = [
        'ativo'         => 1,
        'frequencia'    => 'diario',
        'hora'          => '02:00',
        'destino'       => 'local',
        'email_destino' => '',
        'ftp_host'      => '',
        'ftp_user'      => '',
        'ftp_pass'      => '',
        'ftp_dir'       => '/backups/',
        'retencao'      => 10,
        'gzip'          => 1,
    ];
}

// ── Verificar se está ativo ───────────────────────────────────────────────────
if (empty($cfg['ativo'])) {
    log_msg('INFO', 'Backup agendado desativado nas configurações. A terminar.');
    mysqli_close($db);
    exit(0);
}


// ── Preparar pasta de backups ─────────────────────────────────────────────────
function prepare_backup_dir(string $dir): void {
    if (!is_dir($dir) && !mkdir($dir, 0750, true)) {
        throw new RuntimeException("Não foi possível criar {$dir}");
    }
    $htaccess = $dir . '.htaccess';
    $rules = "Order Allow,Deny\nDeny from all\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n";
    if (!file_exists($htaccess) || file_get_contents($htaccess) !== $rules) {
        file_put_contents($htaccess, $rules);
    }
}

// ── Escape de valores (igual ao backup.php) ───────────────────────────────────
function escape_value(mysqli $conn, mixed $value, bool $is_blob): string {
    if ($value === null) return 'NULL';
    if ($is_blob) {
        if ($value === '' || strlen($value) === 0) return 'NULL';
        return '0x' . bin2hex($value);
    }
    return "'" . mysqli_real_escape_string($conn, (string)$value) . "'";
}

function get_column_types(mysqli $conn, string $table): array {
    $blob_types = ['blob','tinyblob','mediumblob','longblob','binary','varbinary'];
    $result  = mysqli_query($conn, "SHOW COLUMNS FROM `{$table}`");
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $base = strtolower(preg_replace('/\(.*\)/', '', $row['Type']));
        $columns[$row['Field']] = ['type' => $base, 'is_blob' => in_array($base, $blob_types, true)];
    }
    return $columns;
}

function fmt_size(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    return round($bytes / 1024, 1) . ' KB';
}

function rotate_backups(string $dir, int $max): void {
    if ($max <= 0) return;
    $files = glob($dir . 'backup_*.sql*') ?: [];
    usort($files, fn($a,$b) => filemtime($a) - filemtime($b));
    while (count($files) > $max) { unlink(array_shift($files)); }
}

// ── EXECUTAR BACKUP ───────────────────────────────────────────────────────────
log_msg('INFO', "Início do backup agendado (BD: {$DB_NAME})");

set_time_limit(0);
ini_set('memory_limit', '512M');

$ts_start = microtime(true);
$status   = 'erro';
$erros    = [];
$final_path = '';
$final_name = '';

try {
    prepare_backup_dir(BACKUP_DIR);

    $filename = 'backup_' . $DB_NAME . '_' . date('Ymd_His') . '.sql';
    $filepath = BACKUP_DIR . $filename;

    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    if (!$conn) throw new RuntimeException('Erro de ligação: ' . mysqli_connect_error());
    mysqli_set_charset($conn, $DB_CHARSET);

    $fh = fopen($filepath, 'w');
    if (!$fh) throw new RuntimeException("Não foi possível criar {$filepath}");

    fwrite($fh, "-- ============================================================\n");
    fwrite($fh, "-- Backup  : {$DB_NAME}\n");
    fwrite($fh, "-- Data    : " . date('Y-m-d H:i:s') . "\n");
    fwrite($fh, "-- Host    : {$DB_HOST}\n");
    fwrite($fh, "-- Origem  : cron agendado\n");
    fwrite($fh, "-- ============================================================\n\n");
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
    fwrite($fh, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
    fwrite($fh, "SET NAMES {$DB_CHARSET};\n\n");

    $tables = [
        'escolas','logotipo','salas','equipamento','avarias_reparacoes',
        'chat_message','equip_requisitado','manutencao','outro_equipamento',
        'periodos','requisicao','settings','tarefas','tipos_equipamento',
        'tipos_manutencao','utilizadores','backup_agendamento','backup_historico',
    ];

    $total_rows = 0;
    foreach ($tables as $table) {
        // Verificar se tabela existe
        $check = mysqli_query($conn, "SHOW TABLES LIKE '{$table}'");
        if (!$check || mysqli_num_rows($check) === 0) {
            log_msg('AVISO', "Tabela {$table} não existe, ignorada.");
            continue;
        }

        fwrite($fh, "\n-- ------------------------------------------------------------\n");
        fwrite($fh, "-- Tabela: `{$table}`\n");
        fwrite($fh, "-- ------------------------------------------------------------\n");
        fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");

        $cr = mysqli_query($conn, "SHOW CREATE TABLE `{$table}`");
        $cr_row = mysqli_fetch_row($cr);
        fwrite($fh, $cr_row[1] . ";\n\n");

        $col_types    = get_column_types($conn, $table);
        $col_names    = array_keys($col_types);
        $cols_escaped = implode(', ', array_map(fn($c) => "`{$c}`", $col_names));

        $data = mysqli_query($conn, "SELECT * FROM `{$table}`", MYSQLI_USE_RESULT);
        $row_count = 0;
        while ($row = mysqli_fetch_assoc($data)) {
            $values = array_map(
                fn($col) => escape_value($conn, $row[$col], $col_types[$col]['is_blob']),
                $col_names
            );
            fwrite($fh, "INSERT INTO `{$table}` ({$cols_escaped}) VALUES (" . implode(', ', $values) . ");\n");
            $row_count++;
        }
        mysqli_free_result($data);
        fwrite($fh, "\n");
        $total_rows += $row_count;
        log_msg('OK', "→ {$table}: {$row_count} linha(s)");
    }

    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
    fwrite($fh, "-- Fim do backup — {$total_rows} linhas exportadas\n");
    fclose($fh);
    mysqli_close($conn);

    $final_path = $filepath;
    $final_name = $filename;

    // ── Comprimir ─────────────────────────────────────────────────────────────
    if (!empty($cfg["gzip"]) && class_exists("ZipArchive")) {
        $zip_path = $filepath . ".zip";
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) === true) {
            $zip->addFile($filepath, $filename);
            $zip->close();
            unlink($filepath);
            $final_path = $zip_path;
            $final_name = $filename . ".zip";
            log_msg("OK", "Comprimido: {$final_name} (" . fmt_size(filesize($zip_path)) . ")");
        }
    }

    rotate_backups(BACKUP_DIR, (int)($cfg['retencao'] ?? 10));
    $elapsed    = round(microtime(true) - $ts_start, 2);
    $file_size  = filesize($final_path);
    $status     = 'ok';
    log_msg('OK', "Backup concluído em {$elapsed}s — " . fmt_size($file_size));

} catch (RuntimeException $e) {
    $status = 'erro';
    $erros[] = $e->getMessage();
    log_msg('ERRO', $e->getMessage());
}

// ── ENVIO POR EMAIL ───────────────────────────────────────────────────────────
$email_ok = false;
$destino  = $cfg['destino'] ?? 'local';

log_msg('INFO', "Destino configurado: {$destino} | email_destino: " . ($cfg['email_destino'] ?: '(vazio)'));

if (empty($cfg['email_destino']) && in_array($destino, ['email', 'email_ftp'], true)) {
    log_msg('ERRO', 'email_destino está vazio na tabela backup_agendamento — email não será enviado. Preenche o campo na configuração de backup.');
}

if ($status === 'ok' && in_array($destino, ['email', 'email_ftp'], true) && !empty($cfg['email_destino'])) {
    log_msg('INFO', "A enviar backup por email para {$cfg['email_destino']}...");

    require_once GEI_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
    require_once GEI_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
    require_once GEI_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();

        // ── Carregar configurações SMTP da BD (versão CLI-safe) ──────────────
        if (empty($SMTP_KEY)) {
            throw new PHPMailer\PHPMailer\Exception('Variável SMTP_KEY não definida no .env');
        }

        $stmt_smtp = mysqli_prepare($db,
            "SELECT email_user, nome_app, AES_DECRYPT(pass, ?) AS pass_dec,
                    email_smtp, email_smtpport
             FROM settings LIMIT 1"
        );
        if (!$stmt_smtp) {
            throw new PHPMailer\PHPMailer\Exception('Erro ao preparar query SMTP: ' . mysqli_error($db));
        }
        mysqli_stmt_bind_param($stmt_smtp, 's', $SMTP_KEY);
        mysqli_stmt_execute($stmt_smtp);
        $row_smtp = mysqli_stmt_get_result($stmt_smtp)->fetch_assoc();
        mysqli_stmt_close($stmt_smtp);

        if (empty($row_smtp) || empty($row_smtp['email_smtp'])) {
            throw new PHPMailer\PHPMailer\Exception('Configurações SMTP não encontradas na tabela settings.');
        }

        $mail->Host       = $row_smtp['email_smtp'];
        $mail->Port       = (int)$row_smtp['email_smtpport'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $row_smtp['email_user'];
        $mail->Password   = $row_smtp['pass_dec'];
        $mail->SMTPSecure = ((int)$row_smtp['email_smtpport'] === 465)
                                ? PHPMailer::ENCRYPTION_SMTPS
                                : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAutoTLS = true;
        $mail->Timeout     = 15;

        // Remetente
        $mail->From     = $row_smtp['email_user'];
        $mail->FromName = $row_smtp['nome_app'] ?? 'GEI';
        $mail->Sender   = $row_smtp['email_user'];
        $mail->addReplyTo($row_smtp['email_user'], $row_smtp['nome_app'] ?? 'GEI');

        // Destinatários
        $addrs_validos = 0;
        foreach (array_map('trim', explode(',', $cfg['email_destino'])) as $addr) {
            if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($addr);
                $addrs_validos++;
            }
        }
        if ($addrs_validos === 0) throw new PHPMailer\PHPMailer\Exception('Nenhum endereço de email válido: ' . $cfg['email_destino']);

        $mail->isHTML(true);
        $mail->Subject = '[GEI] Backup BD ' . $DB_NAME . ' — ' . date('d/m/Y H:i');
        $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
  <div style="background-color:#003366;padding:20px 24px;">
    <h2 style="color:#ffffff;margin:0;font-size:18px;">&#128190; Backup Automático — GEI</h2>
  </div>
  <div style="padding:24px;background-color:#f9f9f9;">
    <p style="font-size:14px;color:#333;">O backup agendado da base de dados foi concluído com sucesso.</p>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Base de dados</td><td style="padding:6px 10px;color:#222;">' . htmlspecialchars($DB_NAME) . '</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Ficheiro</td><td style="padding:6px 10px;color:#222;font-family:monospace;">' . htmlspecialchars($final_name) . '</td></tr>
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;">Tamanho</td><td style="padding:6px 10px;color:#222;">' . fmt_size($file_size) . '</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Data/hora</td><td style="padding:6px 10px;color:#222;">' . date('d/m/Y H:i:s') . '</td></tr>
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;">Duração</td><td style="padding:6px 10px;color:#222;">' . $elapsed . 's</td></tr>
    </table>
    <p style="font-size:12px;color:#888;margin-top:16px;">O ficheiro de backup está em anexo. Guarde-o num local seguro.</p>
  </div>
  <div style="background-color:#eeeeee;padding:12px 24px;text-align:center;font-size:12px;color:#888;">Este email foi gerado automaticamente. Por favor não responda.</div>
</div>';
        $mail->AltBody = "Backup GEI concluído: {$final_name} (" . fmt_size($file_size) . ") — " . date('d/m/Y H:i:s');

        if ($file_size <= 20 * 1024 * 1024) {
            $mail->addAttachment($final_path, $final_name);
        } else {
            $mail->Body .= '<p style="color:#e74c3c;font-size:12px;padding:0 24px;">&#9888; Ficheiro demasiado grande para anexo (' . fmt_size($file_size) . ').</p>';
        }

        $mail->send();
        $email_ok = true;
        log_msg('OK', 'Email enviado com sucesso para ' . $cfg['email_destino']);

    } catch (PHPMailer\PHPMailer\Exception $e) {
        $erros[] = 'Email falhou: ' . $mail->ErrorInfo;
        log_msg('ERRO', 'Email: ' . $mail->ErrorInfo);
    }
}

// ── ENVIO POR FTP ─────────────────────────────────────────────────────────────
$ftp_ok = false;
if ($status === 'ok' && in_array($destino, ['ftp', 'email_ftp'], true) && !empty($cfg['ftp_host'])) {
    log_msg('INFO', "A enviar backup para FTP {$cfg['ftp_host']}...");

    if (!function_exists('ftp_connect')) {
        $erros[] = 'Extensão FTP não disponível no PHP.';
        log_msg('ERRO', 'Extensão FTP não disponível.');
    } else {
        $ftp = @ftp_connect($cfg['ftp_host'], (int)($cfg['ftp_port'] ?? 21), 15);
        if (!$ftp) {
            $erros[] = "FTP: não foi possível ligar a {$cfg['ftp_host']}";
            log_msg('ERRO', "FTP: falha na ligação a {$cfg['ftp_host']}");
        } else {
            $ftp_login = @ftp_login($ftp, $cfg['ftp_user'], $cfg['ftp_pass']);
            if (!$ftp_login) {
                $erros[] = 'FTP: autenticação falhou.';
                log_msg('ERRO', 'FTP: autenticação falhou.');
            } else {
                ftp_pasv($ftp, true);
                $ftp_dir = rtrim($cfg['ftp_dir'] ?? '/backups', '/') . '/';
                @ftp_mkdir($ftp, $ftp_dir);
                $remote = $ftp_dir . $final_name;
                if (ftp_put($ftp, $remote, $final_path, FTP_BINARY)) {
                    $ftp_ok = true;
                    log_msg('OK', "FTP: ficheiro enviado para {$remote}");
                } else {
                    $erros[] = "FTP: falha ao enviar {$remote}";
                    log_msg('ERRO', "FTP: falha ao enviar {$remote}");
                }
            }
            ftp_close($ftp);
        }
    }
}

// ── ENVIO PARA S3 (AWS SDK via Composer) ──────────────────────────────────────

// ── REGISTAR NO HISTÓRICO ─────────────────────────────────────────────────────
$destino_realizado = 'local';
if ($email_ok && $ftp_ok)        $destino_realizado = 'email+ftp';
elseif ($email_ok)               $destino_realizado = 'email';
elseif ($ftp_ok)                 $destino_realizado = 'ftp';

$notas     = empty($erros) ? null : implode(' | ', $erros);
$file_size_val = ($final_path && file_exists($final_path)) ? filesize($final_path) : 0;

$stmt_hist = mysqli_prepare($db,
    "INSERT INTO backup_historico (ficheiro, tamanho_bytes, destino, status, notas, criado_em)
     VALUES (?, ?, ?, ?, ?, NOW())"
);
if ($stmt_hist) {
    mysqli_stmt_bind_param($stmt_hist, 'sisss',
        $final_name, $file_size_val, $destino_realizado, $status, $notas
    );
    mysqli_stmt_execute($stmt_hist);
    mysqli_stmt_close($stmt_hist);
    log_msg('INFO', "Histórico registado (status: {$status}).");
} else {
    log_msg('AVISO', 'Não foi possível registar no histórico: ' . mysqli_error($db));
}

mysqli_close($db);

$exit_code = ($status === 'ok') ? 0 : 1;
log_msg('INFO', "Script terminado (código de saída: {$exit_code}).");
exit($exit_code);
