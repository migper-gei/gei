<!DOCTYPE html>
<html lang="pt">


<style>

   </style>


   <head>
      
   <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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


     <?php include ("header2.php");
     
     //include("sessao_timeout.php");
     ?>
     

     
      
      <!-- about -->
      <div  class="about">


      
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                  <div style="  text-align: right;">   
                           

                           <a href="l">
                           
                           
                               <button type="button" class="btn btn-outline-primary">Login</button>
                           </a>
                                          </div>
                     <h2> Funcionalidades</h2>
                   
                  </div>
               </div>
            </div>
            




            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-2">
                  


               <p>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               possibilidade de gerir outro equipamento que não seja informático.
                            
                              <br>  <br>
                               <p>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               configuração das definições de envio de emails, do tempo de sessão e duração da password dos utilizadores.
                            
                              <br>  <br>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                               possibilidade de requisição de material e de um equipamento mudar de sala.
                            
                              <br>  <br>
                              <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                                criação de etiquetas/código de barras dos equipamentos. 
                              <br>  <br>
                               <img src="<?php echo SVRURL ?>images/check.svg" alt="" > 
                                gestão do equipamento de uma ou várias escolas/instituições. 
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


