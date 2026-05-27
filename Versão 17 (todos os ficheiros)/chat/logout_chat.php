<?php
/**
 * GEI Chat — Terminar sessão / Logout
 */
include('database_connection.php');

$_uid = (int)($_SESSION['user_id'] ?? 0);
if ($_uid > 0) {
    $stmt = $connect->prepare(
        "UPDATE utilizadores SET sessao_ativa='0' WHERE id=?"
    );
    $stmt->bind_param("i", $_uid);
    $stmt->execute();
    $stmt->close();
}
?>
<script>window.close();</script>
