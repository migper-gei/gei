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
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// ── Controlo de acesso: apenas administradores (tipo == 1) ────
// O parâmetro x=1 indica a rota do painel de administração.
// x=0 correspondia ao auto-registo público (desativado por segurança).
$_x_param = isset($_GET['x']) ? (int)base64_decode($_GET['x']) : -1;

if (
    !isset($_SESSION['login_user']) ||
    !isset($_SESSION['tipo'])       ||
    (int)$_SESSION['tipo'] !== 1
) {
    // Não autenticado ou sem permissão de administrador
    header('Location: ' . (defined('SVRURL') ? SVRURL : '/') . 'l');
    exit;
}

if ($_x_param !== 1) {
    // Rota inválida ou auto-registo público — bloquear
    header('Location: ' . (defined('SVRURL') ? SVRURL : '/') . 'l');
    exit;
}
// ─────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
//require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
//require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';


require __DIR__ . '/vendor/autoload.php';
?>


   </head>


   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">CONFIGURAÇÕES</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">UTILIZADORES >> INSERIR</li>
                  </ol>
               </nav>
                  <div class="titlepage">
             
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ( empty($_POST['nome']) || empty($_POST['email']))
{



   if (base64_decode($_GET['x'])==1)
   {

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>inserirutil';
}, 10);
</script>


<?php
   }
   else
   {
?>




<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>registauser';
}, 10);
</script>


<?php
}
}
?>




<?php



