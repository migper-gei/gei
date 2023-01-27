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
               <div class="titlepage">
                     <h2>Inserir utilizador </h2>
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

              
<?php

//echo base64_encode($idescola);
//$idescry=base64_encode($idescola);
//echo '<br>';

  // echo base64_decode($cry);


?>




   <form action = "<?php echo SVRURL ?>gravaus?x=<?php echo base64_encode(1)?>" method = "post" >

   <input type="hidden" name="token" value="<?php echo $token; ?>" >
<br>  
<input type = "text"  required class="fadeIn second" name="nome" placeholder="Primeiro e último nome">
      
      <input required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email" class="fadeIn second" placeholder="Email">

                    
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                 </form>

<form action = "<?php echo SVRURL ?>utiliz" method="post" >
<input type = "hidden"  value = "<">
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