<?php
/**
 * backup_http.php — GEI
 * ─────────────────────────────────────────────────────────────────────────────
 * Wrapper HTTP para disparar o backup agendado a partir do fallback curl do
 * backup_check.php (usado apenas em Windows sem php.exe acessível via CLI).
 *
 * NÃO é para uso directo por utilizadores — qualquer pedido sem HMAC válido
 * é rejeitado com 403 sem revelar informação.
 *
 * Fluxo:
 *   backup_check.php (fallback curl, Windows)
 *       → POST backup_http.php  [X-Backup-Token: HMAC-SHA256, X-Backup-Nonce, X-Backup-DB]
 *           → verifica HMAC
 *           → lança backup_cron.php via CLI em background
 *           → responde 202 imediatamente
 *
 * Variável de ambiente obrigatória:
 *   BACKUP_SECRET — mínimo 32 bytes aleatórios, definido no .env ou nas vars do sistema
 *
 * Gerar um secret seguro:
 *   php -r "echo bin2hex(random_bytes(32));"
 * ─────────────────────────────────────────────────────────────────────────────
 */

declare(strict_types=1);

// ── Só aceita pedidos HTTP — não deve ser chamado via CLI ────────────────────
if (PHP_SAPI === 'cli') {
    fwrite(STDERR, 'backup_http.php é um endpoint HTTP; use backup_cron.php para CLI.' . PHP_EOL);
    exit(1);
}

// ── Rejeitar qualquer método que não seja GET/POST ───────────────────────────
if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'POST'], true)) {
    http_response_code(405);
    exit;
}

// ── Carregar .env (mesma lógica dos outros ficheiros GEI) ────────────────────
$_gei_envFile = __DIR__ . '/.env';
if (file_exists($_gei_envFile)) {
    foreach (file($_gei_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_gei_line) {
        if (str_starts_with(trim($_gei_line), '#') || !str_contains($_gei_line, '=')) continue;
        [$_gei_k, $_gei_v] = explode('=', $_gei_line, 2);
        if (!isset($_ENV[trim($_gei_k)]) && getenv(trim($_gei_k)) === false) {
            $_ENV[trim($_gei_k)] = trim($_gei_v, " '\"");
        }
    }
}

// ── Ler secret de ambiente ───────────────────────────────────────────────────
$secret = $_ENV['BACKUP_SECRET'] ?? getenv('BACKUP_SECRET');
if (empty($secret) || strlen($secret) < 32) {
    // Sem secret configurado → recusar silenciosamente
    http_response_code(403);
    exit;
}

// ── Ler headers ──────────────────────────────────────────────────────────────
// getallheaders() pode não existir em todos os SAPIs; fallback via $_SERVER
function _gei_header(string $name): string {
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        // Normalizar para case-insensitive
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, $name) === 0) return trim($v);
        }
    }
    // Fallback $_SERVER: HTTP_X_BACKUP_TOKEN, etc.
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return trim($_SERVER[$key] ?? '');
}

$received_hmac = _gei_header('X-Backup-Token');
$nonce         = _gei_header('X-Backup-Nonce');
$db_name       = _gei_header('X-Backup-DB');

// ── Validações básicas ───────────────────────────────────────────────────────
if (empty($received_hmac) || empty($nonce) || empty($db_name)) {
    http_response_code(403);
    exit;
}

// Nonce: apenas hex, 32 chars (16 bytes × 2)
if (!preg_match('/^[0-9a-f]{32}$/i', $nonce)) {
    http_response_code(403);
    exit;
}

// db_name: apenas caracteres seguros para nome de BD MySQL
if (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $db_name)) {
    http_response_code(403);
    exit;
}

// ── Verificar HMAC-SHA256 (timing-safe) ─────────────────────────────────────
$payload       = $nonce . '|' . $db_name;
$expected_hmac = hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expected_hmac, $received_hmac)) {
    http_response_code(403);
    exit;
}

// ── Proteção anti-replay: guardar nonce usado ────────────────────────────────
// Usa um ficheiro de lock por nonce com TTL de 5 minutos.
// Produção com Redis/Memcached pode substituir por SET NX EX 300.
$nonce_file = sys_get_temp_dir() . '/.gei_backup_nonce_' . $nonce;
if (file_exists($nonce_file) && (time() - filemtime($nonce_file)) < 300) {
    // Nonce já utilizado — pedido duplicado ou replay
    http_response_code(409);
    exit;
}
@file_put_contents($nonce_file, time());
// Limpar nonces antigos (> 10 min) de forma oportunista
foreach (glob(sys_get_temp_dir() . '/.gei_backup_nonce_*') ?: [] as $_f) {
    if (file_exists($_f) && (time() - filemtime($_f)) > 600) @unlink($_f);
}

// ── Lançar backup_cron.php via CLI em background ─────────────────────────────
$cron_script = __DIR__ . '/backup_cron.php';
if (!file_exists($cron_script)) {
    http_response_code(500);
    exit;
}

$php_bin = PHP_BINARY ?: (PHP_BINDIR . '/php');
if (!file_exists($php_bin)) $php_bin = 'php';

// db_name já validado por regex — seguro para escapeshellarg
$cmd = escapeshellarg($php_bin)
     . ' ' . escapeshellarg($cron_script)
     . ' ' . escapeshellarg($db_name)
     . ' > /dev/null 2>&1 &';

$launched = false;
if (function_exists('exec'))           { exec($cmd);        $launched = true; }
elseif (function_exists('proc_open')) {
    $p = proc_open($cmd, [], $pipes);
    if ($p !== false) { proc_close($p); $launched = true; }
}
elseif (function_exists('shell_exec')) { shell_exec($cmd);  $launched = true; }

if (!$launched) {
    error_log('[GEI backup_http] Nenhuma função de execução disponível para lançar backup_cron.php');
    http_response_code(503);
    exit;
}

// ── Responder imediatamente sem bloquear ─────────────────────────────────────
http_response_code(202); // Accepted
exit;
