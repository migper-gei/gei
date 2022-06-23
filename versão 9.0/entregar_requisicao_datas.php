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
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>



<?php
$idr=$_GET['idr'];
$d1=$_GET['d1'];
$d2=$_GET['d2'];
//echo $d;


$d=date("Y-m-d"); 


$sql = "update requisicao
set dataentrega=STR_TO_DATE('".$d."','%Y-%m-%d') 
 where id=".$idr."";

 $result = mysqli_query($db,$sql);



?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=1&&d1=<?php echo $d1?>&&d2=<?php echo $d2?>';
          },40);
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