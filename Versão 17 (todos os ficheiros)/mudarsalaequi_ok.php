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

   <body class="main-layout">
      <?php include("loader.php"); ?>

      <?php include ("header.php"); ?>

      <?php
      include("sessao_timeout.php");

      // ── Sanitização de TODOS os parâmetros externos ──────────────────────────
      // GET
      $idescola  = (int)($_GET['id']     ?? 0);   // era interpolado direto no SQL → injeção SQL
      $sala_get  = (int)($_GET['sala']   ?? 0);   // usado no redirect sem cast
      $escola_get = (int)($_GET['escola'] ?? 0);  // usado no redirect sem cast

      // POST
      $novasala  = (int)($_POST['sala']  ?? 0);   // já estava correto; manter consistência
      // ─────────────────────────────────────────────────────────────────────────

      // Query com prepared statement (era concatenação direta — SQL injection)
      $stmt_esc = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ? LIMIT 1");
      $stmt_esc->bind_param('i', $idescola);
      $stmt_esc->execute();
      $rows11 = $stmt_esc->get_result()->fetch_row();
      $stmt_esc->close();
      ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <!-- Breadcrumb melhorado -->
                  <nav style="margin-bottom:10px;">
                     <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                           <span style="color:#4b6cb7;">Equipamentos</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Ver equipamentos da sala &raquo; Mudar de sala</li>
                     </ol>
                  </nav>
                  <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                     <div class="welcome-section">
                        <?php include("msg_bemvindo.php"); ?>
                     </div>

<?php

// ── Atualizar sala do equipamento ─────────────────────────────────────────────
// Todos os valores já são (int) — sem risco de injeção SQL
$stmt_ms = $db->prepare("UPDATE equipamento SET id_sala = ? WHERE id = ?");
$stmt_ms->bind_param("ii", $novasala, $idescola);
$stmt_ms->execute();
$stmt_ms->close();

mysqli_close($db);

?>

<script>
swal({
    title: 'Os dados foram guardados!',
    icon: 'success',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>verequipsala"
        + "?x=<?php echo base64_encode('2') ?>"
        + "&&si=<?php echo base64_encode((string)$sala_get) ?>"
        + "&&ies=<?php echo base64_encode((string)$escola_get) ?>";
});
</script>

<br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
