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
               <div class="col-md-8 offset-md-2">

<?php

// Apenas administradores
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) { ?>
    <script>
    swal({ title: 'Sem permissão', text: 'Não tem permissão para esta operação.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>equip"; });
    </script>
<?php
    mysqli_close($db);
    exit;
}

// Recolher e validar parâmetros
$sala_origem   = isset($_POST['sala_origem'])    ? (int)$_POST['sala_origem']    : 0;
$idescola      = isset($_POST['idescola'])        ? (int)$_POST['idescola']        : 0;
$sala_destino  = isset($_POST['sala_destino_val'])? (int)$_POST['sala_destino_val']: 0;
$equips        = isset($_POST['equip'])           ? $_POST['equip']                : [];

if ($sala_origem <= 0 || $idescola <= 0 || $sala_destino <= 0 || empty($equips)) { ?>
    <script>
    swal({ title: 'Dados inválidos', text: 'Parâmetros em falta ou inválidos.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>equip"; });
    </script>
<?php
    mysqli_close($db);
    exit;
}

// Validar que a sala de destino existe
$res_sd = mysqli_query($db, "SELECT id FROM salas WHERE id=$sala_destino");
if (mysqli_num_rows($res_sd) == 0) { ?>
    <script>
    swal({ title: 'Sala inválida', text: 'A sala de destino não existe.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>equip"; });
    </script>
<?php
    mysqli_close($db);
    exit;
}

// Sanitizar array de IDs (apenas inteiros positivos pertencentes à sala de origem)
$ids_validos = [];
foreach ($equips as $eid) {
    $eid = (int)$eid;
    if ($eid <= 0) continue;
    // Confirmar que o equipamento pertence à sala de origem
    $chk = mysqli_query($db, "SELECT id FROM equipamento WHERE id=$eid AND id_sala=$sala_origem");
    if (mysqli_num_rows($chk) > 0) {
        $ids_validos[] = $eid;
    }
}

if (empty($ids_validos)) { ?>
    <script>
    swal({ title: 'Nenhum equipamento válido', text: 'Os equipamentos selecionados não pertencem à sala de origem.', icon: 'error' })
    .then(function(){ window.location = "<?php echo SVRURL ?>equip"; });
    </script>
<?php
    mysqli_close($db);
    exit;
}

// Executar UPDATE em massa com prepared statement
$ids_str   = implode(',', $ids_validos);  // seguro: são todos inteiros
$stmt = $db->prepare("UPDATE equipamento SET id_sala = ? WHERE id IN ($ids_str)");
$stmt->bind_param("i", $sala_destino);
$stmt->execute();
$afetados = $stmt->affected_rows;
$stmt->close();

mysqli_close($db);

$url_voltar = SVRURL . 'verequipsala?x=' . base64_encode(1)
            . '&&si='  . base64_encode($sala_origem)
            . '&&ies=' . base64_encode($idescola);

if ($afetados > 0): ?>
<script>
swal({
    title: 'Equipamentos movidos!',
    text: '<?php echo $afetados; ?> equipamento(s) movido(s) com sucesso.',
    icon: 'success'
}).then(function(){
    window.location = "<?php echo $url_voltar ?>";
});
</script>
<?php else: ?>
<script>
swal({
    title: 'Sem alterações',
    text: 'Nenhum equipamento foi movido. Verifique se já estavam na sala de destino.',
    icon: 'info'
}).then(function(){
    window.location = "<?php echo $url_voltar ?>";
});
</script>
<?php endif; ?>

               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
