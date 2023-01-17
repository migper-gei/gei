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
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
               <h2>Atualizar equipamento</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                    
<br>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        


   $id= base64_decode($_GET['ide']);
   $sa= base64_decode($_GET['sai']);


$sql = "update equipamento 
set nomeequi='".$_POST["nomeq"]."',
id_sala=".$sa.",
tipo='".$_POST["tipoeq"]."',
marca_modelo='".$_POST["marcamod"]."',
numserie='".$_POST["nserie"]."',
data_compra='".$_POST["datacompra"]."',
observacoes='".$_POST["obs"]."'
where id=".$id." ";

$result = mysqli_query($db,$sql);

/*
$sql2 = "update avarias_reparacoes
set nomeequi='".$_POST["nomeq"]."'
where nomeequi='".$_GET['nomeant']."' ";


$result2 = mysqli_query($db,$sql2);
*/


mysqli_close($db);

//$id=$_GET['id'];
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atualiza_dadostecredes.php?ide=<?php echo base64_encode($id);?>";
});



</script>

<br><br><br><br><br><br><br><br><br><br>
<?php

}

?>
<br><br>


                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>