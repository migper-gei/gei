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
                     <h2>Avarias</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    


<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>


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

<br><br>
  




<form name="frme" id="frme" action = "" method = "post" >

   <div style="text-align: left;">


   <h3 style="color:black;">ESCOLA:
   
 
   
   
   <select title="Escolha a escola" 
   class="btn btn-info dropdown-toggle"
    name="escola" onChange="showescola(this.value);"  style="width:310px;">
   
   
   <?php
   
   
   //echo $idescola;
   
   
   
   $sql2 = "SELECT * FROM escolas ORDER by nome_escola";
   $result2 = mysqli_query($db,$sql2);
   
   //echo('<option value=""> Escolha a escola  </option>');  
   
   while($row2=mysqli_fetch_array($result2))
   {
   
      if ($row2['id']==$_REQUEST["escola"])
      {
        //'.$row2['nome_escola'].'
         echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
      }
      else
   
     echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
   }
   
   
   echo('</select>');
   
   ?>
   
   </div>
   
</form>
    
<?php
$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];



if (!empty($_POST["escola"])) {
              
   $esc=$_POST["escola"];
   
   }
   else{
    $esc=$nes;  //1;
   }

   //echo $esc;


?>

<br>




<form action="<?php echo SVRURL ?>insereavaria?aves=<?php echo base64_encode($esc) ?>" method="post">

<button style="width:310px;" title="Inserir equipamento" type="submit" class="btn btn-outline-primary" >Inserir na escola</button>
  
</form>
<br>



<form action="<?php echo SVRURL ?>myavarias?op=t" method="post" >

<button style="width:310px;" type="submit" class="btn btn-outline-primary" title="Ver equipamento da sala">Minhas avarias</button>
</form >
  
  


<br>
<?php
    

    
        if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
        {
           ?>

<form action="<?php echo SVRURL ?>last5avarias?aves=<?php echo base64_encode($esc)?>" method="post" >

<button style="width:310px;" type="submit" class="btn btn-outline-primary" title="Últimas 5 avarias registadas">Últimas 5 avarias registadas</button>
        </form >





           
      <?php 
      }
       ?>


    <?php
    
//echo($_SESSION['tipo']);

    if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
    {

       
   
   ?>
  

  


<br>



<?php


$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, avarias_reparacoes ar
where e.id=s.id_escola and ar.id_sala=s.id
and e.id=$esc and ar.datareparacao is null
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>



<form action="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=<?php echo base64_encode(0) ?>&&op=t&&ies=<?php echo base64_encode($esc)?>" method="post" >


<button style="width:310px;" type="submit" class="btn btn-outline-primary" title="Ver avarias/reparações da sala">Ver avarias da sala</button>


<br>


<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }
?>     
</select>


<?php } ?>

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