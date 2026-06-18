<?php

use PHPMailer\PHPMailer\PHPMailer;

//$mail->SMTPDebug = 1;

// $emailConfigOk é inicializada a false antes do include (em gravauser.php)
// Este ficheiro define-a a true apenas se tudo correr bem.

// --- Chave de desencriptação via variável de ambiente ---
$_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';

if (empty($_smtpKey)) {
    error_log("[PTE] email_settings.php: SMTP_KEY não está definida no servidor.");
    // $emailConfigOk permanece false — o caller trata o erro
    return;
}

$stmt_cfg = $db->prepare("SELECT email_user, AES_DECRYPT(pass, ?) as pass_dec, email_smtp, email_smtpport FROM settings LIMIT 1");
$stmt_cfg->bind_param("s", $_smtpKey);
$stmt_cfg->execute();
$row02 = $stmt_cfg->get_result()->fetch_assoc();
$stmt_cfg->close();

if ($row02 && !empty($row02['email_smtp']) && !empty($row02['pass_dec'])) {
    $mail->Host        = $row02['email_smtp'];
    $mail->Port        = (int)$row02['email_smtpport'];
    $mail->SMTPAuth    = true;
    $mail->Username    = $row02['email_user'];
    $mail->Password    = $row02['pass_dec'];
    $mail->SMTPSecure  = ((int)$row02['email_smtpport'] === 465)
                            ? PHPMailer::ENCRYPTION_SMTPS
                            : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAutoTLS = true;
    $mail->Timeout     = 15;

    // Certificado TLS activo por defeito.
    // Se o servidor SMTP usar CA interna/auto-assinada, descomente:
    // $mail->SMTPOptions = [
    //     'ssl' => [
    //         'verify_peer'       => true,
    //         'verify_peer_name'  => true,
    //         'allow_self_signed' => false,
    //         'cafile'            => '/caminho/para/ca-bundle.crt',
    //     ],
    // ];

    $emailConfigOk = true;

} else {
    error_log("[PTE] email_settings.php: settings não encontradas na BD ou pass_dec vazia. Verifique SMTP_KEY e a tabela settings.");
    // $emailConfigOk permanece false
}
?>
