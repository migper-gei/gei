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
               <div class="titlepage">
                     <h2>Inserir dados escola/agrupamento</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-6 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    


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

</script>


<form name="a" action = "gravaesc"
               method = "post" enctype="multipart/form-data">

                    

                    <label>Logotipo (imagem: PNG) (tamanho: 120px larg x 110px alt): </label>  <br>  
                  <input accept="image/png" size=50 type="file" name = "logo" id="img" onchange="validateImage()" />
                
                    <!--  <input type="file" multiple accept=".png"/>    -->

                    <br />  <br />
                 
                    <label>Nome do agrupamento/escola: </label>  
                    <br>
                     <input style="background-color:#CEF6CE" required size="80" type = "text" name = "nomeescola">
                     <br>     <br />
                    <label>Site: (http://www.....)  </label>  
                    <br>
                     <input style="background-color:#CEF6CE" required size="80" type = "text" name = "site" placeholder="http://www."">
                    
                                    
			<br><br>
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>

                              
              
                 </form>
             
                 <form action = "<?php echo SVRURL ?>configura" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
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