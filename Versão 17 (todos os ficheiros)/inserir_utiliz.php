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

<?php
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

<script>
var emailExiste = false;

function email_validation() {
    'use strict';
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var email_name  = document.getElementById("email");
    var email_value = email_name.value.trim();

    if (!email_value.match(mailformat) || email_value.length === 0) {
        document.getElementById('email_err').innerHTML = '<br>Email inválido.';
        document.getElementById('email_err').style.color = "#FF0000";
        emailExiste = false;
        return;
    }

    // Formato válido — verificar existência na BD via AJAX
    document.getElementById('email_err').innerHTML = '<br><span style="color:#888;">A verificar...</span>';
    emailExiste = false;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_email.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var resp = JSON.parse(xhr.responseText);
            if (resp.existe) {
                document.getElementById('email_err').innerHTML = '<br>&#10007; Email já registado no sistema.';
                document.getElementById('email_err').style.color = "#FF0000";
                emailExiste = true;
                email_name.focus();
            } else {
                document.getElementById('email_err').innerHTML = '<br>&#10003; Email válido e disponível';
                document.getElementById('email_err').style.color = "#00AF33";
                emailExiste = false;
            }
        }
    };
    xhr.send('email=' + encodeURIComponent(email_value));
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

// ── Controlo de acesso: apenas administradores ────────────────
if (!isset($_SESSION['tipo']) || (int)$_SESSION['tipo'] !== 1) {
    header('Location: ' . (defined('SVRURL') ? SVRURL : '/') . 'l');
    exit;
}
// ─────────────────────────────────────────────────────────────


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
                     <li style="color:#1e2a45;">Utilizadores >> Inserir</li>
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

<?php
$token=bin2hex(random_bytes(32));
$_SESSION['token']=$token;
?>

              
<?php
//$idescry=base64_encode($idescola);


?>

<div class="form-container">


   <form action = "<?php echo SVRURL ?>gravaus?x=<?php echo base64_encode(1)?>" method = "post" class="needs-validation" novalidate>

   <input type="hidden" name="token" value="<?php echo $token; ?>" >
<br>  
<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
<input style="width:100%" class="form-control required-field" type = "text"  required  name="nome" placeholder="Primeiro e último nome">
<br><br>

<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
      <input style="width:100%" class="form-control required-field" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
    type = "text" name = "email"  placeholder="Email"
    onBlur="email_validation();" id="email"><span id="email_err"></span>

                    
    <br>
    <label>Tipo: </label><br>
    <select style="width:100%" class="form-control required-field" name="tipo" required>
        <option value="1">1 – Administrador</option>
        <option value="2" selected>2 – Utilizador</option>
        <option value="3">3 – Reparador</option>
        <option value="4">4 – Funcionário</option>
    </select>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#ede8fc;color:#6f42c1;border:1.5px solid #6f42c1;">1 – Administrador</span>
        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0eeff;color:#00509e;border:1.5px solid #00509e;">2 – Utilizador</span>
        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0f5fb;color:#0891b2;border:1.5px solid #0891b2;">3 – Reparador</span>
        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0f7f0;color:#059669;border:1.5px solid #059669;">4 – Funcionário</span>
    </div>

    <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        Inserir utilizador
                                    </button>
                              
                           
                        </div>   
                 </form>
                 </div>

               


<a href="<?php echo SVRURL ?>utiliz">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br>


<br>
                
               
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
                        // Bloquear se email já existe
                        if (emailExiste) {
                            event.preventDefault();
                            event.stopPropagation();
                            document.getElementById('email_err').innerHTML = '<br>&#10007; Email já registado no sistema.';
                            document.getElementById('email_err').style.color = "#FF0000";
                            document.getElementById('email').focus();
                            form.classList.add('was-validated');
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