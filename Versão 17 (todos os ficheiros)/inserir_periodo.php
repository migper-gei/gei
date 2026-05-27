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

// ── CSRF TOKEN ────────────────────────────────────────────────────────────────
// Token único, chave consistente 'csrf_token'.
// Gerado uma só vez por sessão; regenerado após cada submit bem-sucedido
// (isso deve ser feito em grava_periodo.php após validação).
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
// ─────────────────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include("head.php"); ?>
   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>

      <?php
        include("css_inserir.php");
        include("sessao_timeout.php");
      ?>

      <!-- about -->
      <div class="about">
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
                     <li style="color:#1e2a45;">Períodos &gt;&gt; Inserir</li>
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

<script>
var periodoExiste = false;

var _checkTimer     = null;
var _checkDatasTimer = null;
var datasConflito   = false;

function verificaDatasConflito() {
    var ano    = document.getElementById('anoletivo').value.trim();
    var datai  = document.getElementById('datai').value;
    var dataf  = document.getElementById('dataf').value;
    var errDiv = document.getElementById('datas_err');

    if (!datai || !dataf) {
        errDiv.innerHTML = '';
        datasConflito = false;
        return;
    }

    if (dataf <= datai) {
        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; A data fim deve ser superior à data início.</small>';
        datasConflito = true;
        return;
    }

    errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar datas...</small>';
    datasConflito = false;

    clearTimeout(_checkDatasTimer);
    _checkDatasTimer = setTimeout(function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_periodo.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.conflito_datas) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; As datas sobrepõem-se com o ' + resp.periodo_conflito + ' do ano ' + resp.ano_conflito + '.</small>';
                        datasConflito = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Datas disponíveis</small>';
                        datasConflito = false;
                    }
                } catch(e) {
                    errDiv.innerHTML = '';
                }
            } else {
                errDiv.innerHTML = '';
            }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; datasConflito = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; datasConflito = false; };
        xhr.send('acao=datas&ano=' + encodeURIComponent(ano) + '&datai=' + encodeURIComponent(datai) + '&dataf=' + encodeURIComponent(dataf));
    }, 400);
}

function verificaPeriodoDuplicado() {
    var ano     = document.getElementById('anoletivo').value.trim();
    var periodo = document.getElementById('periodo').value;
    var errDiv  = document.getElementById('periodo_err');

    if (!ano || !periodo) {
        errDiv.innerHTML = '';
        periodoExiste = false;
        clearTimeout(_checkTimer);
        return;
    }

    clearTimeout(_checkTimer);
    _checkTimer = setTimeout(function() {
        errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar...</small>';
        periodoExiste = false;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_periodo.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.existe) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; Este período já existe para o ano indicado.</small>';
                        periodoExiste = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Disponível</small>';
                        periodoExiste = false;
                    }
                } catch(e) {
                    errDiv.innerHTML = '';
                }
            } else {
                errDiv.innerHTML = '';
            }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; periodoExiste = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; periodoExiste = false; };
        xhr.send('ano=' + encodeURIComponent(ano) + '&periodo=' + encodeURIComponent(periodo));
    }, 400);
}

function clickMe() {
    var datai = document.forms.per.elements.datai.value;
    var dataf = document.forms.per.elements.dataf.value;
    var di = new Date(Date.parse(datai));
    var df = new Date(Date.parse(dataf));
    var ano     = document.getElementById('anoletivo').value.trim();
    var periodo = document.getElementById('periodo').value;

    if (df <= di) {
        swal({
            title: "A data final deve ser superior à data inicial!",
            type: "warning",
            confirmButtonText: "OK",
            closeOnConfirm: false,
            closeOnCancel: false
        });
        return false;
    }

    // Verificação síncrona de duplicado no momento do submit
    if (ano && periodo) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_periodo.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        try {
            xhr.send('ano=' + encodeURIComponent(ano) + '&periodo=' + encodeURIComponent(periodo));
            if (xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.existe) {
                    document.getElementById('periodo_err').innerHTML =
                        '<small style="color:#dc3545;font-weight:600;">&#10007; Este período já existe para o ano indicado.</small>';
                    document.getElementById('anoletivo').focus();
                    periodoExiste = true;
                    return false;
                } else {
                    periodoExiste = false;
                }
            }
        } catch (err) {
            // Se AJAX falhar, a verificação server-side em grava_periodo.php serve de barreira
        }
    }

    if (periodoExiste) {
        document.getElementById('periodo_err').innerHTML =
            '<small style="color:#dc3545;font-weight:600;">&#10007; Este período já existe para o ano indicado.</small>';
        document.getElementById('anoletivo').focus();
        return false;
    }

    if (datasConflito) {
        document.getElementById('datas_err').innerHTML =
            '<small style="color:#dc3545;font-weight:600;">&#10007; As datas inseridas conflituam com um período já existente.</small>';
        document.getElementById('datai').focus();
        return false;
    }

    return true;
}
</script>

<div class="form-container">

    <div class="step-indicator">
        <i class="fas fa-info-circle mr-2"></i>
        Complete todos os campos obrigatórios (indicados com fundo azul claro)
    </div>

    <form name="per" action="<?php echo SVRURL ?>gravaper" method="post"
          class="needs-validation" novalidate onsubmit="return clickMe();">

        <!-- ── CSRF TOKEN (chave única: csrf_token) ── -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

        <label>Ano: </label>
        <input style="width:100%" required type="text" name="anoletivo" id="anoletivo"
               class="form-control required-field" placeholder="Ano"
               onBlur="verificaPeriodoDuplicado()" onInput="verificaPeriodoDuplicado()">

        <br><br>

        <label>Período de tempo: </label><br>
        <select name="periodo" id="periodo"
                style="width:100%;height:35px;"
                class="form-control required-field" required
                onchange="verificaPeriodoDuplicado()"
                onBlur="verificaPeriodoDuplicado()">
            <option value=""> -- Selecione -- </option>
            <option value="1">1º</option>
            <option value="2">2º</option>
            <option value="3">3º</option>
            <option value="4">4º</option>
            <option value="5">5º</option>
        </select>
        <div id="periodo_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>

        <br><br>

        <label>Data Início: </label>
        <input required type="date" name="datai" id="datai"
               class="form-control required-field" onchange="verificaDatasConflito()">
        <br>

        <label>Data Fim: </label>
        <input required size="10" type="date" name="dataf" id="dataf"
               class="form-control required-field" onchange="verificaDatasConflito()">
        <div id="datas_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>

        <br>
        <div class="text-center mt-4">
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                &nbsp;Inserir período
            </button>
        </div>

    </form>

</div>

<a href="<?php echo SVRURL ?>peri">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include("footer.php"); ?>

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
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

         <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->




      <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
    });
});
</script>
</body>
</html>
