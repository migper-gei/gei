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

<?php
 include ("head.php");
?>

   </head>

   <!-- body -->
   <body class="main-layout">

     <?php include ("header.php");?>

     <?php
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
                     <li style="color:#1e2a45;">Tarefas a realizar &rsaquo; Atualizar</li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
<?php
include("msg_bemvindo.php");
?>
</div>

<?php

$idescola = (int)base64_decode($_GET["esi"]);
$idtar    = (int)base64_decode($_GET["ti"]);

// ── Validação de campos obrigatórios ──────────────────────────────────────────
if (!isset($_POST['salatar'])    || !isset($_POST['descricao'])   ||
    !isset($_POST['urgencia'])   || !isset($_POST['criado_por'])  ||
    !isset($_POST['data_criacao'])||
    empty($_POST['salatar'])     || empty($_POST['descricao'])    ||
    empty($_POST['urgencia'])    || empty($_POST['criado_por'])   ||
    empty($_POST['data_criacao'])||
    empty($idescola) || !isset($idescola) ||
    empty($idtar)    || !isset($idtar)    || !is_numeric($idescola)) {
?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>tarefas?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>';
    }, 10);
    </script>
<?php
} else {

    // ── Validação CSRF — sempre antes de qualquer operação ────────────────────
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erro: token CSRF inválido.');
    }

    // ── Recolha e sanitização de todos os campos ──────────────────────────────
    $_t_sala  = (int)($_POST['salatar']      ?? 0);
    $_t_desc  = $_POST['descricao']          ?? '';
    $_t_urg   = $_POST['urgencia']           ?? '';
    $_t_criad = $_POST['criado_por']         ?? '';
    $_t_dci   = $_POST['data_criacao']       ?? '';
    $_t_conc  = $_POST['concluido_por']      ?? '';
    $_t_dcf   = trim($_POST['data_conclusao'] ?? '');

    // ── UPDATE: com ou sem data de conclusão ──────────────────────────────────
    if ($_t_dcf !== '') {
        $stmt = $db->prepare(
            "UPDATE tarefas
             SET id_sala=?, descricao=?, urgencia=?, criado_por=?,
                 data_criacao=STR_TO_DATE(?,'%Y-%m-%d'),
                 concluido_por=?,
                 data_conclusao=STR_TO_DATE(?,'%Y-%m-%d')
             WHERE id=?"
        );
        $stmt->bind_param("issssssi",
            $_t_sala, $_t_desc, $_t_urg, $_t_criad,
            $_t_dci, $_t_conc, $_t_dcf, $idtar
        );
    } else {
        $stmt = $db->prepare(
            "UPDATE tarefas
             SET id_sala=?, descricao=?, urgencia=?, criado_por=?,
                 data_criacao=STR_TO_DATE(?,'%Y-%m-%d'),
                 concluido_por=?,
                 data_conclusao=NULL
             WHERE id=?"
        );
        $stmt->bind_param("isssssi",
            $_t_sala, $_t_desc, $_t_urg, $_t_criad,
            $_t_dci, $_t_conc, $idtar
        );
    }

    $stmt->execute();
    $stmt->close();
    mysqli_close($db);
?>

<script>
swal({
    title: 'Os dados foram atualizados!',
    icon: 'success'
}).then(function() {
    window.location = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>&&z=<?php echo base64_encode(1) ?>";
});
</script>

<?php
}
?>

<br><br><br><br><br><br><br>
                    </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php");?>

   </body>
</html>
