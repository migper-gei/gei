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
function confirmarEntrega(rid, d, esc) {
    event.preventDefault();
    swal({
        title: "Deseja entregar os equipamentos?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            window.location.href = '<?php echo SVRURL ?>entregar_requisicao.php?ir=' + rid + '&d=' + d + '&ies=' + esc;
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<?php

// ── 1. Validar presença de x e ies ──────────────────────────────────────────
if ( empty($_GET["x"]) || !isset($_GET["x"]) || empty($_GET["ies"]) || !isset($_GET["ies"]) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$x   = (int) base64_decode($_GET["x"]);
$esc = (int) base64_decode($_GET["ies"]);

// ── 2. Máximo de escolas ─────────────────────────────────────────────────────
$result2a = mysqli_query($db, "SELECT MAX(id) AS me FROM escolas");
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = (int) $rows2a[0];

// ── 3. Validações básicas ────────────────────────────────────────────────────
if ( $esc <= 0 || $esc > $maxesc || $x < 0 || $x > 1 ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 4. Nome da escola ─────────────────────────────────────────────────────────
$stmt_ne = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_ne, 'i', $esc);
mysqli_stmt_execute($stmt_ne);
$rows11 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_ne));
if (!$rows11) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ne = $rows11[0];

// ── 5. Obter data ─────────────────────────────────────────────────────────────
if ($x === 0) {
    $d = $_POST['data'] ?? '';
} else {
    $d = base64_decode($_GET['d'] ?? '');
}

if ( empty($d) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 6. Paginação ─────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}
$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;

// ── 7. Query principal ───────────────────────────────────────────────────────
$stmt = mysqli_prepare($db,
    "SELECT r.id AS rid, r.*, s.nome, s.id AS sid, u.nome AS nu
     FROM requisicao r
     INNER JOIN salas s ON s.id = r.id_sala
     INNER JOIN utilizadores u ON u.email = r.email_util
     WHERE r.datautil = STR_TO_DATE(?, '%Y-%m-%d')
       AND r.dataentrega IS NULL
       AND s.id_escola = ?
     ORDER BY r.datautil
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'siii', $d, $esc, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── 8. Total de registos ─────────────────────────────────────────────────────
$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(*) FROM requisicao r
     INNER JOIN salas s ON s.id = r.id_sala
     WHERE r.datautil = STR_TO_DATE(?, '%Y-%m-%d')
       AND r.dataentrega IS NULL
       AND s.id_escola = ?"
);
mysqli_stmt_bind_param($stmt_total, 'si', $d, $esc);
mysqli_stmt_execute($stmt_total);
$row_total   = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
$totalLinhas = (int) ($row_total[0] ?? 0);
$totalPages  = ($limit > 0) ? (int) ceil($totalLinhas / $limit) : 1;

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
                        <a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Requisições a terminar no dia</li>
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

                  <!-- Card data + instituição -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Data</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo date('d/m/Y', strtotime($d)); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
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
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-equip-tag { display:inline-flex; align-items:center; padding:2px 8px; border-radius:5px; font-size:.76rem; font-weight:700; background:#f3e8fe; color:#6a1b9a; border:1.5px solid #d7b8f5; margin:2px 3px 2px 0; }
.gei-btn-entregar { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; border-radius:7px; font-size:.8rem; font-weight:700; text-decoration:none; border:1.5px solid #4b6cb7; background:#4b6cb7; color:#fff; transition:background .15s; cursor:pointer; }
.gei-btn-entregar:hover { background:#253d6e; border-color:#253d6e; color:#fff; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:130px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>



<!-- Nº de linhas por página -->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
    <form action="" method="post">
        <input type="hidden" name="data" value="<?php echo htmlspecialchars($d, ENT_QUOTES); ?>">
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
    Não existem requisições a terminar neste dia.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Requisições a terminar no dia
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th style="width:5%">Nº</th>
                <th style="width:22%">Utilizador</th>
                <th style="width:25%">Utilização</th>
                <th style="width:35%">Equipamentos</th>
                <th style="width:13%"></th>
            </tr>
        </thead>
        <tbody>
<?php while ($row2 = mysqli_fetch_array($result)): ?>
            <?php
            $rid = $row2['rid'];
            // Equipamentos da requisição — prepared statement
            $stmt3 = mysqli_prepare($db,
                "SELECT e.nomeequi FROM equip_requisitado er
                 INNER JOIN equipamento e ON e.id = er.id_equip
                 WHERE er.id_req = ?"
            );
            mysqli_stmt_bind_param($stmt3, 'i', $rid);
            mysqli_stmt_execute($stmt3);
            $result3 = mysqli_stmt_get_result($stmt3);
            ?>
            <tr>
                <td data-label="Nº">
                    <span class="gei-badge"><?php echo $rid; ?></span>
                </td>

                <td data-label="Utilizador">
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row2['nu'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <br>
                    <span style="font-size:.78rem;color:#7b88a0;"><?php echo htmlspecialchars($row2['email_util'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <br>
                    <span class="gei-label" style="margin-top:6px;">Data requisição</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row2['datarequi'])); ?></span>
                </td>

                <td data-label="Utilização">
                    <span class="gei-label">Data</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row2['datautil'])); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Sala</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row2['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Horário</span>
                    <span><?php echo htmlspecialchars($row2['horainicio'], ENT_QUOTES, 'UTF-8'); ?> &ndash; <?php echo htmlspecialchars($row2['horafim'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>

                <td data-label="Equipamentos">
                    <?php while ($row3 = mysqli_fetch_array($result3)): ?>
                        <span class="gei-equip-tag">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            <?php echo htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endwhile; ?>
                </td>

                <td data-label="Ação" style="vertical-align:middle;">
                    <a class="gei-btn-entregar"
                       onclick="confirmarEntrega(<?php echo $rid; ?>,'<?php echo $d; ?>',<?php echo $esc; ?>);"
                       href="<?php echo SVRURL ?>entregar_requisicao.php"
                       title="Entregar equipamentos">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Entregar
                    </a>
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
                   echo SVRURL . 'requisicoes_terminar_dia.php?x=' . base64_encode(1)
                      . '&&d='   . base64_encode($d)
                      . '&&ies=' . base64_encode($esc)
                      . '&&page='. $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(1); ?>&&d=<?php echo base64_encode($d); ?>&&ies=<?php echo base64_encode($esc); ?>&&page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'requisicoes_terminar_dia.php?x=' . base64_encode(1)
                      . '&&d='   . base64_encode($d)
                      . '&&ies=' . base64_encode($esc)
                      . '&&page='. $next;
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

<a href="<?php echo SVRURL ?>lista">
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
