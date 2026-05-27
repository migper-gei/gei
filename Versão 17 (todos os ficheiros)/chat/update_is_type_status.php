<?php
/**
 * GEI Chat — Atualizar estado "a escrever"
 */
include('database_connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$is_type = in_array($_POST['is_type'] ?? '', ['yes', 'no'])
    ? $_POST['is_type']
    : 'no';

$login_details_id = (int)($_SESSION['login_details_id'] ?? 0);

if ($login_details_id <= 0) {
    http_response_code(400);
    exit;
}

$stmt = $connect->prepare(
    "UPDATE login_details SET is_type=? WHERE login_details_id=?"
);
$stmt->bind_param("si", $is_type, $login_details_id);
$stmt->execute();
?>
