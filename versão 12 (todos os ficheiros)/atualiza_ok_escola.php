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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> INSTITUIÇÃO >> ATUALIZAR</a>
               <div class="titlepage">
                   
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

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>
<?php
}
?>


<?php

$sql = "update escolas set nome_escola='".$_POST["nome"]."' 
where id=".base64_decode ($url[0])."";
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
window.location = "<?php echo SVRURL ?>dadosescola";
})
;

</script>




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