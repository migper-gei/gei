<?php
// Sessão segura
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
}

// Apenas aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Requere sessão válida (utilizador autenticado)
if (empty($_SESSION['user_id']) && empty($_SESSION['utilizador'])) {
    http_response_code(403);
    echo json_encode(['existe' => false, 'erro' => 'não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

include("config.php"); // ligação $db

$email = trim($_POST['email'] ?? '');

// Validação básica de formato
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['existe' => false]);
    exit;
}

// Prepared statement — elimina necessidade de escape manual
$stmt = mysqli_prepare($db, "SELECT id FROM utilizadores WHERE email = ? LIMIT 1");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['existe' => false, 'erro' => 'erro interno']);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

echo json_encode(['existe' => (mysqli_stmt_num_rows($stmt) > 0)]);

mysqli_stmt_close($stmt);
