<?php
include('database_connection.php');
header("Content-type: application/json; charset=utf-8");

if (!isset($_SESSION['login_user']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$from_user_id = (int)$_SESSION['user_id'];
$to_user_id   = (int)($_POST['to_user_id'] ?? 0);

if ($to_user_id <= 0) {
    http_response_code(400);
    exit;
}

// Eliminar mensagens entre os dois utilizadores (em ambas as direções)
$stmt = $connect->prepare(
    "DELETE FROM chat_message
     WHERE (from_user_id = ? AND to_user_id = ?)
        OR (from_user_id = ? AND to_user_id = ?)"
);
$stmt->bind_param("iiii", $from_user_id, $to_user_id, $to_user_id, $from_user_id);
$stmt->execute();
$stmt->close();

echo json_encode(['ok' => true]);
?>
