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
         

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span style="color:#4b6cb7;">AVARIAS</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Eliminar</li>
                  </ol>
               </nav>
               <div class="titlepage">
                 
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>


<?php

//$nome = $_GET["nome"];


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = 'avaria';
}, 10);
</script>
<?php
}



$id = (int)$url[0];

require_once('gei_audit.php');

// ── 1. Capturar snapshot ANTES de apagar ─────────────────────────────────────
$stmt_snap = $db->prepare("
    SELECT
        ar.id,
        ar.avaria,
        s.nome          AS sala_nome,
        e.nome_escola   AS escola_nome
    FROM avarias_reparacoes ar
    LEFT JOIN salas   s ON s.id = ar.id_sala
    LEFT JOIN escolas e ON e.id = ar.id_escola
    WHERE ar.id = ?
    LIMIT 1
");
$stmt_snap->bind_param('i', $id);
$stmt_snap->execute();
$snap = $stmt_snap->get_result()->fetch_assoc();
$stmt_snap->close();

// Formatar detalhe: id=446 | avaria=Ecrã partido | autor=João | data=2026-05-03 | sala=Sala 3.3 | escola=aaaa
$detalhe = '(registo não encontrado)';
if ($snap) {
    $partes = [
        'id='     . $snap['id'],
        'avaria=' . mb_strimwidth($snap['avaria']      ?? '', 0, 80, '…'),
        'sala='   . ($snap['sala_nome']   ?? ''),
        'escola=' . ($snap['escola_nome'] ?? ''),
    ];
    $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
}

// ── 2. Apagar ────────────────────────────────────────────────────────────────
$sql = $db->prepare("DELETE FROM avarias_reparacoes WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

// ── 3. Auditar com snapshot ───────────────────────────────────────────────────
gei_audit($db, 'eliminar', 'avaria', $id, $detalhe);

mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
          },10);
          </script>




<br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>