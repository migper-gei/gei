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
               <a href="#" class="btn btn-secondary disabled">AVARIAS >> REPARAR</a>
               <div class="titlepage">
               
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        



                  <?php

$id=base64_decode($_GET["ia"]);

if ( !isset($_POST['datarep']) || !isset($_POST['reparacao']) || !isset($_POST['repar_por'])
|| empty($_POST['datarep']) || empty($_POST['reparacao']) || empty($_POST['repar_por'])
|| !isset($id) || empty ($id) || !is_numeric(($id))
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>avaria';
}, 10);
</script>


<?php
}
?>






                  <?php



if($_SERVER["REQUEST_METHOD"] == "POST") {



$dr=($_POST["datarep"]);
$re=($_POST["reparacao"]);
$repor=($_POST["repar_por"]);


//<img src="data:image/jpeg;base64,'.base64_encode($row['name'] ).'" height="200" width="200" class="img-thumnail" />
//problema_resolvido='".$_POST["resolvido"]."' 
$sql2 = "update avarias_reparacoes
set datareparacao=STR_TO_DATE('".$_POST["datarep"]."','%Y-%m-%d'),
reparacao='".$_POST["reparacao"]."',
rep_efectuada_por='".$_POST["repar_por"]."'
where id=".$id."";

$result = mysqli_query($db,$sql2);



mysqli_close($db);


//echo $id;

?>


<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Um email vai ser enviado com os dados da reparação!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>enviar_email_reparacao.php?ia=<?php echo base64_encode($id) ?>";
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