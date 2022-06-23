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


include("sessao_timeout.php");



 
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



    
<script language="javascript">
	function clickMe3()
	{
	
    var datar = (document.forms.requisi.elements.datareq.value);
    var dr=new Date(Date.parse(datar));

    var da=Date.now();

//    alert (da);

  //  alert(dr);



		if ( dr <= da )
				{
			//alert(" A data final deve ser superior à data inicial");
   
         event.preventDefault(); // prevent form submit

swal({

title: "A data da requisição deve ser superior à data atual!",
//text: "Sala: "+s1+" (Escola: "+ne1+")",
type: "warning",
//showCancelButton: true,
//confirmButtonColor: "#DD6B55",


confirmButtonText: "OK",
//cancelButtonText: "Não",
closeOnConfirm: false,
closeOnCancel: false

} );
   
   
         return false;
		}
		
    else {
      return true;

  }
	}
</script>



<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>



<?php
   $sql = "SELECT * FROM escolas ORDER by nome_escola";
   $result = mysqli_query($db,$sql);
   $rowcount = mysqli_num_rows($result);
   ?>



<form name="frme" id="frme" action = "" method = "post" >

   <div style="text-align: left;">


   <h3 style="color:black;">ESCOLA:
   
 
   
   
   <select title="Escolha a escola" 
   class="btn btn-info dropdown-toggle"
    name="escola" onChange="showescola(this.value);"  style="width:310px;" >
   
   
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
</h3>

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

<?php
  
if ( $_SESSION['tipo']==1)
{
   
   ?>
<form action="<?php echo SVRURL ?>inserirequip?x=0&&escola=<?php echo $esc ?>" method="post">

<!--  style="color:blue;"-->

<h3>

<button style="width:310px;" title="Inserir equipamento" type="submit" class="btn btn-outline-primary" >Inserir na escola</button>




</h3>
  
</form>



<br>

<?php
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






<form action="<?php echo SVRURL ?>verequipsala?x=0&&escola=<?php echo $esc?>" method="post" >

<button style="width:310px;"  type="submit" class="btn btn-outline-primary" title="Ver equipamento da sala">Ver da sala</button>



   


<br>

<select style="width:310px;"  class="btn btn-outline-secondary dropdown-toggle" name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }




?>     
</select>

</form>



<br>


<?php
if ( $_SESSION['tipo']==1)
{
   
?>


<?php


$sqla2 = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id
and e.id=$esc
order by s.nome";

$resulta2 = mysqli_query($db,$sqla2);
?>



<form action="<?php echo SVRURL ?>criar_etiq.php?escola=<?php echo $esc ?>" method="post" target="_new">

    <h3 >
    
    <button style="width:310px;" title="Ver etiquetas" type="submit" class="btn btn-outline-primary" >Ver etiquetas da sala</button>

    
<br>

<select style="width:310px;"  class="btn btn-outline-secondary dropdown-toggle" name="salaet" required>


<?php
while($rowa2=mysqli_fetch_array($resulta2))
{

      echo('<option value="'.$rowa2['sid'].'">'.$rowa2['no'].'</option>');

        }




?>     
</select>
   </h3>
</form>

<br>

<?php


$sqla3 = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id
and e.id=$esc
order by s.nome";

$resulta3 = mysqli_query($db,$sqla3);
?>


<form action="<?php echo SVRURL ?>criar_codbar.php?escola=<?php echo $esc ?>" method="post" target="_new">

    <h3 >
    
    <button style="width:310px;" title="Ver Código de barras" type="submit" class="btn btn-outline-primary" >Ver código de barras da sala</button>
   

    
<br>

<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" name="salacod" required>


<?php
while($rowa3=mysqli_fetch_array($resulta3))
{

      echo('<option value="'.$rowa3['sid'].'">'.$rowa3['no'].'</option>');

        }




?>     
</select>
   </h3>
</form>


  

    <?php
}
?>
    <br>
  
<?php
if ( $_SESSION['tipo']<>4)
{
   
?>
    
    <?php
                   $date = date("Y-m-d");
                   $mod_date = strtotime($date."+ 1 days");
                    
                 ?>


    <form name="requisi" action="<?php echo SVRURL ?>reqequip?x=0&&escola=<?php echo $esc ?>" method="post">

    <button onclick="return clickMe3();" style="width:310px;" title="Inserir requisição" type="submit" class="btn btn-outline-primary" >Inserir requisição para o dia</button>
    <br>
<input   value="<?php echo date("Y-m-d",$mod_date);?>"   style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required size="10" type = "date" name = "datareq" > 
 
</form>


    <br>


    <form action="<?php echo SVRURL ?>myrequi" method="post">

    <button style="width:310px;"  title="Minhas requisições" type="submit" class="btn btn-outline-primary" >Minhas requisições</button>
   
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
    
      <?php include ("jquery_bootstrap.php");?>

      <?php include ("footer.php");?>


   </body>
</html>