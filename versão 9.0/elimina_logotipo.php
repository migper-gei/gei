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
                     <h2>Eliminar logotipo</h2>
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



if ($_SESSION['tipo']==1  )


{

$sql = "update logotipo
set logotipo=null";
$result = mysqli_query($db,$sql);



?>







<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>dadosesc';
          },40);
          </script>

<?php
}
else
{
?>
    <script>

    swal({
title: 'Não pode eliminar o logotipo '+'<?php echo $rows[1]?>!',
text: 'Não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>dadosesc";
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