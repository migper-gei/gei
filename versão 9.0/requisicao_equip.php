<!DOCTYPE html>
<html lang="pt">
   <head>

<?php
 include ("head.php");
?>






   </head>


   <script src="<?php echo SVRURL ?>js/jquery1102.js"></script>

  <script src="<?php echo SVRURL ?>js/jqueryselectlistactions.js"></script>
  

  <link rel="stylesheet" href="<?php echo SVRURL ?>css/listboxs.css">


   <!-- body   
<script src="lbox/bootstrap.js"></script>

  <link rel="stylesheet" href="lbox/bootstrap.css">
-->
   <body class="main-layout">
      <!-- loader
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>  -->
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php

include("sessao_timeout.php");

 
  ?>


    



<?php

if ($_GET["x"]==0)
{
    $dr=$_POST['datareq'];

}


elseif ($_GET["x"]==1)
{
$dr=$_GET["dr"];
}




$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if ($_GET["escola"]>$maxesc)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },40);
          </script>


<?php
}


$idescola=$_GET["escola"];

//echo $idescola;


$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];


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
   //alert(df);
   var outradata = new Date();
   outradata.setDate(di.getDate() + 1);
   //alert (outradata);

   var hi1 = (document.forms.req.elements.horainicio.value);
   var hf1 = (document.forms.req.elements.horafim.value);
 // alert (hi1);
   //alert (hf1);






var lbox2=document.getElementById("lstBox2");
//alert(lbox2.length);


/*

var ddlArray= new Array();
for (i = 0; i < lbox2.options.length; i++) {
   ddlArray[i] = lbox2 .options[i].value;
   alert( ddlArray[i]);
   }

*/

/*
if ( di >= df  )
				{
			alert(" A data de utilização deve ser superior à data da requisição em pelo menos 1 dia");
      return false;
		}
*/

