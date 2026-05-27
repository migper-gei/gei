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

// ── Proteção CSRF ──────────────────────────────────────────────────────────────
// Gerar token uma vez por sessão (rotação a cada 30 minutos)
if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) ||
    (time() - $_SESSION['csrf_token_time']) > 1800) {
    $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Validar token nos pedidos POST que chegam a esta página (form "frm" e "escola")
// Os outros formulários POST submetem para páginas distintas; a validação CSRF
// dessas páginas deve ser feita nas próprias páginas de destino.
// Aqui validamos apenas o form "frm" (seleção de escola) que faz POST para "".
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['escola'])) {
    if (empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Pedido inválido: token CSRF em falta ou incorreto.');
    }
}
// ──────────────────────────────────────────────────────────────────────────────
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
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Manutenções</li>
                  </ol>
               </nav>
               
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

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

<div style="text-align: left;">





<select  style="width:100%;" name="escola" onChange="showesc(this.value);"  class="custom-select">


<?php



$sql2 = "SELECT * FROM escolas ORDER by id";
$result2 = mysqli_query($db,$sql2);

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
?>




<?php
 

 $esc_int = (int)$esc;
 $sql1 = "select nome_escola from escolas where id=$esc_int";
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

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">




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





<form name="manutencoes" action="<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo urlencode(base64_encode(0))?>&amp;esm=<?php echo urlencode(base64_encode($esc));?>" method="post" class="needs-validation" novalidate>

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

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


<form name="manutencoes2" action="<?php echo SVRURL ?>manutencoes_equip_entredatas.php?x=<?php echo urlencode(base64_encode(0))?>&amp;esm=<?php echo urlencode(base64_encode($esc));?>" method="post" class="needs-validation" novalidate>

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

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