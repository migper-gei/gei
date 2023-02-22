<!DOCTYPE html>
<html lang="pt">
   <head>
      <script>
         function myFunction() {
           var x = document.getElementById("mypass");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
         </script>



<?php include ("head.php");
   include("sessao_timeout.php");

//include ("verifica_sessao.php");
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
     




      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Login / Registo </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


  

    <!-- Login Form -->
    <form action = "<?php echo SVRURL ?>validaus/<?php echo base64_encode(0)?>" method = "post">
    <!--
    <i class="fa fa-envelope" aria-hidden="true"></i>
      
     
<img src=images/users_demo.png>  -->   

      <input required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$" data-validate = "Email válido: ex@abc.xyz"
					type = "text" name = "email"  id="login" class="fadeIn second" name="login" placeholder="Email">
              <!--
               <br>
               <i class="fa fa-lock" aria-hidden="true"></i>
      -->
      <input required id="mypass" type = "password" name = "password" class="fadeIn third" placeholder="Password">
      <br>
      <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br><br>
      <input title="Login" type="submit" class="fadeIn fourth" value="Login">
       </form>

    
    <?php
//$h="recuperapass/"."0";
//$h1="recuperapass/"."1";
?>


    <div id="formFooter">
    <a  class="underlineHover" href="registauser" title="Registar">Registo</a>
      |
      <a  class="underlineHover" href="<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>" title="Recuperar password">Recuperar Password</a>
      |
      <a class="underlineHover" href="<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(1)?>" title="Mudar password">Mudar Password</a>
    </div>

  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

</body>
</html>