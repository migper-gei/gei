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
// Gerar token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>

   <body class="main-layout">
          <!-- loader  
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>
     <?php include("sessao_timeout.php"); ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span style="color:#4b6cb7;">Avarias</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Reparar</li>
                  </ol>
               </nav>
               <div class="titlepage"></div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
                  <div class="welcome-section">
                  <?php include("msg_bemvindo.php"); ?>
                  </div>

                  <?php
$id = (int)base64_decode($_GET["ia"]);

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || !isset($_POST['datarep']) || !isset($_POST['reparacao']) || !isset($_POST['repar_por'])
    || empty($_POST['datarep']) || empty($_POST['reparacao']) || empty($_POST['repar_por'])
    || empty($id) || !is_numeric($id))
{
?>
<script>window.location.href = '<?php echo SVRURL ?>avaria';</script>
<?php
    exit;
}
?>

                  <?php
// POST já verificado acima — processar dados
if (true) {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erro: token CSRF inválido.');
    }

    $_dr    = $_POST["datarep"]   ?? '';
    $_re    = $_POST["reparacao"] ?? '';
    $_repor = $_POST["repar_por"] ?? '';

    $stmt_rep = $db->prepare("UPDATE avarias_reparacoes SET datareparacao=STR_TO_DATE(?,'%Y-%m-%d'), reparacao=?, rep_efectuada_por=? WHERE id=?");
    $stmt_rep->bind_param("sssi", $_dr, $_re, $_repor, $id);
    $stmt_rep->execute();
    $stmt_rep->close();
    mysqli_close($db);

    if (isset($_POST['my_check'])) {
?>
<script>
swal({
    title: 'Os dados foram guardados!',
    text: 'Um email vai ser enviado com os dados da reparação!',
    icon: 'success',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>enviar_email_reparacao.php?ia=<?php echo base64_encode($id) ?>";
});
</script>
<?php
    } else {
?>
<script>
swal({
    title: 'Os dados foram guardados!',
    icon: 'success',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>avaria";
});
</script>
<?php
    }
}
?>

<br><br><br><br><br><br><br><br><br><br><br><br>
                    </div>
               </div>
            </div>
         </div>
      </div>

      <?php include ("footer.php"); ?>
   </body>
</html>
