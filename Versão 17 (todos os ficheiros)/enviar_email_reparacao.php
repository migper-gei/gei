<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// ─────────────────────────────────────────────
//  PHPMailer 6.x — instalação manual (ficheiros src)
// ─────────────────────────────────────────────

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

//require __DIR__ . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>

   <!-- body -->
   <body class="main-layout">
     <?php include ("header.php"); ?>

     <?php
$id = (int)base64_decode($_GET["ia"]);

$stmt_rep2 = $db->prepare(
    "SELECT ar.*, s.nome, eq.nomeequi
     FROM avarias_reparacoes ar, salas s, equipamento eq
     WHERE ar.id_sala=s.id AND eq.id=ar.id_equi AND ar.id=?"
);
$stmt_rep2->bind_param("i", $id);
$stmt_rep2->execute();
$row = $stmt_rep2->get_result()->fetch_assoc();
$stmt_rep2->close();

if (!$row) {
    echo '<script>swal({title:"Reparação não encontrada!",icon:"error"}).then(function(){window.location="'.SVRURL.'avaria";});</script>';
    mysqli_close($db);
    exit;
}

// Configurar PHPMailer com exceções ativas
$mail = new PHPMailer(true);

try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();

    include('email_settings.php');
    include('dados_enviar_email.php');

    $mail->isHTML(true);
    $mail->Subject = 'Reparação da avaria.';
    $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">

  <div style="background-color:#003366;padding:20px 24px;">
    <h2 style="color:#ffffff;margin:0;font-size:18px;letter-spacing:1px;">&#128295; Gestão de Avarias</h2>
  </div>

  <div style="padding:24px;background-color:#f9f9f9;">

    <h3 style="color:#003366;border-bottom:2px solid #003366;padding-bottom:6px;margin-top:0;">&#9888; Avaria</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr>
        <td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Sala / Equipamento</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['nome']).' / '.htmlspecialchars($row['nomeequi']).'</td>
      </tr>
      <tr style="background-color:#eef2f7;">
        <td style="padding:6px 10px;font-weight:bold;color:#555;">Data da avaria</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['dataavaria']).'</td>
      </tr>
      <tr>
        <td style="padding:6px 10px;font-weight:bold;color:#555;">Descrição</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['avaria']).'</td>
      </tr>
    </table>

    <h3 style="color:#006633;border-bottom:2px solid #006633;padding-bottom:6px;margin-top:24px;">&#10003; Reparação</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr>
        <td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Data da reparação</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['datareparacao']).'</td>
      </tr>
      <tr style="background-color:#eef2f7;">
        <td style="padding:6px 10px;font-weight:bold;color:#555;">Descrição</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['reparacao']).'</td>
      </tr>
      <tr>
        <td style="padding:6px 10px;font-weight:bold;color:#555;">Reparado por</td>
        <td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['rep_efectuada_por']).'</td>
      </tr>
    </table>

  </div>

  <div style="background-color:#eeeeee;padding:12px 24px;text-align:center;font-size:12px;color:#888;">
    Este email foi gerado automaticamente. Por favor não responda.
  </div>

</div>';

    $mail->addAddress($row['autoravaria']);

    $mail->send();
    $email_enviado = true;

} catch (Exception $e) {
    $email_enviado = false;
    $email_erro = $mail->ErrorInfo;
    error_log('Erro PHPMailer enviar_email_reparacao: ' . $mail->ErrorInfo);
}

mysqli_close($db);
?>

<script>
<?php if ($email_enviado): ?>
swal({
    title: 'O email foi enviado!',
    icon: 'success'
}).then(function() {
    window.location = "<?php echo SVRURL ?>avaria";
});
<?php else: ?>
swal({
    title: 'Erro ao enviar email!',
    text: '<?php echo addslashes($email_erro ?? "Erro desconhecido"); ?>',
    icon: 'error'
}).then(function() {
    window.location = "<?php echo SVRURL ?>avaria";
});
<?php endif; ?>
</script>

   </body>
</html>
