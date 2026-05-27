<?php

$stmt_from = $db->prepare("SELECT email_user, nome_app FROM settings LIMIT 1");
$stmt_from->execute();
$row00 = $stmt_from->get_result()->fetch_assoc();
$stmt_from->close();

if ($row00) {
    $mail->IsHTML(true);
    $mail->From       = $row00['email_user'];
    $mail->FromName   = $row00['nome_app'];
    $mail->Sender     = $row00['email_user'];
    $mail->AddReplyTo($row00['email_user'], $row00['nome_app']);
}
?>
