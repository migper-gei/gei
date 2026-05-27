<?php
// ================================================================
// check_email_public.php
// Endpoint AJAX — verifica se um email existe na tabela utilizadores
// Usado na recuperação de password (utilizador NÃO autenticado)
// Requer token CSRF da sessão para prevenir enumeração de emails
// ================================================================

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

header('Content-Type: application/json; charset=utf-8');

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['existe' => false]);
    exit;
}

// Validar CSRF — obrigatório mesmo sem autenticação
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode(['existe' => false]);
    exit;
}

// Validar formato do email
$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['existe' => false]);
    exit;
}

// Validar código (obrigatório para saber qual BD consultar)
$codigo = trim($_POST['codigo'] ?? '');
if (!ctype_digit($codigo) || strlen($codigo) < 1 || strlen($codigo) > 9) {
    echo json_encode(['existe' => false]);
    exit;
}

// Ligar à BD principal para obter nomebd/serverbd a partir do código
include('config_serverbd_settings.php');
$db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($db0->connect_errno) {
    http_response_code(500);
    echo json_encode(['existe' => false]);
    exit;
}

$stmtBd = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
$stmtBd->bind_param("s", $codigo);
$stmtBd->execute();
$rowBd = $stmtBd->get_result()->fetch_assoc();
$stmtBd->close();
$db0->close();

if (!$rowBd) {
    // Código não existe — não revelar pormenores
    echo json_encode(['existe' => false]);
    exit;
}

// Ligar à BD do utilizador
$db = new mysqli($rowBd['serverbd'], DB_USERNAME, DB_PASSWORD, $rowBd['nomebd']);

if ($db->connect_errno) {
    http_response_code(500);
    echo json_encode(['existe' => false]);
    exit;
}

$stmt = $db->prepare("SELECT id FROM utilizadores WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$existe = $stmt->num_rows > 0;
$stmt->close();
$db->close();

echo json_encode(['existe' => $existe]);
