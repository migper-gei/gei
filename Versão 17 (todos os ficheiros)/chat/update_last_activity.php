<?php
/**
 * GEI Chat — Atualizar última atividade do utilizador
 */
include('database_connection.php');

$login_details_id = (int)($_SESSION['login_details_id'] ?? 0);

if ($login_details_id <= 0) {
    http_response_code(400);
    exit;
}

$stmt = $connect->prepare(
    "UPDATE login_details SET last_activity=NOW() WHERE login_details_id=?"
);
$stmt->bind_param("i", $login_details_id);
$stmt->execute();
?>
