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
   <head>
<?php include ("head.php"); ?>
   </head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
     <?php include ("header.php"); ?>
     <?php

$sql2a = "select max(id) as me from escolas";
$result2a = mysqli_query($db, $sql2a);
$rows2a = mysqli_fetch_row($result2a);
$maxesc = $rows2a[0];

$x        = (int)base64_decode(urldecode($_GET["x"]));
$idescola = (int)base64_decode(urldecode($_GET["esm"]));

if ($x == 1) {
    $di = base64_decode(urldecode($_GET["dmi"]));
    $df = base64_decode(urldecode($_GET["dmf"]));
    $sa = (int)base64_decode(urldecode($_GET["sai"]));
} elseif ($x == 2 && (empty($_POST['sala']) || !isset($_POST['sala'])
    || empty($_POST['datami']) || !isset($_POST['datami'])
    || empty($_POST['datamf']) || !isset($_POST['datamf'])
)) {
    $di = base64_decode(urldecode($_GET["dmi"]));
    $df = base64_decode(urldecode($_GET["dmf"]));
    $sa = (int)base64_decode(urldecode($_GET["sai"]));
} else {
    if ($idescola > $maxesc || $idescola < 0
        || $x > 1 || $x < 0 || !isset($x) || !is_numeric($x)
        || !isset($idescola) || empty($idescola) || !is_numeric($idescola)
        || !isset($_POST['datami']) || !isset($_POST['datamf']) || !isset($_POST['sala'])
        || empty($_POST['datami']) || empty($_POST['datamf']) || empty($_POST['sala'])
    ) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>manut'; },10);</script>
<?php
    }
}

if ($x == 0) {
    $di = $_POST['datami'];
    $df = $_POST['datamf'];
    $sa = $_POST["sala"];
} elseif ($x == 1) {
    $di = base64_decode(urldecode($_GET["dmi"]));
    $df = base64_decode(urldecode($_GET["dmf"]));
    $sa = (int)base64_decode(urldecode($_GET["sai"]));
}

if (!isset($di) || !isset($df) || !isset($sa)) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>manut'; },10);</script>
<?php
}
?>

     <?php
include("sessao_timeout.php");

$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db, $sql11);
$rows11 = mysqli_fetch_row($result11);
$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);

$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db, $sql10);
$rows10 = mysqli_fetch_row($result10);
$ns = $rows10[0];
$num_ns = mysqli_num_rows($result10);
?>

<?php if ($num_ns==0 || $num_ne==0): ?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>manut'; },10);</script>
<?php endif; ?>

<script>
function a(n) {
    var n1 = n;
    event.preventDefault();
    swal({ title:"Deseja eliminar?", type:"warning", showCancelButton:true, confirmButtonText:"Sim", cancelButtonText:"Não", closeOnConfirm:false, closeOnCancel:false },
    function(isConfirm){
        if (isConfirm) { window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>eliminaman/'+n1; },10); }
        else { swal("Cancelado."); }
    });
}
</script>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">Manutenções</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Sala entre datas</li>
                  </ol>
               </nav>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.88rem; font-weight:700; color:#182848;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                     <?php echo date('d/m/Y', strtotime($di)); ?> — <?php echo date('d/m/Y', strtotime($df)); ?>
                  </span>
                  <span style="color:#c5cde0;">|</span>
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:1.05rem; font-weight:700; color:#182848;">
                     <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                     <?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                  <span style="color:#c5cde0;">|</span>
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
               </div>

<?php
if (isset($_POST['records-limit'])) { $_SESSION['records-limit'] = $_POST['records-limit']; }
$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

$sql = "
SELECT MIN(m.codigo) as codigo, e.id, e.nomeequi, e.tipo, s.nome,
       m.data_manutencao, m.pessoa,
       GROUP_CONCAT(m.descricao ORDER BY m.descricao ASC SEPARATOR '|||') as descricao,
       MIN(m.observacoes) as mobs
FROM manutencao m, equipamento e, salas s
WHERE m.id_equi=e.id AND e.id_sala=s.id
  AND e.id_sala=".$sa." AND m.data_manutencao BETWEEN
  STR_TO_DATE('$di','%Y-%m-%d') AND STR_TO_DATE('$df','%Y-%m-%d')
GROUP BY e.id, e.nomeequi, e.tipo, s.nome, m.data_manutencao, m.pessoa
ORDER BY m.data_manutencao DESC, e.nomeequi ASC
LIMIT $paginationStart, $limit";
$result = mysqli_query($db, $sql);

$sql1 = "SELECT count(*) FROM (
    SELECT e.id, m.data_manutencao, m.pessoa
    FROM manutencao m, equipamento e, salas s
    WHERE m.id_equi=e.id AND e.id_sala=s.id
      AND e.id_sala=".$sa." AND m.data_manutencao BETWEEN
      STR_TO_DATE('$di','%Y-%m-%d') AND STR_TO_DATE('$df','%Y-%m-%d')
    GROUP BY e.id, m.data_manutencao, m.pessoa
) as t";
$result1 = mysqli_query($db, $sql1);
$rows = mysqli_fetch_row($result1);
$totallinhas = $rows[0];
$totoalPages = ceil($totallinhas / $limit);
$prev = $page - 1;
$next = $page + 1;
?>

