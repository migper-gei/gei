<?php
/**
 * GEI - Credenciais da base de dados principal
 *
 * Prioridade: .env → variáveis de ambiente do sistema → erro
 */

// Carregar .env se existir (não obrigatório em produção com env vars do sistema)
$_gei_envFile = __DIR__ . '/.env';
if (file_exists($_gei_envFile)) {
    foreach (file($_gei_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_gei_line) {
        if (str_starts_with(trim($_gei_line), '#') || !str_contains($_gei_line, '=')) continue;
        [$_gei_k, $_gei_v] = explode('=', $_gei_line, 2);
        // Só definir se ainda não estiver definida no ambiente do sistema
        if (!isset($_ENV[trim($_gei_k)]) && getenv(trim($_gei_k)) === false) {
            $_ENV[trim($_gei_k)] = trim($_gei_v, " '\"");
        }
    }
    unset($_gei_line, $_gei_k, $_gei_v);
}
unset($_gei_envFile);

// Função auxiliar para ler variável de ambiente (sem fallback hardcoded)
// Apenas definida se ainda não existir (pode ser incluído após config_serverbd_settings.php)
if (!function_exists('_gei_env')) {
    function _gei_env(string $key): string {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            throw new \RuntimeException("Variável de ambiente obrigatória não definida: {$key}");
        }
        return (string) $value;
    }
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', _gei_env('DB_USER'));
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', _gei_env('DB_PASS'));
}

// ── Auto-bootstrap do BACKUP_SECRET ──────────────────────────────────────────
// Se a variável não existir (instalação nova), gera um secret aleatório e
// persiste-o no .env automaticamente — sem intervenção manual.
// Em produção com vars de ambiente do sistema, este bloco é ignorado.
if (empty($_ENV['BACKUP_SECRET']) && getenv('BACKUP_SECRET') === false) {
    $_gei_secret_file = __DIR__ . '/.env';
    $_gei_new_secret  = bin2hex(random_bytes(32)); // 256 bits

    // Escrever no .env apenas se o ficheiro existir e for gravável
    // (evita criar um .env inesperado em ambientes que usam vars de sistema)
    if (file_exists($_gei_secret_file) && is_writable($_gei_secret_file)) {
        file_put_contents(
            $_gei_secret_file,
            PHP_EOL . '# Gerado automaticamente na primeira instalação' . PHP_EOL .
            'BACKUP_SECRET=' . $_gei_new_secret . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    // Disponibilizar imediatamente nesta execução
    $_ENV['BACKUP_SECRET'] = $_gei_new_secret;
    unset($_gei_secret_file, $_gei_new_secret);
}
