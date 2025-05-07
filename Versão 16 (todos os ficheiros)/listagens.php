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
               <a href="#" class="btn btn-secondary disabled">Listagens</a>
             
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
	
    var datai = (document.forms.datas.elements.datai.value);
    var dataf = (document.forms.datas.elements.dataf.value);
	
 alert(datai);

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





<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>




<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">






<select  class="custom-select" style="width:100%;" name="escola" onChange="showesc(this.value);"  >


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

        </div>




        <div class="action-section">
        <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar quantidade de equipamento</h2>
            


<form action="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode('li') ?>&&x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post"  class="needs-validation" novalidate>


<div class="row align-items-end">
<div class="col-md-8 mb-3">



<label for="salaSelect" class="form-label">
<i class="fas fa-door-open btn-icon"></i> 

&nbsp;
Selecione a sala:</label>



<?php


$sql = "SELECT DISTINCT(s.nome) as no,s.id 
FROM salas s, equipamento e
where s.id=e.id_sala
and s.id_escola=$esc 
order by nome";

$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);

echo('<select style="width:100%;" class="form-control required-field"  required name="sala">');


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

</div>

     
   
      <div class="col-md-4 mb-1">
      <button  style="width:100%" title="Quantidade de equipamento da sala" type="submit" class="action-button btn-secondary-action" ><i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade por sala</button>
      </div>
      </div>

 </form>


 <br>

 <?php



if ( $_SESSION['tipo']<>4 )
{
   
?>
 <form action="<?php echo SVRURL ?>qta_equipamentos_sala_tiposequi.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post"  class="needs-validation" novalidate>


 <div class="row align-items-end">
<div class="col-md-8 mb-3">



<label for="salaSelect" class="form-label">
<i class="fa-solid fa-server"></i>

&nbsp;
Selecione o tipo de equipamento:</label>



<br>



<?php


$sqleq = "SELECT DISTINCT(t.nome) as noeq 
FROM  tipos_equipamento t, equipamento eq, salas s
where eq.tipo=t.nome and eq.id_sala=s.id and s.id_escola=$esc
order by t.nome";

$resulteq = mysqli_query($db,$sqleq);
$rowcount = mysqli_num_rows($result);



echo('<select style="width:100%;" class="form-control required-field" required name="tiposequi">');

if ($rowcount > 0) {


while($roweq=mysqli_fetch_array($resulteq))
{

      echo('<option value="'.$roweq['noeq'].'">'.$roweq['noeq'].'</option>');

}
} else {
   echo('<option value="">Sem tipos de equipamento disponíveis</option>');
}
       echo('</select>');


?>     

</div>
   
   <div class="col-md-4 mb-1">
   <button  style="width:100%" title="Quantidade por sala do tipo" type="submit" class="action-button btn-secondary-action" >
      <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade por tipo
   </button>



   </div>
   </div>


 </form>


<?php
}
?>



 <?php
if ($_SESSION['tipo']==1)
{
?>



<br>


        <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar quantidade total</h2>


        <div class="row">
                <div class="col-md-6 mb-3">
      
<form action="<?php echo SVRURL ?>qta_equipamentos_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >






<button  style="width:100%" title="Quantidade de equipamento total por sala" type="submit" class="action-button btn-secondary-action" >
      <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade total por sala
   </button>

 </form>
</div>



 <br>
 <div class="col-md-6 mb-3">

<form action="<?php echo SVRURL ?>qta_equipamentos_total.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" >




<button  style="width:100%" title="Quantidade de equipamento total" type="submit" class="action-button btn-secondary-action" >
      <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade total
   </button>

 </form>

 </div></div>



</div>











<div class="action-section">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar avarias</h2>
            



<form name="avarias" action="num_avarias_entredatas.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo  base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>


<label for="salaSelect" class="form-label">
   <i class="fas fa-door-open btn-icon"></i> 

&nbsp;
Selecione as datas:</label>
<br>

<div class="row">
<div class="col-md-6 mb-3">

 <input  style="width:100%;"  class="form-control required-field" required  type ="date" name ="datai" > </div>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  e  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  

<div class="col-md-5 mb-3">
<input  style="width:100%;" class="form-control required-field" required  type ="date" name ="dataf" >   
</div>

</div>

 
  
  <button title="Nº de avarias e reparações entre datas" type="submit" onclick="return clickMe1();" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver nº de avarias entre datas
                        </button>
 </form>





 <br>








 <div class="row align-items-end">
<div class="col-md-6 mb-3">

<label for="salaSelect" class="form-label">
<i class="fa-solid fa-laptop-code"></i>
    Selecione o equipamento:</label>




 <form action="<?php echo SVRURL ?>num_avarias_equipamento.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post"  class="needs-validation" novalidate>



<?php


$sql = "SELECT DISTINCT(e.nomeequi) as no,ar.id_equi
FROM equipamento e, avarias_reparacoes ar
where ar.id_equi=e.id
and ar.id_escola=$esc
order by e.nomeequi;";

$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);

