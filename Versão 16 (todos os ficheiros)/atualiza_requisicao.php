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


   <script src="js/jquery1102.js"></script>

  <script src="js/jqueryselectlistactions.js"></script>
  

  <link rel="stylesheet" href="css/listboxs.css">


   <!-- body   
<script src="lbox/bootstrap.js"></script>

  <link rel="stylesheet" href="lbox/bootstrap.css">
-->
   <body class="main-layout">
      <!-- loader -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div> 
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php

include ("css_inserir.php");

include("sessao_timeout.php");

 
  ?>


    





<script language="JavaScript" >
	function verificadados()
	{
	
    var datar = (document.forms.req.elements.datareq.value);
    var datau = (document.forms.req.elements.datautil.value);
	
 //alert(datai);

  
    var di=new Date(Date.parse(datar));
    var df=new Date(Date.parse(datau));
  
    
   //alert (di);
   var outradata = new Date();
   outradata.setDate(getDate() + 1);
   //alert (outradata);

   var hi1 = (document.forms.req.elements.horainicio.value);
   var hf1 = (document.forms.req.elements.horafim.value);
 // alert (hi1);
   //alert (hf1);




var lbox2=document.getElementById("lstBox2");
//alert(lbox2.length);


if ( df <= outradata  )
				{
			alert(" A data de utilização deve ser superior à data atual em 1 dia.");
      return false;
		}


if (hi1>hf1)
{
	alert(" A hora de fim deve ser superior à hora de inicio.");
      return false;

}


if (lbox2.length==0)
{
	alert(" A lista deve ter pelo menos um equipamento a requisitar.");
      return false;

}



	
		
    else {
      return true;
 }

 
	}
</script>



