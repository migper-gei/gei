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

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir dados email/sessão</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>
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


<form action = "<?php echo SVRURL ?>gravaemse" method = "post" >


<input style="background-color:#CEF6CE" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email" class="fadeIn second" placeholder="Email">
    <br>  
    <br>  
                    <input id="mypass" type = "password" placeholder="Password do email" size=50 type = "text" name = "pass"  required style="background-color:#CEF6CE"/>
                    <br> 
                    <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br>
                     <br>  
                      
                    <input placeholder="Smtp" size=50 type = "text" name = "smtp"  required style="background-color:#CEF6CE"/>
                    <br><br> 
                    <label>Smtp porta: </label>  
                    <input placeholder="Smtp porta"  type = "number" name = "smtpport"  required style="background-color:#CEF6CE"/>
                    <br><br> 
                    <input placeholder="Nome (sigla)" size=50 type = "text" name = "nome"  required style="background-color:#CEF6CE"/>
                    <br>  <br> 
                    <label>Tempo duração da sessão (em segundos): </label>   
                    <input  type = "number" name = "sessao"  required style="background-color:#CEF6CE"/>
           

                    <br> <br> 


                                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                         
                 
                                       
                 </form>

                 <form action = "<?php echo SVRURL ?>email_sessao.php" method="post" >
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