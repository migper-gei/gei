<?php
// SEGURANÇA: display_errors desativado em produção
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

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


<?php include ("head.php"); ?>

   <!-- Scripts de validação: colocados após head.php pois SVRURL só fica disponível após svrurl.php -->
   <script>
// Validação de formato do email (client-side)
function email_validation(){
   'use strict';
   var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
   var email_value = document.getElementById("email").value;
   if (!email_value.match(mailformat) || email_value.length === 0) {
      document.getElementById('email_err').innerHTML = '<br>Email inválido.<br>';
      document.getElementById('email_err').style.color = "#FF0000";
   } else {
      // Formato OK — verificar no servidor
      verificar_email();
   }
}

// Verificação do email no servidor
function verificar_email(){
   var email  = document.getElementById("email").value;
   var codigo = document.getElementById("cod") ? document.getElementById("cod").value : "";
   var csrf   = document.querySelector('[name=csrf_token]') ? document.querySelector('[name=csrf_token]').value : "";

   var xhr = new XMLHttpRequest();
   xhr.open("POST", "<?php echo SVRURL ?>check_email_public.php", true);
   xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
         var resp = JSON.parse(xhr.responseText);
         var el   = document.getElementById('email_err');
         if (resp.existe) {
            el.innerHTML   = "<br>Email válido.<br>";
            el.style.color = "#00AF33";
         } else {
            el.innerHTML   = "";
         }
      }
   };
   xhr.send(
      "email="       + encodeURIComponent(email)  +
      "&codigo="     + encodeURIComponent(codigo) +
      "&csrf_token=" + encodeURIComponent(csrf)
   );
}

// Verificação do código no servidor
function verificar_codigo(){
   'use strict';
   var cod     = document.getElementById("cod").value;
   var numbers = /^[0-9]+$/;
   var csrf    = document.querySelector('[name=csrf_token]') ? document.querySelector('[name=csrf_token]').value : "";

   if (!cod.match(numbers) || cod.length < 1 || cod.length > 9) {
      document.getElementById('cod_err').innerHTML   = "<br>Código inválido.";
      document.getElementById('cod_err').style.color = "#FF0000";
      return;
   }

   var xhr = new XMLHttpRequest();
   xhr.open("POST", "<?php echo SVRURL ?>verifica_codigo.php", true);
   xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
         var resp = JSON.parse(xhr.responseText);
         var el   = document.getElementById('cod_err');
         if (resp.existe) {
            el.innerHTML   = "<br>Código válido.";
            el.style.color = "#00AF33";
         } else {
            el.innerHTML   = "";
         }
      }
   };
   xhr.send("codigo=" + encodeURIComponent(cod) + "&csrf_token=" + encodeURIComponent(csrf));
}
</script>




<?php

if (isset($_GET['url']) && is_numeric(base64_decode($_GET['url'])))
{
    $url    = explode('/', $_GET['url']);
    $rsenha = base64_decode($url[0]);
}
else
{
    ?>
    <script>window.location.href = '<?php echo SVRURL ?>l';</script>
    <?php
    exit;
}

if (!isset($url[0]) || base64_decode($url[0]) > 1 || base64_decode($url[0]) < 0)
{
    ?>
    <script>window.location.href = '<?php echo SVRURL ?>l';</script>
    <?php
    exit;
}
?>



<?php

