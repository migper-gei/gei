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
      <!-- loader 
      <?php include("loader.php"); ?> -->
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
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
                     <li style="color:#1e2a45;">Períodos >> Inserir</li>
                  </ol>
               </nav>
               <div class="titlepage">
                    
                  </div>
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
// --- Validação CSRF ---
// CORRIGIDO: nome do token unificado para 'csrf_token' (era 'token')
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    ?>
    <script>
    swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>inserirper"; });
    </script>
    <?php
    exit;
}
// CORRIGIDO: limpar o token correto da sessão (era 'token')
unset($_SESSION['csrf_token']);

// --- Validação de campos obrigatórios ---
if (empty($_POST['anoletivo']) || empty($_POST['periodo']) || empty($_POST['datai']) || empty($_POST['dataf'])) {
?>
<script>
window.setTimeout(function() { window.location.href = '<?php echo SVRURL ?>peri'; }, 10);
</script>
<?php
    exit;
}
{
?>







<?php
// --- Sanitização e validação dos inputs ---
$al = trim(htmlspecialchars($_POST["anoletivo"] ?? '', ENT_QUOTES, 'UTF-8'));
$np = (int)($_POST["periodo"] ?? 0);
$di = trim($_POST["datai"] ?? '');
$df = trim($_POST["dataf"] ?? '');

// Validar formato das datas (YYYY-MM-DD)
$diObj = DateTime::createFromFormat('Y-m-d', $di);
$dfObj = DateTime::createFromFormat('Y-m-d', $df);

if (!$diObj || !$dfObj || $diObj->format('Y-m-d') !== $di || $dfObj->format('Y-m-d') !== $df) {
?>
<script>
swal({ title: 'Datas inválidas!', text: 'As datas introduzidas não têm um formato válido.', icon: 'error' })
.then(function() { window.location = "<?php echo SVRURL ?>inserirper"; });
</script>
<?php
    exit;
}

if ($dfObj <= $diObj) {
?>
<script>
swal({ title: 'A data final deve ser superior à data inicial!', icon: 'error' })
.then(function() { window.location = "<?php echo SVRURL ?>inserirper"; });
</script>
<?php
    exit;
}

if (empty($al) || $np < 1 || $np > 5) {
?>
<script>
swal({ title: 'Dados inválidos!', text: 'Ano letivo ou período inválido.', icon: 'error' })
.then(function() { window.location = "<?php echo SVRURL ?>inserirper"; });
</script>
<?php
    exit;
}



$stmt_chk = $db->prepare("SELECT count(*) FROM periodos WHERE ano_lectivo=? AND num_periodo=?");
$stmt_chk->bind_param("si", $al, $np);
$stmt_chk->execute();
$rows_chk = $stmt_chk->get_result()->fetch_row();
$stmt_chk->close();

if ($rows_chk[0] > 0)

{
?>
    <script>
    swal({
title: 'O ano e período já existem!',
text: '<?php echo $al; ?>' + '  -  ' + '<?php echo $np; ?>',
icon: 'error',
})
.then(function() {
window.location = "<?php echo SVRURL ?>inserirper";
});
</script>
<?php
    exit;
}

else
{
    // Verificar sobreposição de datas no mesmo ano letivo
    $stmt_ovl = $db->prepare("
        SELECT num_periodo FROM periodos
        WHERE ano_lectivo = ?
          AND (
              STR_TO_DATE(?, '%Y-%m-%d') <= data_fim
              AND STR_TO_DATE(?, '%Y-%m-%d') >= data_inicio
          )
    ");
    $stmt_ovl->bind_param("sss", $al, $di, $df);
    $stmt_ovl->execute();
    $rows_ovl = $stmt_ovl->get_result()->fetch_row();
    $stmt_ovl->close();

    if ($rows_ovl) {
?>
<script>
swal({
    title: 'Datas sobrepostas!',
    text: 'As datas inseridas sobrepõem-se com o período <?php echo $rows_ovl[0]; ?> do ano <?php echo htmlspecialchars($al, ENT_QUOTES, "UTF-8"); ?>!',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>inserirper";
});
</script>
<?php
        exit;
    } elseif ($_SESSION['tipo'] == 1) {

        $stmt_ins = $db->prepare("INSERT INTO periodos (ano_lectivo,num_periodo,data_inicio,data_fim) VALUES (?,?,STR_TO_DATE(?,'%Y-%m-%d'),STR_TO_DATE(?,'%Y-%m-%d'))");
        $stmt_ins->bind_param("siss", $al, $np, $di, $df);
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
    window.location = "<?php echo SVRURL ?>peri";
});
</script>

<?php
    } else {
?>
<script>
swal({
    title: 'Não pode inserir!',
    text: 'Não tem permissão!',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>peri";
});
</script>

<?php
    }
} // fim else sobreposição/chk
} // fim else POST válido


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
