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
                     <h2>Eliminar utilizador</h2>
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


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = 'i';
}, 10); 
</script>
<?php
}



if ($_SESSION['tipo']==1  )


{



$sql = "delete from utilizadores where id='".$url[0]."'";
//$result = mysqli_query($db,$sql);

//header("Refresh:0;url=utilizadores.php");
mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>utiliz';
          },40);
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
window.location = "<?php echo SVRURL ?>utiliz";
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