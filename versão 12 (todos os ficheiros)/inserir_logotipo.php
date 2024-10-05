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



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> GERAIS</a>
               <div class="titlepage">
                
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    

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



<br>
<div style="text-align: center;">  


<form action="<?php echo SVRURL ?>dadosescola" method="post">

<button title="Ver escolas" type="submit" class="btn btn-outline-primary" > Ver instituições</button>

</form>

     
</div>
<br>

<table style="width:60%; margin-left:auto;margin-right:auto;">
  <tr>
    <th>
    <form action="<?php echo SVRURL ?>atulog" method="post">

<button   title="Alterar logotipo" type="submit" class="btn btn-outline-success" >Alterar logotipo</button>

</form>

    </th>
    <th>

    <form action="<?php echo SVRURL ?>elimina_logotipo.php" method="post">

<button   title="Remover logotipo" type="submit" class="btn btn-outline-danger" >Remover logotipo</button>

</form>
    </th>
  </tr>
</table>





<br><br>
<img title="Informação" src="images/informacao.svg" alt="Informação" width="3%" height="3%" />
<label>(Ao inserir os dados obrigatórios (a verde), os anteriores serão substituídos) </label>  
<br><br>
<form name="a" action="gravaesc" method="post" enctype="multipart/form-data">

                    

                    <label>Logotipo (imagem: PNG) (tamanho: 120px larg x 110px alt): </label>  <br>  
                  <input   accept="image/png" style=" width:50%" type="file" name = "logo" id="img" onchange="validateImage()" />
                
                    <!--  <input type="file" multiple accept=".png"/>    -->



       

<br>

                    <br />  <br />
                 
                    <label>Nome da instituição: </label>  
                    <br>
                     <input style="background-color:#CEF6CE; width:100%" required  type = "text" name="nomeescola" placeholder="Nome">
                     <br>     
                    <label>Site: (http://www.....)  </label>  
                    <br>
                     <input style="background-color:#CEF6CE;width:100%" required  type = "text" name="site" placeholder="http://www.">
                     <br>
                     <label>Morada: </label>  
                    <br>
                     <input style="background-color:#CEF6CE;width:100%" " type = "text" name="morada" placeholder="Morada">
                     <br>
                     <label>Código Postal (0000-000): </label>  
                    <br>
                     <input maxlength="8" pattern="\d{4}-\d{3}"
                     onBlur="validarCP();" id="cp"
                     style="background-color:#CEF6CE;width:100%"  type = "text" name="codpostal" placeholder="Código Postal">
                     <span id="cp_err"></span>
                     <br>  
                     <label>Localidade: </label>  
                    <br>
                     <input style="background-color:#CEF6CE;width:100%"  type = "text" name="localidade" placeholder="Localidade">
                     <br>
          
                     <label>Contato: </label>  
                    <br>
                     <input onBlur="cont_validation();" 
                     maxlength="9" 
                                  style="background-color:#CEF6CE;width:100%"  type = "text" name="telefone" placeholder="Contato" id="con">
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
<br> 

        </div>
    </div>
</div>

              
             


                
                  
             
    
                <div  style=" text-align:center;width:100%"> 
                <input  type = "submit" value = "Inserir"/>     
</form> 
            <br>

       



   <form action="<?php echo SVRURL ?>configura" method="post"></div>
<input title="Voltar" type="image" src="<?php echo SVRURL ?>images/voltar.svg">
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