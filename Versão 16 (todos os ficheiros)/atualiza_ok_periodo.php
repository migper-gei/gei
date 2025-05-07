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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Períodos >> Atualizar</a>
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

$ip= base64_decode($_GET["pi"]);
//echo $ui;

if ( !isset($_POST['anoletivo']) || !isset($_POST['periodo']) || !isset($_POST['datai']) || !isset($_POST['dataf']) 
|| empty($_POST['anoletivo'])  || empty($_POST['periodo'])  || empty($_POST['datai'])  || empty($_POST['dataf']) 
|| empty($ip) || !isset($ip) )

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

if ( $_SESSION['tipo']==1  && $_SERVER["REQUEST_METHOD"] == "POST" )


{

$sql = "update periodos set ano_lectivo='".$_POST["anoletivo"]."',num_periodo=".$_POST["periodo"].",data_inicio=STR_TO_DATE('".$_POST["datai"]."','%Y-%m-%d'),data_fim=STR_TO_DATE('".$_POST["dataf"]."','%Y-%m-%d')
where id=".$ip."";
$result = mysqli_query($db,$sql);




//header("Refresh:0;url=periodos");
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
window.location = "<?php echo SVRURL ?>peri";
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
        title: 'Não pode atualizar!',
        text: 'Não tem permissão.',
        icon: 'error',
        
        })
        .then(function() {
        window.location = "<?php echo SVRURL ?>peri";
        });
        
        
        
        </script>
        
        <?php
        }
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