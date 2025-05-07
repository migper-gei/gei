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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Utilizadores >> Atualizar</a>
               <div class="titlepage">
               
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        
         
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    
</div>



<?php

$ui= base64_decode($_GET["ui"]);
//echo $ui;

if ( !isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['tipo']) 
|| empty($_POST['nome'])  || empty($_POST['email'])  || empty($_POST['tipo']) 
|| empty($ui) || !isset($ui) || !is_numeric($ui)  )

{
   //echo "aaaaaa";
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>utiliz';
}, 10);
</script>


<?php
}
else
{
?>

<?php



if ( $_SESSION['tipo']==1 && $_SERVER["REQUEST_METHOD"] == "POST" )


{




$sql = "update utilizadores set tipo=".$_POST["tipo"].", nome='".$_POST["nome"]."',email='".$_POST["email"]."'
where id=".$ui."";

$result = mysqli_query($db,$sql);




//header("Refresh:0;url=salas.php");
mysqli_close($db);
?>


<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,    
//position: 'top-rigth',

})
.then(function() {
window.location = "<?php echo SVRURL ?>utiliz";
})
;

</script>
<?php
}
else
{
    
        ?>
            <script>
        
            swal({
        title: 'Não pode atualizar o utilizador',
        text: 'Não tem permissão.',
        icon: 'error',
        
        })
        .then(function() {
        window.location = "<?php echo SVRURL ?>utiliz";
        });
        
        
        
        </script>
        
        <?php
        }
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