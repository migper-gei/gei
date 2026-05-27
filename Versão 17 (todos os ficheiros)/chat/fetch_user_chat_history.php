<?php
include('database_connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

echo fetch_user_chat_history(
    (int)($_SESSION['user_id'] ?? 0),
    (int)($_POST['to_user_id'] ?? 0),
    $connect
);
?>
