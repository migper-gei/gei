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
<?php include("head.php"); ?>

<script>
function email_validation() {
    'use strict';
    var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
    var email_name  = document.getElementById("email");
    var email_value = document.getElementById("email").value;
    var email_length = email_value.length;
    if (!email_value.match(mailformat) || email_length === 0) {
        document.getElementById('email_err').innerHTML = '<br>Email inválido.';
        email_name.focus();
        document.getElementById('email_err').style.color = "#FF0000";
    } else {
        document.getElementById('email_err').innerHTML = '<br>Email válido';
        document.getElementById('email_err').style.color = "#00AF33";
    }
}

function myFunction() {
    var x = document.getElementById("mypass");
    x.type = (x.type === "password") ? "text" : "password";
}

function toggleCodigoQR() {
    var x   = document.getElementById("codigo_acesso_qr");
    var ico = document.getElementById("ico_codigo_qr");
    if (x.type === "password") {
        x.type = "text";
        ico.className = "fa-regular fa-eye-slash";
    } else {
        x.type = "password";
        ico.className = "fa-regular fa-eye";
    }
}
</script>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>
      <?php
          include("css_inserir.php");
          include("sessao_timeout.php");
      ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <!-- Breadcrumb -->
                  <nav style="margin-bottom:10px;">
                     <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                           <span style="color:#4b6cb7;">Configurações</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Email/Sessão &gt;&gt; Inserir</li>
                     </ol>
                  </nav>
                  <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                     <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                     </div>

<div class="form-container">

   <div class="step-indicator">
      <i class="fas fa-info-circle mr-2"></i>
      Complete todos os campos obrigatórios (indicados com fundo azul claro)
   </div>

   <form action="<?php echo SVRURL ?>gravaemse" method="post" class="needs-validation" novalidate>

      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

      <!-- ── Bloco 1: Email / SMTP ─────────────────────────────── -->
      <div class="step-indicator">

         <label>Email: </label><br>
         <input class="form-control required-field" style="width:100%"
                required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
                type="text" name="email" id="email"
                onBlur="email_validation();">
         <span id="email_err"></span>
         <br>

         <label>Password: </label><br>
         <input class="form-control required-field" id="mypass"
                type="password" name="pass" required style="width:100%">
         <br>
         <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password
         <br><br>

         <label>Servidor SMTP: </label><br>
         <input class="form-control required-field"
                type="text" name="smtp" required style="width:100%">
         <br><br>

         <label>SMTP porta: </label><br>
         <input class="form-control required-field" required maxlength="5"
                type="text" name="smtpport" style="width:100%"
                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
         <br><br>

         <label>Sigla de envio de email: </label><br>
         <input class="form-control required-field"
                type="text" name="nome" required style="width:100%">

      </div>

      <!-- ── Bloco 2: Sessão / Password ────────────────────────── -->
      <div class="step-indicator">

         <label>Tempo duração da sessão (em segundos): </label>
         <input class="form-control required-field"
                type="number" name="sessao" required style="width:100%">
         <br>

         <label>Tempo duração da password (em dias): </label>
         <input class="form-control required-field"
                type="number" name="tempodurapass" required style="width:100%">

      </div>

      <!-- ── Bloco 3: Código de acesso QR ──────────────────────── -->
      <div class="step-indicator">

         <label>
            <i class="fa-solid fa-qrcode"></i>&nbsp;
            Código de acesso QR (admins / reparadores):
         </label><br>
         <small style="color:#7b88a0;">
            Código secreto que permite a admins e reparadores aceder à ficha
            do equipamento via QR Code sem sessão ativa. Máximo 20 caracteres.
         </small>
         <br><br>

         <div style="position:relative;">
            <input class="form-control required-field"
                   id="codigo_acesso_qr" type="password"
                   name="codigo_acesso_qr" maxlength="20"
                   autocomplete="new-password" required
                   style="width:100%; letter-spacing:2px; padding-right:42px;">
            <span onclick="toggleCodigoQR()" title="Mostrar/ocultar código"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#4b6cb7;">
               <i class="fa-regular fa-eye" id="ico_codigo_qr"></i>
            </span>
         </div>

      </div>

      <!-- ── Botão submit ───────────────────────────────────────── -->
      <div style="text-align:center; width:100%;">
         <button type="submit" class="btn-submit">
            <i class="fa-solid fa-circle-check"></i>
            &nbsp;Inserir email/sessão
         </button>
      </div>

   </div><!-- /form-container -->

   </form>

   <a href="<?php echo SVRURL ?>emsess" title="Voltar">
      <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
   </a>
   <br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>

   <script>
      (function () {
          'use strict';
          window.addEventListener('load', function () {
              var forms = document.getElementsByClassName('needs-validation');
              Array.prototype.filter.call(forms, function (form) {
                  form.addEventListener('submit', function (event) {
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

      <?php include("footer.php"); ?>
   </body>
</html>
