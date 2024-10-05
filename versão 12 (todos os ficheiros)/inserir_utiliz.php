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
     


     <?php
//session_start();



include("sessao_timeout.php");

 


  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">UTILIZADORES >> INSERIR</a>
               <div class="titlepage">
                  
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
<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
<input type = "text"  required class="fadeIn second" name="nome" placeholder="Primeiro e último nome">
<br>

<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
      <input required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email" class="fadeIn second" placeholder="Email"
    onBlur="email_validation();" id="email"><span id="email_err"></span>

                    
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