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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> OUTRO EQUIPAMENTO >> ATUALIZAR</a>
               <div class="titlepage">
         
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                    
<br>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        


   $id= base64_decode($_GET['ide']);
   $sa= base64_decode($_GET['sai']);
   $ies= base64_decode($_GET['ies']);

   //echo $sa;


$sql = "update outro_equipamento 
set nomeoutro='".$_POST["nomeq"]."',
qta='".$_POST["qta"]."',
observacoes='".$_POST["obs"]."'
where id=".$id." ";


$result = mysqli_query($db,$sql);




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
    window.location = "<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($ies)?>";

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