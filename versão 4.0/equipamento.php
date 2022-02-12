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
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>

<br>

<?php
if ( $_SESSION['tipo']==1)
{
?>


<a class="underlineHover" title="Inserir equipamento" href="<?php echo SVRURL ?>inserirequip"><h3 style="color:blue;">INSERIR</h3>
</a>

<br><br>

<?php
}
?>






<form action="<?php echo SVRURL ?>verequipsala?x=0" method="post" >

<?php


$sql = "SELECT DISTINCT(sala) as no FROM equipamento";

$result = mysqli_query($db,$sql);
?>
<h3 style="color:black;">EQUIPAMENTO DA SALA:

<select name="sala">


<?php
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');

        }




?>     
</select>

   &nbsp; &nbsp; 

<input title="Ver equipamento da sala" type=image name=sala 
src="<?php echo SVRURL ?>images/lupa2.svg"  >



</h3>
   
 </form>
 

<br><br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>