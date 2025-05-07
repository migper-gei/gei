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
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
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
               <a href="#" class="btn btn-secondary disabled">Configurações</a>
             
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


      <div class="action-section">

      
      <div class="row">

      <div class="col-md-6 mb-3">

      <form action="<?php echo SVRURL ?>peri" method="post">

<button   title="Períodos/Semestres" type="submit" class="action-button btn-primary-action" > 
<i class="fa-solid fa-calendar-days"></i>  
&nbsp; Períodos</button>

</form>


</div>



<div class="col-md-6 mb-3">


<?php
if ($_SESSION['tipo']==1)
{
  ?>

<form action="<?php echo SVRURL ?>dadosesc" method="post">

<button type="submit" class="action-button btn-primary-action" title="Definições Gerais (Logotipo, nome, site, ...)">
<i class="fa-solid fa-gear"></i>
                            &nbsp; Configurações gerais
                        </button>

                        <!--

<button  style="width:100%;" title="Logotipo /nome da escola" type="submit" class="btn btn-outline-primary" >
DEFINIÇÕES GERAIS (Logotipo, nome, site, ...)</button>
-->


</form>

<?php 
  }
?>

</div>

</div>


<br>


<div class="row">

<div class="col-md-6 mb-3">



<form action="<?php echo SVRURL ?>utiliz" method="post">

<button  title="Utilizadores" type="submit" 
class="action-button btn-primary-action"  > 
<i class="fa-solid fa-users"></i>  
&nbsp; Utilizadores</button>

</form>

<?php
if ($_SESSION['tipo']==1)
{
  //echo str_repeat("&nbsp;", 71);
?>


<a class="underlineHover" title="Importação de utilizadores" href="<?php echo SVRURL ?>importarusers"><h6 style="color:blue;">Importação</h6> </a>


<?php
echo str_repeat("&nbsp;", 5); 
?>
|
<?php
echo str_repeat("&nbsp;", 5); 
?>
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/utilizadores.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>
<?php 
  }
?>


</div>






<div class="col-md-6 mb-3">
<form action="<?php echo SVRURL ?>tiposequip" method="post">

<button title="Tipos de equipamento" type="submit" 
class="action-button btn-primary-action" > 
<i class="fa-solid fa-rectangle-list"></i>
&nbsp; Tipos de equipamento</button>

</form>





<?php
if ($_SESSION['tipo']==1)
{

?>



<a class="underlineHover" title="Importação de tipos equipamentos" href="<?php echo SVRURL ?>importar_tiposequip.php"><h6 style="color:blue;">Importação</h6> </a>
<?php
echo str_repeat("&nbsp;", 5);
?>
|
<?php
echo str_repeat("&nbsp;", 5);
?>
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/tiposequipamento.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>
<?php 
  }
?>



</div>
</div>

<br>


<div class="row">

<div class="col-md-6 mb-3">





<form action="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(0) ?>" method="post">

<button  title="Salas" type="submit" class="action-button btn-primary-action" > 
<i class="fa-solid fa-door-open"></i>
&nbsp;   
Salas</button>

</form>
           



<?php

if ($_SESSION['tipo']==1)
{
  //echo str_repeat("&nbsp;", 20);
?>



<a class="underlineHover" title="Importação de salas" href="<?php echo SVRURL ?>importarsalas"><h6 style="color:blue;">Importação</h6> </a>
<?php
echo str_repeat("&nbsp;", 5);
?>
|
<?php
echo str_repeat("&nbsp;", 5);
?>
<a  class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/salas.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>

<?php 
  }
?>

</div>


<div class="col-md-6 mb-3">

<form action="<?php echo SVRURL ?>tiposmanuten" method="post">

<button  title="Tipos de manutenção" type="submit"  
class="action-button btn-primary-action"
> 
<i class="fa-solid fa-screwdriver-wrench"></i>
&nbsp;
Tipos de manutenção</button>

</form>





<?php
if ($_SESSION['tipo']==1)
{

?>



<a class="underlineHover" title="Importação de tipos de manutenção" href="<?php echo SVRURL ?>importar_tiposmanuten.php"><h6 style="color:blue;">Importação</h6> </a>
<?php
echo str_repeat("&nbsp;", 5);
?>
|
<?php
echo str_repeat("&nbsp;", 5);
?>
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/tiposmanutencao.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>
<?php 
  }
?>



</div>


</div>


<br>





<div class="row">

<div class="col-md-6 mb-3">

<form action="<?php echo SVRURL ?>equip" method="post">

<button  title="Equipamentos" type="submit" 
class="action-button btn-primary-action"
> 
<i class="fa-solid fa-laptop"></i>
&nbsp; 
Equipamentos Informáticos</button>

</form>



<?php
if ($_SESSION['tipo']==1)
{

?>

<a class="underlineHover" title="Importação de equipamentos" href="<?php echo SVRURL ?>importar_equip.php"><h6 style="color:blue;">Importação</h6> </a>
<?php
echo str_repeat("&nbsp;", 5);
?>
|
<?php
echo str_repeat("&nbsp;", 5);
?>
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/equipamento.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>

<?php 
  }
?>

</div>

<div class="col-md-6 mb-3">


<form action="<?php echo SVRURL ?>equip" method="post">

<button title="Outros Equipamentos" type="submit" 

class="action-button btn-primary-action"
> 
 <i class="fa-solid fa-list-ul"></i>&nbsp;
Outros Equipamentos</button>

</form>



<?php
if ($_SESSION['tipo']==1)
{

?>

<a class="underlineHover" title="Importação de outros equipamentos" href="<?php echo SVRURL ?>importar_outro_equip.php"><h6 style="color:blue;">Importação</h6> </a>
<?php
echo str_repeat("&nbsp;", 5);
?>
|
<?php
echo str_repeat("&nbsp;", 5);
?>
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/outro_equipamento.csv"><h6 style="color:blue;">Ficheiro exemplo CSV </h6></a>
<?php 
  }
?>




</div>

</div>






<br>


<div class="row">

<div class="col-md-6 mb-3">



<?php
if ($_SESSION['tipo']==1)
{
 ?>

<form action="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&&z=<?php echo base64_encode(1) ?>" method="post">

<button title="Tarefas a realizar" type="submit" 
class="action-button btn-primary-action"
> 


<i class="fa-solid fa-list-check"></i>
&nbsp;Tarefas a realizar</button>

</form>
 

</div>



<div class="col-md-6 mb-3">

<form action="<?php echo SVRURL ?>emsess" method="post">

<button style="width:100%" title="Email / Tempo sessão" type="submit" 
class="action-button btn-primary-action"
 > 
<i class="fa-solid fa-envelopes-bulk"></i> 
&nbsp;
 Email / Tempo de sessão</button>

</form>

 
<?php
 }
?>

</div>





</div>





                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>