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
                     
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                    
<br>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        


$sql = "update equipamento 
set nomeequi='".$_POST["nomeq"]."',
sala='".$_POST["sala"]."',
tipo='".$_POST["tipoeq"]."',
marca_modelo='".$_POST["marcamod"]."',
numserie='".$_POST["nserie"]."'
where id=".$_GET['id']." ";

$result = mysqli_query($db,$sql);


$sql2 = "update avaria_reparacao
set nomeequi='".$_POST["nomeq"]."'
where nomeequi='".$_GET['nomeant']."' ";

//echo ($_GET['nomeant']);
$result2 = mysqli_query($db,$sql2);

mysqli_close($db);

$id=$_GET['id'];
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atualiza_dadostecredes.php?id=<?php echo $id;?>";
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