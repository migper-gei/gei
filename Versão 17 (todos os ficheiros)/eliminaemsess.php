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
<?php include("head.php"); ?>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>
      <?php include("sessao_timeout.php"); ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2>Eliminar configuração Email/Sessão</h2>
                  </div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
                     <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                     </div>

<?php

require_once('gei_audit.php');

$pode_eliminar = isset($_SESSION['tipo']) && (int)$_SESSION['tipo'] === 1;

if ($pode_eliminar) {

    // ── 1. Capturar snapshot ANTES de apagar ─────────────────────────────
    $stmt_snap = $db->prepare("SELECT id, email_user, email_smtp FROM settings LIMIT 1");
    $stmt_snap->execute();
    $snap = $stmt_snap->get_result()->fetch_assoc();
    $stmt_snap->close();

    $detalhe = '(registo não encontrado)';
    $snap_id = 0;
    if ($snap) {
        $snap_id = (int)$snap['id'];
        $detalhe = 'id=' . $snap['id']
                 . ' | email=' . ($snap['email_user'] ?? '')
                 . ' | smtp='  . ($snap['email_smtp'] ?? '');
    }

    // ── 2. Apagar ─────────────────────────────────────────────────────────
    $sql = $db->prepare("DELETE FROM settings LIMIT 1");
    $sql->execute();
    $sql->close();

    // ── 3. Auditar ────────────────────────────────────────────────────────
    gei_audit($db, 'eliminar', 'settings', $snap_id, $detalhe);

    mysqli_close($db);
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>emsess';
}, 10);
</script>
<?php

} else {
?>
<script>
swal({
    title: 'Não pode eliminar!',
    text: 'Não tem permissão para eliminar a configuração.',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>emsess";
});
</script>
<?php
}
?>

<br><br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
