<?php
// ================================================================
// verifica_codigo.php
// Endpoint AJAX — verifica se um código existe na tabela settingsbd
// Chamado pelo login.php no onBlur do campo "Código"
// ================================================================

// Sessão segura (necessário para validar CSRF)
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

header('Content-Type: application/json');

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['existe' => false]);
    exit;
}

// Validar CSRF
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    echo json_encode(['existe' => false]);
    exit;
}

// Validar formato do código
$codigo = trim($_POST['codigo'] ?? '');
if (!ctype_digit($codigo) || strlen($codigo) < 1 || strlen($codigo) > 9) {
    echo json_encode(['existe' => false]);
    exit;
}

// Verificar na BD
include ("config_serverbd_settings.php");
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($db->connect_errno) {
    echo json_encode(['existe' => false]);
    exit;
}

$stmt = $db->prepare("SELECT COUNT(*) FROM settingsbd WHERE codigo = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$count = (int) $stmt->get_result()->fetch_row()[0];
$stmt->close();
mysqli_close($db);

echo json_encode(['existe' => $count > 0]);
