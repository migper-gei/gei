<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}
?>

<?php
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
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
      <?php include("loader.php"); ?>


     <?php include ("header.php");
     
     include ("css_inserir.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Dados da(s) Instituição(ões)</li>
                  </ol>
               </nav>
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


function cont_validation(){
'use strict';
var numbers = /^[0-9]+$/;
var zip_name = document.getElementById("con");
var zip_value = document.getElementById("con").value;
var zip_length = zip_value.length;
if(!zip_value.match(numbers) || zip_length !== 9)
{
document.getElementById('con_err').innerHTML = '<br>'+'Contato inválido.';
zip_name.focus();
document.getElementById('con_err').style.color = "#FF0000";
}
else
{
   //zip_name.focus();
document.getElementById('con_err').innerHTML = '<br>'+'Contato válido.';
document.getElementById('con_err').style.color = "#00AF33";
}
}


function validarCP() {
   var cp_name = document.getElementById("cp");
   var input = document.getElementById("cp").value;

    if (input.length === 8 && /^[0-9]{4}-[0-9]{3}$/.test(input)) {
        //alert("Código postal válido!");
      
document.getElementById('cp_err').innerHTML = '<br>'+'Código postal válido.';
document.getElementById('cp_err').style.color = "#00AF33";
    } else {
        //alert("Código postal inválido!");
      
        document.getElementById('cp_err').innerHTML = '<br>'+'Código postal inválido.';
cp_name.focus();
document.getElementById('cp_err').style.color = "#FF0000";

    }
}

</script>



    <script type="text/javascript">
function validateImage() {

 
//Get reference of File.
var fileUpload = document.getElementById("img");



 //Check whether the file is valid Image.
 var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.png)$");
 if (regex.test(fileUpload.value.toLowerCase())) {

     //Check whether HTML5 is supported.
     if (typeof (fileUpload.files) != "undefined") {
         //Initiate the FileReader object.
         var reader = new FileReader();
         //Read the contents of Image File.
         reader.readAsDataURL(fileUpload.files[0]);
         reader.onload = function (e) {
             //Initiate the JavaScript Image object.
             var image = new Image();

             //Set the Base64 string return from FileReader as source.
             image.src = e.target.result;
                    
             //Validate the File Height and Width.
             image.onload = function () {
                 var height = this.height;
                 var width = this.width;

                 //alert (width);
                // alert (height);
            
                 if (width > 120 || height > 110 ) {

                    //show width and height to user
                     //document.getElementById("width").innerHTML=width;
                     //document.getElementById("height").innerHTML=height;
                     //alert("Height and Width must not exceed 200px.");
                    
                     swal({
title: 'A imagem não tem as medidas desejadas (120px larg x 110px alt)!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>dadosesc";
}); 
   
                     
                     return false;
                 }
                // alert("Uploaded image has valid Height and Width.");

  
                swal({
title: 'A imagem tem as medidas desejadas!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

});
               

                 return true;
             };

         }
     } else {
         //alert("This browser does not support HTML5.");
         return false;
     }
 } else {
     //alert("Please select a valid Image file.");

     swal({
title: 'Deve inserir uma imagem do tipo .PNG!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>dadosesc";
});

     
     return false;
 }
}



function l() {

//alert ('aaa');


  //event.preventDefault(); // prevent form submit
  

  

swal({

  title: "Deseja eliminar o logotipo?",
 //text: "Sala: "+s1+" (Escola: "+ne1+")",
  type: "warning",
  showCancelButton: true,
  //confirmButtonColor: "#DD6B55",


  confirmButtonText: "Sim",
  cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
 
},
function(isConfirm){
  if (isConfirm) {
    
    
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>elimina_logotipo.php';
}, 10);


          
  } else {
    swal("Cancelado.");

//    window.setTimeout(function() {
  //  window.location.href = '<?php echo SVRURL ?>salas?x=1&&escola='+es1;
//}, 10);
  

  }

});

}

</script>

<div class="form-container">



<table style="width:40%; margin-left:auto;margin-right:auto;">
  <tr>

  <th>
  <form action="<?php echo SVRURL ?>dadosescola" method="post">

<button title="Ver escolas" type="submit" class="btn btn-outline-primary" > Ver instituições</button>

</form>

    </th>
  

    <th>
    

    </th>

    <th>
    <form action="<?php echo SVRURL ?>atulog" method="post">

<button   title="Alterar logotipo" type="submit" class="btn btn-outline-success" >Alterar logotipo</button>

</form>

    </th>

    <th>
    

    </th>

<?php

$sql2 = "select count(*) from logotipo where logotipo<>''";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];

if ($conta==1)
{

?>

    <th>

    <form action="<?php echo SVRURL ?>elimina_logotipo.php" method="post">

<button   title="Remover logotipo" type="submit" class="btn btn-outline-danger" >Remover logotipo</button>

</form>
    </th>

<?php }?>


  </tr>
