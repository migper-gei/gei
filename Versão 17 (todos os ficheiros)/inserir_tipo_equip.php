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
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// [CORRIGIDO] Token CSRF único, gerado uma só vez por sessão.
// Antes existiam dois tokens gerados em sítios diferentes ($csrf_token e $token)
// e o form enviava $token enquanto o servidor validava $csrf_token (ou nenhum).
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>

<script>
var tipoExiste = false;
var _checkTipoTimer = null;

function verificaTipoDuplicado() {
    var nome   = document.getElementById('nome_tipo').value.trim();
    var errDiv = document.getElementById('tipo_err');

    if (!nome) {
        errDiv.innerHTML = '';
        tipoExiste = false;
        clearTimeout(_checkTipoTimer);
        return;
    }

    clearTimeout(_checkTipoTimer);
    _checkTipoTimer = setTimeout(function() {
        errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar...</small>';
        tipoExiste = false;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_tipo_equip.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.existe) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; Este tipo de equipamento já existe.</small>';
                        tipoExiste = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Disponível</small>';
                        tipoExiste = false;
                    }
                } catch(e) { errDiv.innerHTML = ''; }
            } else { errDiv.innerHTML = ''; }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; tipoExiste = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; tipoExiste = false; };
        xhr.send('nome=' + encodeURIComponent(nome));
    }, 400);
}
</script>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>

     <?php
include ("css_inserir.php");
include("sessao_timeout.php");
?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Tipos de equipamento >> Inserir</li>
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

   <!-- [CORRIGIDO] O form envia csrf_token (igual ao que o servidor valida).
        Antes enviava $token (variável local diferente de $csrf_token).        -->
   <form action="<?php echo SVRURL ?>gravatequip" method="post" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

      <label>Tipo de equipamento: </label><br>
      <input style="width:100%" required type="text" name="nome" id="nome_tipo"
             class="form-control required-field"
             onBlur="verificaTipoDuplicado()" onInput="verificaTipoDuplicado()">
      <div id="tipo_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>

      <br>
      <div class="text-center mt-4">
         <button type="submit" class="btn-submit">
            <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
            &nbsp;Inserir tipo de equipamento
         </button>
      </div>
   </form>

</div>
<br>

<a href="<?php echo SVRURL ?>tiposequip">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>
<br>
                    </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (tipoExiste) {
                            event.preventDefault();
                            event.stopPropagation();
                            document.getElementById('tipo_err').innerHTML =
                                '<small style="color:#dc3545;font-weight:600;">&#10007; Este tipo de equipamento já existe.</small>';
                            document.getElementById('nome_tipo').focus();
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

      <?php include ("footer.php"); ?>

      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
</body>
</html>
