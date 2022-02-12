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
                     <h2>Inserir sala</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>
                   


<?php



//$nome = $_GET["nome"];
if( isset($_POST['nome']) )
{

$sa=$_POST["nome"];


$sql = "select count(*) from salas where nome='".$sa."'";
$result = mysqli_query($db,$sql);

$count = mysqli_fetch_array($result);


//echo ($count[0]); && $_SESSION['tipo']<>1

if ($count[0]>0 )

{
?>
    <script>
    
    swal({
title: 'A sala já existe!',
text: '<?php echo $sa; ?>',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inserirsala";
});


</script>


<?php
}


elseif ( $_SESSION['tipo']==1)
{

$sql = "insert into salas (nome,localizacao,departamento) values ('".$_POST["nome"]."','".$_POST["localizacao"]."','".$_POST["departamento"]."')";
$result = mysqli_query($db,$sql);

//header("Refresh:0;url=salas.php");
mysqli_close($db);


?>


<script>
    
swal({
title: 'Os dados foram guardados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>salas";
});


</script>

<?php
}

else

{
?>


<script>
    
swal({
title: 'Não pode inserir!',
text: 'Não tem permisssão!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>salas";
});


</script>


<?php
}

}


else

{
?>
    <script>
window.setTimeout(function() {
    window.location.href = 'salas';
}, 10);
</script>


<?php

}

?>




<br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>