?>

   <style>
      /* ══ DARK MODE — recupera_reset_pass.php ══ */
      :root,
      [data-theme="light"] {
         --bg:      #f0f4fb;
         --surface: #ffffff;
         --border:  #e3e8f4;
         --text:    #1e2a45;
         --muted:   #7b88a0;
      }

      [data-theme="dark"] {
         --bg:      #0f1117;
         --surface: #1a1d27;
         --border:  #2a2f45;
         --text:    #e8eaf0;
         --muted:   #8b95b0;
      }

      /* Transição suave após load */
      .gei-theme-ready * {
         transition: background-color .25s ease, color .2s ease, border-color .2s ease, box-shadow .2s ease !important;
      }

      /* Fundo geral */
      html, body, body.main-layout, .about,
      .wrapper, #wrapper, #page-content-wrapper {
         background: var(--bg);
         background-image: none;
      }

      [data-theme="dark"] body,
      [data-theme="dark"] .about {
         color: var(--text);
      }

      /* Títulos */
      [data-theme="dark"] .titlepage h2 {
         color: var(--text);
      }

      /* Caixa do formulário */
      [data-theme="dark"] #formContent {
         background: var(--surface);
         color: var(--text);
         box-shadow: 0 6px 24px rgba(0,0,0,.45);
      }

      /* Inputs */
      [data-theme="dark"] #formContent input[type="text"],
      [data-theme="dark"] #formContent input[type="password"] {
         background: #0f1117;
         color: var(--text);
         border-color: var(--border);
      }

      [data-theme="dark"] #formContent input[type="text"]::placeholder,
      [data-theme="dark"] #formContent input[type="password"]::placeholder {
         color: var(--muted);
      }

      /* Caixa de info azul claro */
      [data-theme="dark"] #formContent > div[style] {
         background: #1e2a45 !important;
         border-color: #2a3a5a !important;
         color: #a8c0f0 !important;
      }
   </style>

   </head>


   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


      <?php include ("header2.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                  
                  <div style="  text-align: right;">              
                  
                <!--
                  <a href="l">

<button type="button" class="btn btn-outline-primary">Login</button>
</a>

-->

                  <?php
if ($rsenha==0)
{
   ?>
   <!--
               <a  class="underlineHover" href="l" title="Login/Registo" style="color:blue;">Login</a>
-->
  
    


              <?php
}
           ?> 
               </div>


<?php
if ($rsenha==0)

{

?>
         <h2> Esqueci-me da password </h2>

 <?php
}
elseif ($rsenha==1) 
{
?>
    <h2> Mudar password </h2>
   
<?php

}
?>


                  </div>
                  
               </div>

     

            </div>
            
            <div class="container">
               <div class="row">
      
<div class="wrapper fadeInDown">




  <div id="formContent">




  <?php
if ($rsenha==0)

{

?>
    <!-- Login Form -->
    <form action = "<?php echo SVRURL ?>recupera_pass_OK.php" method = "post">

      <!-- Token CSRF -->
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-key" aria-hidden="true"></i>
						</span>
  
    <input title="Código" required type="text" name="codigo" id="cod" class="fadeIn third" placeholder="Código"
       oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="9"
       onBlur="verificar_codigo();" /><span id="cod_err"></span>
    <br>
    <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>

    
      <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
					type="text" name="email" class="fadeIn second" placeholder="Email"
               onBlur="email_validation();" id="email"><span id="email_err"></span>
 <br><br>
      <input title="Enviar nova password" id="btnEnviar" type="submit" class="fadeIn fourth"
             value="Enviar nova password" />
  
      <br>
</form>
     

       <div style="display:inline-flex;align-items:center;gap:7px;margin-top:14px;padding:8px 16px;background:#eef2fb;border:1.5px solid #c7d4f0;border-radius:8px;font-size:.8rem;font-weight:500;color:#4b6cb7;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Após clicar no botão, será enviado um email com uma nova password.
</div>
<br><br>

       <?php
}


//mudar password
elseif ($rsenha==1) 
{
   $nobd     = $_SESSION['nobd'];
   $serverbd = $_SESSION['serverbd'];
   // SEGURANÇA: removido echo $nobd e echo $serverbd — expunha credenciais na página
?>

<script>
/* ── Password Strength Indicator ── */
function calcStrength(pwd) {
    var score = 0;
    if (pwd.length >= 12) score++;
    if (pwd.length >= 16) score++;
    if (/[a-z]/.test(pwd)) score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[\'^£$%&*()}{@#~?><>,|=_+!\-]/.test(pwd)) score++;
    return score; // 0-6
}

function strengthLabel(score) {
    if (score <= 1) return { label: 'Muito fraca', color: '#e53935', width: '16%' };
    if (score === 2) return { label: 'Fraca',       color: '#fb8c00', width: '33%' };
    if (score === 3) return { label: 'Razoável',    color: '#fdd835', width: '50%' };
    if (score === 4) return { label: 'Boa',         color: '#7cb342', width: '67%' };
    if (score === 5) return { label: 'Forte',       color: '#2e7d32', width: '84%' };
    return              { label: 'Muito forte',  color: '#1565c0', width: '100%' };
}

function updateStrengthIndicator(inputId, barId, labelId) {
    var pwd  = document.getElementById(inputId).value;
    var info = strengthLabel(calcStrength(pwd));
    var bar  = document.getElementById(barId);
    var lbl  = document.getElementById(labelId);
    if (!bar || !lbl) return;
    if (pwd.length === 0) { bar.style.width = '0%'; lbl.textContent = ''; return; }
    bar.style.width       = info.width;
    bar.style.background  = info.color;
    lbl.textContent       = info.label;
    lbl.style.color       = info.color;
}

/* ── Have I Been Pwned check (k-anonymity) ── */
async function checkHIBP(pwd) {
    try {
        var buf    = await crypto.subtle.digest('SHA-1', new TextEncoder().encode(pwd));
        var hex    = Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('').toUpperCase();
        var prefix = hex.slice(0, 5);
        var suffix = hex.slice(5);
        var resp   = await fetch('https://api.pwnedpasswords.com/range/' + prefix, {cache:'no-store'});
        if (!resp.ok) return false;
        var text   = await resp.text();
        return text.split('\n').some(function(line){ return line.split(':')[0].trim() === suffix; });
    } catch(e) { return false; }
}

async function validateAndSubmitReset(e) {
    e.preventDefault();
    var pwd = document.getElementById('rr_password').value;
    var cpw = document.getElementById('rr_confirmapassword').value;

    /* comprimento mínimo */
    if (pwd.length < 12) {
        document.getElementById('rr_pwd_err').textContent = 'A password deve ter pelo menos 12 caracteres.';
        document.getElementById('rr_pwd_err').style.color = '#e53935';
        return;
    }

    /* confirmação */
    if (pwd !== cpw) {
        document.getElementById('rr_pwd_err').textContent = 'As passwords não coincidem.';
        document.getElementById('rr_pwd_err').style.color = '#e53935';
        return;
    }

    /* HIBP */
    var btn = document.getElementById('rr_submitBtn');
    btn.disabled = true;
    btn.value    = 'A verificar...';
    var pwned    = await checkHIBP(pwd);
    btn.disabled = false;
    btn.value    = 'Mudar';

    if (pwned) {
        document.getElementById('rr_pwd_err').innerHTML =
            '⚠️ Esta password foi exposta em fugas de dados conhecidas.<br>Por favor escolha uma password diferente.';
        document.getElementById('rr_pwd_err').style.color = '#e53935';
        return;
    }

    document.getElementById('rrForm').submit();
}
</script>

<form id="rrForm" action="<?php echo SVRURL ?>reset_pass_OK.php" method="post" onsubmit="validateAndSubmitReset(event)">

   <!-- Token CSRF -->
   <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

   <!-- Email: pré-preenchido da sessão, não editável -->
   <span class="focus-input100"></span>
   <span class="symbol-input100"><i class="fa fa-envelope" aria-hidden="true"></i></span>
   <input title="Email" required
          type="text" name="email" class="fadeIn second" placeholder="Email"
          id="email" readonly
          value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
          style="background:#f0f0f0; cursor:not-allowed; color:#555;">
   <span id="email_err"></span>

   <br>

   <!-- Password atual -->
   <div style="background:#fff8e1; border-left:4px solid #f0a500; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
      <label for="passworda" style="display:block; font-size:13px; font-weight:bold; color:#7a5200; margin-bottom:2px;">
         <i class="fa fa-lock-open" style="margin-right:5px;"></i> Password atual
      </label>
      <small style="color:#7a5200;">Introduza a sua password atual para confirmar a sua identidade.</small>
   </div>
   <span class="focus-input100"></span>
   <span class="symbol-input100"><i class="fa fa-lock-open" aria-hidden="true"></i></span>
   <input title="Password atual"
          class="fadeIn second" placeholder="Introduza a sua password atual"
          type="password" name="passworda" id="passworda"
          pattern=".{12,}" minlength="12" required/>

   <br><br>

   <!-- Nova password -->
   <div style="background:#e8f5e9; border-left:4px solid #2e7d32; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
      <label for="rr_password" style="display:block; font-size:13px; font-weight:bold; color:#1b5e20; margin-bottom:2px;">
         <i class="fa fa-key" style="margin-right:5px;"></i> Nova password
      </label>
      <small style="color:#1b5e20;">
         A nova password deve ter <strong>pelo menos 12 caracteres</strong> e incluir <strong>letras, números e símbolos</strong>.
      </small>
   </div>
   <span class="focus-input100"></span>
   <span class="symbol-input100"><i class="fa fa-key" aria-hidden="true"></i></span>
   <input title="Nova password (>= 12 caracteres, letras, números e símbolo)"
          class="fadeIn second" placeholder="Introduza a nova password"
          type="password" name="password" id="rr_password"
          pattern=".{12,}" minlength="12" required
          oninput="updateStrengthIndicator('rr_password','rr_strength-bar','rr_strength-label')"/>

   <!-- Indicador de força -->
   <div style="margin:6px 0 2px 0; background:#e0e0e0; border-radius:4px; height:8px; overflow:hidden;">
      <div id="rr_strength-bar" style="height:100%; width:0%; border-radius:4px; transition:width 0.35s ease, background 0.35s ease;"></div>
   </div>
   <small id="rr_strength-label" style="font-weight:600; font-size:12px;"></small>
   <span id="rr_pwd_err" style="display:block; font-size:12px; margin-top:4px;"></span>

   <br>

   <!-- Confirmar nova password -->
   <div style="background:#e3f2fd; border-left:4px solid #1565c0; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
      <label for="rr_confirmapassword" style="display:block; font-size:13px; font-weight:bold; color:#0d3c6e; margin-bottom:2px;">
         <i class="fa fa-check-circle" style="margin-right:5px;"></i> Confirmar nova password
      </label>
      <small style="color:#0d3c6e;">Repita a nova password para confirmar que não existem erros.</small>
   </div>
   <span class="focus-input100"></span>
   <span class="symbol-input100"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
   <input title="Confirmar password" class="fadeIn second" placeholder="Repita a nova password"
          pattern=".{12,}" type="password" minlength="12"
          name="confirmapassword" id="rr_confirmapassword" required/>

   <br><br>
   <input id="rr_submitBtn" class="fadeIn fourth" title="Mudar password" type="submit" value="Mudar"/>
   <br/>

</form>

<?php

}
?>






  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    
<br><br>
      <?php include ("footer.php");?>

</body>
</html>
