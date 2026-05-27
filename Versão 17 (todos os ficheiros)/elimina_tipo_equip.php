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
                     <h2>Eliminar tipos equipamento</h2>
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
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}

$id = (int)$url[0];

require_once('gei_audit.php');

// ── 1. Capturar snapshot ANTES de apagar ─────────────────────────────────────
$stmt_snap = $db->prepare("
    SELECT te.id, te.nome,
           COUNT(e.id) AS total_equip
    FROM tipos_equipamento te
    LEFT JOIN equipamento e ON e.tipo = te.nome
    WHERE te.id = ?
    GROUP BY te.id, te.nome
    LIMIT 1
");
$stmt_snap->bind_param('i', $id);
$stmt_snap->execute();
$snap = $stmt_snap->get_result()->fetch_assoc();
$stmt_snap->close();

// Formatar detalhe: id=3 | nome=Computador | equip_associado=0
$detalhe = '(registo não encontrado)';
if ($snap) {
    $partes = [
        'id='             . $snap['id'],
        'nome='           . ($snap['nome'] ?? ''),
        'equip_associado='. ($snap['total_equip'] ?? 0),
    ];
    $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
}

// ── 2. Verificar: tipo existe e não tem equipamento associado ─────────────────
$existe       = $snap !== null;
$tem_equip    = ($snap['total_equip'] ?? 0) > 0;
$pode_eliminar = $existe && !$tem_equip && $_SESSION['tipo'] == 1;

if ($pode_eliminar)
{

// ── 3. Apagar ─────────────────────────────────────────────────────────────────
$sql = $db->prepare("DELETE FROM tipos_equipamento WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

// ── 4. Auditar com snapshot ───────────────────────────────────────────────────
gei_audit($db, 'eliminar', 'tipo_equipamento', $id, $detalhe);

mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tiposequip';
          },10);
          </script>


<?php
}
else
{
?>
    

    <script>

swal({
title: 'Não pode eliminar!',
text: '<?php
if ($_SESSION['tipo'] != 1)  echo 'Não tem permissão para eliminar tipos de equipamento.';
elseif (!$existe)            echo 'O tipo de equipamento não foi encontrado.';
else                         echo 'O tipo tem equipamento associado e não pode ser eliminado.';
?>',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>tiposequip";
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
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>