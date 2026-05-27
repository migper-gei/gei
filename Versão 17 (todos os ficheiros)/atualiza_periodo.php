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


     <?php include ("header.php");?>
     


     <?php
//session_start();

include ("css_inserir.php");

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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                             <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>

                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">
                <a href="<?php echo SVRURL ?>peri" style="color:#4b6cb7;text-decoration:none;">Periodos</a>
  
               
                        
                     >> Atualizar</li>
                  </ol>
               </nav>
               <div class="titlepage">
                  
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




<script>
$(function() {
    $( "#calendario" ).datepicker({
       
      changeMonth: true,
      changeYear: true,
      
      showOtherMonths: true,
        selectOtherMonths: true,
        showAnim: "slide",
        showOn: "button",
        buttonImage: "<?php echo SVRURL ?>images/calendario_datapicker.png",
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
    });


    $( "#calendario2" ).datepicker({
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
        selectOtherMonths: true,
      showAnim: "slide",
        showOn: "button",
        buttonImage: "<?php echo SVRURL ?>images/calendario_datapicker.png",
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
        //monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
    });
});
</script>

<?php
if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = 'periodos';
}, 10);
</script>
<?php
    exit;
}


    ?>


<?php

// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CORRIGIDO: uso de prepared statement em vez de concatenação directa
$stmt_sel = $db->prepare("SELECT * FROM periodos WHERE id = ?");
$stmt_sel->bind_param("i", $id_param);
$id_param = (int)base64_decode($url[0]);
$stmt_sel->bind_param("i", $id_param);
$stmt_sel->execute();
$result = $stmt_sel->get_result();
$row = mysqli_fetch_array($result);


if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>periodos';
}, 10);
</script>

<?php
}
else
{ 
?>




<script language="JavaScript" >
function validardatas(){

//alert("aaaa");

var di= document.getElementsByName("datai")[0].value;
var df= document.getElementsByName("dataf")[0].value;

//alert (di);
//alert (df);


var di2=new Date(di);
//alert (di2);
var df2=new Date(df);
//alert (df2);

//alert(di2- df2); 



if((di2-df2)>0 )
{
      //alert( "A data de fim deve ser igual ou superior à data de inicio." );
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
   //alert( "ccc");
      return true;

  }

}

</script>

<div class="form-container">


<form  onSubmit="return validardatas();" action = "<?php echo SVRURL ?>atualiza_ok_periodo.php?pi=<?php echo base64_encode($row['id']);?>" method = "post"
class="needs-validation" novalidate >
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <label>Ano: </label>  <br>  
                    <input style="width:100%" class="form-control required-field" placeholder="Ano"  size=10 type = "text" name="anoletivo"  required value="<?php echo htmlspecialchars($row['ano_lectivo'], ENT_QUOTES, 'UTF-8'); ?>"/><br /><br />
                    
                    <!--pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
                    <select name="anoletivo">
                            <option value="<php echo htmlspecialchars($row['ano_lectivo'], ENT_QUOTES, 'UTF-8'); ?><" selected><?php echo htmlspecialchars($row['ano_lectivo'], ENT_QUOTES, 'UTF-8'); ?></option>
                            
                        </select>
                    -->
                   
                     <label>Período: </label>  <br>  

                     <select name="periodo" class="form-control required-field">
                            <?php 
                              if ($row['num_periodo']==1)
                              {
                            ?>
                            <option value="1" selected>1º</option>
                            <option value="2">2º</option>
                            <option value="3">3º</option>
                            <?php 
                              }
                            elseif ($row['num_periodo']==2)
                            {
                                // CORRIGIDO: value="1" em vez de value="2" na opção 1º
                                ?>
                                <option value="2" selected>2º</option>
                                <option value="1">1º</option>
                                <option value="3">3º</option>
                            <?php
                            }
                            elseif ($row['num_periodo']==3)
                            {
                                // CORRIGIDO: value="1" e value="2" corrigidos (ambos tinham value errado)
                            ?>
                            <option value="3" selected>3º</option>
                            <option value="1">1º</option>
                            <option value="2">2º</option>
                            <?php
                            }
                            ?>




                        </select>

                   <br />
                    <br>
                    <label>Data Início: </label>  <br>  
                       
                    <input class="form-control required-field" value="<?php echo htmlspecialchars($row['data_inicio'], ENT_QUOTES, 'UTF-8'); ?>"  required size="10" type = "date" name="datai" >          
                
                    <br> <br> 
                    <label>Data Fim: </label>  <br>  
                
                    <input class="form-control required-field"
                   
                    value="<?php echo htmlspecialchars($row['data_fim'], ENT_QUOTES, 'UTF-8'); ?>"  required   size="10" type = "date" name="dataf" >          
                
                    <br />
                    <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar período
                                    </button>
                                </div>
                 </form>


</div>

<a href="<?php echo SVRURL ?>peri" title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br>
<?php
}
?>
<br>   
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
