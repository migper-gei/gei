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
                     <h2>Eliminar manutenção</h2>
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



//$nome = $_GET["nome"];



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
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>
<?php
}

$id=$url[0];
$sql3 =$db->prepare( "select count(*) from manutencao where codigo=?");
$sql3->bind_param("i", $id);
$sql3->execute();

$rows3 = $sql3->get_result()->fetch_row();



if ($rows3[0]<>0 && $_SESSION['tipo']==1)


{
$id=$url[0];
$sql = $db->prepare("delete from manutencao where codigo=?");
$sql->bind_param("i", $id);
$sql->execute();


mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>manut';
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
text: 'O tipo tem equipamento associado ou não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>manut";
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