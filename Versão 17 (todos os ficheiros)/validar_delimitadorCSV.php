<?php

// ── Verificação de sessão ────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_name('gei_session');
    session_start();
}

if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

// ── Verificação do upload ────────────────────────────────────────────────────
if (
    !isset($_FILES['file']) ||
    $_FILES['file']['error'] !== UPLOAD_ERR_OK ||
    !is_uploaded_file($_FILES['file']['tmp_name'])
) {
    http_response_code(400);
    exit;
}

// Limitar tamanho a 5 MB para evitar leitura de ficheiros muito grandes.
// define() com verificação evita "constant already defined" em includes repetidos.
if (!defined('CSV_MAX_BYTES')) {
    define('CSV_MAX_BYTES', 5 * 1024 * 1024);
}

if ($_FILES['file']['size'] > CSV_MAX_BYTES) {
    http_response_code(413);
    exit;
}

// ── Deteção do delimitador ───────────────────────────────────────────────────
$delimiters = [
    'semicolon' => ';',
    'tab'       => "\t",
    'comma'     => ',',
];

$file = $_FILES['file']['tmp_name'];

$csv = file_get_contents($file, false, null, 0, CSV_MAX_BYTES);

if ($csv === false) {
    http_response_code(500);
    exit;
}

foreach ($delimiters as $key => $delim) {
    $res[$key] = substr_count($csv, $delim);
}

arsort($res);
reset($res);
$first_key = key($res);

$d = $delimiters[$first_key];
