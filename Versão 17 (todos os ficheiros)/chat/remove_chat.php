<?php
/**
 * GEI Chat — Eliminar mensagem (soft delete: status=2)
 */
include('database_connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$chat_message_id = (int)($_POST['chat_message_id'] ?? 0);
$from_user_id    = (int)($_SESSION['user_id']      ?? 0);

if ($chat_message_id <= 0 || $from_user_id <= 0) {
    http_response_code(400);
    exit;
}

$stmt = $connect->prepare(
    "UPDATE chat_message SET status='2' WHERE chat_message_id=? AND from_user_id=?"
);
$stmt->bind_param("ii", $chat_message_id, $from_user_id);
$stmt->execute();
?>
