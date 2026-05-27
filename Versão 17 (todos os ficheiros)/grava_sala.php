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
// Gerar token CSRF se ainda não existir (fora do if, para cobrir sessões já iniciadas)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>
     <?php include("sessao_timeout.php"); ?>

<?php
$idescola = (int)base64_decode($_GET['ie']);

// Validar token CSRF
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
?>
<script>
swal({
    title: 'Ação não autorizada!',
    text: 'Token de segurança inválido ou expirado.',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>";
});
</script>
<?php
    exit;
}

// Validar escola
$stmt_esc = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt_esc->bind_param("i", $idescola);
$stmt_esc->execute();
$row_esc = $stmt_esc->get_result()->fetch_row();
$stmt_esc->close();

$ne = $row_esc[0] ?? '';

// Validar parâmetros
if (
    empty($idescola) || $idescola < 1 || empty($ne)
    || !isset($_POST['nome']) || empty(trim($_POST['nome']))
    || !isset($_POST['localizacao']) || empty(trim($_POST['localizacao']))
    || !isset($_POST['eqreq']) || empty($_POST['eqreq'])
) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>';
}, 10);
</script>
<?php
    exit;
}
?>

      <!-- about -->
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
                     <li style="color:#1e2a45;">Salas &gt;&gt; Inserir</li>
                  </ol>
               </nav>
               <div class="titlepage">
                  <h2><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></h2>
               </div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

<?php
if ($_SESSION['tipo'] != 1) {
?>
<script>
swal({
    title: 'Não tem permissão!',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>";
});
</script>
<?php
    exit;
}

$nome       = trim($_POST['nome']);
$local      = trim($_POST['localizacao']);
$depart     = trim($_POST['departamento'] ?? '');
$eqreq      = $_POST['eqreq'];

// Verificar se já existe sala com o mesmo nome nesta escola
$stmt_chk = $db->prepare("SELECT COUNT(*) FROM salas WHERE nome = ? AND id_escola = ?");
$stmt_chk->bind_param("si", $nome, $idescola);
$stmt_chk->execute();
$rows_chk = $stmt_chk->get_result()->fetch_row();
$stmt_chk->close();

if ($rows_chk[0] > 0) {
?>
<script>
swal({
    title: 'Já existe!',
    text: 'Já existe uma sala com esse nome nesta instituição!',
    icon: 'warning',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>";
});
</script>
<?php
    exit;
}

// Inserir
$stmt_ins = $db->prepare("INSERT INTO salas (nome, localizacao, departamento, id_escola, equip_requisitavel) VALUES (?, ?, ?, ?, ?)");
$stmt_ins->bind_param("sssss", $nome, $local, $depart, $idescola, $eqreq);
$stmt_ins->execute();
$stmt_ins->close();
mysqli_close($db);
?>
<script>
swal({
    title: 'Os dados foram guardados!',
    icon: 'success',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>";
});
</script>

<?php
?>

<br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
