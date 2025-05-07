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
               <a href="#" class="btn btn-secondary disabled">Manutenções</a>
               
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>


    
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
	function clickMe2()
	{
	
    var datai = (document.forms.manutencoes2.elements.datami2.value);
    var dataf = (document.forms.manutencoes2.elements.datamf2.value);
	
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





<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}




</script>



<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>




<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">





<select  style="width:100%;" name="escola" onChange="showesc(this.value);"  class="custom-select">


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by id";
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




<?php
 

 $sql1 = "select nome_escola
 from escolas 
 where id=$esc";
 $result1 = mysqli_query($db,$sql1); 
 $rows =mysqli_fetch_row($result1);
 
 
 $ne = $rows[0];
            
            ?>
     
     <div class="text-center mt-3">
                <span class="badge badge-primary p-2" style="font-size: 1rem;">
                    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
                </span>
            </div>
        </div>




<?php

if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
{

?>


<br>

<div class="action-section">
<h2 class="section-title">
        <i class="fas fa-plus-circle btn-icon"></i> 

Manutenções</h2>

<form action="inserirmanut?esm=<?php echo base64_encode($esc);?>" method="post"  class="needs-validation" novalidate>




<?php


$sql = "SELECT DISTINCT(nome) as no,s.id 
FROM salas s, equipamento eq
where s.id_escola=$esc and eq.id_sala=s.id
order by s.nome";

$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);
?>


<div class="row align-items-end">
<div class="col-md-8 mb-3">

<label for="salaSelect" class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a Sala:</label>
<?php

echo('<select style="width:100%;" class="form-control required-field" name="sala" required>' );


if ($rowcount > 0) {

while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

      } else {
         echo('<option value="">Sem salas disponíveis</option>');
     }



       echo('</select>');


?>     
</diV>

<div class="col-md-4 mb-1">

     
      <button style="width:100%;" title="Inserir manutenção da sala" type="submit" class="action-button btn-primary-action"> 
      <i class="fa-solid fa-screwdriver-wrench"></i>
      &nbsp;  
      Inserir

      </button>
      </div>


      </diV>

    
    
    </form>

      </diV>




   
   
 </form>


<?php
}
?>


<div class="action-section">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar manutenções entre datas</h2>





<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(0)?>&&esm=<?php echo base64_encode($esc);?>" method="post" class="needs-validation" novalidate>

<!--
<button style="width:100%;" onclick="return clickMe();" title="Manutenção da sala entre datas" type="submit" class="btn btn-outline-primary" > Ver manutenções da sala entre datas</button>
-->


<label for="salaSelect" class="form-label">
   <i class="fas fa-door-open btn-icon"></i> 
<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione a sala e datas:</label>
<br>

<?php


$sql2 = "SELECT DISTINCT(s.nome) as no,s.id 
FROM manutencao m, salas s, equipamento e
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by s.nome";

$result2 = mysqli_query($db,$sql2);
$rowcount = mysqli_num_rows($result2);

echo('<select style="width:100%;" class="custom-select" name="sala" required>>');


if ($rowcount > 0) {

while($row=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

        }

      } else {
         echo('<option value="">Sem salas disponíveis</option>');
     }


       echo('</select>');

?>

<br><br>

<div class="row">
<div class="col-md-6 mb-3">


<input style="width:100%;" class="form-control required-field" required size="10" type = "date" name="datami" > 
      </diV>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  e  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

  <div class="col-md-5 mb-3">
<input style="width:100%" class="form-control required-field" required size="10" type = "date" name="datamf" >   
      </div></diV>
      
      
<button title="Manutenção da sala entre datas" type="submit" onclick="return clickMe();" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver manutenções da sala entre datas
                        </button>
   
 </form>




 <br>  <br> 


<form name="manutencoes2" action="<?php echo SVRURL ?>manutencoes_equip_entredatas.php?x=<?php echo base64_encode(0)?>&&esm=<?php echo  base64_encode($esc);?>" method="post" class="needs-validation" novalidate>

<label for="salaSelect" class="form-label">
<i class="fa-solid fa-laptop-code"></i>
<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione o equipamento e datas:</label>



<br>





<?php


$sql2 = "SELECT DISTINCT(e.nomeequi) as no, e.id
FROM manutencao m, equipamento e, salas s
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by e.nomeequi";

$result2 = mysqli_query($db,$sql2);
$rowcount = mysqli_num_rows($result2);

