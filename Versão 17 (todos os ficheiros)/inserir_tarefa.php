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

// [CORRIGIDO] Token CSRF único por sessão (não gerar um segundo token local $token)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
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

     <?php
include ("css_inserir.php");
include("sessao_timeout.php");

// [CORRIGIDO] Prepared statement em vez de query directa (proteção SQL injection)
$stmt_maxesc = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt_maxesc->execute();
$maxesc = (int)$stmt_maxesc->get_result()->fetch_row()[0];
$stmt_maxesc->close();

$idescola = (int)base64_decode($_GET['ti'] ?? '');

if (empty($idescola) || !is_numeric($idescola)) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>';
}, 10);
</script>
<?php
}

if ($idescola > $maxesc) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>';
}, 40);
</script>
<?php
}

// [CORRIGIDO] Prepared statement em vez de interpolação directa de $idescola
$stmt_esc = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt_esc->bind_param("i", $idescola);
$stmt_esc->execute();
$rows11 = $stmt_esc->get_result()->fetch_row();
$stmt_esc->close();
$ne = $rows11[0] ?? '';
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
                     <li style="color:#1e2a45;">Tarefas a realizar &rsaquo; Inserir</li>
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

               <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin:14px 0 10px;padding:10px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                  <span style="display:inline-flex;align-items:center;gap:7px;font-size:1.05rem;font-weight:700;color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
               </div>

<div class="form-container">
<div class="step-indicator">
    <i class="fas fa-info-circle mr-2"></i>
    Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>

<!-- [CORRIGIDO] O form envia csrf_token (igual ao que o servidor valida).
     Antes enviava $token (variável local diferente) e o servidor nunca validava. -->
<form name="tar" class="needs-validation" novalidate
      action="<?php echo SVRURL ?>grava_tarefa.php?ti=<?php echo base64_encode($idescola) ?>" method="post">

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

<br>

<?php
// [CORRIGIDO] Prepared statement em vez de interpolação directa de $idescola
$stmt_salas = $db->prepare("SELECT DISTINCT nome, id FROM salas WHERE id_escola = ? ORDER BY nome");
$stmt_salas->bind_param("i", $idescola);
$stmt_salas->execute();
$resulta = $stmt_salas->get_result();
$stmt_salas->close();
?>

<label>Sala: </label>
<select style="width:100%;height:35px;" class="form-control required-field" name="salatar" required>
    <option value="">-- Selecione --</option>
<?php while ($rowa = $resulta->fetch_assoc()): ?>
    <option value="<?php echo (int)$rowa['id']; ?>"><?php echo htmlspecialchars($rowa['nome'], ENT_QUOTES, 'UTF-8'); ?></option>
<?php endwhile; ?>
</select>

<br><br>
<label>Descrição: </label><br>
<textarea class="form-control required-field" required style="width:100%" rows="5" cols="70" name="descricao"></textarea>
<br><br>

<label>Urgência: </label><br>
<select style="width:100%;height:35px;" class="form-control required-field" name="urgencia" required>
    <option value="">-- Selecione --</option>
    <option value="Alta">Alta</option>
    <option value="Média">Média</option>
    <option value="Baixa">Baixa</option>
</select>
<br><br>

<label>Criado por: </label><br>
<input class="form-control required-field" type="text" name="criado_por" required style="width:100%"/><br><br>

<label>Data: </label><br>
<input style="width:100%" class="form-control required-field" required
       value="<?php echo date('Y-m-d'); ?>"
       size="10" type="date" name="data_criacao">
<br><br>

<div style="text-align:center;width:100%">
    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>&nbsp;Inserir tarefa a realizar
    </button>
</div>

</form>
</div>

<a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" title="Voltar">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

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

      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>

      <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
    });
});
      </script>
</body>
</html>