<script>
function selectAll(selectBox,selectAll) { 
    // have we been passed an ID 
    var lbox2=document.getElementById("lstBox2");
    if (lbox2.length==0)
{
	//alert(" A lista deve ter pelo menos um equipamento a requisitar.");
      
    event.preventDefault(); // prevent form submit

swal({

title: " A lista deve ter pelo menos um equipamento a requisitar!",
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
else
{
    if (typeof selectBox == "string") { 
        selectBox = document.getElementById(selectBox);
    } 
    // is the select box a multiple select box? 
    if (selectBox.type == "select-multiple") { 
        for (var i = 0; i < selectBox.options.length; i++) { 
             selectBox.options[i].selected = selectAll; 
        } 
    }
   }
   }

  </script> 



<?php

   $id=base64_decode($_GET['ri']);



   if ( !isset($id)    || empty($id)     || !is_numeric($id) 
   )
   
   {
      //echo "aaaaaa";
   ?>
   
   
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>myrequi';
   }, 10);
   </script>
   
   
   <?php
   }



   $em=$_SESSION['email'];



   $sql0 ="
   SELECT count(*) as c 
   FROM requisicao
   where id=$id and email_util='".$em."'  ";
   
   $result0 = mysqli_query($db,$sql0);
   $rows0 =mysqli_fetch_row($result0);
   $c = $rows0[0];



if ($c==0)
{
?>


<script>
window.setTimeout(function() {
   window.location.href = '<?php echo SVRURL ?>myrequi';
}, 10);
</script>




<?php
}


      $sql11 = "SELECT r.*,s.nome,es.nome_escola, es.id
      from requisicao r,  salas s, escolas es 
      where  r.id_sala=s.id and s.id_escola=es.id and r.id=$id";
      $result11 = mysqli_query($db,$sql11); 
      $row1 =mysqli_fetch_array($result11);
    ?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Equipamentos >> Minhas requisições >> Atualizar</a>
               <div class="titlepage">
                     <h2>Requisição nº <?php echo $id; ?>
                     <br> <?php echo $row1['nome_escola']; ?>
                        <br>
                     </h2>
                  </div>

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


    <div class="form-container">

          
    <div class="step-indicator">
      <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
      Ver tabela em baixo com requisições já efetuadas.<br>
        A data da utilização deve ser superior em 1 dia em relação à data atual.
</div>

      <form onSubmit="return verificadados();" name="req"  
      action="<?php echo SVRURL ?>atualokrequi/<?php echo base64_encode($id);?>" method="post" class="needs-validation" novalidate>
<br>


                   

                   <!--onSubmit="return verificadados();"
                    <label>Data Atual: </label>  
                    <?php echo date("Y-m-d"); ?>           
                   
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; -->
                    
                    <div class="row">
                    <div class="col-md-6 mb-3">

                    <label>Data da requisição: </label>
                    <br>
                    <input style="width:100%"  class="form-select" readonly  value="<?php echo $row1['datarequi']; ?>"            
                    size="10" type = "date" name = "datareq" >
                        </div>

                 <?php
                   $date = date("Y-m-d");
                   $mod_date = strtotime($date."+ 15 days");
                    
                 ?>
                  
            
                  
                  <div class="col-md-6 mb-3">
                    <label>Data da utilização: </label>  
                  

              
                    <input onChange="show2(this.value);"                 
                    class="form-control required-field" required  
                    value="<?php echo $row1['datautil']; ?>"            
                    size="10" type = "date" name = "datautil" >
               
  </div></div>
                   <br> 
                 
                   <label>Hora de inicio: </label>  
                    <input class="form-control required-field" required  
                    value="<?php echo $row1['horainicio']; ?>" 
                    size="10" type = "time" name = "horainicio" >
                 <br>
                   <label>Hora de fim: </label>  
                    <input class="form-control required-field" required   
                    value="<?php echo $row1['horafim']; ?>"          
                    size="10" type = "time" name = "horafim" >


                   <br />
                   <br>
                  
      <label>Sala: </label>  
      
      <?php


$sql = "SELECT DISTINCT(nome) as no,id 
FROM salas
where id_escola=".$row1['id']." and equip_requisitavel='Não'
order by nome";

$result = mysqli_query($db,$sql);


//echo('<select name="sala">');
?>


<select required class="form-control required-field" name="sala"  >

<?php

//echo('<option value=""> Escolha a sala   </option>');  

while($row=mysqli_fetch_array($result))
{

if ($row['id']==$row1['id_sala'])

{

    echo('<option selected value="'.$row['id'].'">'.$row['no'].'</option>');
}

else
{
echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');
}

}

echo('</select>');
?>    
                  

<br><br>



             
       




<?php

//echo $row1['id'];

$sql0 = "SELECT  DISTINCT(eq.nomeequi), eq.id
from requisicao r, equip_requisitado er, equipamento eq, salas s
where  r.id=er.id_req
and er.id_equip=eq.id
and r.id_sala=s.id
and s.id_escola=".$row1['id']."
and r.id<>$id
and r.dataentrega is null  
order by eq.id;";
$result0 = mysqli_query($db,$sql0);


$i=0;
$arrid=array();
while($row1aa=mysqli_fetch_array($result0))
{
$arrid[$i]=$row1aa['id'];
$i=$i+1;
}

/*
foreach ($arrid as $var_listar)
{
    echo $var_listar;
    echo (' ');
}

*/



$sql = "SELECT e.id, e.nomeequi
FROM equipamento e, salas s
where e.id_sala=s.id
and s.id_escola=".$row1['id']."
and s.equip_requisitavel='Sim' 
and e.id NOT IN
(
select er.id_equip
from requisicao r, equip_requisitado er
where er.id_req=$id and r.id=er.id_req
)
ORDER by e.tipo,e.nomeequi;";
$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);


//echo ($rowcount);


?>




   <div class="row style-select">
      <div class="col-md-12">


         <div class="subject-info-box-1">
            <label>Equipamentos disponíveis:<br>(Cor laranja: já em requisições)</label>
            
                 
                 <select size="10" multiple class="form-control" id="lstBox1" name="eqdisp[]">


                 <?php