echo('<select style="width:100%;"  class="form-control required-field"  required name="equi">');


if ($rowcount > 0) {

while($row=mysqli_fetch_array($result))
{

      echo('<option value="'.$row['id_equi'].'">'.$row['no'].'</option>');

        }


      } else {
         echo('<option value="">Sem equipamentos disponíveis</option>');
      }
      

       echo('</select>');


?>     

</diV>

<div class="col-md-6 mb-6">

<button  style="width:100%;" title="Nº de avarias do equipamento" type="submit" class="action-button btn-secondary-action">
<i class="fas fa-eye btn-icon"></i>   
&nbsp;
Ver nº de avarias do equipamento </button>


 </form>
 </div>


</diV>


 <br>



 <div class="row align-items-end">
<div class="col-md-6 mb-3">

<label for="salaSelect" class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a sala:</label>

<form action="<?php echo SVRURL ?>num_avarias_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc)?>" method="post"  class="needs-validation" novalidate>



<?php


$sql = "SELECT DISTINCT(id_sala) as no, s.nome
FROM avarias_reparacoes ar, salas s
where ar.id_sala=s.id and ar.id_escola=$esc
 order by id_sala";

$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);

echo('<select style="width:100%;" class="form-control required-field" required  required name="sala2">');


if ($rowcount > 0) {


while($row=mysqli_fetch_array($result))
{

     echo('<option value="'.$row['no'].'">'.$row['nome'].'</option>');

       }


      } else {
         echo('<option value="">Sem datas disponíveis</option>');
      }
      


      echo('</select>');


?>     
</div>

<div class="col-md-6 mb-6">


<button  style="width:100%;" title="Nº de avarias da sala" type="submit" class="action-button btn-secondary-action">
<i class="fas fa-eye btn-icon"></i>   
&nbsp;
Ver nº de avarias da sala </button>
  
</form>


</diV></diV>


<br>



<div class="row align-items-end">
<div class="col-md-6 mb-3">

<label for="salaSelect" class="form-label">
<i class="fa-solid fa-rectangle-list"></i>
   Selecione o tipo de equipamento:</label>




<form action="<?php echo SVRURL ?>num_avarias_tipoeq.php?x=<?php echo base64_encode(0)?>&&ies=<?php echo base64_encode($esc)?>" method="post"  class="needs-validation" novalidate>



<?php


$sql = "SELECT DISTINCT(eq.tipo) as tipo
FROM avarias_reparacoes ar, equipamento eq
where ar.id_equi=eq.id and ar.id_escola=$esc
 order by eq.tipo";

$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);

echo('<select style="width:100%;" class="form-control required-field"  required name="tipoeq">');

if ($rowcount > 0) {

while($row=mysqli_fetch_array($result))
{

     echo('<option value="'.$row['tipo'].'">'.$row['tipo'].'</option>');

       }


      } else {
         echo('<option value="">Sem tipos de equipamento disponíveis</option>');
      }
      
      echo('</select>');


?>     
  </div>

