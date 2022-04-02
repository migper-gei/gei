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


$ne = $rows11[0];
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir sala <br> <?php echo $ne ?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              

<?php
$token=md5(uniqid(rand(), TRUE));
$_SESSION['token']=$token;
?>

              

   <form action = "<?php echo SVRURL ?>gravasala?id=<?php echo $idescola?>" method = "post" >

   <input type="hidden" name="token" value="<?php echo $token; ?>" >
<br>  
                    <label>Nome da sala: </label>  <br>  
                    <input size=50 type = "text" name = "nome"  required style="background-color:#CEF6CE"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input size=50 type = "text" name = "localizacao"  required style="background-color:#CEF6CE"/><br /><br />
                   
                    <label>Departamento / Grupo / Serviço: </label>  <br>  
                    <input size=50 type = "text" name = "departamento"  /><br /><br />
                    
                    


                    
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                 </form>

<form action = "<?php echo SVRURL ?>salas?x=1&&escola=<?php echo $idescola ?>" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>



<br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>