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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Email/Sessão >> Atualizar</a>
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



         function myFunction() {
           var x = document.getElementById("mypass1");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
         </script>


<?php


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = 'configura';
}, 10);
</script>
<?php
}


    



 $sql = "select * from settings where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
$row=mysqli_fetch_array($result);

//echo $row['id'];
//echo $row['nome'];
?>
 


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>emsess';
}, 10);
</script>

<?php
}
else
{ 
  
$sql = $db->prepare("select AES_DECRYPT(`pass`, 'secret') from settings");
$sql->execute();
$row0 = $sql->get_result()->fetch_row();

 
?>

    
<div class="form-container">

<form action = "<?php echo SVRURL ?>atualiza_ok_emailsessao.php?i=<?php echo base64_encode($row['id']);?>" method = "post" class="needs-validation" novalidate>
<label>Email:    </label>  <br>                    
<input   class="form-control required-field" style="width:100%" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
value="<?php echo ($row['email_user']); ?>" type = "text" name = "email" class="fadeIn second" placeholder="Email"
onBlur="email_validation();" id="email"><span id="email_err"></span>
    

<br>  
    <br>  
    <label>Password:  </label>  <br>  
                    <input  class="form-control required-field" value="<?php echo ($row0[0]); ?>"  id="mypass1" type = "password" placeholder="Password do email" size=50 type = "text" name = "pass"  required style="width:100%"/>
                    <br> 
                    <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br>  <br>
         <label>Smtp: </label>  <br>
         <input    class="form-control required-field" value="<?php echo ($row['email_smtp']); ?>" placeholder="Smtp" size=50 type = "text" name = "smtp"  required style="width:100%"/>
                    <br><br> 

                    <label>Smtp porta: </label> <br>
                    <input  class="form-control required-field" required maxlength="3" type = "text"  
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" placeholder="Smtp porta"  
                    value="<?php echo ($row['email_smtpport']); ?>"
                     name = "smtpport"   style="width: 100%;"/>
                    <br><br> 
                     
                     <label>Nome (sigla):  </label>  <br>  
                    <input  class="form-control required-field" value="<?php echo ($row['nome_app']); ?>" placeholder="Nome (sigla)"  type = "text" name = "nome"  required style="width:100%"/>
                    <br>  <br> 
                    <label>Tempo duração da sessão (em segundos): </label>   <br>
                    <input  class="form-control required-field" value="<?php echo ($row['sessao_timeout']); ?>" type = "number" name = "sessao"  required style="width:100%"/>
                    <br> <br> 


<label>Tempo duração da password (em dias): </label>  
<br> 
<input value="<?php echo ($row['tempoduracaopass']); ?>" type = "number" name = "tempodurapass"  style="width:100%" required  class="form-control required-field" />

                    
                    <br>  <br>

  


                    <div  style=" text-align:center;width:100%"> 
                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar email/sessão
                                    </button>  
                     </div>
                          

                 </form>
</div>


<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>emsess">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                        </div>






<?php

}
?>

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