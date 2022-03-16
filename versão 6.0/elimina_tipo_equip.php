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
                     <h2>Eliminar tipos equipamento</h2>
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
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
}

$sql3 = "select count(*) from tipos_equipamento where id='".$url[0]."'";
$result3 = mysqli_query($db,$sql3);
$rows3 =mysqli_fetch_row($result3);

//echo $rows3[0];


//echo $rows[0];
//echo $rows[1];

//echo $_SESSION['tipo'];

if ($rows3[0]<>0 && $_SESSION['tipo']==1)


{

$sql = "delete from tipos_equipamento where id='".$url[0]."'";
$result = mysqli_query($db,$sql);

//header("Refresh:0;url=tiposequip");
mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tiposequip';
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
text: 'O tipo tem equipamento associado ou não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>tiposequip";
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