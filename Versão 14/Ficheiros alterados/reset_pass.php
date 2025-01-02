<?php
  session_start();

  ?>

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

document.getElementById('email_err').innerHTML = '<br>'+'Email inválido.'+'<br>';
email_name.focus();
document.getElementById('email_err').style.color = "#FF0000";
}
else
{
   document.getElementById('email_err').innerHTML = '<br>'+'Email válido'+'<br>';
document.getElementById('email_err').style.color = "#00AF33";
}
}
</script>

<?php include ("head.php");

?>




   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


      <?php include ("header2.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                  
              




    <h2> Mudar password </h2>
   



                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


<?php
   $nobd=$_SESSION['nobd'];
$serverbd=$_SESSION['serverbd'];



?>


<form action = "<?php echo SVRURL ?>reset_pass_OK.php" method = "post">
           

<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>

             <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
					type = "text" name = "email"  class="fadeIn second"  placeholder="Email"
               onBlur="email_validation();" id="email"><span id="email_err"></span>
                    
             
                   <br>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                   <input title="Password antiga" 
                         class="fadeIn second" placeholder="Password antiga" type = "password" name = "passworda" pattern=".{8,}" minlength="8" required/>
                   
                   <br>                 <br>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                   <input title="Password (>= 8 digitos, letras e números)" 
                       class="fadeIn second" placeholder="Password (>= 8 digitos, letras e números)" type = "password" name = "password" pattern=".{8,}" minlength="8" required/>
                      
                   <br>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
              <input title="Confirmar password" class="fadeIn second" placeholder="Confirmar password"  pattern=".{8,}" type = "password" minlength="8" name = "confirmapassword"  required/>
              <br>
									<input  class="fadeIn fourth" title="Mudar password" type = "submit" value = "Mudar"/>  
                                           
                                              <br />
                                  
                                            
                 </form>









  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    
<br><br>
      <?php include ("footer.php");?>

</body>
</html>