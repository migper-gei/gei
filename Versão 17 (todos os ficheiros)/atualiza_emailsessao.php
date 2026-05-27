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

// --- Verificar autenticação e permissão antes de qualquer output ---
if (empty($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header('Location: ' . SVRURL . 'emsess');
    exit;
}

// Gerar token CSRF — sempre regenerar a cada carregamento do formulário
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include("head.php"); ?>
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
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">
                        <a href="<?php echo SVRURL ?>emsess" style="color:#4b6cb7;text-decoration:none;">Email/Sessão</a>
                        &gt;&gt; Atualizar
                     </li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

<script>
function email_validation() {
    'use strict';
    var mailformat = /^\w+([\.\-]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$/;
    var email_name  = document.getElementById("email");
    var email_value = email_name.value;
    if (!email_value.match(mailformat) || email_value.length === 0) {
        document.getElementById('email_err').innerHTML = '<br>Email inválido.';
        document.getElementById('email_err').style.color = "#FF0000";
    } else {
        document.getElementById('email_err').innerHTML = '<br>Email válido';
        document.getElementById('email_err').style.color = "#00AF33";
    }
}
function myFunction() {
    var x = document.getElementById("mypass1");
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

<?php

// --- Obter ID do registo ---
if (!isset($_GET['url'])) { ?>
    <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>configura'; },10);</script>
<?php exit; }

$url  = explode('/', $_GET['url']);
$_sid = (int)base64_decode($url[0]);

// --- Desencriptar password com chave de ambiente ---
$_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';

if (empty($_smtpKey)) { ?>
    <script>
    swal({ title: 'Configuração em falta!', text: 'A variável SMTP_KEY não está definida no servidor.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php exit; }

$stmt_s = $db->prepare("
    SELECT id, email_user,
           AES_DECRYPT(pass, ?) AS pass_dec,
           CAST(pass AS CHAR)   AS pass_txt,
           email_smtp, email_smtpport, nome_app,
           sessao_timeout, tempoduracaopass,
           COALESCE(retencao_ativa, 0)       AS retencao_ativa,
           COALESCE(retencao_anos, 3)        AS retencao_anos,
           COALESCE(retencao_dias_aviso, 30) AS retencao_dias_aviso,
           COALESCE(codigo_acesso_qr, '')    AS codigo_acesso_qr
    FROM settings WHERE id = ?
");
$stmt_s->bind_param("si", $_smtpKey, $_sid);
$stmt_s->execute();
$row = $stmt_s->get_result()->fetch_assoc();
$stmt_s->close();

if (!$row) { ?>
    <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>emsess'; },10);</script>
<?php exit; }

// Se AES_DECRYPT devolveu NULL → password ainda em texto claro (registo antigo)
$passDecifrada = (!empty($row['pass_dec']))
    ? $row['pass_dec']
    : $row['pass_txt'];
?>

<div class="form-container">
<form action="<?php echo SVRURL ?>atualiza_ok_emailsessao.php?i=<?php echo base64_encode($row['id']); ?>" method="post" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <!-- ── Bloco 1: Email / SMTP ──────────────────────────────────────── -->
    <div class="step-indicator">

        <label>Email:</label><br>
        <input class="form-control required-field" style="width:100%" required
               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
               value="<?php echo htmlspecialchars($row['email_user'], ENT_QUOTES, 'UTF-8'); ?>"
               type="text" name="email" onBlur="email_validation();" id="email">
        <span id="email_err"></span>

        <br><br>
        <label>Password:</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo htmlspecialchars($passDecifrada, ENT_QUOTES, 'UTF-8'); ?>"
               id="mypass1" type="password" name="pass" required>
        <br>
        <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password

        <br><br>
        <label>SMTP:</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo htmlspecialchars($row['email_smtp'], ENT_QUOTES, 'UTF-8'); ?>"
               type="text" name="smtp" required>

        <br><br>
        <label>SMTP Porta:</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo (int)$row['email_smtpport']; ?>"
               type="text" name="smtpport" maxlength="5" required
               oninput="this.value=this.value.replace(/[^0-9]/g,'');">

        <br><br>
        <label>Nome (sigla):</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo htmlspecialchars($row['nome_app'], ENT_QUOTES, 'UTF-8'); ?>"
               type="text" name="nome" required>

    </div>

    <!-- ── Bloco 2: Sessão / Password ────────────────────────────────── -->
    <div class="step-indicator">

        <label>Tempo duração da sessão (em segundos):</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo (int)$row['sessao_timeout']; ?>"
               type="number" name="sessao" required>

        <br><br>
        <label>Tempo duração da password (em dias):</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo (int)$row['tempoduracaopass']; ?>"
               type="number" name="tempodurapass" required>

    </div>

    <!-- ── Bloco 3: Código de acesso QR ──────────────────────────────── -->
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
                   value="<?php echo htmlspecialchars($row['codigo_acesso_qr'], ENT_QUOTES, 'UTF-8'); ?>"
                   style="width:100%; letter-spacing:2px; padding-right:42px;">
            <span onclick="toggleCodigoQR()" title="Mostrar/ocultar código"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#4b6cb7;">
                <i class="fa-regular fa-eye" id="ico_codigo_qr"></i>
            </span>
        </div>

    </div>

    <!-- ── Bloco 4: Retenção de dados ────────────────────────────────── -->
    <div class="step-indicator">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e67e22" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <line x1="17" y1="8" x2="23" y2="8"/><line x1="20" y1="5" x2="20" y2="11"/>
            </svg>
            <strong style="color:#182848;font-size:.9rem;">Política de Retenção de Dados</strong>
        </div>

        <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;cursor:pointer;font-weight:600;font-size:.88rem;">
            <input type="checkbox" name="retencao_ativa" value="1"
                   <?php echo (int)($row['retencao_ativa'] ?? 0) ? 'checked' : ''; ?>
                   style="width:16px;height:16px;cursor:pointer;">
            Política de retenção ativa
            <span style="font-size:.75rem;font-weight:400;color:#7b88a0;">(identifica e permite eliminar utilizadores inativos)</span>
        </label>

        <label>Anos de inatividade para considerar utilizador inativo:</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo (int)($row['retencao_anos'] ?? 3); ?>"
               type="number" name="retencao_anos" min="1" max="10">
        <small style="color:#7b88a0;font-size:.75rem;">Utilizadores sem login durante este período ficam visíveis em "Utilizadores inativos". (1–10 anos)</small>

        <br><br>
        <label>Dias de aviso prévio antes de eliminar:</label><br>
        <input class="form-control required-field" style="width:100%"
               value="<?php echo (int)($row['retencao_dias_aviso'] ?? 30); ?>"
               type="number" name="retencao_dias_aviso" min="7" max="90">
        <small style="color:#7b88a0;font-size:.75rem;">Número de dias de aviso por email antes da eliminação definitiva. (7–90 dias)</small>
    </div>

    <!-- ── Botão submit ───────────────────────────────────────────────── -->
    <div style="text-align:center;">
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-pen"></i>&nbsp;Atualizar email/sessão
        </button>
    </div>

</form>
</div>

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

      <?php include("footer.php"); ?>
   </body>
</html>
