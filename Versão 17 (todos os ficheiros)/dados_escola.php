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
<?php include ("head.php"); ?>
   </head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
     <?php include ("header.php");?>
     <?php include("sessao_timeout.php"); ?>
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">›</li>
                     <li style="color:#1e2a45;">Dados da(s) Instituição(ões)</li>
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
if(isset($_POST['records-limit'])){ $_SESSION['records-limit'] = $_POST['records-limit']; }
$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;
$sql = "select * from escolas order by id LIMIT $paginationStart, $limit";
$result = mysqli_query($db, $sql);
$totallinhas = $result->num_rows;
$totoalPages = ceil($totallinhas / $limit);
$prev = $page - 1;
$next = $page + 1;
?>
<script>
function a(n,s) {
    var n1, s1; n1 = n; s1 = s;
    event.preventDefault();
    swal({ title: "Deseja eliminar?", text: "Instituição: " + s1, type: "warning", showCancelButton: true, confirmButtonText: "Sim", cancelButtonText: "Não", closeOnConfirm: false, closeOnCancel: false },
    function(isConfirm) {
        if (isConfirm) { window.setTimeout(function() { window.location.href = '<?php echo SVRURL ?>eliminaesc/' + n1; }, 10); }
        else { swal("Cancelado."); }
    });
}
</script>
<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px; }
.gei-table-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:12px 16px; background:#f4f6fb; border-bottom:1px solid #e3e8f4; }
.gei-table-toolbar-left  { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#182848; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; white-space:nowrap; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background:#eef2fb; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-action-btn { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600; text-decoration:none !important; border:none; cursor:pointer; transition:opacity .15s,transform .12s; white-space:nowrap; }
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-locked { background:#f4f6fb; color:#aab0bb !important; border:1.5px solid #dde2ec; cursor:not-allowed; opacity:.7; }
.gei-pagination { display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap; padding:12px 0; }
.gei-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:6px; font-size:.8rem; font-weight:600; text-decoration:none !important; border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff; transition:all .15s; }
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
@media (max-width: 768px) {
    .gei-table-wrap { border-radius:8px; }
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; font-size:.83rem; }
    .gei-table td::before { content: attr(data-label); min-width:120px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
    .gei-table td[data-label="Ações"] { flex-wrap:wrap; gap:6px; padding-top:8px; border-top:1px dashed #e3e8f4; margin-top:4px; }
    .gei-table td[data-label="Ações"]::before { display:none; }
}
</style>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 0;margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:7px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e87722" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-size:.8rem;color:#7d4e00;font-weight:500;">Só é possível eliminar instituições sem salas.</span>
    </div>
</div>
<div class="gei-table-wrap">
 
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Nome da Instituição</th>
                <?php if ($_SESSION['tipo']==1): ?><th style="text-align:center;width:18%">Ações</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        while($row = mysqli_fetch_array($result)):
            $i++;
            $id = $row['id'];
            $no = $row['nome_escola'];
            $sql2 = "select count(*) from escolas,salas where salas.id_escola=escolas.id and escolas.id='$id'";
            $result2 = mysqli_query($db,$sql2);
            $rows2   = mysqli_fetch_row($result2);
            $contaid = $rows2[0];
        ?>
        <tr>
            <td data-label="Instituição">
                <span style="font-weight:600;"><?php echo htmlspecialchars($row['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if ($row['id']==1): ?>
                <div style="margin-top:4px;font-size:.78rem;color:#7b88a0;line-height:1.6;">
                    <?php echo htmlspecialchars($row['morada'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php echo htmlspecialchars($row['codigopostal'], ENT_QUOTES, 'UTF-8'); ?>
                     <?php echo htmlspecialchars($row['localidade'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php echo htmlspecialchars($row['telefone'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php endif; ?>
            </td>
            <?php if ($_SESSION['tipo']==1): ?>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                <?php if ($i != 1): ?>
                <a class="gei-action-btn gei-btn-edit" title="Atualizar" href="<?php echo SVRURL ?>atualizaesc/<?php echo base64_encode($id); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                </a>
                <?php if ($contaid == 0): ?>
                <a class="gei-action-btn gei-btn-delete" title="Eliminar" onclick="a(<?php echo (int)$id; ?>,'<?php echo htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?>');" href="<?php echo SVRURL ?>eliminaesc">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Eliminar
                </a>
                <?php else: ?>
                <span class="gei-action-btn gei-btn-locked" title="Com salas associadas">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Bloqueado
                </span>
                <?php endif; ?>
                <?php else: ?>
                <span style="font-size:.75rem;color:#aab0bb;">—</span>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>
<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>" href="<?php echo $page<=1 ? '#' : '?page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>" href="<?php echo SVRURL ?>dadosescola?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>" href="<?php echo $page>=$totoalPages ? '#' : '?page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>
<a href="<?php echo SVRURL ?>configura"><img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar"></a>
<?php include ("jquery_bootstrap.php");?>
<br>
                    </div>
               </div>
            </div>
         </div>
      </div>
      <?php include ("footer.php");?>
   </body>
</html>