echo('<select style="width:100%;" class="custom-select" name="equip" required>');
if ($rowcount > 0) {
while($row2=mysqli_fetch_array($result2))
{

      echo('<option value="'.$row2['id'].'">'.$row2['no'].'</option>');

        }
      } else {
         echo('<option value="">Sem equipamentos disponíveis</option>');
     }

       echo('</select>');

?>

<br><br>
<div class="row">
<div class="col-md-6 mb-3">
        
<input style="width:100%;" class="form-control required-field" required size="10" type = "date" name = "datami2" > 
</div>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  e  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <div class="col-md-5 mb-3">
<input style="width:100%;" class="form-control required-field" required size="10" type = "date" name = "datamf2" >   
</div>

</div>

<button style="width:100%;" onclick="return clickMe2();" title="Manutenção do equipamento entre datas" type="submit" class="action-button btn-secondary-action" > 
<i class="fas fa-eye btn-icon"></i> &nbsp;   
Ver manutenções do equipamento entre datas

</button>
 </form>

 </div>












<br>


<div class="action-section">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar manutenções por data específica </h2>


<form name="manutencoes2"   
action="<?php echo SVRURL ?>manutencoes_sala_data.php?x=<?php echo base64_encode(0)?>&&esm=<?php echo base64_encode($esc);?>" method="post" class="needs-validation" novalidate>

<div class="row align-items-end">
<div class="col-md-8 mb-3">



<label for="salaSelect" class="form-label">
<i class="fas fa-door-open btn-icon"></i> 
<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione a sala e a data:</label>

 
<?php


$sql2a = "SELECT DISTINCT(s.nome) as no,s.id ,m.data_manutencao
FROM manutencao m, salas s, equipamento e
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by s.nome,m.data_manutencao desc";

$result2a = mysqli_query($db,$sql2a);
$rowcount = mysqli_num_rows($result2a);

echo('<select style="width:100%;" class="form-control required-field" name="saladata"  required>');

if ($rowcount > 0) {

while($row2a=mysqli_fetch_array($result2a))
{

      echo('<option value="'.$row2a['id'].'  '.$row2a['data_manutencao'].'">'.$row2a['no'].'   |   '.date('d/m/Y',strtotime($row2a['data_manutencao'])).'         </option>');

      //echo('<option value="'.$row2a['data_manutencao'].'"> </option>');

        }

      } else {
         echo('<option value="">Sem dados disponíveis</option>');
     }

       echo('</select>');

?>
    </div>


    <div class="col-md-4 mb-1">

<button  style="width:100%" title="Sala e data" type="submit" class="action-button btn-secondary-action" ><i class="fas fa-eye btn-icon"></i>&nbsp; Ver manutenção</button>
  </div>
  </div>
 </form>


 



<br><br>

 <form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_equip_data.php?x=<?php echo base64_encode(0)?>&&esm=<?php echo base64_encode($esc);?>" method="post" class="needs-validation" novalidate>

 <div class="row align-items-end">
<div class="col-md-8 mb-3">



 <label for="salaSelect" class="form-label">
<i class="fa-solid fa-laptop-code"></i>
<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione o equipamento e datas:</label>

 

<?php


$sql2b = "SELECT DISTINCT(e.nomeequi) as no, e.id,m.data_manutencao
FROM manutencao m, equipamento e, salas s
where m.id_equi=e.id and e.id_sala=s.id and s.id_escola=$esc
order by e.nomeequi, m.data_manutencao desc";

$result2b = mysqli_query($db,$sql2b);
$rowcount = mysqli_num_rows($result2b);

echo('<select style="width:100%;" class="form-control required-field" name="equipdata"  required>');
if ($rowcount > 0) {
while($row2b=mysqli_fetch_array($result2b))
{

      echo('<option value="'.$row2b['id'].' '.$row2b['data_manutencao'].'">'.$row2b['no'].'  |   '.date('d/m/Y',strtotime($row2b['data_manutencao'])).'</option>');

        }
      } else {
         echo('<option value="">Sem dados disponíveis</option>');
     }

       echo('</select>');

?>
 </div>


<div class="col-md-4 mb-1">
   
<button  style="width:100%" title="Sala e data" type="submit" class="action-button btn-secondary-action" ><i class="fas fa-eye btn-icon"></i>&nbsp; Ver manutenção</button>

      </div>
      </div>
 </form>



 </div>







<br>
<?php include ("jquery_bootstrap.php");?>

                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    

<!-- Script para validação do formulário -->
<script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

      <?php include ("footer.php");?>


   </body>
</html>