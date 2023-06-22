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


     <?php include ("header.php");
     
     include("sessao_timeout.php");
     ?>
     

     
      
      <!-- about -->
      <div  class="about">


      
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Gestão do Equipamento Informático</h2>
                  </div>
               </div>
            </div>
            






            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
                  <div style="  text-align: right;">              
                  <a  class="underlineHover" href="l" title="Login/Registo" style="color:blue;">Login/Registo</a>
                    
               </div>


               <p>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               possibilidade de gerir outro equipamento que não seja informático.
                            
                              <br>  <br>
                               <p>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               configuração das definições de envio de emails e do tempo de sessão do utilizador.
                            
                              <br>  <br>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               possibilidade de requisição de material e de um equipamento mudar de sala.
                            
                              <br>  <br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                                criação de etiquetas/código de barras dos equipamentos. 
                              <br>  <br>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                                gestão do equipamento de uma ou várias escolas. 
                              <br>  <br>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                                inventário preciso de todos os equipamentos informáticos e suas caraterísticas. 
                              <br>  <br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               gestão e histórico das ações de avarias/reparações e tarefas de manutenção.
                              <br><br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               registo das avarias dos utilizadores.
                              <br><br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               alertas de reparações aos utilizadores.
                              <br><br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                              listagens e estatísticas.
                              <br><br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               importação e exportação de dados. 
                              <br><br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               chat entre os utilizadores. 
                             
                           </p>
                          
                      
                    
               
                      

               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
      <br>

      <?php include ("footer.php");?>




   </body>


</html>


