<?php
// =====================================================================
// streamvideo.php — ficheiro AUTÓNOMO, chamado directamente pelo browser
// NÃO passa pelo index.php/router — assim os headers HTTP ficam limpos
// =====================================================================

// Sessão segura (igual aos outros ficheiros)
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

// Requer sessão autenticada
if (empty($_SESSION['email'])) {
    http_response_code(403);
    exit('Acesso negado.');
}

// Definir SVRURL antes de incluir config.php (necessário para o redirect de sessão inválida)
if (!defined('SVRURL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'];
    // Detectar o subpath da aplicação (ex: /gei/)
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $basePath  = rtrim($scriptDir, '/') . '/';
    define('SVRURL', $protocol . '://' . $host . $basePath);
}

include __DIR__ . '/config.php';

// Validar ID da avaria — vem na querystring: ?id=MTI=
$idav = 0;
if (!empty($_GET['id'])) {
    $idav = (int) base64_decode(urldecode($_GET['id']));
}

if ($idav <= 0) {
    http_response_code(400);
    exit('ID inválido.');
}

$em       = $_SESSION['email'];
$tipo     = $_SESSION['tipo'] ?? 0;
$isAdmRep = ($tipo == 1 || $tipo == 3);

if ($isAdmRep) {
    $stmt = $db->prepare("SELECT video FROM avarias_reparacoes WHERE id = ?");
    $stmt->bind_param("i", $idav);
} else {
    $stmt = $db->prepare("SELECT video FROM avarias_reparacoes WHERE id = ? AND autoravaria = ?");
    $stmt->bind_param("is", $idav, $em);
}

$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
mysqli_close($db);

if (!$row || empty($row['video'])) {
    http_response_code(404);
    exit('Vídeo não encontrado.');
}

$videoData = $row['video'];
$videoSize = strlen($videoData);

// Detectar MIME real a partir dos bytes
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeReal = finfo_buffer($finfo, $videoData);
finfo_close($finfo);

$allowedMimes = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/webm'];
if (!in_array($mimeReal, $allowedMimes)) {
    http_response_code(415);
    exit('Tipo de vídeo não suportado.');
}

// Range requests (obrigatório para seekbar e reprodução em Chrome/Firefox)
$start  = 0;
$end    = $videoSize - 1;
$length = $videoSize;

if (isset($_SERVER['HTTP_RANGE'])) {
    preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches);
    $start  = (int) $matches[1];
    $end    = ($matches[2] !== '') ? (int) $matches[2] : $videoSize - 1;
    $end    = min($end, $videoSize - 1);
    $length = $end - $start + 1;

    http_response_code(206);
    header("Content-Range: bytes {$start}-{$end}/{$videoSize}");
} else {
    http_response_code(200);
}

// Limpar output buffering pendente
while (ob_get_level()) ob_end_clean();

header("Content-Type: {$mimeReal}");
header("Content-Length: {$length}");
header("Accept-Ranges: bytes");
header("Cache-Control: no-store");

echo substr($videoData, $start, $length);
exit;
