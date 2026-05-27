<?php
/**
 * GEI - Renovação de sessão via AJAX
 * Chamado pelo session-warning.js quando o utilizador clica "Continuar"
 */

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

header('Content-Type: application/json');

// Sessão inválida ou expirada
if (!isset($_SESSION['login_user'])) {
    echo json_encode(['ok' => false, 'expired' => true]);
    exit;
}

// Renovar timestamp
$_SESSION['lastaccess'] = time();

echo json_encode(['ok' => true, 'lastaccess' => $_SESSION['lastaccess']]);
exit;
