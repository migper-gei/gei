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

$nome = trim($_POST['nome'] ?? '');
$sala = (int)($_POST['sala'] ?? 0);

if ($nome === '' || $sala < 1) {
    echo json_encode(['existe' => false]);
    exit;
}

// Verificação case-insensitive por nome + sala (igual ao grava_outro_equipamento.php)
$stmt = $db->prepare("SELECT COUNT(*) FROM outro_equipamento WHERE LOWER(nomeoutro) = LOWER(?) AND id_sala = ?");
$stmt->bind_param("si", $nome, $sala);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];
$stmt->close();

echo json_encode(['existe' => ($count > 0)]);
