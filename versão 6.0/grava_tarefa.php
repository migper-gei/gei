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

$idescola=$_GET['id'];



$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);
 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Gravar tarefa <br> <?php echo $rows11[0] ?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>
                   


<?php


if ( $_SESSION['tipo']==1)
{
 


$sql = "insert into tarefas (id_escola,id_sala,descricao,urgencia,criado_por,data_criacao) 
values (".$idescola.",".$_POST["salatar"].",'".$_POST["descricao"]."','".$_POST["urgencia"]."','".$_POST["criado_por"]."',STR_TO_DATE('".$_POST["data_criacao"]."','%Y-%m-%d')  )";
$result = mysqli_query($db,$sql);


mysqli_close($db);


?>




<script>
    
swal({
title: 'Os dados foram guardados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>tarefas.php?x=1&&escola=<?php echo $idescola ?>";
});

</script>






<?php
}



else

{
?>
    <script>
window.setTimeout(function() {
    window.location.href = 'tarefas.php';
}, 10);
</script>


<?php

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