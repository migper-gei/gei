<!DOCTYPE html>
<html lang="pt">
   <head>

<?php 

include ("head.php");?>


   </head>







   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     

      

           <div  class="about">
         <div class="container">
            
               <div class="row">
               <div class="col-md-10 offset-md-2">
               <a href="#" class="btn btn-secondary disabled">LINKS DE ACESSO RÁPIDO</a>
              <div class="titlepage">
                    
                  </div>
   

<?php



include("sessao_timeout.php");



      include("msg_bemvindo.php");
      //echo('<br>');
      include("texto_gei.php");
        



           
   ?>  







  



             
            </div>
         </div>
      </div>
      <!-- end about -->
    
<br>
      <?php include ("footer.php");?>

</body>
</html>
