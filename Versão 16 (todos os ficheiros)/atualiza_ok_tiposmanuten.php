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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tipos de manutenção >> Atualizar</a>
               <div class="titlepage">
               
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>



<?php
$idtipo=base64_decode($_GET['tpi']);
//echo $_POST['nomema'];

if ( !isset($_POST['nomema']) || empty($_POST['nomema']) 
|| empty($idtipo) || !isset($idtipo) )

{
   //echo "aaaaaa";
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tiposmanuten';
}, 10);
</script>


<?php
}
else
{



//echo($_GET["nom"]);


$sql0 = "update tipos_manutencao set nome='".$_POST["nomema"]."' where id=".$idtipo." ";
$result = mysqli_query($db,$sql0);


//echo($_GET["nom"]);
//echo('<br>');


$sql2 = "update manutencao set descricao='".$_POST["nomema"]."' where descricao='".$_GET["nom"]."'";
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
window.location = "<?php echo SVRURL ?>tiposmanuten";
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