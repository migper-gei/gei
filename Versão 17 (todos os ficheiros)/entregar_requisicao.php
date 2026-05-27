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
                     <h2>Entregar requisição</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

                  <div class="welcome-section">
                  <?php
include("msg_bemvindo.php");
?></div>



<?php
$idr=($_GET['ir']);
$d1=($_GET['d']);

$esc=($_GET['ies']);

if (
 !isset($idr)  || empty($idr)  || !is_numeric($idr)  
|| !isset($d1)  || empty($d1)  
)
{

?>


<script>

window.setTimeout(function() {
             window.location.href = '<?php echo SVRURL ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(1) ?>&&d=<?php echo base64_encode($d1)?>&&ies=<?php echo base64_encode($esc) ?>';
          },10);
          </script>


<?php
}


$d=date("Y-m-d"); 


$sql = "update requisicao
set dataentrega=STR_TO_DATE('".$d."','%Y-%m-%d') 
 where id=".$idr."";

 $result = mysqli_query($db,$sql);



?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(1) ?>&&d=<?php echo base64_encode($d1)?>&&ies=<?php echo base64_encode($esc) ?>';
          },10);
          </script>


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