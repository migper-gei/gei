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
                     <h2>Avarias/Reparações</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    

    <?php


$sql3 = "select max(data_fim) as md, max(ano_lectivo) as mal from periodos";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_array($result3);

//echo($rows3['mal']);

$maxdata=$rows3[0];
$dataatual=date('Y-m-d');
$ma=date('n');
$da=date('d');

//echo($da);

if(strtotime($dataatual) > strtotime($maxdata)  && $ma>=8 && $da>=1)
{
//echo('aaa');

$al=$rows3['mal'];
$rest = substr($al, -4); 
$rest1=$rest+1;
//echo($rest);

$novoal=$rest."/".$rest1; //.$rest+1;
//echo($novoal);

$di=$rest."-"."09"."-"."01";
$df=$rest."-"."12"."-"."31";


$di2=$rest1."-"."01"."-"."01";
$df2=$rest1."-"."03"."-"."31";
//echo($df);
$di3=$rest1."-"."04"."-"."01";
$df3=$rest1."-"."08"."-"."31";

$sql4 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',1,STR_TO_DATE('$di','%Y-%m-%d'),STR_TO_DATE('$df','%Y-%m-%d'))";
$result4 = mysqli_query($db,$sql4);


$sql5 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',2,STR_TO_DATE('$di2','%Y-%m-%d'),STR_TO_DATE('$df2','%Y-%m-%d'))";
$result5 = mysqli_query($db,$sql5);

$sql6 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',3,STR_TO_DATE('$di3','%Y-%m-%d'),STR_TO_DATE('$df3','%Y-%m-%d'))";
$result6 = mysqli_query($db,$sql6);


}


?>


<?php 

$sql2 = "select max(ano_lectivo) from periodos";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];


 ?>
 <!--
<br>
<h3 style="text-align:center;">ANO LETIVO: <?php echo($conta);  ?>
</h3>
-->
<br><br>

     
    <a class="underlineHover" title="Inserir avaria" href="<?php echo SVRURL ?>insereavaria">
    <h3 style="color:blue;">INSERIR AVARIA</h3></a>
  
<br>

<!--
<php
    
if ($_SESSION['tipo']==1)
{
?>
<br>
    <a class="underlineHover" title="Reparações a efetuar" href="<?php echo SVRURL ?>reparafaz">
    <h3 style="color:blue;">
    REPARAÇÕES A EFETUAR</h3></a>
  
    <br><br>
<php } ?>
-->


<br>

    <a class="underlineHover" title="Minhas avarias" href="<?php echo SVRURL ?>myavarias?op=t">
    <h3 style="color:blue;">
    MINHAS AVARIAS</h3></a>
  


    <?php
    
//echo($_SESSION['tipo']);

    if ($_SESSION['tipo']==1)
    {
    ?>

    <form action="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=0&&op=t" method="post" >


    <?php


$sql = "SELECT DISTINCT(sala) as no FROM avaria_reparacao where datareparacao is null order by sala";

$result = mysqli_query($db,$sql);

$na=mysqli_num_rows($result);

//echo($na);

if ($na>0)
{

?>

  
  <br>
  <h3 style="color:black;">
  REPARAÇÕES A EFETUAR NA SALA:

  <?php

echo('<select name="sala">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     
  &nbsp;&nbsp;&nbsp;
        
    <input type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"  title="Reparações a efetuar na sala">
</h3>
    <?php } ?>
   
 </form>
 <?php } ?>




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