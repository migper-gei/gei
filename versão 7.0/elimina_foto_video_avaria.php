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
                     <h2>Eliminar Foto/Vídeo</h2>
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
$url2 = explode('/',$_GET['url2']);

//echo $url[0];
//echo $url2[0];

$nav=$url[0];

$tipo=$url2[0];


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

//echo ($tipo);


if ($tipo=="f")
{
$sql = "update avarias_reparacoes 
set imgavaria=null
where id=".$url[0]."";
}
elseif ($tipo=="v")
{
$sql = "update avarias_reparacoes 
set video=null
where id=".$url[0]."";
}

$result = mysqli_query($db,$sql);



?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
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