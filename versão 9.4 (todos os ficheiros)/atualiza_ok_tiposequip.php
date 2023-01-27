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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Atualizar tipos equipamento</h2>
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
$idtipo=base64_decode($_GET['tpi']);


if ( !isset($_POST['nomeeq']) || empty($_POST['nomeeq']) 
|| empty($idtipo) || !isset($idtipo) )

{
   //echo "aaaaaa";
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tiposequi';
}, 10);
</script>


<?php
}
else
{

//echo($_POST["nomeeq"]);
//echo($_GET["id"]);

//echo($_GET["nom"]);


$sql0 = "update tipos_equipamento set nome='".$_POST["nomeeq"]."' where id=".$idtipo." ";
$result = mysqli_query($db,$sql0);

//echo($_GET["nom"]);
//echo('<br>');


$sql2 = "update equipamento set tipo='".$_POST["nomeeq"]."' where tipo='".$_GET["nom"]."'";
$result2 = mysqli_query($db,$sql2);


//header("Refresh:0;url=tiposequip");
mysqli_close($db);
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>tiposequip";
})
;


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