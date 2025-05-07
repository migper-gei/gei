<?php
  session_start();
  session_regenerate_id();
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
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


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
$idr=$_GET['ir'];
$d1=$_GET['d1'];
$d2=$_GET['d2'];
$esc=($_GET['ies']);

//echo $d;

if (
   !isset($idr)  || empty($idr)  || !is_numeric($idr)  
  || !isset($d1)  || empty($d1)  
  || !isset($d2)  || empty($d2)  
  )
  {
  
  ?>
  
  
  <script>
  
  window.setTimeout(function() {
               window.location.href = '<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(2) ?>&&d1=<?php echo base64_encode($d1)?>&&d2=<?php echo base64_encode($d2)?>&&ies=<?php echo base64_encode($esc) ?>';
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
              window.location.href = '<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(2) ?>&&d1=<?php echo base64_encode($d1)?>&&d2=<?php echo base64_encode($d2)?>&&ies=<?php echo base64_encode($esc) ?>';
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