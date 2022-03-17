<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>-->
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
                     <h2>Manutenções</h2>
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

    
    <script language="javascript">
	function clickMe()
	{
	
    var datai = (document.forms.manutencoes.elements.datami.value);
    var dataf = (document.forms.manutencoes.elements.datamf.value);
	
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
	function clickMe2()
	{
	
    var datai = (document.forms.manutencoes.elements.datami2.value);
    var dataf = (document.forms.manutencoes.elements.datamf2.value);
	
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





<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}




</script>




<br>


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

  // echo $esc;
?>

<br>


<?php

if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
{

?>



<form action="inserirmanut?escola=<?php echo $esc;?>" method="post" >


Inserir manutenção da sala:

<?php


$sql = "SELECT DISTINCT(nome) as no,id 
FROM salas 
where id_escola=$esc
order by nome";

$result = mysqli_query($db,$sql);


echo('<select name="sala">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     



  &nbsp;&nbsp;&nbsp;
        
  <input type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"   title="Inserir manutenção da sala">

   

    
    
    </form>

<br>
<br>


   
   
 </form>


<?php
}
?>







<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=0&&escola=<?php echo $esc;?>" method="post" >

<br>
Sala:    
<?php


$sql2 = "SELECT DISTINCT(s.nome) as no,s.id 
FROM manutencao m, salas s, equipamento e
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by s.nome";

$result2 = mysqli_query($db,$sql2);


echo('<select name="sala" required>>');
while($row=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

       echo('</select>');

?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
entre  &nbsp;&nbsp;&nbsp;
        
<input  required size="10" type = "date" name = "datami" > 
&nbsp;&nbsp; e &nbsp;&nbsp;    <input  required size="10" type = "date" name = "datamf" >   

&nbsp;&nbsp;&nbsp;
    <input onclick="return clickMe();" type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"  title="Manutenções da sala entre datas">

   
   
 </form>




 <br> 


<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_equip_entredatas.php?x=0&&escola=<?php echo $esc;?>" method="post" >



Equipamento:
<?php


$sql2 = "SELECT DISTINCT(e.nomeequi) as no, e.id
FROM manutencao m, equipamento e, salas s
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by e.nomeequi";

$result2 = mysqli_query($db,$sql2);


echo('<select name="equip" required>');
while($row2=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row2['id'].'">'.$row2['no'].'</option>');

        }

       echo('</select>');

?>

&nbsp;&nbsp;&nbsp;
entre  &nbsp;&nbsp;&nbsp;&nbsp;
        
<input  required size="10" type = "date" name = "datami2" > 
&nbsp;&nbsp; e &nbsp;&nbsp;    <input  required size="10" type = "date" name = "datamf2" >   

&nbsp;&nbsp;&nbsp;
    <input onclick="return clickMe2();" type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"  title="Manutenções do equipamento entre datas">

   
   
 </form>


<br><br>


<form name="manutencoes2"   
action="<?php echo SVRURL ?>manutencoes_sala_data.php?x=0&&escola=<?php echo $esc;?>" method="post" >


<br>
Sala e Data:    
<?php


$sql2a = "SELECT DISTINCT(s.nome) as no,s.id ,m.data_manutencao
FROM manutencao m, salas s, equipamento e
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by s.nome,m.data_manutencao desc";

$result2a = mysqli_query($db,$sql2a);


echo('<select name="saladata">');
while($row2a=mysqli_fetch_array($result2a))
{

      echo('<option value="'.$row2a['id'].'  '.$row2a['data_manutencao'].'">'.$row2a['no'].'   |   '.$row2a['data_manutencao'].'         </option>');

      //echo('<option value="'.$row2a['data_manutencao'].'"> </option>');

        }

       echo('</select>');

?>



&nbsp;&nbsp;&nbsp;
    <input onclick="return clickMe();" type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"  title="Manutenções da sala numa data">


 </form>


<br>

 <form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_equip_data.php?x=0&&escola=<?php echo $esc;?>" method="post" >



Equipamento e Data:
<?php


$sql2b = "SELECT DISTINCT(e.nomeequi) as no, e.id,m.data_manutencao
FROM manutencao m, equipamento e, salas s
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by e.nomeequi, m.data_manutencao desc";

$result2b = mysqli_query($db,$sql2b);


echo('<select name="equipdata">');
while($row2b=mysqli_fetch_array($result2b))
{

      echo('<option value="'.$row2b['id'].' '.$row2b['data_manutencao'].'">'.$row2b['no'].'  |   '.$row2b['data_manutencao'].'</option>');

        }

       echo('</select>');

?>

&nbsp;&nbsp;&nbsp;
    <input onclick="return clickMe2();" type=image name=sala src="<?php echo SVRURL ?>images/lupa2.svg"  
    title="Manutenções do equipamento numa data">

   
   
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