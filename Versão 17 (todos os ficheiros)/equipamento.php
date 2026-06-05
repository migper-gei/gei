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


     <?php include ("header.php");
  
     ?>
     


     <?php


include("sessao_timeout.php");

// Gerar token CSRF para o formulário "Inserir Outro Equipamento"
if (empty($_SESSION['csrf_token_outequip'])) {
    $_SESSION['csrf_token_outequip'] = bin2hex(random_bytes(32));
}
$csrf_token_outequip = $_SESSION['csrf_token_outequip'];

  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div >
      
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Equipamentos</li>
                  </ol>
               </nav>



               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

 <!-- Welcome Section -->
 <div class="welcome-section">
               
               <div>
             
                   <?php include("msg_bemvindo.php"); ?>
               </div>
      
       </div>
    




    
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





<script>


function a2(ne,es) {

  var es1,ne1;

es1=es;
ne1=ne;



 //alert(es1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar todos os equipamentos informáticos (todas as avarias serão eliminadas)?",
 text: "Instituição: "+es1+" ",
  type: "warning",
  showCancelButton: true,
  //confirmButtonColor: "#DD6B55",


  confirmButtonText: "Sim",
  cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
 
},
function(isConfirm){
  if (isConfirm) {
    
    
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaequiesc/'+ne1;
}, 10);


          
  } else {
    swal("Cancelado.");

 

  }

});

}
</script>






<?php
   $sql = "SELECT * FROM escolas ORDER by id";
   $result = mysqli_query($db,$sql);
   $rowcount = mysqli_num_rows($result);
   ?>


<div class="action-section">
    
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>






<form name="frme" id="frme" action = "" method = "post" >

   <div style="text-align: left;">


<select class="custom-select" title="Escolha a instituição" name="escola" onChange="showescola(this.value);">
   
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
$limit = 1;

$sql4 =  $db->prepare("select id from escolas limit ?");
$sql4->bind_param("i", $limit);
$sql4->execute();
$rows4 = $sql4->get_result()->fetch_row();

$nes = $rows4[0];



if (!empty($_POST["escola"])) {
              
   $esc=$_POST["escola"];
   
   }
   else{
    $esc=$nes;  //1;
   }


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
  
if ( $_SESSION['tipo']==1)
{
   
   

   $sql3 = "SELECT count(eq.id) FROM 
   equipamento eq, salas s
   WHERE eq.id_sala=s.id
   and s.id_escola=$esc";
 
   $result3 = mysqli_query($db,$sql3);

   $rows3 =mysqli_fetch_row($result3);


   $contaeq = $rows3[0];
   
   
   ?>
   <div style="display:flex;justify-content:flex-end;gap:8px;padding:10px 0;margin-bottom:8px;">

   <!-- Exportar CSV -->
   <a href="<?php echo SVRURL ?>equipamentos_csv.php?id=<?php echo base64_encode($esc);?>" target="_blank" title="Exportar para CSV"
      style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);transition:opacity .15s,transform .15s;">
       <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
           <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
           <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
       </svg>
       Exportar CSV
   </a>

   <!-- Exportar XLSX -->
   <a href="<?php echo SVRURL ?>equipamentos_xlsx.php?id=<?php echo base64_encode($esc);?>" target="_blank" title="Exportar para Excel (.xlsx) com formatação, filtros e múltiplas folhas"
      style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#1D6F42 !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(29,111,66,0.22);transition:opacity .15s,transform .15s;">
       <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
           <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
           <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
       </svg>
       Exportar Excel
   </a>


   <?php
if ($contaeq>0) {
?>
   &nbsp;&nbsp;&nbsp;

   <a onclick="a2('<?php echo $esc;?>','<?php echo $ne;?>')" href="<?php echo SVRURL ?>elimina_equi_esc.php?id=<?php echo base64_encode($esc);?>" target="_blank">
                    <button title="Eliminar todos os equipamentos informáticos da escola" type="submit" class="btn btn-danger-action">
                        <i class="fas fa-trash-alt btn-icon"></i> Eliminar Todos
                    </button>
                </a>

   <?php
}
?>


</div>




<div class="action-section">
<h2 class="section-title"><i class="fas fa-plus-circle btn-icon"></i> Adicionar Equipamentos</h2>

