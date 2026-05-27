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
               <div class="titlepage">
                     <h2>Eliminar sala</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>



<?php

//include("config.php");
//include ("svrurl.php");

//$nome = $_GET["nome"];


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$url2 = explode('/',$_GET['url2']);

$idescola=$url2[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
}

$id = (int)$url[0];

require_once('gei_audit.php');

// ── 1. Capturar snapshot ANTES de apagar ─────────────────────────────────────
$stmt_snap = $db->prepare("
    SELECT
        s.id,
        s.nome          AS sala_nome,
        e.nome_escola   AS escola_nome
    FROM salas s
    LEFT JOIN escolas e ON e.id = s.id_escola
    WHERE s.id = ?
    LIMIT 1
");
$stmt_snap->bind_param('i', $id);
$stmt_snap->execute();
$snap = $stmt_snap->get_result()->fetch_assoc();
$stmt_snap->close();

// Formatar detalhe: id=12 | sala=Sala 3.3 | escola=EB Aveiro
$detalhe = '(registo não encontrado)';
if ($snap) {
    $partes = [
        'id='     . $snap['id'],
        'sala='   . ($snap['sala_nome']   ?? ''),
        'escola=' . ($snap['escola_nome'] ?? ''),
    ];
    $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
}

// ── 2. Verificar dependências: equipamento ───────────────────────────────────
$sql2 = $db->prepare("SELECT COUNT(*), s.nome AS sa
    FROM equipamento e
    JOIN salas s ON s.id = e.id_sala
    WHERE s.id = ?
    GROUP BY s.nome");
$sql2->bind_param("i", $id);
$sql2->execute();
$rows = $sql2->get_result()->fetch_row();
$sql2->close();

$count_equip = $rows[0] ?? 0;
$nome_sala   = $rows[1] ?? ($snap['sala_nome'] ?? '');

// ── 2b. Verificar dependências: requisicao ───────────────────────────────────
$sql3 = $db->prepare("SELECT COUNT(*) FROM requisicao WHERE id_sala = ?");
$sql3->bind_param("i", $id);
$sql3->execute();
$count_req = $sql3->get_result()->fetch_row()[0] ?? 0;
$sql3->close();

$pode_eliminar = ($count_equip == 0 && $count_req == 0 && $_SESSION['tipo'] == 1);

if ($pode_eliminar)
{

$sql = $db->prepare("DELETE FROM salas WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

// ── 3. Auditar com snapshot ───────────────────────────────────────────────────
gei_audit($db, 'eliminar', 'sala', $id, $detalhe);




?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola)?>';
          },10);
          </script>

<?php
}
else
{
?>
    <script>

    swal({
title: 'Não pode eliminar a sala <?php echo htmlspecialchars($nome_sala, ENT_QUOTES, 'UTF-8')?>!',
text: '<?php
if ($_SESSION['tipo'] != 1)              echo 'Não tem permissão para eliminar salas.';
elseif ($count_equip > 0 && $count_req > 0) echo 'A sala tem equipamento e requisições associadas.';
elseif ($count_equip > 0)                  echo 'A sala tem equipamento associado.';
else                                        echo 'A sala tem requisições associadas.';
?>',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>sala";
});



</script>

<?php
}

?>

          <?php
          mysqli_close($db);
          ?>


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