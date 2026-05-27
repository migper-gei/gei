<?php
/**
 * backup_check.php — GEI
 * ─────────────────────────────────────────────
 * Incluir no topo de qualquer página PHP da aplicação (ex: index.php, dashboard.php).
 * Verifica silenciosamente se está na hora de executar o backup agendado
 * e, se sim, dispara o backup_cron.php em background sem bloquear a página.
 *
 * Uso:
 *   include_once __DIR__ . '/backup_check.php';
 * ─────────────────────────────────────────────
 */

// Só verifica se existe ligação à BD ($db) e utilizador em sessão
if (!isset($db) || !($db instanceof mysqli)) return;

// Evitar verificações desnecessárias — usar ficheiro de lock para não
// correr mais do que uma vez por minuto, mesmo com muitos utilizadores
$lock_file = sys_get_temp_dir() . '/.backup_last_check_gei';
$now       = time();

// Verificar no máximo uma vez por minuto
if (file_exists($lock_file) && ($now - filemtime($lock_file)) < 60) return;

// Atualizar timestamp do lock
@file_put_contents($lock_file, $now);

// ── Ler configuração de agendamento da BD ────────────────────────────────────
$tbl = @$db->query("SHOW TABLES LIKE 'backup_agendamento'");
if (!$tbl || $tbl->num_rows === 0) return;

$r = @$db->query("SELECT * FROM backup_agendamento LIMIT 1");
if (!$r) return;
$cfg = $r->fetch_assoc();
if (!$cfg || empty($cfg['ativo'])) return;

// ── Verificar janela horária (±2 horas da hora configurada) ──────────────────
// Feito ANTES das queries ao histórico para evitar trabalho desnecessário
$hora_cfg = $cfg['hora'] ?? '02:00';
$t_cfg    = strtotime(date('Y-m-d') . ' ' . $hora_cfg);
if (abs($now - $t_cfg) > 9000) return; // Fora da janela de 5 horas

$janela_inicio = date('Y-m-d H:i:s', $t_cfg - 9000);
$janela_fim    = date('Y-m-d H:i:s', $t_cfg + 9000);

