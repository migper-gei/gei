<?php
/**
 * GEI - Credenciais da base de dados de settings (multi-escola)
 *
 * Esta BD contém a tabela `settingsbd` com os códigos de acesso
 * às bases de dados de cada escola/instituição.
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
if (!function_exists('_gei_env')) {
    function _gei_env(string $key): string {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            throw new \RuntimeException("Variável de ambiente obrigatória não definida: {$key}");
        }
        return (string) $value;
    }
}

if (!defined('DB_SERVER')) {
    define('DB_SERVER',   _gei_env('DB_HOST'));
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', _gei_env('DB_USER'));
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', _gei_env('DB_PASS'));
}
if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', _gei_env('DB_SETTINGS_NAME'));
}
