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

// Requere sessão válida
if (empty($_SESSION) || count($_SESSION) === 0) {
    http_response_code(403);
    echo json_encode(['existe' => false, 'erro' => 'não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

include("config.php"); // ligação $db

$nome     = trim($_POST['nome']     ?? '');
$idescola = (int)($_POST['idescola'] ?? 0);

if ($nome === '' || $idescola < 1) {
    echo json_encode(['existe' => false]);
    exit;
}

// Verificar por nome (case-insensitive) + escola
$stmt = $db->prepare("SELECT COUNT(*) FROM salas WHERE LOWER(nome) = LOWER(?) AND id_escola = ?");
$stmt->bind_param("si", $nome, $idescola);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];
$stmt->close();

echo json_encode(['existe' => ($count > 0)]);