// ── Verificar se já correu dentro desta janela horária ───────────────────────
// Usa a janela de ±2h em vez de "hoje inteiro", para que backups manuais
// feitos fora da janela não bloqueiem o backup agendado.
$tbl_hist = @$db->query("SHOW TABLES LIKE 'backup_historico'");
if ($tbl_hist && $tbl_hist->num_rows > 0) {
    $freq = $cfg['frequencia'] ?? 'diario';

    if ($freq === 'diario') {
        // Já correu dentro da janela de ±2h de hoje?
        $res = @$db->query("SELECT id FROM backup_historico
                            WHERE status = 'ok'
                            AND criado_em BETWEEN '$janela_inicio' AND '$janela_fim'
                            LIMIT 1");
        if ($res && $res->num_rows > 0) return;

    } elseif ($freq === 'semanal') {
        // É o dia da semana configurado?
        $dia_cfg = (int)($cfg['dia_semana'] ?? 1);
        if ((int)date('N') !== $dia_cfg) return;
        // Já correu dentro da janela desta semana?
        $res = @$db->query("SELECT id FROM backup_historico
                            WHERE status = 'ok'
                            AND criado_em BETWEEN '$janela_inicio' AND '$janela_fim'
                            LIMIT 1");
        if ($res && $res->num_rows > 0) return;

    } elseif ($freq === 'mensal') {
        // É o dia do mês configurado?
        $dia_cfg = (int)($cfg['dia_mes'] ?? 1);
        if ((int)date('j') !== $dia_cfg) return;
        // Já correu dentro da janela deste mês?
        $res = @$db->query("SELECT id FROM backup_historico
                            WHERE status = 'ok'
                            AND criado_em BETWEEN '$janela_inicio' AND '$janela_fim'
                            LIMIT 1");
        if ($res && $res->num_rows > 0) return;
    }
}

// ── Disparar backup em background ────────────────────────────────────────────
$cron_script = __DIR__ . '/backup_cron.php';
if (!file_exists($cron_script)) return;

// Tentar obter o binário PHP de várias formas
$php_bin = PHP_BINARY;
if (empty($php_bin) || !file_exists($php_bin)) {
    $php_bin = PHP_BINDIR . '/php';
}
if (empty($php_bin) || !file_exists($php_bin)) {
    $php_bin = 'php'; // fallback para PATH do sistema
}

// ── Disparar backup em background (compatível com XAMPP/Windows) ─────────────
$cron_script = __DIR__ . '/backup_cron.php';
if (!file_exists($cron_script)) return;

// Obter o nome da BD da sessão activa
$db_name = $_SESSION['nobd'] ?? '';
if (empty($db_name)) return; // Sem BD na sessão, não é possível fazer backup

$dispatched = false;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

    // Caminho real do php.exe no XAMPP
    $php_candidates = [
        'C:\xampp\php\php.exe',
        PHP_BINDIR . '\php.exe',
        'php',
    ];

    $php_bin = null;
    foreach ($php_candidates as $candidate) {
        if (file_exists($candidate)) {
            $php_bin = $candidate;
            break;
        }
    }

    if ($php_bin) {
        // Passar o nome da BD como argumento ao cron
        $cmd = 'start /B "" ' . escapeshellarg($php_bin) . ' ' . escapeshellarg($cron_script) . ' ' . escapeshellarg($db_name) . ' > NUL 2>&1';
        exec($cmd);
        $dispatched = true;
    }

    // Fallback — curl HTTP (não depende de php.exe)
    // Aponta para backup_http.php (wrapper HTTP que verifica HMAC e chama backup_cron.php via CLI)
    // NUNCA aponta para backup_cron.php directamente — esse script rejeita pedidos HTTP (PHP_SAPI !== 'cli')
    if (!$dispatched && function_exists('curl_init')) {
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host  = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        $dir   = dirname($_SERVER['SCRIPT_NAME']);
        $url   = $proto . '://' . $host . rtrim($dir, '/') . '/backup_http.php';

        // HMAC-SHA256 com secret de ambiente — nunca MD5 com valor fixo
        $secret    = $_ENV['BACKUP_SECRET'] ?? getenv('BACKUP_SECRET');
        if (empty($secret)) {
            // BACKUP_SECRET não definido: registar erro e não disparar
            @file_put_contents(
                __DIR__ . '/backups/backup_erro.log',
                date('Y-m-d H:i:s') . " — BACKUP_SECRET não definido no ambiente; fallback curl cancelado\n",
                FILE_APPEND
            );
        } else {
            $nonce     = bin2hex(random_bytes(16));          // previne replay
            $payload   = $nonce . '|' . $db_name;
            $hmac      = hash_hmac('sha256', $payload, $secret);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT,        2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-Backup-Token: ' . $hmac,
                'X-Backup-Nonce: ' . $nonce,
                'X-Backup-DB: '    . $db_name,
            ]);
            @curl_exec($ch);
            curl_close($ch);
            $dispatched = true;
        }
    }

} else {
    // Linux/Mac
    $php_bin = PHP_BINARY ?: (PHP_BINDIR . '/php') ?: 'php';
    $cmd     = escapeshellarg($php_bin) . ' ' . escapeshellarg($cron_script) . ' ' . escapeshellarg($db_name) . ' > /dev/null 2>&1 &';

    if (function_exists('exec'))          { exec($cmd);        $dispatched = true; }
    elseif (function_exists('proc_open')) {
        $p = proc_open($cmd, [], $pipes);
        if ($p !== false) { proc_close($p); $dispatched = true; }
    }
    elseif (function_exists('shell_exec')) { shell_exec($cmd); $dispatched = true; }
}

// Nenhuma função disponível — registar erro no log
if (!$dispatched) {
    @file_put_contents(
        __DIR__ . '/backups/backup_erro.log',
        date('Y-m-d H:i:s') . " — Nenhuma função de execução disponível\n",
        FILE_APPEND
    );
}
