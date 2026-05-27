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
      <?php include("loader.php"); ?>

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
                     <li style="color:#1e2a45;">Tarefas a realizar &rsaquo; N.º por instituição</li>
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
if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page  = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

$sql = "select count(*) as ct, e.nome_escola, e.id
        from tarefas t, escolas e
        where t.id_escola=e.id and t.data_conclusao is null
        group by t.id_escola
        order by e.nome_escola
        LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);

$totallinhas = $result->num_rows;
$totoalPages = ceil($totallinhas / $limit);
$prev = $page - 1;
$next = $page + 1;
?>

<style>
.gei-table-wrap {
    background:#fff; border-radius:10px;
    box-shadow:0 2px 12px rgba(75,108,183,.10);
    border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px;
}
.gei-table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px; padding:12px 16px;
    background:#f4f6fb; border-bottom:1px solid #e3e8f4;
}
.gei-table-toolbar-left { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th {
    padding:10px 14px; background:#182848; color:#fff;
    font-size:.75rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.5px; border:none; white-space:nowrap;
}
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background:#eef2fb; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-count-link {
    display:inline-flex; align-items:center; gap:7px;
    font-size:1rem; font-weight:800; color:#182848;
    text-decoration:none;
    background:#d6e4f7; border:2px solid #4b6cb7;
    padding:4px 14px; border-radius:6px;
    transition:background .15s, transform .12s;
}
.gei-count-link svg { stroke:#4b6cb7; }
.gei-count-link:hover { background:#b8d0f0; transform:translateY(-1px); color:#00509e; text-decoration:none; }
.gei-count-link:hover svg { stroke:#00509e; }
.gei-pagination {
    display:flex; align-items:center; justify-content:center;
    gap:4px; flex-wrap:wrap; padding:12px 0;
}
.gei-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:32px; height:32px; padding:0 10px; border-radius:6px;
    font-size:.8rem; font-weight:600; text-decoration:none !important;
    border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff;
    transition:all .15s;
}
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
@media (max-width: 768px) {
    .gei-table-wrap { border-radius:8px; }
    .gei-table thead { display:none; }
    .gei-table tbody tr {
        display:block; border:1px solid #e3e8f4; border-radius:8px;
        margin-bottom:10px; padding:10px 12px; background:#fff;
        box-shadow:0 1px 6px rgba(75,108,183,.08);
    }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table tbody tr:hover { background:#f5f8ff; }
    .gei-table td {
        display:flex; align-items:center; gap:8px;
        padding:5px 2px; border:none; font-size:.83rem;
    }
    .gei-table td::before {
        content: attr(data-label); min-width:140px;
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.4px; color:#7b88a0; flex-shrink:0;
    }
}
</style>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="<?php echo SVRURL ?>tarefas_num_escola.php" method="post" style="margin:0;">
                <?php include("num_linhas.php"); ?>
            </form>
        </div>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Instituição</th>
                <th style="text-align:center;width:20%">N.º de tarefas</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_array($result)): ?>
        <tr>
            <td data-label="Instituição" style="font-weight:500;">
                <?php echo htmlspecialchars($row['nome_escola'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
            <td data-label="N.º de tarefas" style="text-align:center;">
                <a class="gei-count-link" title="Ver tarefas a realizar"
                   href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode(0) ?>&&esi=<?php echo base64_encode($row['id']) ?>">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <?php echo htmlspecialchars($row['ct'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </td>
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
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : '?page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>tarefas_num_escola.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>

<a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&&z=<?php echo base64_encode(0) ?>">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<?php include ("jquery_bootstrap.php"); ?>

<br>

                    </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php");?>

   </body>
</html>
