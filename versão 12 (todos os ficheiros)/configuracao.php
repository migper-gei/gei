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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES</a>
               <div class="titlepage">
                 
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

<?php
include("msg_bemvindo.php");
?>
    



  
<?php
if ($_SESSION['tipo']==1)
{
  ?>

<form action="<?php echo SVRURL ?>dadosesc" method="post">

<button  style="width:100%;" title="Logotipo /nome da escola" type="submit" class="btn btn-outline-primary" >
DEFINIÇÕES GERAIS (Logotipo, nome, site, ...)</button>

</form>

<?php 
  }
?>


<br>

<form action="<?php echo SVRURL ?>peri" method="post">

<button  style="width:100%;" title="Períodos/Semestres" type="submit" class="btn btn-outline-primary" > PERÍODOS </button>

</form>


<br>

<form action="<?php echo SVRURL ?>utiliz" method="post">

<button  style="width:100%;" title="Utilizadores" type="submit" class="btn btn-outline-primary" > UTILIZADORES</button>

</form>

<?php
if ($_SESSION['tipo']==1)
{
  //echo str_repeat("&nbsp;", 71);
?>


<a class="underlineHover" title="Importação de utilizadores" href="<?php echo SVRURL ?>importarusers"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>


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


<br><br>
<form action="<?php echo SVRURL ?>tiposequip" method="post">

<button style="width:100%;" title="Tipos de equipamento" type="submit" class="btn btn-outline-primary" > TIPOS DE EQUIPAMENTO</button>

</form>





<?php
if ($_SESSION['tipo']==1)
{

?>



<a class="underlineHover" title="Importação de tipos equipamentos" href="<?php echo SVRURL ?>importar_tiposequip.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
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



<br><br>
 
<form action="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(0) ?>" method="post">

<button style="width:100%;" title="Salas" type="submit" class="btn btn-outline-primary" > SALAS</button>

</form>
           



<?php

if ($_SESSION['tipo']==1)
{
  //echo str_repeat("&nbsp;", 20);
?>



<a class="underlineHover" title="Importação de salas" href="<?php echo SVRURL ?>importarsalas"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
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


<br><br>

<form action="<?php echo SVRURL ?>equip" method="post">

<button style="width:100%;" title="Equipamentos" type="submit" class="btn btn-outline-primary" > EQUIPAMENTOS INFORMÁTICOS</button>

</form>



<?php
if ($_SESSION['tipo']==1)
{

?>

<a class="underlineHover" title="Importação de equipamentos" href="<?php echo SVRURL ?>importar_equip.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
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




<br><br>

<form action="<?php echo SVRURL ?>equip" method="post">

<button style="width:100%;" title="Outros Equipamentos" type="submit" class="btn btn-outline-primary" > OUTROS EQUIPAMENTOS</button>

</form>



<?php
if ($_SESSION['tipo']==1)
{

?>

<a class="underlineHover" title="Importação de outros equipamentos" href="<?php echo SVRURL ?>importar_outro_equip.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
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



<br><br>
<form action="<?php echo SVRURL ?>tiposmanuten" method="post">

<button style="width:100%;" title="Tipos de manutenção" type="submit" class="btn btn-outline-primary" > TIPOS DE MANUTENÇÃO</button>

</form>





<?php
if ($_SESSION['tipo']==1)
{

?>



<a class="underlineHover" title="Importação de tipos de manutenção" href="<?php echo SVRURL ?>importar_tiposmanuten.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
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




<br><br>

<?php
if ($_SESSION['tipo']==1)
{
 ?>

<form action="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&&z=<?php echo base64_encode(1) ?>" method="post">

<button style="width:100%" title="Tarefas a realizar" type="submit" class="btn btn-outline-primary" > TAREFAS A REALIZAR</button>

</form>
 
 <br>

<form action="<?php echo SVRURL ?>emsess" method="post">

<button style="width:100%" title="Email / Tempo sessão" type="submit" class="btn btn-outline-primary" > EMAIL / TEMPO SESSÃO</button>

</form>

 
<?php
 }
?>





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