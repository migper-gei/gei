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


   <script>
function email_validation(){
'use strict';

var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
var email_name = document.getElementById("email");
var email_value = document.getElementById("email").value;
var email_length = email_value.length;
if(!email_value.match(mailformat) || email_length === 0)
{

document.getElementById('email_err').innerHTML = '<br>'+'Email inválido.'+'<br>';
email_name.focus();
document.getElementById('email_err').style.color = "#FF0000";
}
else
{
   document.getElementById('email_err').innerHTML = '<br>'+'Email válido'+'<br>';
document.getElementById('email_err').style.color = "#00AF33";
}
}

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
    var pwd = document.getElementById(inputId).value;
    var info = strengthLabel(calcStrength(pwd));
    var bar = document.getElementById(barId);
    var lbl = document.getElementById(labelId);
    if (!bar || !lbl) return;
    if (pwd.length === 0) {
        bar.style.width = '0%';
        lbl.textContent = '';
        return;
    }
    bar.style.width = info.width;
    bar.style.background = info.color;
    lbl.textContent = info.label;
    lbl.style.color = info.color;
}

/* ── Have I Been Pwned check (k-anonymity) ── */
async function checkHIBP(pwd) {
    try {
        var buf = await crypto.subtle.digest('SHA-1', new TextEncoder().encode(pwd));
        var hex = Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('').toUpperCase();
        var prefix = hex.slice(0, 5);
        var suffix = hex.slice(5);
        var resp = await fetch('https://api.pwnedpasswords.com/range/' + prefix, {cache:'no-store'});
        if (!resp.ok) return false; // falha silenciosa: não bloqueia
        var text = await resp.text();
        return text.split('\n').some(function(line){
            return line.split(':')[0].trim() === suffix;
        });
    } catch(e) {
        return false; // sem ligação: não bloqueia
    }
}

async function validateAndSubmit(e) {
    e.preventDefault();
    var pwd = document.getElementById('password').value;

    /* comprimento mínimo */
    if (pwd.length < 12) {
        document.getElementById('pwd_err').textContent = 'A password deve ter pelo menos 12 caracteres.';
        document.getElementById('pwd_err').style.color = '#e53935';
        return;
    }

    /* HIBP */
    var hibpBtn = document.getElementById('submitBtn');
    hibpBtn.disabled = true;
    hibpBtn.value = 'A verificar...';
    var pwned = await checkHIBP(pwd);
    hibpBtn.disabled = false;
    hibpBtn.value = 'Mudar';

    if (pwned) {
        document.getElementById('pwd_err').innerHTML =
            '⚠️ Esta password foi exposta em fugas de dados conhecidas.<br>' +
            'Por favor escolha uma password diferente.';
        document.getElementById('pwd_err').style.color = '#e53935';
        return;
    }

    /* tudo OK — submeter */
    document.getElementById('resetForm').submit();
}
</script>

<?php include ("head.php");

?>




   </head>


   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


      <?php include ("header2.php");?>
      <script>
        /* Nesta página o botão Home deve ir para acessorap, não para index */
        document.querySelectorAll('a.home-button').forEach(function(btn){
          btn.href = '<?php echo SVRURL ?>acessorap';
        });
      </script>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                  
              




    <h2> Mudar password </h2>
   



                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


<?php
   $nobd=$_SESSION['nobd'];
$serverbd=$_SESSION['serverbd'];



?>


<form id="resetForm" action = "<?php echo SVRURL ?>reset_pass_OK.php" method = "post" onsubmit="validateAndSubmit(event)">

      <!-- Token CSRF -->
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>

             <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
					type="text" name="email" class="fadeIn second" placeholder="Email"
               id="email" readonly
               value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
               style="background:#f0f0f0; cursor:not-allowed; color:#555;"><span id="email_err"></span>
                    
             
                   <br>
                   <div style="background:#fff8e1; border-left:4px solid #f0a500; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
                     <label for="passworda" style="display:block; font-size:13px; font-weight:bold; color:#7a5200; margin-bottom:2px;">
                       <i class="fa fa-lock-open" style="margin-right:5px;"></i> Password atual
                     </label>
                     <small style="color:#7a5200;">Introduza a sua password atual para confirmar a sua identidade.</small>
                   </div>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock-open" aria-hidden="true"></i>
						</span>
                   <input title="Password antiga" 
                         class="fadeIn second" placeholder="Introduza a sua password atual" type="password" name="passworda" id="passworda" pattern=".{12,}" minlength="12" required/>
                   
                   <br><br>
                   <div style="background:#e8f5e9; border-left:4px solid #2e7d32; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
                     <label for="password" style="display:block; font-size:13px; font-weight:bold; color:#1b5e20; margin-bottom:2px;">
                       <i class="fa fa-key" style="margin-right:5px;"></i> Nova password
                     </label>
                     <small style="color:#1b5e20;">
                       A nova password deve ter <strong>pelo menos 12 caracteres</strong> e incluir <strong>letras, números e símbolos</strong>.
                     </small>
                   </div>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-key" aria-hidden="true"></i>
						</span>
                   <input title="Password (>= 12 caracteres, letras, números e símbolo)" 
                       class="fadeIn second" placeholder="Introduza a nova password" type="password" name="password" id="password" pattern=".{12,}" minlength="12" required
                       oninput="updateStrengthIndicator('password','strength-bar','strength-label')"/>

                   <!-- Indicador de força -->
                   <div style="margin:6px 0 2px 0; background:#e0e0e0; border-radius:4px; height:8px; overflow:hidden;">
                     <div id="strength-bar" style="height:100%; width:0%; border-radius:4px; transition:width 0.35s ease, background 0.35s ease;"></div>
                   </div>
                   <small id="strength-label" style="font-weight:600; font-size:12px;"></small>
                   <span id="pwd_err" style="display:block; font-size:12px; margin-top:4px;"></span>

                   <br>
                   <div style="background:#e3f2fd; border-left:4px solid #1565c0; border-radius:4px; padding:8px 12px; margin:12px 0 4px 0; text-align:left;">
                     <label for="confirmapassword" style="display:block; font-size:13px; font-weight:bold; color:#0d3c6e; margin-bottom:2px;">
                       <i class="fa fa-check-circle" style="margin-right:5px;"></i> Confirmar nova password
                     </label>
                     <small style="color:#0d3c6e;">Repita a nova password para confirmar que não existem erros.</small>
                   </div>
                   <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-check-circle" aria-hidden="true"></i>
						</span>
              <input title="Confirmar password" class="fadeIn second" placeholder="Repita a nova password" pattern=".{12,}" type="password" minlength="12" name="confirmapassword" id="confirmapassword" required/>
              <br>  <br>
									<input id="submitBtn" class="fadeIn fourth" title="Mudar password" type = "submit" value = "Mudar"/>  
                                           
                                              <br />
                                  
                                            
                 </form>









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