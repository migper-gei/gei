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
               <div class="titlepage">
                     <h2> Configurações</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

<?php
include("msg_bemvindo.php");
?>
    


<br><br>


<?php
if ($_SESSION['tipo']==1)
{
  ?>

<a class="underlineHover" title="Logotipo /nome da escola" href="<?php echo SVRURL ?>dadosesc"><h3 style="color:blue;">DADOS DO AGRUPAMENTO/ESCOLA</h3></a> (Logotipo, nome, site)
<?php 
  }
?>


<br><br>

    <a  class="underlineHover" title="Períodos/Semestres" href="<?php echo SVRURL ?>periodos"><h3 style="color:blue;">
    PERÍODOS / SEMESTRES</h3></a>
<br><br>


<a class="underlineHover" title="Utilizadores" href="<?php echo SVRURL ?>utiliz"><h3 style="color:blue;">UTILIZADORES</h3></a>
<?php
if ($_SESSION['tipo']==1)
{
  echo str_repeat("&nbsp;", 37);
?>

<a class="underlineHover" title="Importação de utilizadores" href="<?php echo SVRURL ?>importarusers"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/users.csv"><h6 style="color:blue;">(Ficheiro exemplo CSV) (Caso já exista o utilizador, não é importado)</h6></a>
<?php 
  }
?>


<br><br>

<a class="underlineHover" title="Tipos de equipamento" href="<?php echo SVRURL ?>tiposequip">
<h3 style="color:blue;">TIPOS DE EQUIPAMENTO</h3></a>



<?php
if ($_SESSION['tipo']==1)
{
  echo str_repeat("&nbsp;", 14);
?>

<a class="underlineHover" title="Importação de tipos equipamentos" href="<?php echo SVRURL ?>importar_tiposequip.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/tiposequipamento.csv"><h6 style="color:blue;">(Ficheiro exemplo CSV) (Caso já exista o tipo de equipamento, não é importado)</h6></a>
<?php 
  }
?>



<br><br>




<?php


$sql = "SELECT id,nome_escola as no FROM escolas ORDER by nome_escola";
$result = mysqli_query($db,$sql);

$rowcount = mysqli_num_rows($result);
//echo $rowcount;

 ?>

<?php
 if ($rowcount>0)
{
 
  ?>

           
<form action="<?php echo SVRURL ?>salas?x=0" method="post">


 <h3 style="color:blue;">SALAS 
 
 

 <select  name="escola">


<?php


while($row=mysqli_fetch_array($result))
{


   echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');


}


echo('</select>');

?>
&nbsp; &nbsp; 
<input title="Ver salas da escola" type=image src="<?php echo SVRURL ?>images/lupa2.svg">

</h3>
   
 </form>



<?php
}

else{


 //echo('<h3 style="color:blue;">SALAS ');
 

echo ("<h3>SALAS:</h3> Ainda não inseriu os dados da escola/agrupamento.");
echo ('<br>');
}





if ($_SESSION['tipo']==1)
{
  //echo str_repeat("&nbsp;", 20);
?>

<?php
  echo str_repeat("&nbsp;", 72);
  ?>

<a class="underlineHover" title="Importação de salas" href="<?php echo SVRURL ?>importarsalas"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a  class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/salas.csv"><h6 style="color:blue;">(Ficheiro exemplo CSV) (Caso já exista a sala, não é importado)</h6></a>

<?php 
  }
?>


<br><br>


<a class="underlineHover" title="Utilizadores" href="<?php echo SVRURL ?>equip">
<h3 style="color:blue;">EQUIPAMENTOS</h3></a>


<?php
if ($_SESSION['tipo']==1)
{
  echo str_repeat("&nbsp;", 34);
?>

<a class="underlineHover" title="Importação de equipamentos" href="<?php echo SVRURL ?>importar_equip.php"><h6 style="color:blue;">IMPORTAÇÃO</h6> </a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a class="underlineHover" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/equipamento.csv"><h6 style="color:blue;">(Ficheiro exemplo CSV) (Caso já exista o equipamento, não é importado)</h6></a>
<?php 
  }
?>





<!--
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
  <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
</svg>
-->





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