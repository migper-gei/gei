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
			//alert(" A data final deve ser superior à data inicial");
      
         event.preventDefault(); // prevent form submit

swal({

title: "A data final deve ser superior à data inicial!",
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
			//alert(" A data final deve ser superior à data inicial");
     
     // alert('Escolha pelo menos um equipamento!');
   event.preventDefault(); // prevent form submit

swal({

title: "A data final deve ser superior à data inicial!",
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


    
<script language="javascript">
	function clickMe2()
	{
	
    var datai = (document.forms.requi.elements.datai1.value);
    var dataf = (document.forms.requi.elements.dataf1.value);
	
 //alert(datai);

    //var myDate = new Date("2013/1/16");
    //var date8 =  new Date(Date.parse("2020-09-08"));
    var di=new Date(Date.parse(datai));
    var df=new Date(Date.parse(dataf));
   //alert(date8);
    
  
//alert(compara1);

		if ( df <= di )
				{
			//alert(" A data final deve ser superior à data inicial");
   
         event.preventDefault(); // prevent form submit

swal({

title: "A data final deve ser superior à data inicial!",
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




<br>


<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">


<h3 style="color:black;">ESCOLA/INSTITUIÇÃO:




<select  style="width:310px;" name="escola" onChange="showesc(this.value);"   class="btn btn-info dropdown-toggle">


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




</form>
</div >

<div style="text-align: left;">


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


<br><br>


<form action="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode('li') ?>&&x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >


<button style="width:310px;" title="Quantidade de equipamento da sala" type="submit" class="btn btn-outline-primary" > Quantidade de equipamento da sala</button>

<br>
<?php


$sql = "SELECT DISTINCT(s.nome) as no,s.id 
FROM salas s, equipamento e
where s.id=e.id_sala
and s.id_escola=$esc
order by nome";

$result = mysqli_query($db,$sql);


echo('<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required name="sala">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     

   
   
 </form>


 <br>

 <?php



if ( $_SESSION['tipo']<>4 )
{
   
?>
 <form action="<?php echo SVRURL ?>qta_equipamentos_sala_tiposequi.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >


 <button style="width:310px;" title="Quantidade por sala do tipo" type="submit" class="btn btn-outline-primary" >Quantidade por sala do tipo</button>


<br>



<?php


$sqleq = "SELECT DISTINCT(t.nome) as noeq 
FROM  tipos_equipamento t, equipamento eq, salas s
where eq.tipo=t.nome and eq.id_sala=s.id and s.id_escola=$esc
order by t.nome";

$resulteq = mysqli_query($db,$sqleq);


echo('<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required name="tiposequi">');
while($roweq=mysqli_fetch_array($resulteq))
{

      echo('<option value="'.$roweq['noeq'].'">'.$roweq['noeq'].'</option>');

        }

       echo('</select>');


?>     

   
 </form>


 <?php
}
?>



 <?php
if ($_SESSION['tipo']==1)
{
?>



<br>


<form action="<?php echo SVRURL ?>qta_equipamentos_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >


<button style="width:310px;" title="Quantidade de equipamento total (por sala)" type="submit" class="btn btn-outline-primary" >Quantidade equipamento (por sala)</button>

   
 </form>



 <br>


<form action="<?php echo SVRURL ?>qta_equipamentos_total.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >


<button style="width:310px;" title="Quantidade de equipamento (TOTAL)" type="submit" class="btn btn-outline-primary" >Quantidade total equipamento</button>

   
 </form>

 <div  style="text-align: center">

</div>

<br>

<form name="avarias" action="num_avarias_entredatas.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo  base64_encode($esc) ?>" method="post" >


<button style="width:310px;" onclick="return clickMe1();" title="Nº de avarias e reparações entre datas" type="submit" class="btn btn-outline-primary" >Nº de avarias entre </button>
<br>

 <input  style="width:140px;" class="btn btn-outline-secondary dropdown-toggle" required  type ="date" name ="datai" > 
 e  
<input  style="width:140px;" class="btn btn-outline-secondary dropdown-toggle" required  type ="date" name ="dataf" >   

  
 </form>
 <br>



 <form action="<?php echo SVRURL ?>num_avarias_equipamento.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >

 <button  style="width:310px;" title="Nº de avarias do equipamento" type="submit" class="btn btn-outline-primary" >Nº de avarias do equipamento </button>


<br>
<?php


$sql = "SELECT DISTINCT(e.nomeequi) as no,ar.id_equi
FROM equipamento e, avarias_reparacoes ar
where ar.id_equi=e.id
and ar.id_escola=$esc
order by e.nomeequi;";

$result = mysqli_query($db,$sql);


echo('<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required name="equi">');
while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id_equi'].'">'.$row['no'].'</option>');

        }

       echo('</select>');


?>     

   
 </form>


 <br>



<form action="<?php echo SVRURL ?>num_avarias_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc)?>" method="post" >


<button style="width:310px;" title="Nº de avarias da sala" type="submit" class="btn btn-outline-primary" >Nº de avarias da sala</button>


<br>
<?php


$sql = "SELECT DISTINCT(id_sala) as no, s.nome
FROM avarias_reparacoes ar, salas s
where ar.id_sala=s.id and ar.id_escola=$esc
 order by id_sala";

$result = mysqli_query($db,$sql);


echo('<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle"  required name="sala2">');
while($row=mysqli_fetch_array($result))
{

     echo('<option value="'.$row['no'].'">'.$row['nome'].'</option>');

       }

      echo('</select>');


?>     

  
</form>





<br>



<form action="<?php echo SVRURL ?>num_avarias_tipoeq.php?x=<?php echo base64_encode(0)?>&&ies=<?php echo base64_encode($esc)?>" method="post" >

<button  style="width:310px;" title="Nº de avarias por tipo equipamento" type="submit" class="btn btn-outline-primary" >Nº de avarias por tipo equipamento</button>



<br>
<?php


$sql = "SELECT DISTINCT(eq.tipo) as tipo
FROM avarias_reparacoes ar, equipamento eq
where ar.id_equi=eq.id and ar.id_escola=$esc
 order by eq.tipo";

$result = mysqli_query($db,$sql);


echo('<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required name="tipoeq">');
while($row=mysqli_fetch_array($result))
{

     echo('<option value="'.$row['tipo'].'">'.$row['tipo'].'</option>');

       }

      echo('</select>');


?>     
  
  
</form>


<div  style="text-align: center">

</div><br>
<form action="<?php echo SVRURL ?>estatistica_avarias.php?ies=<?php echo base64_encode($esc) ?>" method="post" >


<button  style="width:310px;" title="Avarias (últimos 5 anos letivos) " type="submit" class="btn btn-outline-primary" >Avarias (últimos 5 anos letivos) </button>

   
   
 </form>

 <div  style="text-align: center">

</div><br>
<form action="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($esc) ?> " method="post" >


<button  style="width:310px;" title="Equipamentos com mais avarias (top 10)" type="submit" class="btn btn-outline-primary" >Equipamentos com mais avarias (top 10) </button>

 </form>


 <br>
<form action="salas_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($esc) ?>" method="post" >

<button  style="width:310px;" title="Salas com mais avarias (top 10)  " type="submit" class="btn btn-outline-primary" >Salas com mais avarias (top 10)  </button>


 </form>


 <?php
}
?>


 <?php



if ( $_SESSION['tipo']==4 || $_SESSION['tipo']==1)
{
   
?>

 <br> 
<form action="<?php echo SVRURL ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(0)?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >


<button   style="width:310px;" title="Requisições a terminar no dia " type="submit" class="btn btn-outline-primary" >Requisições a terminar no dia  </button>
<br>

<!--
 <input style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" required size="10" type = "date" name = "data" value="<?php echo date("Y-m-d"); ?>"> 
      -->
      <select title="Escolha a data" required
      class="btn btn-outline-secondary dropdown-toggle"
    name="data"  style="width:310px;" >
   
    <?php
   
   
   //echo $idescola;
   
   
   
   $sql2 = "SELECT DISTINCT(r.datautil)
   FROM requisicao r, salas s
   WHERE r.id_sala=s.id and 
   s.id_escola=$esc
   and r.dataentrega is null
   order by r.datautil;";
   $result2 = mysqli_query($db,$sql2);
   
   //echo('<option value=""> Escolha a escola  </option>');  
 
   while($row2=mysqli_fetch_array($result2))
   {
   
    
   
     echo('<option value="'.$row2['datautil'].'">'.date('d/m/Y',strtotime($row2['datautil'])).'</option>');
   
   
   }
   
   
   echo('</select>');
   
   ?>
 </form>



 <br> 
<form name="requi" action="<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >

<button  style="width:310px;" onclick="return clickMe2();"  title="Requisições a terminar entre datas" type="submit" class="btn btn-outline-primary" >Requisições a terminar entre </button>

<br>
<input style="width:140px;" class="btn btn-outline-secondary dropdown-toggle" required size="10" type = "date" name="datai1" > 
 e   
 <input style="width:140px;" class="btn btn-outline-secondary dropdown-toggle" required size="10" type = "date" name="dataf1" >   

 </form>

 </div>

 <?php
}
?>


<?php include ("jquery_bootstrap.php");?>

<br>


                    </>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>