if (hi1>hf1)
{
	//alert(" A hora de fim deve ser superior à hora de inicio.");
      
    event.preventDefault(); // prevent form submit

swal({

title: "A hora de fim deve ser superior à hora de inicio!",
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



      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Equipamentos >> Requisição de equipamento
                        <br><?php echo $ne?> | 
                        <?php echo date('d/m/Y',strtotime($dr)); ?>
                     </h2>
                  </div>

               </div>
            </div>
 

            <div class="container">
               <div class="row">
                  <div class="col-md-9 offset-md-2">
              
                        

                  <?php
include("msg_bemvindo.php");
?>
<br>



      <form onSubmit="return verificadados();" name="req" 
      action="<?php echo SVRURL ?>grava_requisicao.php?id=<?php echo $idescola?>&&dr=<?php echo $dr ;?>" method="post">

      <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
      Ver tabela em baixo com requisições já efetuadas para o dia pretendido.<br>
       
      <!--A data da utilização deve ser superior em pelo menos um 1 dia em relação à data da requisição.-->


                   

                   <br>  <br>
                   <!--onSubmit="return verificadados();"
                    <label>Data Atual: </label>  
                    <?php echo date("Y-m-d"); ?>           
                   
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; -->
                    
                    <label>Data da requisição: </label>
                    <input   readonly  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "datareq" >
                  
                 <?php
                  // $date = date("Y-m-d");
                   //$mod_date = strtotime($date."+ 1 days");

                    
                 ?>
                  
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp;
                    <label>Data da utilização: </label>  
                    <!--  onChange="return verificadata();"
                date("Y-m-d",$mod_date)
                -->
                    <input    readonly             
                     required  
                    value="<?php echo $dr;?>"            
                    size="10" type = "date" name = "datautil" >


                   <br> <br>
                   <label>Hora de inicio: </label>  
                    <input style="background-color:#CEF6CE" required  
                     size="10" type = "time" name = "horainicio" >
                    &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; 
               
                    &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; 
                   <label>Hora de fim: </label>  
                    <input style="background-color:#CEF6CE" required            
                    size="10" type = "time" name = "horafim" >


                   <br />
                   <br>
                  
      <label>Sala: </label>  
      
      <?php


$sql = "SELECT DISTINCT(nome) as no,id 
FROM salas
where id_escola=$idescola and equip_requisitavel='Não'
order by nome";

$result = mysqli_query($db,$sql);


//echo('<select name="sala">');
?>


<select required style="background-color:#CEF6CE"  name="sala"  >

<?php

//echo('<option value=""> Escolha a sala   </option>');  

while($row=mysqli_fetch_array($result))
{




echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

}

echo('</select>');
?>    
                  

<br><br>



             
       




<?php

$sql = "SELECT e.id, e.nomeequi
FROM equipamento e, salas s
where e.id_sala=s.id
and s.equip_requisitavel='Sim'
ORDER by e.tipo,e.nomeequi";
$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);


//echo ($rowcount);

?>



<?php
$sql1a = "
SELECT DISTINCT(er.id_equip)
  from requisicao r, salas s, equip_requisitado er
  where s.id=r.id_sala and r.id=er.id_req
  and s.id_escola=$idescola
  and r.datautil=STR_TO_DATE('".$dr."','%Y-%m-%d')
  and r.dataentrega is null
order by er.id_equip";
$result1a = mysqli_query($db,$sql1a);

$i=0;
$arrid=array();
while($row1a=mysqli_fetch_array($result1a))
{
$arrid[$i]=$row1a['id_equip'];
$i=$i+1;
}



//foreach ($arrid as $var_listar)
//{
    //echo $var_listar;
   // echo (' ');
    //echo count($arrid);
//}


?>




   <div class="row style-select">
      <div class="col-md-12">


         <div class="subject-info-box-1">
            <label>Equipamentos disponíveis: <br>(Cor laranja: já em requisições do dia)</label>
            
                 
<select size="10" multiple class="form-control" id="lstBox1" name="eqdisp[]">


<?php



while($row3=mysqli_fetch_array($result))
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
            
  

                 <select style="background-color:#CEF6CE" size="10" multiple  class="form-control" id="lstBox2" name="eqrequi[]">
             
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
        Após clicar no botão "Requisitar" é verificado se os equipamento ainda estão disponíveis para a data, horas e sala. Caso um dos equipamentos não esteja disponível, a requisição não é feita.

     <br>
     <br>  
     <div style="  text-align: center;">   
  <input type="submit" value="Requisitar" >
  </div>
</form>





Requisições já efetuadas:
<table class="table table-sm" >
   <thead>
       <tr >
          <th scope="col">Nº</th>
          <th scope="col">Data da requisição</th>
           <th scope="col">Sala / Horas</th>
           <th  scope="col">Equipamentos</th>
         
                        
           
       </tr>
   </thead>
   <tbody>


<?php  
echo date('d/m/Y',strtotime($dr));

  $sql2 = "
  SELECT   r.id as rid,r.*,s.*
  from requisicao r, salas s
  where s.id=r.id_sala
  and s.id_escola=$idescola 
  and r.datautil=STR_TO_DATE('".$dr."','%Y-%m-%d')
  and r.dataentrega is null  
  order by r.datarequi,s.nome,r.horainicio";
   
$result2 = mysqli_query($db,$sql2);


      

while($row2=mysqli_fetch_array($result2)) { 


?>
<tr>
<td width="5%"  scope="row"><?php echo $row2['rid']; echo('<br>');

?>


</td>
<td width="20%"  scope="row"><?php echo date('d/m/Y',strtotime($row2['datarequi'])); echo('<br>');

?>

</td>


<td width="30%" >

<?php echo $row2['nome'];  echo('<br>');?> 

<?php echo $row2['horainicio'];   

?>


-
<?php echo $row2['horafim'];   

?>
</td>


<td >
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


             <a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>


                    </div>
                 
               </div>
         
            </div>

         </div>
      
 
      
      </div>
      <!-- end about -->
  
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">


     
      <?php include ("footer.php");?>




     

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
