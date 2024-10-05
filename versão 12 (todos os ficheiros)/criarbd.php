<!DOCTYPE html>
<html lang="pt">
   <head>
   
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
document.getElementById('email_err').innerHTML = '<br>'+'Email válido';
document.getElementById('email_err').style.color = "#00AF33";
}
}




function nif_validation(){
'use strict';
var numbers = /^[0-9]+$/;
var zip_name = document.getElementById("nif");
var zip_value = document.getElementById("nif").value;
var zip_length = zip_value.length;
if(!zip_value.match(numbers) || zip_length !== 9)
{
document.getElementById('nif_err').innerHTML = '<br>'+'NIF inválido.';
zip_name.focus();
document.getElementById('nif_err').style.color = "#FF0000";
}
else
{
   //zip_name.focus();
document.getElementById('zip_err').innerHTML = '<br>'+'NIF válido.';
document.getElementById('zip_err').style.color = "#00AF33";
}
}
</script>





<?php include ("head.php");?>




   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header2.php");
     //include ("config.php");
     ?>
          
          

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Criar base de dados </h2>
                   
                  </div>

                  <div style="  text-align: right;">              
                  <a  class="underlineHover" href="l" title="Login/Registo" style="color:blue;">Login</a>
                    </div>
               </div>
            </div>
            
          


            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


    <!-- Login Form -->
    <form action="<?php echo SVRURL ?>gravacriarbd.php" method = "post">

    <input required 
    type = "text" name = "nome_esc_inst" class="fadeIn second" 
    placeholder="Nome da escola/instituição">

      <input type = "text" maxlength="9" size="9"
       required class="fadeIn second" name="codigo" placeholder="Código"
       id="codigo" onBlur="nif_validation();" /><span id="nif_err"></span>
      
      <input required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email" class="fadeIn second" placeholder="Email"
    onBlur="email_validation();" id="email"><span id="email_err"></span>

    <input required   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
     maxlength="9"     type = "text" name = "contato" class="fadeIn second" placeholder="Contato">

     <input required 
    type = "text" name = "serverbd" class="fadeIn second" 
    placeholder="Servidor BD">
     <br>

     

         <br>
      <input title="Criar base de dados" type="submit" class="fadeIn fourth" value="Criar base de dados">
       
      <br>

   <!--  
<h7>Após clicar no botão, será enviado um email com o nome da base de dados e com a password do utilizador da aplicação.</h7>
-->
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