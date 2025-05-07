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

include ("css_inserir.php");

include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Email/Sessão >> Inserir</a>
               <div class="titlepage">
               
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>




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



<div class="form-container">

<div class="step-indicator">




                        

<i class="bi bi-info-circle-fill me-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>


<form action = "<?php echo SVRURL ?>gravaemse" method = "post" class="needs-validation" novalidate>

<div class="step-indicator">

<label>Email: </label>  <br>
<input class="form-control required-field" style="width:100%" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email" class="fadeIn second"
    onBlur="email_validation();" id="email"><span id="email_err"></span>
    <br>  
    <label>Password: </label>  <br>
                    <input class="form-control required-field" id="mypass" type = "password"  size=50 type = "text" name = "pass"  required style="width:100%"/>
                    <br> 
                    <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br>
                     <br>  
                     <label>Servidor SMTP: </label>  <br>
                    <input class="form-control required-field"  type = "text" name = "smtp"  required style="width:100%"/>
                    <br><br> 

                    <label>Smtp porta: </label>  <br>
                    <input class="form-control required-field" required maxlength="3" type = "text" 
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  
                    name = "smtpport" style="width: 100%;"/>
              

                    <br><br>  
                        <label>Sigla de envio de email: </label> 
                    <input class="form-control required-field"   type = "text" name = "nome"  required style="width:100%"/>

      </div>
      <div class="step-indicator">
             
                    <label>Tempo duração da sessão (em segundos): </label>   
                    <input class="form-control required-field" type = "number" name = "sessao"  required style="width:100%"/>
           

                    <br> 

                    <label>Tempo duração da password(em dias): </label>   
                    <input  class="form-control required-field" type = "number" name = "tempodurapass"  required style="width:100%"/>
           
      </div>
                                    <div  style=" text-align:center;width:100%"> 
                                       
                                      <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir email/sessão
                                    </button> 
    </div>
                         
    </div>       
                                       
                 </form>

                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>email_sessao.php">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>






                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
   <!-- Script para validação do formulário -->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

      <?php include ("footer.php");?>


   </body>
</html>