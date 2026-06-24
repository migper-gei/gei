<?php
// ============================================================
// grava_emailsessao.php — GEI
// Processa o POST de inserir_emailsessao.php e grava na BD.
// ============================================================

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
                           <span style="color:#4b6cb7;">CONFIGURAÇÕES</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">EMAIL/SESSÃO &gt;&gt; INSERIR</li>
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

// ── 1. Só aceita POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>
    <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>inseriremse'; }, 10);</script>
<?php exit; }

// ── 2. Verificar CSRF ─────────────────────────────────────────────────────────
if (!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) { ?>
    <script>
    swal({ title: 'Pedido inválido!', text: 'Token de segurança inválido.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php exit; }

// ── 3. Verificar campos obrigatórios ──────────────────────────────────────────
$campos = ['pass', 'email', 'nome', 'sessao', 'smtp', 'smtpport', 'tempodurapass', 'codigo_acesso_qr'];
foreach ($campos as $c) {
    if (!isset($_POST[$c])) { ?>
        <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>inseriremse'; }, 10);</script>
<?php   exit; }
}

// ── 4. Apenas administradores (tipo 1) ───────────────────────────────────────
if ((int)($_SESSION['tipo'] ?? 0) !== 1) { ?>
    <script>
    swal({ title: 'Não pode inserir!', text: 'Não tem permissão!', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php exit; }

// ── 5. Recolher e sanitizar ───────────────────────────────────────────────────
$em         = trim($_POST['email']);
$_pa        = $_POST['pass'];
$_smtp      = trim($_POST['smtp']);
$_smtpport  = (int)$_POST['smtpport'];
$_nome      = trim(htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'));
$_sessao    = (int)$_POST['sessao'];
$_tempdur   = (int)$_POST['tempodurapass'];
$_codigo_qr = trim($_POST['codigo_acesso_qr']);

// ── 6. Verificar email duplicado ──────────────────────────────────────────────
$stmt_chk = $db->prepare("SELECT COUNT(*) FROM settings WHERE email_user = ?");
$stmt_chk->bind_param("s", $em);
$stmt_chk->execute();
$count = $stmt_chk->get_result()->fetch_row();
$stmt_chk->close();

if ($count[0] > 0) { ?>
    <script>
    swal({ title: 'O email já existe!', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php exit; }

// ── 7. Validações básicas ─────────────────────────────────────────────────────
if (empty($_pa) || empty($_smtp)
    || $_smtpport < 1 || $_smtpport > 65535
    || empty($_codigo_qr) || strlen($_codigo_qr) > 20) { ?>
    <script>
    swal({ title: 'Dados inválidos!',
           text: 'Verifique password, SMTP, porta e código de acesso QR.',
           icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php exit; }

// ── 8. Chave de encriptação via variável de ambiente ─────────────────────────
// Adicionar ao .env:  SMTP_KEY=<chave gerada com bin2hex(random_bytes(32))>
$_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';

if (empty($_smtpKey)) { ?>
    <script>
    swal({ title: 'Configuração em falta!',
           text: 'A variável SMTP_KEY não está definida no .env.',
           icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php exit; }

// ── 9. Inserir na BD ──────────────────────────────────────────────────────────
// CORREÇÃO: a query original tinha 8 colunas mas só 7 valores
// (faltava o placeholder de codigo_acesso_qr). Isso fazia o prepare() falhar
// e o bind_param() seguinte rebentar com um Fatal Error -> página em branco,
// nada gravado, sem redirect.
// Agora o codigo_acesso_qr é gravado já no próprio INSERT, eliminando
// também a necessidade do UPDATE extra a seguir.
$stmt_ins = $db->prepare("
    INSERT INTO settings
        (email_user, pass, email_smtp, email_smtpport,
         nome_app, sessao_timeout, tempoduracaopass, codigo_acesso_qr)
    VALUES
        (?, AES_ENCRYPT(?, ?), ?, ?, ?, ?, ?, ?)
");

if ($stmt_ins === false) {
    // Se o prepare falhar, mostra o erro em vez de rebentar com fatal error
    error_log("Erro no prepare do INSERT settings: " . $db->error); ?>
    <script>
    swal({ title: 'Erro interno!',
           text: 'Não foi possível preparar a operação. Contacte o suporte.',
           icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php
    exit;
}

// Marcadores: 1=email_user 2=pass 3=smtpKey 4=email_smtp 5=email_smtpport
//             6=nome_app 7=sessao_timeout 8=tempoduracaopass 9=codigo_acesso_qr
// Tipos:        s          s      s         s               i
//               s          i               i                  s
$stmt_ins->bind_param(
    "ssssisiis",
    $em,          // s — email_user
    $_pa,         // s — pass (texto, antes do AES_ENCRYPT)
    $_smtpKey,    // s — chave AES (2.º arg de AES_ENCRYPT)
    $_smtp,       // s — email_smtp
    $_smtpport,   // i — email_smtpport
    $_nome,       // s — nome_app
    $_sessao,     // i — sessao_timeout
    $_tempdur,    // i — tempoduracaopass
    $_codigo_qr   // s — codigo_acesso_qr
);

$ok = $stmt_ins->execute();
$novo_id = $db->insert_id;
$stmt_ins->close();

if ($ok && $novo_id > 0) {
    mysqli_close($db); ?>
    <script>
    swal({ title: 'Os dados foram guardados!', icon: 'success' })
    .then(function(){ window.location = "<?php echo SVRURL ?>emsess"; });
    </script>
<?php
} else {
    error_log("Erro ao inserir settings: " . $db->error); ?>
    <script>
    swal({ title: 'Erro ao guardar!',
           text: 'Ocorreu um erro na base de dados. Tente novamente.',
           icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>inseriremse"; });
    </script>
<?php
}

?>

<br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