<div class="col-md-6 mb-6">



<button  style="width:100%;" title="Nº de avarias por tipo equipamento" type="submit" class="action-button btn-secondary-action">
<i class="fas fa-eye btn-icon"></i>   
&nbsp;
Ver nº de avarias por tipo de equipamento</button>


</div>  </div>
</form>


<div  style="text-align: center">

</div><br><br>











<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Estatística de avarias</h2>


<form action="<?php echo SVRURL ?>estatistica_avarias.php?ies=<?php echo base64_encode($esc) ?>" method="post" >


<button  style="width:100%;" title="Avarias (últimos 5 anos letivos) " type="submit" class="action-button btn-secondary-action">Avarias (últimos 5 anos) </button>

   
   
 </form>

 <div  style="text-align: center">

</div><br>



<div class="row">
<div class="col-md-6 mb-3">

<form action="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($esc) ?> " method="post" >


<button  style="width:100%;" title="Equipamentos com mais avarias (top 10)" type="submit" class="action-button btn-secondary-action" >Equipamentos com mais avarias (top 10) </button>

 </form>
      </diV>
 <div class="col-md-6 mb-3">
<form action="salas_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($esc) ?>" method="post" >

<button  style="width:100%;" title="Salas com mais avarias (top 10)  " type="submit" class="action-button btn-secondary-action" >Salas com mais avarias (top 10)  </button>


 </form>
      </diV>

 <?php
}
?>


</div></div>










 <?php



if ( $_SESSION['tipo']==4 || $_SESSION['tipo']==1)
{
   
?>




<div class="action-section">
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar requisições</h2>

<br>



<form action="<?php echo SVRURL ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(0)?>&&ies=<?php echo base64_encode($esc) ?>" method="post"  class="needs-validation" novalidate>



<div class="row">
<div class="col-md-6 mb-3">




<label for="salaSelect" class="form-label">

<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione a data:</label>




      <select title="Escolha a data" required
      class="form-control required-field" 
    name="data"  style="width:100%;" >
   
    <?php
   
   
   //echo $idescola;
   
   
   
   $sql2 = "SELECT DISTINCT(r.datautil)
   FROM requisicao r, salas s
   WHERE r.id_sala=s.id and 
   s.id_escola=$esc
   and r.dataentrega is null
   order by r.datautil;";
   $result2 = mysqli_query($db,$sql2);
   $rowcount = mysqli_num_rows($result2);


   //echo('<option value=""> Escolha a escola  </option>');  
 if ($rowcount > 0) {

   while($row2=mysqli_fetch_array($result2))
   {
   
    
   
     echo('<option value="'.$row2['datautil'].'">'.date('d/m/Y',strtotime($row2['datautil'])).'</option>');
   
   
   }
   

} else {
   echo('<option value="">Sem datas disponíveis</option>');
}

   
   echo('</select>');
   
   ?>

</div>


    <div class="col-md-6 mb-3">


<br>
    <button  style="width:100%" title="ver requisições a terminar no dia " type="submit" class="action-button btn-secondary-action" ><i class="fas fa-eye btn-icon"></i>&nbsp; Ver requisições a terminar na data</button>

</div>

 </form>

 </div>





 
<form name="requi" action="<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>




<label for="salaSelect" class="form-label">
<i class="fa-solid fa-calendar-days"></i>
&nbsp;
Selecione as datas:</label>


<div class="row">
<div class="col-md-6 mb-3">


<input style="width:100%;" class="form-control required-field"  required  type = "date" name="datai1" > 
</diV>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  e  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

  <div class="col-md-5 mb-3">
 <input style="width:100%" class="form-control required-field"  required " type = "date" name="dataf1" >   
 </div></diV>


 <button title="Requisições a terminar entre datas" type="submit" onclick="return clickMe2();" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver requisições a terminar entre datas
                        </button>

 </form>


 </div>

 <?php
}
?>


<?php include ("jquery_bootstrap.php");?>

<br>


                    
               
               </div>
               <br>
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