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

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Eliminar períodos de tempo</h2>
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
    window.location.href = 'configura';
}, 10);
</script>
<?php
    exit;
}

// CORRIGIDO: Validação CSRF para operação de eliminação via GET
// Verificar token anti-CSRF passado como parâmetro GET
if (empty($_GET['csrf_token']) || empty($_SESSION['csrf_token_delete']) || !hash_equals($_SESSION['csrf_token_delete'], $_GET['csrf_token'])) {
?>
<script>
swal({
    title: 'Erro de segurança!',
    text: 'Token inválido. Por favor tente novamente.',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>peri";
});
</script>
<?php
    exit;
}
// Consumir o token após validação (uso único)
unset($_SESSION['csrf_token_delete']);

if ($_SESSION['tipo'] == 1)
{

$id = (int)$url[0]; // CORRIGIDO: cast explícito para inteiro (já existia implicitamente no bind_param)

$sql = $db->prepare("delete from periodos where id=? ");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

mysqli_close($db); // CORRIGIDO: removido o mysqli_close duplicado que existia fora do if/else
?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>peri';
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
text: 'Não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>periodos";
});



</script>

<?php
}

?>











                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>
