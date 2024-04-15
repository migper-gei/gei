<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>

<script>
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
//document.getElementById('email_err').innerHTML = 'Valid email format';
//document.getElementById('email_err').style.color = "#00AF33";
}
}
</script>


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
                     <h2> Registo </h2>
                   
                  </div>

                  <div style="  text-align: right;">              
                  <a  class="underlineHover" href="l" title="Login/Registo" style="color:blue;">Login/Registo</a>
                    </div>
               </div>
            </div>
            
          


            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


    <!-- Login Form -->
    <form action="<?php echo SVRURL ?>gravaus?x=<?php echo base64_encode(0)?>" method = "post">

      <input type = "text"  required class="fadeIn second" name="nome" placeholder="Primeiro e último nome">
     
      
   
      <input required  type = "text" name = "email" class="fadeIn second" placeholder="Email"  
      onBlur="email_validation();" id="email"><span id="email_err"></span>

 <!--pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"

      <input pattern=".{6,}" minlength="6" required  type = "password" name = "password" class="fadeIn third" placeholder="Password (mínimo 6 carateres)">
 
      <input pattern=".{6,}" minlength="6" required  type = "password" name = "confirmapassword" class="fadeIn third" placeholder="Confirmar Password">
 
 -->
     

         <br>
      <input title="Registar" type="submit" class="fadeIn fourth" value="Registar">
       
      <br>

     
<h7>Após clicar no botão, será enviado um email com a password.</h7>
       
       </form>

   

  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

</body>
</html>