</table>





<br>

<div class="step-indicator">
            

<i class="fas fa-info-circle mr-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro).
<br>
Ao inserir os dados obrigatórios (a azul claro), os anteriores serão substituídos
</div>

<!--

<img title="Informação" src="images/informacao.svg" alt="Informação" width="3%" height="3%" />
<label>(Ao inserir os dados obrigatórios (a verde), os anteriores serão substituídos) </label>  

-->


<br>
<form name="a" action="<?php echo SVRURL ?>gravaesc" method="post" enctype="multipart/form-data" class="needs-validation" novalidate >
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">


<div class="upload-section">
                <label class="upload-label">Imagem:</label>
                <div class="file-input-container">
                               <input 
                               style="width: 100%;"
                               type="file" class="file-input" name = "logo" id="img" onChange="validateImage()"  accept="image/png">

</div>
<div class="file-type-info">Formato aceite: PNG (tamanho: 120px larg x 110px alt)</div>
</div>
             
<!--

                    <label>Logotipo (imagem: PNG) (tamanho: 120px larg x 110px alt): </label>  <br>  
                  <input   accept="image/png" style=" width:50%" type="file" name = "logo" id="img" onchange="validateImage()" />
-->
         



                    <br />  <br />
                    
                 
                    <label>Nome da instituição: </label>  
                    <br>
                     <input    style="width: 100%;" class="form-control required-field" required  type = "text" name="nomeescola" placeholder="Nome da instituição">
                     <br>   <br>       
                    <label>Site:   </label>  
                    <br>
                     <input style="width:100%" class="form-control required-field"  required  type = "text" name="site" placeholder="https://www.">
                     <br><br>
                     <label>Morada: </label>  
                    <br>
                     <input style="width:100%" class="form-control required-field" type = "text" name="morada" placeholder="Morada" required >
                     <br><br>
                     <label>Código Postal (0000-000): </label>  
                    <br>
                     <input maxlength="8" pattern="\d{4}-\d{3}" required 
                     onBlur="validarCP();" id="cp"
                     style="width:100%"  class="form-control required-field" type = "text" name="codpostal" placeholder="Código Postal">
                     <span id="cp_err"></span>
                     <br>  
                     <label>Localidade: </label>  
                    <br>
                     <input style="width:100%" class="form-control required-field" required  type = "text" name="localidade" placeholder="Localidade">
                     <br><br>
          
                     <label>Contato: </label>  
                    <br>
                     <input onBlur="cont_validation();"  class="form-control required-field"
                     maxlength="9" 
                                  style="width:100%"  type = "text" name="telefone" placeholder="Contato" id="con" required >
                     <span id="con_err"></span>
                    

                     <br />  <br />




                     <label>(Nome de outras instituições) </label>  
                  <br>


                  <div class="container">
    <div class="row">
        <div class="col-6">
        <label>Nome da instituição 2: </label>  
                 <br>
                  <input   style="width:100%" type = "text" name = "nomeescola2">
                  <br>     <br />
                     
                 <label>Nome da instituição 4: </label>  
                 <br>
                  <input  style="width:100%"  type = "text" name = "nomeescola4">
                  <br> 
                  <br>    
              
                     
                     <label>Nome da instituição 6: </label>  
                     <br>
                      <input  style="width:100%"  type = "text" name = "nomeescola6">
                      <br> 
                      <br>  
                      <label>Nome da instituição 8: </label>  
                     <br>
                      <input  style="width:100%"  type = "text" name = "nomeescola8">
                      <br> 
                      <br>  
                      <label>Nome da instituição 10: </label>  
                     <br>
                      <input  style="width:100%"  type = "text" name = "nomeescola10">
                      <br> 
                      <br>  
        </div>



        <div class="col-6">
               
        <label>Nome da instituição 3: </label>  
                 <br>
                  <input  style="width:100%"  type = "text" name = "nomeescola3">
                  <br>     <br />

                       <label>Nome da instituição 5: </label>  
                  <br>
                   <input  style="width:100%"  type = "text" name = "nomeescola5">
                   <br>      
                   
			<br>

  

<label>Nome da instituição 7: </label>  
<br>
<input  style="width:100%"  type = "text" name = "nomeescola7">
<br> 
<br>     

<label>Nome da instituição 9: </label>  
<br>
<input  style="width:100%"  type = "text" name = "nomeescola9">
<br> 
<br>     

<label>Nome da instituição 11: </label>  
<br>
<input  style="width:100%"  type = "text" name = "nomeescola11">


        </div>
    </div>
</div>

              
             
<div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir dados instituição
                                    </button>
                              
                           
                        </div>   
</form> 
</form> 
                 

                    </div>

                    <!--
                    <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>configura">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>
                     -->
                   

<a href="<?php echo SVRURL ?>configura"  title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>
 




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


         <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>