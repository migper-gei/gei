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
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include("head.php"); ?>
   </head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>
      <?php include("sessao_timeout.php"); ?>

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
                     <li style="color:#1e2a45;">Email/Sessão &gt;&gt; Atualizar</li>
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

<?php

// ── 1. Validar ID ─────────────────────────────────────────────────────────────
$id = isset($_GET['i']) ? (int)base64_decode($_GET['i']) : 0;
if ($id <= 0) { ?>
    <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>emsess'; },10);</script>
<?php exit; }

// ── 2. Validar CSRF ───────────────────────────────────────────────────────────
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { ?>
    <script>
    swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php exit; }

// Destruir token usado e gerar novo para a próxima utilização
unset($_SESSION['csrf_token']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// ── 3. Validar permissão ──────────────────────────────────────────────────────
if ($_SESSION['tipo'] != 1) { ?>
    <script>
    swal({ title: 'Sem permissão!', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php exit; }

// ── 4. Sanitizar inputs ───────────────────────────────────────────────────────
$_email          = trim($_POST['email']              ?? '');
$_passmail       = $_POST['pass']                    ?? '';
$_smtp           = trim($_POST['smtp']               ?? '');
$_smtpport       = (int)($_POST['smtpport']          ?? 0);
$_nome           = trim(htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8'));
$_sessao         = (int)($_POST['sessao']            ?? 0);
$_tempodura      = (int)($_POST['tempodurapass']     ?? 0);
$_ret_ativa      = isset($_POST['retencao_ativa'])   ? 1 : 0;
$_ret_anos       = max(1, min(10, (int)($_POST['retencao_anos']       ?? 3)));
$_ret_dias_aviso = max(7, min(90, (int)($_POST['retencao_dias_aviso'] ?? 30)));
$_codigo_qr      = trim($_POST['codigo_acesso_qr']   ?? '');

// ── 5. Validações básicas ─────────────────────────────────────────────────────
if (!filter_var($_email, FILTER_VALIDATE_EMAIL) || empty($_passmail)
    || empty($_smtp) || $_smtpport < 1 || $_smtpport > 65535
    || empty($_codigo_qr) || strlen($_codigo_qr) > 20) { ?>
    <script>
    swal({ title: 'Dados inválidos!', text: 'Verifique todos os campos obrigatórios.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php exit; }

// ── 6. Chave de encriptação via variável de ambiente ─────────────────────────
$_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';

if (empty($_smtpKey)) { ?>
    <script>
    swal({ title: 'Configuração em falta!', text: 'A variável SMTP_KEY não está definida no servidor.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php exit; }

// ── 7. UPDATE ─────────────────────────────────────────────────────────────────
$stmt0 = $db->prepare("
    UPDATE settings SET
        email_user          = ?,
        pass                = AES_ENCRYPT(?, ?),
        nome_app            = ?,
        email_smtp          = ?,
        email_smtpport      = ?,
        sessao_timeout      = ?,
        tempoduracaopass    = ?,
        retencao_ativa      = ?,
        retencao_anos       = ?,
        retencao_dias_aviso = ?,
        codigo_acesso_qr    = ?
    WHERE id = ?
");
// Tipos: s s s s s i i i i i i s i
// Marcadores: 13 (AES_ENCRYPT conta como 2 → total 13 '?' mas bind tem 13 vars)
$stmt0->bind_param(
    "sssssiiiiiisi",
    $_email,          // s — email_user
    $_passmail,       // s — pass (antes do AES_ENCRYPT)
    $_smtpKey,        // s — chave AES (2.º arg de AES_ENCRYPT)
    $_nome,           // s — nome_app
    $_smtp,           // s — email_smtp
    $_smtpport,       // i — email_smtpport
    $_sessao,         // i — sessao_timeout
    $_tempodura,      // i — tempoduracaopass
    $_ret_ativa,      // i — retencao_ativa
    $_ret_anos,       // i — retencao_anos
    $_ret_dias_aviso, // i — retencao_dias_aviso
    $_codigo_qr,      // s — codigo_acesso_qr
    $id               // i — WHERE id
);
$stmt0->execute();
$stmt0->close();
mysqli_close($db);
?>
<script>
swal({ title: 'Os dados foram atualizados!', icon: 'success' })
.then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
</script>

<br><br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