while($row3=mysqli_fetch_array($result))
{


    $sql1 = "select count(*) 
    from avarias_reparacoes ar, equipamento eq, salas s
    where ar.id_equi=eq.id and s.id=ar.id_sala
     and s.id_escola=".$row1['id']."
    and eq.id=".$row3['id']." and datareparacao is null";

 

    $result1 = mysqli_query($db,$sql1); 
    $rows =mysqli_fetch_row($result1);

  //  echo $rows[0];

  if ($rows[0]==0)
{




   $e=0;
        //echo $var_listar;
        //echo count($arrid);
     foreach ($arrid as $var_listar)
    {
    

if ( ($row3['id']==$var_listar))
{
    echo('<option style="color: orange;" value="'.$row3['id'].'">'.$row3['nomeequi'].' </option>');
   $e=1;
}

//else echo('<option value="'.$row3['id'].'">'.$row3['nomeequi'].' </option>');


}

if ( ($row3['id']<>$var_listar) && $e==0)
echo('<option value="'.$row3['id'].'">'.$row3['nomeequi'].' </option>');



}


}


echo('</select>');

?>


         </div>
<br>


            <div class="subject-info-arrows text-center">
					<br />
					<input  id='btnAllRight' value='>>' class="btn btn-default" /><br />
					<input  id='btnRight' value='>' class="btn btn-default" /><br />
					<input  id='btnLeft' value='<' class="btn btn-default" /><br />
					<input  id='btnAllLeft' value='<<' class="btn btn-default" />
			</div>




         <div class="subject-info-box-2">

            
            <label>Equipamentos a requisitar: 
             </label>


        <?php    
             $sql4 = "SELECT er.*, e.id,e.nomeequi
             from requisicao r,  equip_requisitado er, equipamento e
             where  r.id=er.id_req and er.id_equip=e.id
             and r.id=$id";

$result4 = mysqli_query($db,$sql4);
?>


                 <select class="form-control required-field"  size="10" multiple required  id="lstBox2" name="eqrequi[]">
             
                 <?php



while($row4=mysqli_fetch_array($result4))

{
//echo('<option value="'.$row4['id'].'">'.$row4['nomeequi'].' </option>');

$e=0;
        //echo $var_listar;
        //echo count($arrid);
     foreach ($arrid as $var_listar)
    {
    

if ( ($row4['id']==$var_listar))
{
    echo('<option style="color: orange;" value="'.$row4['id'].'">'.$row4['nomeequi'].' </option>');
   $e=1;
}

//else echo('<option value="'.$row3['id'].'">'.$row3['nomeequi'].' </option>');


}

if ( ($row4['id']<>$var_listar) && $e==0)
echo('<option value="'.$row4['id'].'">'.$row4['nomeequi'].' </option>');



}


echo('</select>');





?>

                </select>


                <p style=" font-size:11px; text-align:center">(Deve selecionar os equipamentos antes de clicar no botão "Requisitar")
               
               <button  
               style="height:35px; " type="button" class="btn btn-outline-primary" name="Button" value="Selecionar todos" onclick="selectAll('lstBox2',true)">
               Selecionar todos </button></p>
              </div>

       

      </div>
      </div>
      <br>
      <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Após clicar no botão "Atualizar" é verificado se os equipamento ainda estão disponíveis para a data, horas e sala. Caso um dos equipamentos não esteja disponível, a requisição não é feita. 
        Após a atualização, será atribuído novo número à requisição.


     <br>
     <br>  
     <div style="  text-align: center;"> 
        
     
     <button type="submit" class="btn-submit">
     <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar requisição equipamento
                                    </button>

  </div>
</form>

</div>



Requisições já efetuadas:
<table class="table table-sm" >
   <thead>
       <tr >
       <th scope="col">Nº</th>
           <th scope="col">Data da utilização</th>
           <th scope="col">Sala / Horas</th>
           <th  scope="col">Equipamentos</th>
         
                        
           
       </tr>
   </thead>
   <tbody>


