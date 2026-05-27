<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
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
      <?php include("loader.php"); ?>


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

   $id = (int)base64_decode($_GET['ri']);



   if ( !isset($id)    || empty($id)     || !is_numeric($id) 
   )
   
   {
   ?>
   
   
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>myrequi';
   }, 10);
   </script>
   
   
   <?php
   }



   $em=$_SESSION['email'];



   $stmt0 = $db->prepare("SELECT COUNT(*) AS c FROM requisicao WHERE id = ? AND email_util = ?");
   $stmt0->bind_param("is", $id, $em);
   $stmt0->execute();
   $result0 = $stmt0->get_result();
   $rows0 = $result0->fetch_row();
   $stmt0->close();
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


      $stmt11 = $db->prepare("SELECT r.*, s.nome, es.nome_escola, es.id FROM requisicao r, salas s, escolas es WHERE r.id_sala = s.id AND s.id_escola = es.id AND r.id = ?");
      $stmt11->bind_param("i", $id);
      $stmt11->execute();
      $result11 = $stmt11->get_result();
      $row1 = $result11->fetch_array();
      $stmt11->close();
    ?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Minhas requisições >> Atualizar</li>
                  </ol>
               </nav>
               <div class="titlepage">
                     <h2>Requisição nº <?php echo $id; ?>
                     <br> <?php echo htmlspecialchars($row1['nome_escola'], ENT_QUOTES, 'UTF-8'); ?>
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
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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
                    <input style="width:100%"  class="form-select" readonly  value="<?php echo htmlspecialchars($row1['datarequi'], ENT_QUOTES, 'UTF-8'); ?>"            
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
                    value="<?php echo htmlspecialchars($row1['datautil'], ENT_QUOTES, 'UTF-8'); ?>"            
                    size="10" type = "date" name = "datautil" >
               
  </div></div>
                   <br> 
                 
                   <label>Hora de inicio: </label>  
                    <input class="form-control required-field" required  
                    value="<?php echo htmlspecialchars($row1['horainicio'], ENT_QUOTES, 'UTF-8'); ?>" 
                    size="10" type = "time" name = "horainicio" >
                 <br>
                   <label>Hora de fim: </label>  
                    <input class="form-control required-field" required   
                    value="<?php echo htmlspecialchars($row1['horafim'], ENT_QUOTES, 'UTF-8'); ?>"          
                    size="10" type = "time" name = "horafim" >


                   <br />
                   <br>
                  
      <label>Sala: </label>  
      
      <?php


$idescola_row1 = (int)$row1['id'];
$stmt_salas = $db->prepare("SELECT DISTINCT(nome) AS no, id FROM salas WHERE id_escola = ? AND equip_requisitavel = 'Não' ORDER BY nome");
$stmt_salas->bind_param("i", $idescola_row1);
$stmt_salas->execute();
$result = $stmt_salas->get_result();
$stmt_salas->close();
?>


<select required class="form-control required-field" name="sala"  >

<?php

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

$stmt_eq0 = $db->prepare("SELECT DISTINCT(eq.nomeequi), eq.id FROM requisicao r, equip_requisitado er, equipamento eq, salas s WHERE r.id = er.id_req AND er.id_equip = eq.id AND r.id_sala = s.id AND s.id_escola = ? AND r.id <> ? AND r.dataentrega IS NULL ORDER BY eq.id");
$stmt_eq0->bind_param("ii", $idescola_row1, $id);
$stmt_eq0->execute();
$result0 = $stmt_eq0->get_result();
$stmt_eq0->close();


$i=0;
$arrid=array();
while($row1aa=$result0->fetch_array())
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



$stmt_eq = $db->prepare("SELECT e.id, e.nomeequi FROM equipamento e, salas s WHERE e.id_sala = s.id AND s.id_escola = ? AND s.equip_requisitavel = 'Sim' AND e.id NOT IN (SELECT er.id_equip FROM requisicao r, equip_requisitado er WHERE er.id_req = ? AND r.id = er.id_req) ORDER BY e.tipo, e.nomeequi");
$stmt_eq->bind_param("ii", $idescola_row1, $id);
$stmt_eq->execute();
$result = $stmt_eq->get_result();
$stmt_eq->close();
$rowcount = $result->num_rows;


?>




   <div class="row style-select">
      <div class="col-md-12">


         <div class="subject-info-box-1">
            <label>Equipamentos disponíveis:<br>(Cor laranja: já em requisições)</label>
            
                 
                 <select size="10" multiple class="form-control" id="lstBox1" name="eqdisp[]">


                 <?php


while($row3=$result->fetch_array())
{


    $stmt1 = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes ar, equipamento eq, salas s WHERE ar.id_equi = eq.id AND s.id = ar.id_sala AND s.id_escola = ? AND eq.id = ? AND datareparacao IS NULL");
    $stmt1->bind_param("ii", $idescola_row1, $row3['id']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $rows = $result1->fetch_row();
    $stmt1->close();

  if ($rows[0]==0)
{




   $e=0;
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
             $stmt4 = $db->prepare("SELECT er.*, e.id, e.nomeequi FROM requisicao r, equip_requisitado er, equipamento e WHERE r.id = er.id_req AND er.id_equip = e.id AND r.id = ?");
             $stmt4->bind_param("i", $id);
             $stmt4->execute();
$result4 = $stmt4->get_result();
$stmt4->close();
?>


                 <select class="form-control required-field"  size="10" multiple required  id="lstBox2" name="eqrequi[]">
             
                 <?php



while($row4=$result4->fetch_array())

{

$e=0;
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


  $stmt2 = $db->prepare("SELECT r.id AS rid, r.*, s.* FROM requisicao r, salas s WHERE s.id = r.id_sala AND s.id_escola = ? AND r.id <> ? AND r.dataentrega IS NULL ORDER BY r.datautil, s.nome");
  $stmt2->bind_param("ii", $idescola_row1, $id);
  $stmt2->execute();
$result2 = $stmt2->get_result();
$stmt2->close();


      

while($row2=$result2->fetch_array()) { 


?>
<tr>
<td width="5%"  scope="row"><?php echo htmlspecialchars($row2['rid'], ENT_QUOTES, 'UTF-8'); echo('<br>');

?>

</td>
<td width="20%"  scope="row"><?php echo htmlspecialchars($row2['datautil'], ENT_QUOTES, 'UTF-8'); echo('<br>');

?>

</td>


<td width="30%" >

<?php echo htmlspecialchars($row2['nome'], ENT_QUOTES, 'UTF-8'); echo('<br>'); ?> 

<?php echo htmlspecialchars($row2['horainicio'], ENT_QUOTES, 'UTF-8');   

?>


-
<?php echo htmlspecialchars($row2['horafim'], ENT_QUOTES, 'UTF-8');   

?>
</td>


<td width=50%>
<?php

$idr=$row2['rid'];

$stmt3 = $db->prepare("SELECT e.nomeequi FROM equip_requisitado er, equipamento e WHERE er.id_equip = e.id AND er.id_req = ?");
$stmt3->bind_param("i", $idr);
$stmt3->execute();
$result3 = $stmt3->get_result();
$stmt3->close();

while($row3=$result3->fetch_array()) { 
    echo htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8');
    echo ('  |  ');
}
?>
</td>



</tr>
<?php }          

?>



</tbody>
</table>     




                        <a href="<?php echo SVRURL ?>myrequi">
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
