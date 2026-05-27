<?php 

use PHPMailer\PHPMailer\PHPMailer;

//$mail->SMTPDebug = 1;

// --- Chave de desencriptação via variável de ambiente (não hardcoded) ---
// Deve estar definida no .env: SMTP_KEY=<mesma chave usada em grava_emailsessao.php>
$_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';

if (empty($_smtpKey)) {
?>
<script>
swal({
    title: 'Configuração de email em falta!',
    text: 'A variável SMTP_KEY não está definida no servidor. Contacte o administrador.',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>emsess";
});
</script>
<?php
    return;
}

$stmt_cfg = $db->prepare("SELECT email_user, AES_DECRYPT(pass, ?) as pass_dec, email_smtp, email_smtpport FROM settings LIMIT 1");
$stmt_cfg->bind_param("s", $_smtpKey);
$stmt_cfg->execute();
$row02 = $stmt_cfg->get_result()->fetch_assoc();
$stmt_cfg->close();

if ($row02)
{
    $mail->Host        = $row02['email_smtp'];
    $mail->Port        = (int)$row02['email_smtpport'];
    $mail->SMTPAuth    = true;
    $mail->Username    = $row02['email_user'];
    $mail->Password    = $row02['pass_dec'];
    // Usar STARTTLS (porta 587) ou SMTPS (porta 465)
    $mail->SMTPSecure  = ((int)$row02['email_smtpport'] === 465)
                            ? PHPMailer::ENCRYPTION_SMTPS
                            : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAutoTLS = true;
    $mail->Timeout     = 15;

    // NOTA: SMTPOptions com verify_peer=false foi removido.
    // A verificação do certificado TLS fica activa (comportamento padrão do PHP).
    // Se o servidor SMTP usar uma CA interna/auto-assinada, descomente e ajuste:
    //
    // $mail->SMTPOptions = [
    //     'ssl' => [
    //         'verify_peer'       => true,
    //         'verify_peer_name'  => true,
    //         'allow_self_signed' => false,
    //         'cafile'            => '/caminho/para/ca-bundle.crt',
    //     ],
    // ];
}
else
{
?>
<script>
swal({
    title: 'Ainda não foram definidas as configurações de email!',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>emsess";
});
</script>
<?php
}
?>