<div style="font-size:.82rem;color:#5a6a85;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
   <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:16px;">
   Só é possível atualizar/eliminar manutenções do ano corrente.
</div>

<style>
.gei-table-wrap{background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(75,108,183,.10);border:1px solid #e3e8f4;overflow:hidden;margin-bottom:16px;}
.gei-table-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:12px 16px;background:#f4f6fb;border-bottom:1px solid #e3e8f4;}
.gei-table-toolbar-left{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.gei-table{width:100%;border-collapse:collapse;font-size:.84rem;}
.gei-table thead th{padding:10px 14px;background:#182848;color:#fff;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;border:none;white-space:nowrap;}
.gei-table tbody tr{border-bottom:1px solid #eef1f8;transition:background .15s;}
.gei-table tbody tr:last-child{border-bottom:none;}
.gei-table tbody tr:hover{background:#f0f4fb;}
.gei-table tbody tr:nth-child(even){background:#f7f9fe;}
.gei-table tbody tr:nth-child(even):hover{background:#eef2fb;}
.gei-table td{padding:10px 14px;vertical-align:middle;color:#1e2a45;}
.gei-action-btn{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none !important;border:none;cursor:pointer;transition:opacity .15s,transform .12s;white-space:nowrap;margin:2px;}
.gei-action-btn:hover{opacity:.85;transform:translateY(-1px);}
.gei-btn-edit{background:#eef2fb;color:#00509e !important;border:1.5px solid #c7d4f0;}
.gei-btn-delete{background:#fde8e6;color:#c0392b !important;border:1.5px solid #f5c0bb;}
.gei-btn-locked{background:#f4f6fb;color:#aab0bb !important;border:1.5px solid #dde2ec;cursor:not-allowed;opacity:.7;}
.gei-pagination{display:flex;align-items:center;justify-content:center;gap:4px;flex-wrap:wrap;padding:12px 0;}
.gei-page-btn{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:6px;font-size:.8rem;font-weight:600;text-decoration:none !important;border:1.5px solid #e3e8f4;color:#4b6cb7 !important;background:#fff;transition:all .15s;}
.gei-page-btn:hover{background:#eef2fb;border-color:#4b6cb7;}
.gei-page-btn.active{background:#182848;color:#fff !important;border-color:#182848;}
.gei-page-btn.disabled{opacity:.4;pointer-events:none;}
.gei-page-total{font-size:.78rem;color:#7b88a0;font-weight:600;padding:0 10px;}
@media(max-width:768px){
    .gei-table-wrap{border-radius:8px;}
    .gei-table thead{display:none;}
    .gei-table tbody tr{display:block;border:1px solid #e3e8f4;border-radius:8px;margin-bottom:10px;padding:10px 12px;background:#fff;box-shadow:0 1px 6px rgba(75,108,183,.08);}
    .gei-table tbody tr:nth-child(even){background:#fff;}
    .gei-table tbody tr:hover{background:#f5f8ff;}
    .gei-table td{display:flex;align-items:flex-start;gap:8px;padding:5px 2px;border:none;font-size:.83rem;}
    .gei-table td::before{content:attr(data-label);min-width:120px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;padding-top:2px;flex-shrink:0;}
    .gei-table td[data-label="Ações"]{flex-wrap:wrap;gap:6px;padding-top:8px;border-top:1px dashed #e3e8f4;margin-top:4px;}
    .gei-table td[data-label="Ações"]::before{display:none;}
}
</style>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="" method="post" style="margin:0;">
                <input type="hidden" name="datami" value="<?php echo htmlspecialchars($di, ENT_QUOTES); ?>">
                <input type="hidden" name="datamf" value="<?php echo htmlspecialchars($df, ENT_QUOTES); ?>">
                <input type="hidden" name="sala"   value="<?php echo (int)$sa; ?>">
                <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            </form>
        </div>
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Equipamento</th>
                <th>Data</th>
                <th>Descrição / Realizada por</th>
                <th>Observações</th>
                <th style="text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql2_per = "SELECT max(ano_lectivo), min(num_periodo) FROM periodos";
        $result2_per = mysqli_query($db, $sql2_per);
        $rows2_per = mysqli_fetch_row($result2_per);
        $mal = $rows2_per[0]; $mnp = $rows2_per[1];
        $sql2b_per = "SELECT data_inicio FROM periodos WHERE ano_lectivo='$mal' AND num_periodo=$mnp";
        $result2b_per = mysqli_query($db, $sql2b_per);
        $rows2b_per = mysqli_fetch_row($result2b_per);
        $df2b = $rows2b_per[0];

        while ($row = mysqli_fetch_array($result)):
            $dm = $row['data_manutencao'];
            $sql3 = "SELECT count(*) FROM manutencao WHERE id_equi=".$row['id']." AND data_manutencao=STR_TO_DATE('$dm','%Y-%m-%d') AND STR_TO_DATE(data_manutencao,'%Y-%m-%d') > STR_TO_DATE('$df2b','%Y-%m-%d')";
            $result3 = mysqli_query($db, $sql3);
            $rows3 = mysqli_fetch_row($result3);
            $contama = $rows3[0];
        ?>
        <tr>
            <td data-label="Equipamento"><?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Data" style="white-space:nowrap;"><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_manutencao'])), ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Descrição / Realizada por">
                <?php
                $tipos = explode('|||', $row['descricao']);
                foreach ($tipos as $tipo):
                    $tipo = trim($tipo);
                    if ($tipo === '') continue;
                ?>
                <span style="display:inline-block;background:#eef2fb;color:#1e2a45;border:1px solid #c7d4f0;border-radius:5px;padding:2px 8px;font-size:.78rem;font-weight:500;margin:2px 2px 2px 0;">
                    <?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <?php endforeach; ?>
                <?php if ($row['pessoa']): ?><br><small style="color:#7b88a0;"><?php echo htmlspecialchars($row['pessoa'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?>
            </td>
            <td data-label="Observações"><?php echo htmlspecialchars($row['mobs'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
            <?php if (($_SESSION['tipo']==1 || $_SESSION['tipo']==3) && $contama>0): ?>
                <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                   href="atualiza_manutencao.php?c=<?php echo urlencode(base64_encode($row['codigo']))?>&da1=<?php echo urlencode(base64_encode($di))?>&da2=<?php echo urlencode(base64_encode($df))?>&sa=<?php echo urlencode(base64_encode($sa))?>&ides=<?php echo urlencode(base64_encode($idescola))?>&origem=sala">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                </a>
                <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                   onclick="a(<?php echo htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8');?>);"
                   href="<?php echo SVRURL ?>eliminaman">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                </a>
            <?php else: ?>
                <span class="gei-action-btn gei-btn-locked">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Bloqueado
                </span>
            <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($totallinhas == 0): ?>
        <tr>
            <td colspan="99" style="text-align:center;padding:24px;color:#7b88a0;font-size:.88rem;font-style:italic;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="2" stroke-linecap="round" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Sem registos
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>

<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : '?x='.urlencode(base64_encode(2)).'&dmi='.urlencode(base64_encode($di)).'&dmf='.urlencode(base64_encode($df)).'&sai='.urlencode(base64_encode($sa)).'&esm='.urlencode(base64_encode($idescola)).'&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="manutencoes_sala_entredatas.php?x=<?php echo urlencode(base64_encode(2)) ?>&dmi=<?php echo urlencode(base64_encode($di));?>&dmf=<?php echo urlencode(base64_encode($df));?>&sai=<?php echo urlencode(base64_encode($sa));?>&esm=<?php echo urlencode(base64_encode($idescola));?>&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?x='.urlencode(base64_encode(2)).'&dmi='.urlencode(base64_encode($di)).'&dmf='.urlencode(base64_encode($df)).'&sai='.urlencode(base64_encode($sa)).'&esm='.urlencode(base64_encode($idescola)).'&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>

<?php include ("jquery_bootstrap.php"); ?>

<?php
// ── Swal de sucesso após atualização (padrão PRG) ─────────────────────────────
if (!empty($_SESSION['manut_ok'])) {
    unset($_SESSION['manut_ok']);
    ?>
    <script>
    window.addEventListener('load', function() {
        swal({
            title: 'Os dados foram atualizados!',
            type: 'success',
            confirmButtonText: 'OK',
            closeOnConfirm: true
        });
    });
    </script>
    <?php
}
?>

<a href="<?php echo SVRURL ?>manut">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include ("footer.php"); ?>
   </body>
</html>
