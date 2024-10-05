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



<script>


function cod_validation(){
'use strict';
var numbers = /^[0-9]+$/;
var zip_name = document.getElementById("cod");
var zip_value = document.getElementById("cod").value;
var zip_length = zip_value.length;

//alert(zip_length);

if(!zip_value.match(numbers) || zip_length > 9 || zip_length < 1)
{
document.getElementById('cod_err').innerHTML = '<br>'+'Código inválido.';
zip_name.focus();
document.getElementById('cod_err').style.color = "#FF0000";
}
else
{
   //zip_name.focus();
document.getElementById('cod_err').innerHTML = '<br>'+'Código válido.';
document.getElementById('cod_err').style.color = "#00AF33";
}
}



function email_validation(){
'use strict';

var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
var email_name = document.getElementById("email");
var email_value = document.getElementById("email").value;
var email_length = email_value.length;
if(!email_value.match(mailformat) || email_length === 0)
{

document.getElementById('email_err').innerHTML = '<br>'+'Email inválido.';
email_name.focus();
document.getElementById('email_err').style.color = "#FF0000";
}
else
{
   document.getElementById('email_err').innerHTML = '<br>'+'Email válido';
document.getElementById('email_err').style.color = "#00AF33";
}
}
</script>



<?php include ("head.php");


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


     <?php include ("header2.php");
     
     //include ("header.php");
     
     //include("sessao_timeout.php");
     ?>
     





     


    
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Login  </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">

  Dados de acesso à base de dados teste:
     <br>
Código: 123456<br>
Utilizador: adminteste@escola.pt<br>
Password: admin+123



    <!-- Login Form -->
    <form action = "<?php echo SVRURL ?>validaus/<?php echo base64_encode(0)?>" method = "post">
    <!--
    <i class="fa fa-envelope" aria-hidden="true"></i>
         
     
<img src=images/users_demo.png>
 --> 






 <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-key" aria-hidden="true"></i>
						</span>


    <input title="Código" required  type = "text" name = "codigo" class="fadeIn third"  placeholder="Código"
   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="9"    
   id="cod" onBlur="cod_validation();" /><span id="cod_err"></span> 
   
   <br>



   <br>
   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
      <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$" data-validate = "Email válido: ex@abc.xyz"
					type = "text" name = "email"   class="fadeIn second" name="login" placeholder="Email"
               onBlur="email_validation();" id="email"><span id="email_err"></span>

               
            <br>
               <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
      <input title="Password" required id="mypass" type = "password" name = "password" class="fadeIn third" placeholder="Password">
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
    <!--
    <a  class="underlineHover" href="registauser" title="Registar">Registo</a>
      | -->
      <a  class="underlineHover" href="<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>" title="Recuperar password">
      <button type="button" class="btn" role="button" data-bs-toggle="button">Recuperar Password</button>   
     </a>
      <!--
      |
      <a class="underlineHover" href="<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(1)?>" title="Mudar password">Mudar Password</a>
--> 
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