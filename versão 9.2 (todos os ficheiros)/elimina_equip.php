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
                   window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
            
               <?php
               }
               
?>
<br>

 <?php
 $id=$url[0];

$sql = $db->prepare("delete from equipamento where id=?");
$sql->bind_param("i", $id);
$sql->execute();



$sql2 = $db->prepare("delete from avarias_reparacoes where id_equi=?");
$sql2->bind_param("i", $id);
$sql->execute();

?>


<br>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2)?>&&si=<?php echo base64_encode($url3[0]);?>&&ies=<?php echo base64_encode($url2[0]);?>';
          },10);
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