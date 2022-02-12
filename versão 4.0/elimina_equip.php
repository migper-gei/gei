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
                     <h2>Equipamentos</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>
<?php
               

               if (isset($_GET['url']))
               {
               $url = explode('/',$_GET['url']);
               $url2 = explode('/',$_GET['url2']);
               $url3 = explode('/',$_GET['url3']);
               //echo $url[0];
               //echo $url2[0];
               //echo $url3[0];
               }
               else
               {
                //echo ("aaa");
                   ?>
                   
               <script>
               window.setTimeout(function() {
                   window.location.href = '<?php echo SVRURL ?>i';
               }, 10);
               </script>
            
               <?php
               }
               
?>
<br>

 <?php
$sql = "delete from equipamento where id='".$url[0]."'";
$result = mysqli_query($db,$sql);

$sql2 = "delete from avaria_reparacao where nomeequi='".$url2[0]."'";
$result2 = mysqli_query($db,$sql2);

?>


<br>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php?x=1&&sa=<?php echo ($url3[0]);  ?>';
          },40);
          </script>

    

                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>