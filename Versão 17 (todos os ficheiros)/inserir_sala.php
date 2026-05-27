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
// Gerar token CSRF se ainda não existir (fora do if, cobre sessões já iniciadas)
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

<script>
var salaExiste = false;
var _checkSalaTimer = null;

function verificaSalaDuplicada() {
    var nome   = document.getElementById('nome_sala').value.trim();
    var errDiv = document.getElementById('sala_err');
    var idesc  = document.getElementById('idescola_hidden').value;

    if (!nome) {
        errDiv.innerHTML = '';
        salaExiste = false;
        clearTimeout(_checkSalaTimer);
        return;
    }

    clearTimeout(_checkSalaTimer);
    _checkSalaTimer = setTimeout(function() {
        errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar...</small>';
        salaExiste = false;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_sala.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.existe) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; Já existe uma sala com este nome nesta instituição.</small>';
                        salaExiste = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Disponível</small>';
                        salaExiste = false;
                    }
                } catch(e) { errDiv.innerHTML = ''; }
            } else { errDiv.innerHTML = ''; }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; salaExiste = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; salaExiste = false; };
        xhr.send('nome=' + encodeURIComponent(nome) + '&idescola=' + encodeURIComponent(idesc));
    }, 400);
}
</script>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php");?>
     

     <?php
//session_start();
include ("css_inserir.php");

include("sessao_timeout.php");

 

$stmt_max = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt_max->execute();
$rows2a = $stmt_max->get_result()->fetch_row();
$stmt_max->close();
$maxesc = $rows2a[0];

$idescola = (int)base64_decode($_GET['ie']);

if ((int)base64_decode($_GET['ie']) > $maxesc || !is_numeric(base64_decode($_GET['ie']))) {

?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>salas.php?x=0';
          },40);
          </script>

<?php
}

$stmt_ne = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt_ne->bind_param("i", $idescola);
$stmt_ne->execute();
$ne = $stmt_ne->get_result()->fetch_row()[0] ?? '';
$stmt_ne->close();
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
                        
                        <span style="color:#4b6cb7;">Configurações</span>



                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Salas >> Inserir</li>
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



               <!-- ========================================================
                    CABECALHO: sala + escola (abaixo do utilizador)
                    ======================================================== -->
<div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
   <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.05rem; font-weight:700; color:#182848;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
           style="flex-shrink:0;">
         <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
         <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
   </span>
</div>
               <!-- ===== FIM CABECALHO ===== -->

<div class="form-container">
                      
<div class="step-indicator">
                  

<i class="fas fa-info-circle mr-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>

   <form action = "<?php echo SVRURL ?>gravasala?ie=<?php echo base64_encode($idescola)?>" method = "post" class="needs-validation" novalidate>

   <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
<br>  
                    <label>Nome da sala: </label><br>
                    <input class="form-control required-field" type="text" name="nome" id="nome_sala"
                           required style="width:100%;"
                           onBlur="verificaSalaDuplicada()" onInput="verificaSalaDuplicada()">
                    <input type="hidden" id="idescola_hidden" value="<?php echo $idescola; ?>">
                    <div id="sala_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>
                    <br>
                 
                     <label>Localização: </label>  <br>  
                    <input  class="form-control required-field"type = "text" name ="localizacao"  required style="width:100%;"/><br /><br />
                   
                    <label>Departamento / Grupo / Serviço: </label>  <br>  
                    <input required class="form-control required-field" style="width:100%;" type = "text" name ="departamento"  /><br /><br />
                    
                    <label>Equipamento requisitável: </label>                

                    <select required name="eqreq" required style="width: 100%;
            height: 35px; 
                       "
                      class="form-control required-field">
            
<?php
      echo('<option selected value="">-- Selecione --</option>');
      echo('<option value="Sim">Sim</option>');
      echo('<option  value="Não">Não</option>');
   
?>     
</select>

<br><br>
                    
                    <div  style=" text-align:center;width:100%"> 
                     
                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir sala
                                    </button>
    </div>
                 </form>
</div>
                




<a href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola) ?>">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>



<br><br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
<!-- Script para validação do formulário -->
<script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (salaExiste) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.getElementById('sala_err').innerHTML =
                            '<small style="color:#dc3545;font-weight:600;">&#10007; Já existe uma sala com este nome nesta instituição.</small>';
                        document.getElementById('nome_sala').focus();
                        return;
                    }
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

         <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>