<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
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

   





   
   $sql = "SELECT * FROM escolas ORDER by nome_escola";
   $result = mysqli_query($db,$sql);
   $rowcount = mysqli_num_rows($result);
   

  
if ( $_SESSION['tipo']==1)
{
   
   ?>
<form action="<?php echo SVRURL ?>inserirequip?x=0" method="post">


<h3 style="color:blue;">INSERIR NA ESCOLA


<select  name="escola">


<?php


while($row=mysqli_fetch_array($result))
{


  echo('<option value="'.$row['id'].'">'.$row['nome_escola'].'</option>');


}


echo('</select>');

?>
&nbsp; &nbsp; 

<?php
if ($rowcount>0)
{
?> 
<input title="Inserir equipamento" type=image src="<?php echo SVRURL ?>images/lupa2.svg">

</h3>
  
</form>

<?php
}
?>


<br><br>

<?php
}
?>




<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>







<form name="frm" id="frm" action = "" method = "post" >




<h3 style="color:black;">VER EQUIPAMENTO DA ESCOLA




<select  name="escola" onChange="showesc(this.value);">


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by nome_escola";
$result2 = mysqli_query($db,$sql2);

echo('<option value=""> Escolha a escola  </option>');  

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

</form>


<?php


$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];

//echo $nes;


if (!empty($_POST["escola"])) {
              
              $esc=$_POST["escola"];
              
              }
              else{
               $esc=$nes;  //1;
              }

?>



<?php


$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id
and e.id=$esc
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>



<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
   ?>


<form action="<?php echo SVRURL ?>verequipsala?x=0&&escola=<?php echo $esc?>" method="post" >




<br>

SALA

&nbsp; 



<select name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }




?>     
</select>





&nbsp; &nbsp; 
<?php
if ($rowcount>0)
{
?> 



<input title="Ver equipamento da sala da escola" type=image 
src="<?php echo SVRURL ?>images/lupa2.svg"  >

</form>


<?php
}
?> 

</h3>
   
 </form>
 


 <?php

         }
?>
<br><br><br>


<?php
if ( $_SESSION['tipo']==1)
{
?>
<a class="underlineHover" title="Etiquetas" href="<?php echo SVRURL ?>etiq.php">
    <h3 style="color:blue;">
    ETIQUETAS </h3></a>

    <br><br>
    <a class="underlineHover" title="Código de barras" href="<?php echo SVRURL ?>codbar.php">
    <h3 style="color:blue;">
    CÓDIGO DE BARRAS</h3></a>

    <br><br>
<?php
}
?>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>