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
                     <h2>Eliminar tarefa</h2>
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

//include("config.php");
//include ("svrurl.php");

//$nome = $_GET["nome"];


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$url2 = explode('/',$_GET['url2']);

//echo $url[0];

$idescola=($url2[0]);
//echo $idescola;

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

   $id=$url[0];

$sql = $db->prepare("delete from tarefas where id=?");
$sql->bind_param("i", $id);
$sql->execute();




?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola)?>';
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
window.location = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1)?>&&esi=<?php echobase64_encode($idescola)?>";
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