if($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Validação CSRF ---
    if (empty($_POST['token']) || empty($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
        ?>
        <script>
        swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>inserirutil"; });
        </script>
        <?php
        exit;
    }
    // Invalidar token após uso (one-time use)
    unset($_SESSION['token']);

    // --- Sanitização dos inputs ---
    $no = trim(htmlspecialchars($_POST["nome"] ?? '', ENT_QUOTES, 'UTF-8'));
    $em = strtolower(trim($_POST["email"] ?? ''));

    // --- Validação de formato ---
    if (empty($no) || empty($em) || !filter_var($em, FILTER_VALIDATE_EMAIL)) {
        ?>
        <script>
        swal({ title: 'Dados inválidos!', text: 'Nome e email são obrigatórios e o email deve ter formato válido.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>inserirutil"; });
        </script>
        <?php
        exit;
    }

    // --- Validação do tipo ---
    $tp = isset($_POST['tipo']) ? (int)$_POST['tipo'] : 0;
    if ($tp < 1 || $tp > 4) {
        ?>
        <script>
        swal({ title: 'Tipo inválido!', text: 'Selecione um tipo de utilizador válido (1 a 4).', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>inserirutil"; });
        </script>
        <?php
        exit;
    }

    
// Verificar duplicado de email no servidor (barreira definitiva)
$stmtEmail = $db->prepare("SELECT COUNT(*) FROM utilizadores WHERE email = ?");
$stmtEmail->bind_param("s", $em);
$stmtEmail->execute();
$contaem = $stmtEmail->get_result()->fetch_row()[0];
$stmtEmail->close();



if ($contaem==1)
{

?>
  




<?php
if (base64_decode($_GET['x'])==1)
{
?>


 <script>
         
         swal({
   title: 'ERRO',
   text: 'O email já está registado!',
 icon: 'error',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>inserirutil";
})
;

         </script>

<?php
}
else
{
?>


<script>
         
         swal({
   title: 'ERRO',
   text: 'O email já está registado!',
 icon: 'error',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>registauser";
})
;

         </script>

         <?php
}
         ?>




<?php

}
elseif ($contaem==0)  //não existe o email

{

   $sql01 = "SELECT email_user, pass, email_smtp, email_smtpport FROM settings LIMIT 1";
   $result01 = mysqli_query($db,$sql01);
   $row01 = mysqli_fetch_array($result01);
   $row01['c'] = $row01 ? 1 : 0;
   
   
   
   if ($row01['c']==0)
   {
?>

<script>
         
         swal({
            title: 'Ainda não foram definidas as configurações de email!',
            icon: 'error',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>emsess";
})
;

         </script>

<?php

   }


elseif(($row01['c']>0))
{


   function geraSenha($tamanho = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%*-';
    $len = strlen($chars);
    $pass = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $pass .= $chars[random_int(0, $len - 1)];
    }
    return $pass;
}

   $pa = geraSenha(12);



// SEGURANÇA: password_hash Argon2id em vez de AES_ENCRYPT
$paHash = password_hash($pa, PASSWORD_ARGON2ID);
$stmtInsert = $db->prepare("INSERT INTO utilizadores (nome, email, tipo, pass) VALUES (?, ?, ?, ?)");
$stmtInsert->bind_param("ssis", $no, $em, $tp, $paHash);
$stmtInsert->execute();
$stmtInsert->close();



// --- Envio de email ---
$mail = new PHPMailer(true); // true = activa excepções

try {

    $mail->CharSet  = 'UTF-8';
    $mail->IsSMTP();
    $mail->SMTPAuth = true;

    include('email_settings.php');
    include('dados_enviar_email.php');

    // FIX 1: declarar o email como HTML
    $mail->isHTML(true);

    // FIX 2: Subject sem palavras suspeitas de spam
    $mail->Subject = 'Dados de acesso ao sistema PTE';

    // FIX 3: corpo HTML com linguagem neutra (evita filtros de spam)
    $mail->Body = '
        <div style="font-family:Arial,sans-serif;font-size:14px;color:#333;">
            <p>Olá <strong>' . htmlspecialchars($no, ENT_QUOTES, 'UTF-8') . '</strong>,</p>
            <p>A sua conta foi criada com sucesso. Utilize os dados abaixo para iniciar sessão:</p>
            <p><strong>Código de acesso:</strong></p>
            <p style="font-size:20px;font-weight:bold;letter-spacing:2px;color:#4b6cb7;">' . $pa . '</p>
            <p style="color:#888;font-size:12px;">Recomendamos que altere este código após o primeiro acesso.</p>
            <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
            <p style="font-size:11px;color:#aaa;">Por favor não responda a este email.<br>A equipa PTE</p>
            <br>
            <img width="20%" height="20%" src="cid:imagem_embutida" alt="PTE" />
        </div>';

    // FIX 4: versão texto simples como fallback (melhora entregabilidade)
    $mail->AltBody = "Olá {$no},\n\nA sua conta foi criada.\nCódigo de acesso: {$pa}\n\nRecomendamos que altere este código após o primeiro acesso.\n\nPor favor não responda a este email.\nA equipa PTE";

    $mail->addEmbeddedImage('images/logo_aemgn.png', 'imagem_embutida', 'imagem.jpg');

    $mail->AddAddress($em);

    // FIX 5: enviar e registar resultado no log
    if ($mail->send()) {
        error_log("[PTE] Email de registo enviado com sucesso para: {$em}");
    } else {
        error_log("[PTE] Falha no envio do email para: {$em} | Erro: " . $mail->ErrorInfo);
    }

} catch (Exception $e) {
    // FIX 6: capturar excepções PHPMailer e registar detalhes
    error_log("[PTE] Excepção PHPMailer ao enviar para {$em}: " . $mail->ErrorInfo);
}


?>


<?php
if (base64_decode($_GET['x'])==1)
{
?>


 <script>
         
         swal({
title: 'Os dados foram guardados!',
text: 'Email enviado com os dados de acesso. (Verifique a caixa de entrada e a caixa de spam)',
icon: 'success',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>utiliz";
})
;

         </script>

<?php
}
else
{
?>


<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Email enviado com os dados de acesso. (Verifique a caixa de entrada e a caixa de spam)',
icon: 'success',

})
.then(function() {
window.location = "<?php echo SVRURL ?>l";
})
;


</script>




<?php
}

}

}

?>




<?php
}
mysqli_close($db);
?>


  </div>
</div>


</div>
         </div>
      </div>
      <!-- end about -->
    
<br><br><br><br><br><br><br><br><br><br><br><br><br>
      <?php include ("footer.php");?>



</body>
</html>
