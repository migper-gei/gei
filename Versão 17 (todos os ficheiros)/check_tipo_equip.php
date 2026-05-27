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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (empty($_SESSION) || count($_SESSION) === 0) {
    http_response_code(403);
    echo json_encode(['existe' => false, 'erro' => 'não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

include("config.php");

$nome = trim($_POST['nome'] ?? '');

if ($nome === '') {
    echo json_encode(['existe' => false]);
    exit;
}

// Comparação case-insensitive (LOWER em ambos os lados)
$stmt = $db->prepare("SELECT COUNT(*) FROM tipos_equipamento WHERE LOWER(nome) = LOWER(?)");
$stmt->bind_param("s", $nome);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];
$stmt->close();

echo json_encode(['existe' => ($count > 0)]);
