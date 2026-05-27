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
include ("css_inserir.php");
include("sessao_timeout.php");

$cod    = (int)base64_decode(urldecode($_GET["c"]    ?? ''));
$da1    = base64_decode(urldecode($_GET["da1"]        ?? ''));
$da2    = base64_decode(urldecode($_GET["da2"]        ?? ''));
$ides   = (int)base64_decode(urldecode($_GET["ides"] ?? ''));
$origem = isset($_GET["origem"]) ? trim($_GET["origem"]) : "sala";

if ($origem === "equip") {
    $eq = (int)base64_decode(urldecode($_GET["eq"] ?? ''));
    $sa = 0;
} else {
    $sa = (int)base64_decode(urldecode($_GET["sa"] ?? ''));
    $eq = 0;
}

$sql2a = "SELECT max(id) FROM escolas";
$rows2a = mysqli_fetch_row(mysqli_query($db, $sql2a));
$maxesc = $rows2a[0];

$valido = !($ides > $maxesc || $ides < 0 || !$cod || !is_numeric($cod)
    || empty($ides) || !is_numeric($ides) || empty($da1) || empty($da2)
    || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $da1)
    || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $da2));

if (!$valido) {
    $url_err = ($origem === "equip")
        ? SVRURL.'manutencoes_equip_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&ei='.urlencode(base64_encode($eq)).'&&esm='.urlencode(base64_encode($ides))
        : SVRURL.'manutencoes_sala_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&sai='.urlencode(base64_encode($sa)).'&&esm='.urlencode(base64_encode($ides));
    echo "<script>window.setTimeout(function(){ window.location.href='".$url_err."'; },10);</script>";
    exit();
}

