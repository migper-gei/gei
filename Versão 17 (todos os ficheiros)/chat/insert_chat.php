<?php
/**
 * GEI Chat — Inserir mensagem
 */
include('database_connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$to_user_id   = (int)($_POST['to_user_id']   ?? 0);
$from_user_id = (int)($_SESSION['user_id']   ?? 0);
$chat_message = trim($_POST['chat_message']  ?? '');

if ($to_user_id <= 0 || $from_user_id <= 0 || $chat_message === '') {
    http_response_code(400);
    exit;
}

// Limitar tamanho
$chat_message = mb_substr($chat_message, 0, 1000);

$stmt = $connect->prepare(
    "INSERT INTO chat_message (to_user_id, from_user_id, chat_message, status)
     VALUES (?, ?, ?, 1)"
);
$stmt->bind_param("iis", $to_user_id, $from_user_id, $chat_message);
$stmt->execute();

echo fetch_user_chat_history($from_user_id, $to_user_id, $connect);
?>
