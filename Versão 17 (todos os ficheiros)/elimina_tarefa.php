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
                     <h2>Eliminar tarefa</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

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

$idescola=($url2[0]);

}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas?x=<?php echo base64_encode(0) ?>';
}, 10);
</script>
<?php
}




if ( $_SESSION['tipo']==1  )
{

$id = (int)$url[0];

require_once('gei_audit.php');

// ── 1. Capturar snapshot ANTES de apagar ─────────────────────────────────────
$stmt_snap = $db->prepare("
    SELECT t.id,
           LEFT(t.descricao, 80)  AS descricao,
           t.urgencia,
           t.criado_por,
           t.data_criacao,
           s.nome                 AS sala_nome,
           e.nome_escola          AS escola_nome
    FROM tarefas t
    LEFT JOIN salas   s ON s.id = t.id_sala
    LEFT JOIN escolas e ON e.id = t.id_escola
    WHERE t.id = ?
    LIMIT 1
");
$stmt_snap->bind_param('i', $id);
$stmt_snap->execute();
$snap = $stmt_snap->get_result()->fetch_assoc();
$stmt_snap->close();

// Formatar detalhe: id=7 | descricao=Substituir rato… | urgencia=Alta | criado_por=João | sala=Sala 3.3 | escola=EB Aveiro
$detalhe = '(registo não encontrado)';
if ($snap) {
    $partes = [
        'id='       . $snap['id'],
        'descricao='. mb_strimwidth($snap['descricao'] ?? '', 0, 80, '…'),
        'urgencia=' . ($snap['urgencia']    ?? ''),
        'criado_por='. ($snap['criado_por'] ?? ''),
        'sala='     . ($snap['sala_nome']   ?? ''),
        'escola='   . ($snap['escola_nome'] ?? ''),
    ];
    $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
}

// ── 2. Apagar ────────────────────────────────────────────────────────────────
$sql = $db->prepare("DELETE FROM tarefas WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

// ── 3. Auditar com snapshot ───────────────────────────────────────────────────
gei_audit($db, 'eliminar', 'tarefa', $id, $detalhe);




?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola)?>&&x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode(1) ?>';
          },10);
          </script>

<?php
}
else
{
?>
    <script>

    swal({
title: 'Não pode eliminar a tarefa!',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola)?>";
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