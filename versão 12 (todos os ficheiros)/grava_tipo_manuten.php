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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> TIPOS DE MANUTENÇÃO >> INSERIR</a>
               <div class="titlepage">
                     <h2>Inserir tipos manutenção</h2>
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
if ( !isset($_POST['nome'])  )
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>equip';
}, 140);
</script>


<?php
}
?>



<?php
if( isset($_POST['nome']) )
{
$t=$_POST["nome"];


$sql = "select count(*) from tipos_manutencao where nome='".$t."'";
$result = mysqli_query($db,$sql);

$count = mysqli_fetch_array($result);


//echo ($count[0]);

if ($count[0]>0)

{
?>
    <script>
   
    swal({
title: 'O tipo de manutenção já existe!',
text: '<?php echo $t; ?>',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inserirtmanuten";
});


</script>


<?php

}


elseif   ($_SESSION['tipo']==1)
{



$sql = "insert into tipos_manutencao (nome) values ('".$_POST["nome"]."')";
$result = mysqli_query($db,$sql);

//header("Refresh:0;url=tipos_equipamento.php");

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
window.location ="<?php echo SVRURL ?>tiposmanuten";
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
window.location = "<?php echo SVRURL ?>tiposmanuten";
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
    window.location.href = 'tiposmanuten';
}, 10);
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