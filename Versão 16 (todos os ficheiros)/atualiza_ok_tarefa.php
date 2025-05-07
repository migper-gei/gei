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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tarefas a realizar >> Atualizar</a>
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

$idescola=base64_decode ($_GET["esi"]);
$idtar=base64_decode ($_GET["ti"]);


if ( !isset($_POST['salatar']) || !isset($_POST['descricao']) || !isset($_POST['urgencia']) 
|| !isset($_POST['criado_por']) || !isset($_POST['data_criacao']) 
|| empty($_POST['salatar']) || empty($_POST['descricao']) || empty($_POST['urgencia'])
|| empty($_POST['criado_por']) || empty($_POST['data_criacao'])
|| empty($idescola) || !isset($idescola) || empty($idtar) || !isset($idtar) || !is_numeric($idescola) 
)

{
   //echo "aaaaaa";
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>salas?x=1&&esi=<?php echo base64_encode($rows11[0])?>';
}, 10);
</script>


<?php
}
else
{


$dc=$_POST["data_conclusao"];
   
if ($dc<>"" || $dc<>null)
{
 
$sql = "update tarefas 
set id_sala='".$_POST["salatar"]."',
descricao='".$_POST["descricao"]."',
urgencia='".$_POST["urgencia"]."', 
criado_por='".$_POST["criado_por"]."', 
data_criacao=STR_TO_DATE('".$_POST["data_criacao"]."','%Y-%m-%d'),
concluido_por='".$_POST["concluido_por"]."', 
data_conclusao=STR_TO_DATE('".$_POST["data_conclusao"]."','%Y-%m-%d')
  where id=".$idtar."";

  $result = mysqli_query($db,$sql);
}
else
{

   $sql = "update tarefas 
   set id_sala='".$_POST["salatar"]."',
   descricao='".$_POST["descricao"]."',
   urgencia='".$_POST["urgencia"]."', 
   criado_por='".$_POST["criado_por"]."', 
   data_criacao=STR_TO_DATE('".$_POST["data_criacao"]."','%Y-%m-%d'),
   concluido_por='".$_POST["concluido_por"]."'
     where id=".$idtar."";
   
     $result = mysqli_query($db,$sql);

}






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
window.location = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola)?>&&z=<?php echo base64_encode(1) ?>";
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