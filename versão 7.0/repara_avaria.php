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
                     <h2>Raparar avaria</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        



                  <?php



if($_SERVER["REQUEST_METHOD"] == "POST") {


$id=$_GET["id"];
$dr=($_POST["datarep"]);
$re=($_POST["reparacao"]);
$repor=($_POST["repar_por"]);
//$res=($_POST["resolvido"]);
$em=$_GET["em"];

//echo($_POST["reparacao"]);
//echo('<br>');
//echo($_POST["datarep"]);
//echo('<br>');
//echo ($id);

//<img src="data:image/jpeg;base64,'.base64_encode($row['name'] ).'" height="200" width="200" class="img-thumnail" />
//problema_resolvido='".$_POST["resolvido"]."' 
$sql2 = "update avarias_reparacoes
set datareparacao=STR_TO_DATE('".$_POST["datarep"]."','%Y-%m-%d'),
reparacao='".$_POST["reparacao"]."',
rep_efectuada_por='".$_POST["repar_por"]."'
where id=".$_GET["id"]."";

$result = mysqli_query($db,$sql2);



mysqli_close($db);


echo $id;

?>


<script>
    
    swal({
title: 'Os dados foram guardados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
//window.location = "<?php echo SVRURL ?>enviar_email_reparacao.php?id=<?php echo $id ?>";
});


</script>



<?php
}
?>


<br><br><br><br><br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>
