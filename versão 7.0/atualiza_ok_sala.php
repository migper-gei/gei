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
                     <h2>Atualizar sala</h2>
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



if ( $_SESSION['tipo']==1 && $_SERVER["REQUEST_METHOD"] == "POST" )


{

$sql = "update salas set nome='".$_POST["nome"]."',localizacao='".$_POST["localizacao"]."',departamento='".$_POST["departamento"]."' 
where id=".$_GET["id"]."";
$result = mysqli_query($db,$sql);


$sql11 = "select e.id from escolas e, salas s
where s.id_escola=e.id and
s.id=".$_GET["id"]."";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);



//$sql2 = "update equipamento set id_sala='".$_POST["nome"]."' where sala='".$_GET["nom"]."'";
//$result2 = mysqli_query($db,$sql2);


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
window.location = "<?php echo SVRURL ?>salas?x=1&&escola=<?php echo $rows11[0]?>";
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
        title: 'Não pode atualizar a sala '+'<?php echo $_POST["nome"]?>!',
        text: 'Não tem permissão.',
        icon: 'error',
        
        })
        .then(function() {
        window.location = "<?php echo SVRURL ?>salasx=1&&escola=<?php echo $rows11[0]?>";
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