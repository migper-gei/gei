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

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>
     <?php include("sessao_timeout.php"); ?>

<script>
function confirmarEliminar(n) {
    event.preventDefault();
    swal({
        title: "Deseja eliminar?",
        text: "Nº requisição: " + n,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            window.setTimeout(function() {
                window.location.href = '<?php echo SVRURL ?>eliminarequi/' + n;
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<?php

if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}

$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;
$em              = $_SESSION['email'];

$stmt = mysqli_prepare($db,
    "SELECT r.id as rid, r.*, s.*
     FROM requisicao r, salas s
     WHERE s.id = r.id_sala AND email_util = ?
     ORDER BY r.datautil, r.horainicio
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'sii', $em, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(*) FROM requisicao WHERE email_util = ?"
);
mysqli_stmt_bind_param($stmt_total, 's', $em);
mysqli_stmt_execute($stmt_total);
$row_total   = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
$totalLinhas = (int) ($row_total[0] ?? 0);

$totalPages = ($limit > 0) ? (int) ceil($totalLinhas / $limit) : 1;
$prev = $page - 1;
$next = $page + 1;
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamentos</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Minhas requisições</li>
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

<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:20px; }
.gei-table-section-header { padding:10px 16px; background:#182848; color:#fff; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#253d6e; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:top; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-badge-entregue { background:#e6f9f0; color:#1a7f4b; border-color:#a8e6c8; }
.gei-badge-pend     { background:#fff8e1; color:#b07d00; border-color:#ffe082; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-action-btn { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:7px; font-size:.78rem; font-weight:700; text-decoration:none !important; border:1.5px solid; transition:background .15s; }
.gei-btn-edit { border-color:#4b6cb7 !important; background:#e8f0fe !important; color:#4b6cb7 !important; }
.gei-btn-edit:hover { background:#4b6cb7 !important; color:#fff !important; }
.gei-btn-del  { border-color:#c0392b !important; background:#fdecea !important; color:#c0392b !important; }
.gei-btn-del:hover  { background:#c0392b !important; color:#fff !important; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:120px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<!-- Nº de linhas por página -->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
    <form action="<?php echo SVRURL ?>myrequi" method="post">
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

<?php if ($totalLinhas === 0): ?>
<div style="padding:30px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não foram encontradas requisições registadas.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        Minhas requisições
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th style="width:6%">Nº</th>
                <th style="width:16%">Data requisição</th>
                <th style="width:28%">Utilização / Sala / Horas</th>
                <th style="width:34%">Equipamentos</th>
                <th style="width:16%"></th>
            </tr>
        </thead>
        <tbody>
<?php while ($row2 = mysqli_fetch_array($result)):
    $n        = $row2['rid'];
    $entregue = ($row2['dataentrega'] != null);
?>
            <tr>
                <td data-label="Nº">
                    <span style="font-weight:700;"><?php echo htmlspecialchars($n, ENT_QUOTES, 'UTF-8'); ?></span>
                </td>

                <td data-label="Data requisição">
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row2['datarequi'])); ?></span>
                </td>

                <td data-label="Utilização / Sala / Horas">
                    <span class="gei-label">Data de utilização</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row2['datautil'])); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Sala</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row2['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Horas</span>
                    <span style="font-family:monospace;font-size:.82rem;">
                        <?php echo htmlspecialchars($row2['horainicio'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($row2['horafim'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </td>

                <td data-label="Equipamentos">
                    <?php
                    $stmt3 = mysqli_prepare($db,
                        "SELECT e.nomeequi FROM equip_requisitado er, equipamento e
                         WHERE er.id_equip = e.id AND er.id_req = ?"
                    );
                    mysqli_stmt_bind_param($stmt3, 'i', $n);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);
                    $equips  = [];
                    while ($row3 = mysqli_fetch_array($result3)) {
                        $equips[] = htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8');
                    }
                    echo implode(' &nbsp;|&nbsp; ', $equips);
                    ?>
                    <div style="margin-top:8px;">
                        <?php if ($entregue): ?>
                            <span class="gei-badge gei-badge-entregue">&#10003; Entregue</span>
                            <span style="font-family:monospace;font-size:.78rem;color:#7b88a0;display:block;margin-top:4px;">
                                <?php echo date('d/m/Y', strtotime($row2['dataentrega'])); ?>
                            </span>
                        <?php else: ?>
                            <span class="gei-badge gei-badge-pend">&#9679; Por entregar</span>
                        <?php endif; ?>
                    </div>
                </td>

                <td data-label="Ações" style="vertical-align:middle;">
                    <?php if (!$entregue): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <a class="gei-action-btn gei-btn-edit"
                           href="<?php echo SVRURL ?>atualiza_requisicao.php?ri=<?php echo base64_encode($n); ?>"
                           title="Atualizar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Atualizar
                        </a>
                        <a class="gei-action-btn gei-btn-del"
                           onclick="confirmarEliminar(<?php echo $n; ?>);"
                           href="<?php echo SVRURL ?>eliminarequi"
                           title="Eliminar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            Eliminar
                        </a>
                    </div>
                    <?php endif; ?>
                </td>
            </tr>
<?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Ordenar -->
<div style="font-size:.8rem;color:#7b88a0;margin-bottom:14px;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="vertical-align:middle;margin-right:4px;">
    Clicar na coluna para ordenar.
</div>

<?php include "realcelinhatabela.php"; ?>

<!-- Paginação -->
<nav aria-label="Paginação">
    <ul class="pagination justify-content-center">

        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page <= 1) { echo '#'; } else {
                   echo SVRURL . 'myrequi?page=' . $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>myrequi?page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'myrequi?page=' . $next;
               } ?>">&gt;&gt;</a>
        </li>

        <li class="page-item">
            <?php echo str_repeat("&nbsp;", 5); echo "TOTAL: " . $totalLinhas; ?>
        </li>

    </ul>
</nav>

<?php endif; ?>

<?php include ("jquery_bootstrap.php"); ?>
<?php mysqli_close($db); ?>

<a href="<?php echo SVRURL ?>equip">
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

   </body>
</html>