<?php  


  $sql2 = "
  SELECT   r.id as rid,r.*,s.*
  from requisicao r, salas s
  where s.id=r.id_sala
  and s.id_escola=".$row1['id']." 
  and r.id<>$id
  and r.dataentrega is null  
  order by r.datautil,s.nome";
   
$result2 = mysqli_query($db,$sql2);


      

while($row2=mysqli_fetch_array($result2)) { 


?>
<tr>
<td width="5%"  scope="row"><?php echo $row2['rid']; echo('<br>');

?>

</td>
<td width="20%"  scope="row"><?php echo $row2['datautil']; echo('<br>');

?>

</td>


<td width="30%" >

<?php echo $row2['nome']; echo('<br>'); ?> 

<?php echo $row2['horainicio'];   

?>


-
<?php echo $row2['horafim'];   

?>
</td>


<td width=50%>
<?php

$idr=$row2['rid'];

//echo $idr;

$sql3 = "
select e.nomeequi 
from equip_requisitado er, equipamento e
where er.id_equip=e.id
and er.id_req=".$idr."
"; 
$result3 = mysqli_query($db,$sql3);

while($row3=mysqli_fetch_array($result3)) { 
    echo $row3['nomeequi'];
    echo ('  |  ');
}
?>
</td>



</tr>
<?php }          

?>



</tbody>
</table>     


<div class="text-center mt-3" ">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>myrequi">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                       <br> <br> 
                        </div>




                    </div>
                 
               </div>
         
            </div>

         </div>
      
      
      
      </div>
      <!-- end about -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

     
      <?php include ("footer.php");?>


 
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


     

   </body>
</html>

 
<script>
        $('#btnAvenger').click(function (e) {
            $('select').moveToList('#StaffList', '#PresenterList');
            e.preventDefault();
        });

        $('#btnRemoveAvenger').click(function (e) {
            $('select').removeSelected('#PresenterList');
            e.preventDefault();
        });

        $('#btnAvengerUp').click(function (e) {
            $('select').moveUpDown('#PresenterList', true, false);
            e.preventDefault();
        });

        $('#btnAvengerDown').click(function (e) {
            $('select').moveUpDown('#PresenterList', false, true);
            e.preventDefault();
        });

        $('#btnShield').click(function (e) {
            $('select').moveToList('#StaffList', '#ContactList');
            e.preventDefault();
        });

        $('#btnRemoveShield').click(function (e) {
            $('select').removeSelected('#ContactList');
            e.preventDefault();
        });

        $('#btnShieldUp').click(function (e) {
            $('select').moveUpDown('#ContactList', true, false);
            e.preventDefault();
        });

        $('#btnShieldDown').click(function (e) {
            $('select').moveUpDown('#ContactList', false, true);
            e.preventDefault();
        });

        $('#btnJusticeLeague').click(function (e) {
            $('select').moveToList('#StaffList', '#FacilitatorList');
            e.preventDefault();
        });

        $('#btnRemoveJusticeLeague').click(function (e) {
            $('select').removeSelected('#FacilitatorList');
            e.preventDefault();
        });

        $('#btnJusticeLeagueUp').click(function (e) {
            $('select').moveUpDown('#FacilitatorList', true, false);
            e.preventDefault();
        });

        $('#btnJusticeLeagueDown').click(function (e) {
            $('select').moveUpDown('#FacilitatorList', false, true);
            e.preventDefault();
        });
		
        $('#btnRight').click(function (e) {
            $('select').moveToListAndDelete('#lstBox1', '#lstBox2');
            e.preventDefault();
        });

        $('#btnAllRight').click(function (e) {
            $('select').moveAllToListAndDelete('#lstBox1', '#lstBox2');
            e.preventDefault();
        });

        $('#btnLeft').click(function (e) {
            $('select').moveToListAndDelete('#lstBox2', '#lstBox1');
            e.preventDefault();
        });

        $('#btnAllLeft').click(function (e) {
            $('select').moveAllToListAndDelete('#lstBox2', '#lstBox1');
            e.preventDefault();
        });
    </script>
