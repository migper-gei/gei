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



?>


<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>
   <body class="main-layout">
     <?php include ("header.php"); ?>

     <?php
$id = (int)base64_decode($_GET["ia"]);
$enviar_para_reparadores = (base64_decode($_GET["r"] ?? '') == 1);

// Dados da avaria
$stmt_av = $db->prepare(
    "SELECT ar.*, s.nome as sn, eq.*, u.nome as nu, e.*
     FROM avarias_reparacoes ar, salas s, equipamento eq, utilizadores u, escolas e
     WHERE ar.id_sala=s.id AND eq.id=ar.id_equi AND u.email=ar.autoravaria AND e.id=ar.id_escola AND ar.id=?"
);
$stmt_av->bind_param("i", $id);
$stmt_av->execute();
$row = $stmt_av->get_result()->fetch_assoc();
$stmt_av->close();

if (!$row) {
    echo '<script>swal({title:"Avaria não encontrada!",icon:"error"}).then(function(){window.location="'.SVRURL.'avaria";});</script>';
    mysqli_close($db);
    exit;
}

// Configurar PHPMailer com exceções ativas
$mail = new PHPMailer(true);

try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();

    // Carregar configurações SMTP da BD
    include('email_settings.php');
    // Carregar remetente
    include('dados_enviar_email.php');

    $mail->isHTML(true);

    // Assunto e corpo
    $mail->Subject = 'Dados da avaria.';

    if ($row['escola_digital'] == "Sim") {
        $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
  <div style="background-color:#003366;padding:20px 24px;">
    <h2 style="color:#ffffff;margin:0;font-size:18px;">&#128295; Gestão de Avarias</h2>
  </div>
  <div style="padding:24px;background-color:#f9f9f9;">
    <p style="font-size:14px;color:#333;">Exmos. Srs., encontra-se para envio o seguinte equipamento:</p>
    <h3 style="color:#003366;border-bottom:2px solid #003366;padding-bottom:6px;">&#9888; Avaria</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Equipamento (nº série)</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['numserie']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Avaria</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['avaria']).'</td></tr>
    </table>
    <h3 style="color:#003366;border-bottom:2px solid #003366;padding-bottom:6px;margin-top:24px;">&#127968; Instituição</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Nome</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['nome_escola']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Morada</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['morada']).'</td></tr>
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;">Código Postal</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['codigopostal']).' '.htmlspecialchars($row['localidade']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Telefone</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['telefone']).'</td></tr>
    </table>
  </div>
  <div style="background-color:#eeeeee;padding:12px 24px;text-align:center;font-size:12px;color:#888;">Este email foi gerado automaticamente. Por favor não responda.</div>
</div>';
    } else {
        $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
  <div style="background-color:#003366;padding:20px 24px;">
    <h2 style="color:#ffffff;margin:0;font-size:18px;">&#128295; Gestão de Avarias</h2>
  </div>
  <div style="padding:24px;background-color:#f9f9f9;">
    <h3 style="color:#003366;border-bottom:2px solid #003366;padding-bottom:6px;margin-top:0;">&#128100; Autor da avaria</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Nome</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['nu']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Email</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['autoravaria']).'</td></tr>
    </table>
    <h3 style="color:#003366;border-bottom:2px solid #003366;padding-bottom:6px;margin-top:24px;">&#9888; Avaria</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;width:40%;">Instituição</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['nome_escola']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Sala / Equipamento</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['sn']).' / '.htmlspecialchars($row['nomeequi']).'</td></tr>
      <tr><td style="padding:6px 10px;font-weight:bold;color:#555;">Data</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['dataavaria']).'</td></tr>
      <tr style="background-color:#eef2f7;"><td style="padding:6px 10px;font-weight:bold;color:#555;">Descrição</td><td style="padding:6px 10px;color:#222;">'.htmlspecialchars($row['avaria']).'</td></tr>
    </table>
  </div>
  <div style="background-color:#eeeeee;padding:12px 24px;text-align:center;font-size:12px;color:#888;">Este email foi gerado automaticamente. Por favor não responda.</div>
</div>';
    }

    // Destinatários
    if ($row['escola_digital'] == "Sim") {
        $mail->addAddress($row['email_fornecedor']);
    } elseif ($enviar_para_reparadores && !empty($_POST['rep'])) {
        // Reparadores selecionados manualmente
        foreach ($_POST['rep'] as $rep_id) {
            $rep_id = (int)$rep_id;
            $stmt_rep = $db->prepare("SELECT email FROM utilizadores WHERE id=?");
            $stmt_rep->bind_param("i", $rep_id);
            $stmt_rep->execute();
            $rep_row = $stmt_rep->get_result()->fetch_row();
            $stmt_rep->close();
            if ($rep_row && $rep_row[0]) {
                $mail->addAddress($rep_row[0]);
            }
        }
    } else {
        // Enviar a todos os administradores e reparadores
        $stmt_dest = $db->prepare("SELECT email FROM utilizadores WHERE tipo=1 OR tipo=3");
        $stmt_dest->execute();
        $dest_result = $stmt_dest->get_result();
        while ($dest_row = $dest_result->fetch_row()) {
            $mail->addAddress($dest_row[0]);
        }
        $stmt_dest->close();
    }

    $mail->send();
    $email_enviado = true;

} catch (Exception $e) {
    $email_enviado = false;
    $email_erro = $mail->ErrorInfo;
    error_log('Erro PHPMailer enviar_email_avaria: ' . $mail->ErrorInfo);
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
