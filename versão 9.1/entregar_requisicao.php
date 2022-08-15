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
$idr=($_GET['ir']);
$d1=($_GET['d']);

$esc=($_GET['ies']);

//echo $idr;
//echo $d1;

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




//echo $d;


$d=date("Y-m-d"); 


$sql = "update requisicao
set dataentrega=STR_TO_DATE('".$d."','%Y-%m-%d') 
 where id=".$idr."";

 //$result = mysqli_query($db,$sql);



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