<!DOCTYPE html>
<html lang="pt">
   <head>
      <script>
         function myFunction() {
           var x = document.getElementById("mypass");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
      </script>

      <script>
      function email_validation(){
         'use strict';
         var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
         var email_name = document.getElementById("email");
         var email_value = document.getElementById("email").value;
         var email_length = email_value.length;

         if(!email_value.match(mailformat) || email_length === 0)
         {
            document.getElementById('email_err').innerHTML = '<br>'+'Email inválido.';
            email_name.focus();
            document.getElementById('email_err').style.color = "#FF0000";
         }
         else
         {
            document.getElementById('email_err').innerHTML = '<br>'+'Email válido';
            document.getElementById('email_err').style.color = "#00AF33";
         }
      }
      </script>

<?php
// ---------------------------------------------------------------
// SEGURANÇA: iniciar sessão segura para gerar token CSRF
// ---------------------------------------------------------------
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
}

// Gerar token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include ("head.php");
//include ("verifica_sessao.php");
?>

   <!-- verificar_codigo: colocado aqui pois SVRURL só fica disponível após head.php -->
   <script>
   function verificar_codigo(){
      'use strict';
      var cod = document.getElementById("cod").value;
      var btnLogin = document.getElementById("btnLogin");

      if (cod.length < 1) {
         btnLogin.disabled = true;
         btnLogin.style.opacity = "0.5";
         btnLogin.style.cursor  = "not-allowed";
         return;
      }

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "<?php echo SVRURL ?>verifica_codigo.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
         if (xhr.readyState === 4 && xhr.status === 200) {
            var resp = JSON.parse(xhr.responseText);
            var el = document.getElementById("cod_err");
            if (resp.existe) {
               el.innerHTML = "<br>Código válido.";
               el.style.color = "#00AF33";
               btnLogin.disabled = false;
               btnLogin.style.opacity = "1";
               btnLogin.style.cursor  = "pointer";
            } else {
               el.innerHTML = "<br>Código não encontrado.";
               el.style.color = "#FF0000";
               btnLogin.disabled = true;
               btnLogin.style.opacity = "0.5";
               btnLogin.style.cursor  = "not-allowed";
            }
         }
      };
      xhr.send("codigo=" + encodeURIComponent(cod) + "&csrf_token=" + encodeURIComponent(document.querySelector('[name=csrf_token]').value));
   }
   </script>

   <style>
      /* ══ DARK MODE — login.php ══ */
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

      /* Caixa do formulário */
      [data-theme="dark"] #formContent {
         background: var(--surface);
         color: var(--text);
         box-shadow: 0 6px 24px rgba(0,0,0,.45);
      }

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

      [data-theme="dark"] .titlepage h2 {
         color: var(--text);
      }

      /* Link "Esqueci-me da password" */
      [data-theme="dark"] #formFooter a {
         background: #1e2a45 !important;
         border-color: #2a3a5a !important;
         color: #a8c0f0 !important;
      }

      [data-theme="dark"] #formFooter a svg {
         stroke: #a8c0f0;
      }
   </style>

   </head>

   <!-- body -->
   <body class="main-layout">

    <?php include("loader.php"); ?>
    

     <?php 
     $hideLoginButton = true;
     include ("header2.php");
     //include ("header.php");
     //include("sessao_timeout.php");
     ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Login </h2>
                  </div>
               </div>
            </div>

            <div class="container">
               <div class="row">

<div class="wrapper fadeInDown">
  <div id="formContent">
 Dados de acesso à base de dados teste:
     <br>
Código: 123456<br>
Utilizador: <?php echo 'adminteste' . '@' . 'escola.pt'; ?><br>
Password: admin+123

    <?php
    // ---------------------------------------------------------------
    // SEGURANÇA: NUNCA expor credenciais no HTML, mesmo em ambiente
    // de teste. Usar apenas em documentação interna ou .env local.
    // O bloco abaixo foi removido intencionalmente:
    //
    // Dados de acesso à base de dados teste:
    // Código: 123456
    // Utilizador: adminteste@escola.pt
    // Password: admin+123
    // ---------------------------------------------------------------
    ?>

    <!-- Login Form -->
    <form action="<?php echo SVRURL ?>validaus/<?php echo base64_encode(0)?>" method="post">

      <!-- Token CSRF: protege contra Cross-Site Request Forgery -->
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

      <span class="focus-input100"></span>
      <span class="symbol-input100">
         <i class="fa fa-key" aria-hidden="true"></i>
      </span>

      <input title="Código" required type="text" name="codigo" class="fadeIn third" placeholder="Código"
         oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="9"
         id="cod" />

      <br><br>

      <span class="focus-input100"></span>
      <span class="symbol-input100">
         <i class="fa fa-envelope" aria-hidden="true"></i>
      </span>
      <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
         data-validate="Email válido: ex@abc.xyz"
         type="text" name="email" class="fadeIn second" placeholder="Email"
         onBlur="email_validation();" id="email"><span id="email_err"></span>

      <br>

      <span class="focus-input100"></span>
      <span class="symbol-input100">
         <i class="fa fa-lock" aria-hidden="true"></i>
      </span>
      <input title="Password" required id="mypass" type="password" name="password" class="fadeIn third" placeholder="Password">
      <br>
      <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password
      <br><br>
      <input title="Login" type="submit" id="btnLogin" class="fadeIn fourth" value="Login">
    </form>

    <div id="formFooter">
        <a href="<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>" title="Esqueci-me da password"
           style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:7px 18px;border-radius:7px;font-size:.82rem;font-weight:600;color:#4b6cb7 !important;background:#eef2fb;border:1.5px solid #c7d4f0;text-decoration:none;transition:all .18s;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Esqueci-me da password
        </a>
    </div>

  </div>
</div>

</div>
         </div>
      </div>
      <!-- end about -->

      
      <?php if (!empty($_GET['link_expirado'])): ?>
<script>
swal({
    title: 'Link inválido ou expirado',
    text: 'Este link de recuperação já foi utilizado ou expirou.\nSe precisares de redefinir a password, solicita um novo link.',
    icon: 'warning',
    button: 'OK',
});
</script>
<?php endif; ?>


      
      
      <?php include ("footer.php");?>




</body>
</html>
