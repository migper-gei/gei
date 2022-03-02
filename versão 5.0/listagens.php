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
                     <h2>Listagens</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    


    
    <script language="javascript">
	function clickMe()
	{
	
    var datai = (document.forms.datas.elements.datai.value);
    var dataf = (document.forms.datas.elements.dataf.value);
	
 //alert(datai);

    //var myDate = new Date("2013/1/16");
    //var date8 =  new Date(Date.parse("2020-09-08"));
    var di=new Date(Date.parse(datai));
    var df=new Date(Date.parse(dataf));
   //alert(date8);
    
  
//alert(compara1);

		if ( df <= di )
				{
			alert(" A data final deve ser superior à data inicial");
      return false;
		}
		
    else {
      return true;

  }
	}
</script>



    
<script language="javascript">
	function clickMe1()
	{
	
    var datai = (document.forms.avarias.elements.datai.value);
    var dataf = (document.forms.avarias.elements.dataf.value);
	
 //alert(datai);

    //var myDate = new Date("2013/1/16");
    //var date8 =  new Date(Date.parse("2020-09-08"));
    var di=new Date(Date.parse(datai));
    var df=new Date(Date.parse(dataf));
   //alert(date8);
    
  
//alert(compara1);

		if ( df <= di )
				{
			alert(" A data final deve ser superior à data inicial");
      return false;
		}
		
    else {
      return true;

  }
	}
</script>



<?php


//$_GET['q'] = 0;
//include ("a1.php");
  



if(isset($_SESSION['login_user'])) 
{
  //echo "A sessão está ativa" . $_SESSION['login_user'];
}
  else{
    //echo "A sessão NÂO está ativa"; 
    //header("location: index.php");
?>

<script>

    window.location.href = 'index.php';

</script>

<?php
  }
    ?>





<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>




<br><br>


<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">


<h3 style="color:black;">ESCOLA




<select  name="escola" onChange="showesc(this.value);"  style="background-color:#CEF6CE">


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

-------------------------------------------------------------------------------------------------
<br>

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

<form action="qta_equipamentos_sala.php?x=0&&escola=<?php echo $esc ?>" method="post" >



Quantidade de equipamento da sala:

<?php


$sql = "SELECT DISTINCT(nome) as no,id FROM  salas 
where id_escola=$esc
order by nome";

$result = mysqli_query($db,$sql);


echo('<select required name="sala">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     
  &nbsp;&nbsp;&nbsp;
        
    <input  type=image name=sala src="images/lupa2.svg"   title="Quantidade de equipamento por sala">

   
   
 </form>



 <?php
if ($_SESSION['tipo']==1)
{
?>

 <br>


<form action="qta_equipamentos_total.php?x=0&&escola=<?php echo $esc ?>" method="post" >



Quantidade de equipamento (TOTAL):


  &nbsp;&nbsp;&nbsp;
        
    <input type=image name=sala src="images/lupa2.svg"   title="Quantidade de equipamento - total">

   
   
 </form>

-------------------------------------------------------------------------------------------------

 <br>
 <br>

<form name="avarias" action="num_avarias_entredatas.php?x=0&&escola=<?php echo $esc ?>" method="post" >

Nº de avarias e reparações entre  <input  required size="10" type = "date" name = "datai" > 
&nbsp;&nbsp; e &nbsp;&nbsp;    <input  required size="10" type = "date" name = "dataf" >   

   
  &nbsp;&nbsp;&nbsp;
        
    <input onclick="return clickMe1();" type=image name=sala src="images/lupa2.svg"   title="Nº de reparações entre datas">

   
   
 </form>
 <br>



 <form action="num_avarias_equipamento.php?x=0&&escola=<?php echo $esc ?>" method="post" >



Nº de avarias do equipamento:

<?php


$sql = "SELECT DISTINCT(e.nomeequi) as no,ar.id_equi
FROM equipamento e, avarias_reparacoes ar
where ar.id_equi=e.id
and ar.id_escola=$esc
order by e.nomeequi;";

$result = mysqli_query($db,$sql);


echo('<select required name="equi">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id_equi'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     
  &nbsp;&nbsp;&nbsp;
        
    <input  type=image name=sala src="images/lupa2.svg"   title="Quantidade de equipamento por sala">

   
   
 </form>


 <br>



<form action="num_avarias_sala.php?x=0&&escola=<?php echo $esc?>" method="post" >



Nº de avarias da sala:

<?php


$sql = "SELECT DISTINCT(id_sala) as no, s.nome
FROM avarias_reparacoes ar, salas s
where ar.id_sala=s.id and ar.id_escola=$esc
 order by id_sala";

$result = mysqli_query($db,$sql);


echo('<select required name="sala2">');
while($row=mysqli_fetch_array($result))
{

     echo('<option value="'.$row['no'].'">'.$row['nome'].'</option>');

       }

      echo('</select>');


?>     
 &nbsp;&nbsp;&nbsp;
       
   <input  type=image name=sala src="images/lupa2.svg"   title="Quantidade de equipamento por sala">

  
  
</form>



-------------------------------------------------------------------------------------------------
 <br><br>
<form action="estatistica_avarias.php?escola=<?php echo $esc ?>" method="post" >



Estatística de avarias (últimos 5 anos letivos)  &nbsp;&nbsp;&nbsp;
        
    <input type=image name=sala src="images/lupa2.svg"   title="Estatística de avarias (últimos 5 anos letivos)">

   
   
 </form>

 -------------------------------------------------------------------------------------------------

 <br><br>
<form action="equipamentos_mais_avarias_top.php?op=t&&escola=<?php echo $esc ?> " method="post" >



Equipamentos com mais avarias (top 10)  &nbsp;&nbsp;&nbsp;
        
    <input type=image  src="images/lupa2.svg"   title="Equipamentos com mais avarias (top 10)">

   
   
 </form>


 <br>
<form action="salas_mais_avarias_top.php?op=t&&escola=<?php echo $esc ?>" method="post" >



Salas com mais avarias (top 10)  &nbsp;&nbsp;&nbsp;
        
    <input type=image src="images/lupa2.svg"   title="Salas com mais avarias (top 10)">

   
   
 </form>



 <?php
}
?>



<br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>