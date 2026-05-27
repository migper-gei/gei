<?php
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
    if (!isset($_SESSION['_created'])) { $_SESSION['_created'] = time(); }
    elseif (time() - $_SESSION['_created'] > 1800) { session_regenerate_id(true); $_SESSION['_created'] = time(); }
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head><?php include ("head.php"); ?></head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
     <?php include ("header.php"); ?>
     <?php
include("sessao_timeout.php");

$c      = (int)base64_decode(urldecode($_GET["c"]    ?? ''));
$e      = (int)base64_decode(urldecode($_GET["e"]    ?? ''));
$da1    = base64_decode(urldecode($_GET["da1"]        ?? ''));
$da2    = base64_decode(urldecode($_GET["da2"]        ?? ''));
$ides   = (int)base64_decode(urldecode($_GET["ides"] ?? ''));
$origem = isset($_GET["origem"]) ? trim($_GET["origem"]) : "sala";

$eq = (int)base64_decode(urldecode($_GET["eq"] ?? ''));
$sa = (int)base64_decode(urldecode($_GET["sa"] ?? ''));
if ($origem === "equip") { $sa = 0; } else { $eq = 0; }

if ($origem === "equip") {
    $url_retorno = SVRURL.'manutencoes_equip_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&ei='.urlencode(base64_encode($eq)).'&&esm='.urlencode(base64_encode($ides));
} else {
    $url_retorno = SVRURL.'manutencoes_sala_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&sai='.urlencode(base64_encode($sa)).'&&esm='.urlencode(base64_encode($ides));
}

// Validar POST
if (!isset($_POST['pessoa']) || !isset($_POST['data']) || !isset($_POST['m'])
    || empty($_POST['pessoa']) || empty($_POST['data']) || empty($_POST['m'])) {
    echo "<script>window.setTimeout(function(){ window.location.href='".$url_retorno."'; },10);</script>";
    exit();
}

// Validar GET
$maxesc = mysqli_fetch_row(mysqli_query($db, "SELECT max(id) FROM escolas"))[0];
if ($ides > $maxesc || $ides < 0 || !$c || !$e
    || empty($ides) || empty($da1) || empty($da2)
    || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $da1)
    || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $da2)) {
    echo "<script>window.setTimeout(function(){ window.location.href='".$url_retorno."'; },10);</script>";
    exit();
}

// Obter data original
$stmt_orig = mysqli_prepare($db, "SELECT DATE_FORMAT(data_manutencao,'%Y-%m-%d') FROM manutencao WHERE codigo=?");
mysqli_stmt_bind_param($stmt_orig, 'i', $c);
mysqli_stmt_execute($stmt_orig);
$data_orig = mysqli_fetch_row(mysqli_stmt_get_result($stmt_orig))[0];
mysqli_stmt_close($stmt_orig);

$_data = $_POST["data"]   ?? '';
$_pess = $_POST["pessoa"] ?? '';
$_obs  = $_POST["obs"]    ?? '';

// DELETE por id_equi + data original (apaga TODOS os tipos desse dia, evita duplicados)
$stmt_del = $db->prepare("DELETE FROM manutencao WHERE id_equi=? AND DATE_FORMAT(data_manutencao,'%Y-%m-%d')=?");
$stmt_del->bind_param("is", $e, $data_orig);
$stmt_del->execute();
$stmt_del->close();

// INSERT dos tipos seleccionados
foreach ($_POST['m'] as $m) {
    $stmt = $db->prepare("INSERT INTO manutencao (id_equi, data_manutencao, pessoa, descricao, observacoes) VALUES (?, STR_TO_DATE(?,'%Y-%m-%d'), ?, ?, ?)");
    $stmt->bind_param("issss", $e, $_data, $_pess, $m, $_obs);
    $stmt->execute();
    $stmt->close();
}
?>
      <div class="about"><div class="container">
         <div class="row"><div class="col-md-12">
            <nav style="margin-bottom:10px;"><ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
               <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg><span style="color:#4b6cb7;">Manutenções</span></li>
               <li style="color:#c5cde0;">&#8250;</li>
               <li style="color:#1e2a45;">Atualizar</li>
            </ol></nav>
         </div></div>
         <div class="container"><div class="row"><div class="col-md-10 offset-md-2">
            <div class="welcome-section"><?php include("msg_bemvindo.php"); ?></div>
            <br><br><br><br><br><br>
         </div></div></div>
      </div></div>

<?php include ("jquery_bootstrap.php"); ?>

<script>
// SweetAlert 1 — callback como segundo argumento (NÃO usar .then() que é SweetAlert 2)
swal({
    title: 'Os dados foram atualizados!',
    type: 'success',
    confirmButtonText: 'OK',
    closeOnConfirm: false
},
function() {
    window.location.href = "<?php echo $url_retorno; ?>";
});
</script>

      <?php include ("footer.php"); ?>
   </body>
</html>