$stmt3 = mysqli_prepare($db,
    "SELECT e.id, e.nomeequi AS noeq, e.tipo, es.nome_escola, s.nome, m.*
     FROM equipamento e, manutencao m, salas s, escolas es
     WHERE e.id=m.id_equi AND e.id_sala=s.id AND s.id_escola=es.id AND m.codigo=?");
mysqli_stmt_bind_param($stmt3, 'i', $cod);
mysqli_stmt_execute($stmt3);
$row3 = mysqli_fetch_array(mysqli_stmt_get_result($stmt3));
mysqli_stmt_close($stmt3);

$id_equi  = $row3['id_equi'];
$data_man = $row3['data_manutencao'];
$tipos_marcados = [];
$stmt_rt = mysqli_prepare($db, "SELECT descricao FROM manutencao WHERE id_equi=? AND data_manutencao=?");
mysqli_stmt_bind_param($stmt_rt, 'is', $id_equi, $data_man);
mysqli_stmt_execute($stmt_rt);
$rt_res = mysqli_stmt_get_result($stmt_rt);
while ($rt = mysqli_fetch_row($rt_res)) { $tipos_marcados[] = $rt[0]; }
mysqli_stmt_close($stmt_rt);

if ($origem === "equip") {
    $url_voltar = SVRURL.'manutencoes_equip_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&ei='.urlencode(base64_encode($eq)).'&&esm='.urlencode(base64_encode($ides));
    $url_ok     = SVRURL.'atualiza_ok_manutencao.php?c='.urlencode(base64_encode($cod)).'&&e='.urlencode(base64_encode($id_equi)).'&&da1='.urlencode(base64_encode($da1)).'&&da2='.urlencode(base64_encode($da2)).'&&eq='.urlencode(base64_encode($eq)).'&&ides='.urlencode(base64_encode($ides)).'&&origem=equip';
} else {
    $url_voltar = SVRURL.'manutencoes_sala_entredatas.php?x='.urlencode(base64_encode(1)).'&&dmi='.urlencode(base64_encode($da1)).'&&dmf='.urlencode(base64_encode($da2)).'&&sai='.urlencode(base64_encode($sa)).'&&esm='.urlencode(base64_encode($ides));
    $url_ok     = SVRURL.'atualiza_ok_manutencao.php?c='.urlencode(base64_encode($cod)).'&&e='.urlencode(base64_encode($id_equi)).'&&da1='.urlencode(base64_encode($da1)).'&&da2='.urlencode(base64_encode($da2)).'&&sa='.urlencode(base64_encode($sa)).'&&ides='.urlencode(base64_encode($ides)).'&&origem=sala';
}
?>
      <div class="about"><div class="container">
         <div class="row"><div class="col-md-12">
            <nav style="margin-bottom:10px;"><ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
               <li style="display:flex;align-items:center;gap:4px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg><span style="color:#4b6cb7;">Manutenções</span></li>
               <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
               <li style="color:#1e2a45;">Atualizar</li>
            </ol></nav>
         </div></div>
         <div class="container"><div class="row"><div class="col-md-10 offset-md-2">
            <div class="welcome-section"><?php include("msg_bemvindo.php"); ?></div>
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin:14px 0 10px;padding:10px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
               <span style="display:inline-flex;align-items:center;gap:6px;font-size:1.05rem;font-weight:700;color:#182848;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg><?php echo htmlspecialchars($row3['noeq'], ENT_QUOTES, 'UTF-8'); ?></span>
               <span style="color:#c5cde0;">|</span>
               <span style="display:inline-flex;align-items:center;gap:6px;font-size:.92rem;font-weight:600;color:#4b6cb7;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg><?php echo htmlspecialchars($row3['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
               <span style="color:#c5cde0;">|</span>
               <span style="display:inline-flex;align-items:center;gap:6px;font-size:.88rem;font-weight:500;color:#5a6a85;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><?php echo htmlspecialchars($row3['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
<script>
function validate() {
    if (!document.querySelector('input[name="m[]"]:checked')) {
        event.preventDefault();
        swal({ title:"Escolha pelo menos um tipo de manutenção!", type:"warning", confirmButtonText:"OK", closeOnConfirm:false });
        return false;
    }
    return true;
}
</script>
<style>@media(max-width:768px){.gei-check-grid{grid-template-columns:1fr !important;}}</style>
<div class="form-container">
<form onsubmit="return validate();" method="post" action="<?php echo $url_ok; ?>" class="needs-validation" novalidate>
<label>Data: </label><br>
<input required style="width:100%" class="form-control required-field" value="<?php echo htmlspecialchars($row3['data_manutencao'], ENT_QUOTES, 'UTF-8'); ?>" size="10" type="date" name="data"><br>
<label>Pessoa que realizou: </label><br>
<input required style="width:100%" class="form-control required-field" type="text" name="pessoa" value="<?php echo htmlspecialchars($row3['pessoa'], ENT_QUOTES, 'UTF-8'); ?>">
<br><br>
Escolha o tipo de manutenção:
<?php $result4 = mysqli_query($db, "SELECT nome FROM tipos_manutencao ORDER BY nome ASC"); ?>
<div style="border:1px solid #e3e8f4;border-radius:8px;padding:10px 12px;background:#fafbfd;margin:8px 0 14px;">
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;" class="gei-check-grid">
<?php while ($row4 = mysqli_fetch_array($result4)): ?>
   <label style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:6px;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='#f0f4fb'" onmouseout="this.style.background='transparent'">
      <input type="checkbox" name="m[]" value="<?php echo htmlspecialchars($row4['nome'], ENT_QUOTES, 'UTF-8'); ?>"
             <?php if (in_array($row4['nome'], $tipos_marcados)) echo 'checked'; ?>
             style="width:16px;height:16px;accent-color:#4b6cb7;flex-shrink:0;">
      <span style="font-size:.88rem;color:#1e2a45;font-weight:500;"><?php echo htmlspecialchars($row4['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
   </label>
<?php endwhile; ?>
   </div>
</div>
<br>
<label>Observações: </label><br>
<textarea rows="5" style="width:100%" class="form-select" name="obs"><?php echo htmlspecialchars($row3['observacoes'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<br><br>
<div style="text-align:center;width:100%">
    <button type="submit" class="btn-submit"><i class="fa-solid fa-pen"></i>&nbsp;Atualizar manutenção</button>
</div>
</form>
</div>





   <a href="<?php echo $url_voltar; ?>  title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
    <br><br>


<br>
<?php include ("jquery_bootstrap.php"); ?>
         </div></div></div>
      </div></div>
      <script>(function(){'use strict';window.addEventListener('load',function(){var forms=document.getElementsByClassName('needs-validation');Array.prototype.filter.call(forms,function(form){form.addEventListener('submit',function(event){if(form.checkValidity()===false){event.preventDefault();event.stopPropagation();}form.classList.add('was-validated');},false);});},false);})();</script>
      <?php include ("footer.php"); ?>
   </body>
</html>
