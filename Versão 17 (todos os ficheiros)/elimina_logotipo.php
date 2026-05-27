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
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php include ("head.php"); ?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

      <?php include ("header.php"); ?>
      <?php include("sessao_timeout.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <nav style="margin-bottom:10px;">
                     <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                           <span style="color:#4b6cb7;">CONFIGURAÇÕES</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">ELIMINAR LOGÓTIPO</li>
                     </ol>
                  </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">

                     <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                     </div>



<?php



if ($_SESSION['tipo']==1  )
{

$sql = $db->prepare("update logotipo
set logotipo=Null");
$sql->execute();


?>




<script>

swal({
title: 'O logotipo foi eliminado!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>dadosesc";
});


</script>


<!--

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>dadosesc';
          },40);
          </script>
         -->



<?php

}
else
{
?>
    <script>

    swal({
title: 'Não tem permissão para eliminar o logótipo!',
text: 'Não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>dadosesc";
});



</script>

<?php
}

?>

          <?php mysqli_close($db); ?>

<br><br><br><br><br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>