<div class="row">
                <div class="col-md-6 mb-3">
                    <form action="<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post">
                        <button type="submit" class="action-button btn-primary-action" style="width:100%;">
                            <i class="fas fa-laptop btn-icon"></i>
                            &nbsp; Inserir Equipamento Informático
                        </button>
                    </form>
                </div>

                <div class="col-md-6 mb-3">
                    <a href="<?php echo SVRURL ?>inserir_equipamento_massa.php?ies=<?php echo base64_encode($esc) ?>" style="display:block;width:100%;">
                        <button type="button" class="action-button btn-primary-action" style="width:100%;font-size:unset;">
                            <i class="fas fa-layer-group btn-icon"></i> &nbsp;Inserção em Massa
                        </button>
                    </a>
                </div>
</div>

<div class="row">
                <div class="col-md-6 mb-3">
                    <form action="<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post">
                        <input type="hidden" name="csrf_token_outequip" value="<?php echo htmlspecialchars($csrf_token_outequip, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="action-button btn-primary-action" style="width:100%;">
                            <i class="fas fa-plug btn-icon"></i> &nbsp;Inserir Outro Equipamento
                        </button>
                    </form>
                </div>
</div>

</div>

<?php
}
?>


<?php



$sqla =  $db->prepare("SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id 
and e.id=?
");

$sqla->bind_param("i", $esc);
$sqla->execute();
$resulta = $sqla-> get_result();
//$resulta = mysqli_query($db,$sqla);
?>


<div class="row">
    <div class="<?php echo ($_SESSION['tipo'] == 1) ? 'col-md-6' : 'col-md-12'; ?> mb-3">
        <div class="action-section" style="height:100%;">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar Equipamentos</h2>
            
            <?php
            $sqla = $db->prepare("SELECT DISTINCT(s.nome) as no, s.id as sid
                FROM escolas e, salas s, equipamento eq
                WHERE e.id=s.id_escola AND eq.id_sala=s.id 
                AND e.id=?");
            
            $sqla->bind_param("i", $esc);
            $sqla->execute();
            $resulta = $sqla->get_result();
            $rowcount = mysqli_num_rows($resulta);
            ?>
            
            <form action="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc)?>" method="post" class="needs-validation" novalidate>
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label for="salaSelect" class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a Sala:</label>
                        <select id="salaSelect" class="form-control required-field" name="sala" required>
                            <?php
                            if ($rowcount > 0) {
                                while($rowa = mysqli_fetch_array($resulta)) {
                                    echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');
                                }
                            } else {
                                echo('<option value="">Sem salas disponíveis</option>');
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-1">
                        <button type="submit" class="action-button btn-secondary-action" style="font-size:0.78rem;white-space:nowrap;padding-left:8px;padding-right:8px;">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($_SESSION['tipo'] == 1) { ?>
    <div class="col-md-6 mb-3">
        <div class="action-section" style="height:100%;">
            <h2 class="section-title"><i class="fas fa-tags btn-icon"></i> QR Codes</h2>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <form action="<?php echo SVRURL ?>criar_qr_equipamentos.php" method="post" target="_new" class="needs-validation" novalidate>
                        <button type="submit" class="action-button btn-outline-action" style="width:100%;">
                            <i class="fas fa-qrcode btn-icon"></i>&nbsp;Criar QR Codes dos Equipamentos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>



 
        <?php if ($_SESSION['tipo'] != 4) { ?>
        <!-- Requisitions -->
        <div class="action-section">
            <h2 class="section-title"><i class="fas fa-clipboard-list btn-icon"></i> Requisições</h2>
            
            <?php
            $date = date("Y-m-d");
            $mod_date = strtotime($date."+ 1 days");
            ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <form name="requisi" action="<?php echo SVRURL ?>calendario_reservas.php" method="post">
                    <!--   <form name="requisi" action="<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(0) ?>&&rei=<?php echo base64_encode($esc) ?>" method="post">
                   
                    <div class="form-group">
                            <label><i class="far fa-calendar-alt btn-icon"></i> Data de Requisição:</label>
                            <input value="<?php echo date("Y-m-d", $mod_date); ?>" class="date-input" required type="date" name="datareq">
                        </div>
-->
                        <button onclick="return clickMe3();" type="submit" class="action-button btn-primary-action">
                            <i class="fas fa-plus-circle btn-icon"></i> &nbsp;Nova Requisição
                        </button>
                    </form>
              
                    </div>
                    
                    <div class="col-md-6 mb-3">
           
                    <form action="<?php echo SVRURL ?>myrequi" method="post">
                       
                        <button type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-history btn-icon"></i>&nbsp; Minhas Requisições
                        </button>
                    </form>
            </div>
            </div>
        </div>
        <?php } ?>




</div>
             
            </div>
         </div>
      </div>

 
      <!-- end about -->
    
      <?php include ("jquery_bootstrap.php");?>

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