<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>-->
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
                     <h2>Atualizar tarefa</h2>
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

$idescola=$_GET["escola"];

if ( $_SESSION['tipo']==1 && $_SERVER["REQUEST_METHOD"] == "POST" )


{

$sql = "update tarefas 
set id_sala='".$_POST["salatar"]."',
descricao='".$_POST["descricao"]."',
urgencia='".$_POST["urgencia"]."', 
criado_por='".$_POST["criado_por"]."', 
data_criacao=STR_TO_DATE('".$_POST["data_criacao"]."','%Y-%m-%d'),
concluido_por='".$_POST["concluido_por"]."', 
data_conclusao=STR_TO_DATE('".$_POST["data_conclusao"]."','%Y-%m-%d')
  where id=".$_GET["id"]."";
$result = mysqli_query($db,$sql);




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
window.location = "<?php echo SVRURL ?>tarefas.php?x=1&&escola=<?php echo $idescola?>";
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
        title: 'Não pode atualizar a tarefa.',
        text: 'Não tem permissão.',
        icon: 'error',
        
        })
        .then(function() {
        window.location = "<?php echo SVRURL ?>terefas.php?x=1&&escola=<?php echo $idescola?>";
        });
        
        
        
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