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
                     <h2>Manutenções</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-3">
              
                        

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



<?php

if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
{

?>



<form action="inserirmanut" method="post" >


Inserir manutenção da sala:

<?php


$sql = "SELECT DISTINCT(nome) as no FROM salas order by nome";

$result = mysqli_query($db,$sql);


echo('<select name="sala">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');

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





<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=0" method="post" >

<br>
Sala:    
<?php


$sql2 = "SELECT DISTINCT(sala) as no FROM manutencao order by sala";

$result2 = mysqli_query($db,$sql2);


echo('<select name="sala">');
while($row=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');

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


<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_equip_entredatas.php?x=0" method="post" >



Equipamento:
<?php


$sql2 = "SELECT DISTINCT(nomeequi) as no FROM manutencao order by nomeequi";

$result2 = mysqli_query($db,$sql2);


echo('<select name="equip">');
while($row2=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row2['no'].'">'.$row2['no'].'</option>');

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


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>