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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tipos de manutenção >> Inserir</a>
              
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


<form action = "<?php echo SVRURL ?>gravatmanuten" method = "post" class="needs-validation" novalidate>
                    <label>Nome: </label>  <br>  
                    <input class="form-control required-field" type = "text" name = "nome"  required style="width:100%"/>
                    <br><br>
                                    <div  style=" text-align:center;width:100%"> 
                                       
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir tipo de manutenção
                                    </button> 
    </div>
                         
                 
                                       
                 </form>

<br>

                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>tiposmanuten">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>
    



<br>
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