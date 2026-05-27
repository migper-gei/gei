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

<?php

// ── 1. Validar presença de x e ies ──────────────────────────────────────────
if ( empty($_GET["x"]) || !isset($_GET["x"]) || empty($_GET["ies"]) || !isset($_GET["ies"]) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$x        = (int) base64_decode($_GET["x"]);
$idescola = (int) base64_decode($_GET["ies"]);

// ── 2. Máximo de escolas ─────────────────────────────────────────────────────
$result2a = mysqli_query($db, "SELECT MAX(id) AS me FROM escolas");
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = (int) $rows2a[0];

// ── 3. Validar x e idescola ──────────────────────────────────────────────────
if ( $x < 0 || $x > 2 || $idescola <= 0 || $idescola > $maxesc ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 4. Obter datas conforme origem ───────────────────────────────────────────
$di = null;
$df = null;

if ( $x === 0 ) {
    if ( empty($_POST['datai']) || empty($_POST['dataf']) ) {
        echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
        exit;
    }
    $di = $_POST['datai'];
    $df = $_POST['dataf'];
} elseif ( $x === 1 || $x === 2 ) {
    if ( empty($_GET['di']) || empty($_GET['df']) ) {
        echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
        exit;
    }
    $di = base64_decode($_GET['di']);
    $df = base64_decode($_GET['df']);
}

// ── 5. Validar formato e lógica das datas ───────────────────────────────────
$datai_ts = strtotime($di);
$dataf_ts = strtotime($df);

if ( !$di || !$df || $datai_ts === false || $dataf_ts === false || $dataf_ts < $datai_ts ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$di_safe       = date('Y-m-d', $datai_ts);
$df_safe       = date('Y-m-d', $dataf_ts);
$idescola_safe = (int) $idescola;

// ── 6. Nome da escola ────────────────────────────────────────────────────────
$stmt_ne = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_ne, 'i', $idescola_safe);
mysqli_stmt_execute($stmt_ne);
$rows11  = mysqli_fetch_row(mysqli_stmt_get_result($stmt_ne));

if (!$rows11) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ne = $rows11[0];

// ── 7. Paginação ─────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}
$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;

// ── 8. Query principal — JOIN com salas para filtrar escola e obter nome ─────
$stmt = mysqli_prepare($db,
    "SELECT ar.id_sala, s.nome AS nome_sala, COUNT(*) AS qta
     FROM avarias_reparacoes ar
     INNER JOIN salas s ON ar.id_sala = s.id
     WHERE ar.dataavaria >= ? AND ar.dataavaria <= ?
     AND s.id_escola = ?
     GROUP BY ar.id_sala, s.nome
     ORDER BY s.nome
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'ssiii', $di_safe, $df_safe, $idescola_safe, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── 9. Total de registos para paginação ──────────────────────────────────────
$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(DISTINCT ar.id_sala) AS total
     FROM avarias_reparacoes ar
     INNER JOIN salas s ON ar.id_sala = s.id
     WHERE ar.dataavaria >= ? AND ar.dataavaria <= ?
     AND s.id_escola = ?"
);
mysqli_stmt_bind_param($stmt_total, 'ssi', $di_safe, $df_safe, $idescola_safe);
mysqli_stmt_execute($stmt_total);
$row_total   = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
$totalLinhas = (int) ($row_total[0] ?? 0);
$totalPages  = ($limit > 0) ? (int) ceil($totalLinhas / $limit) : 1;

$prev = $page - 1;
$next = $page + 1;

// ── 10. Prepared statement reutilizável para reparações ──────────────────────
$stmt_rep = mysqli_prepare($db,
    "SELECT COUNT(*) FROM avarias_reparacoes
     WHERE dataavaria >= ? AND dataavaria <= ?
     AND id_sala = ?
     AND datareparacao IS NOT NULL"
);
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
                     <li style="color:#1e2a45;">Nº de avarias entre datas</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <!-- Welcome -->
                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

                  <!-- Card instituição + período -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <div style="margin-left:auto;display:flex;flex-direction:column;align-items:flex-end;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Período</span>
                        <span style="font-size:.9rem;font-weight:700;color:#182848;">
                           <?php echo date('d/m/Y', $datai_ts); ?> &nbsp;→&nbsp; <?php echo date('d/m/Y', $dataf_ts); ?>
                        </span>
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
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-badge-rep { background:#e8f5e9; color:#2e7d32; border:1.5px solid #b2dfdb; }
.gei-link { color:#1e2a45 !important; text-decoration:none !important; display:flex; align-items:center; gap:8px; font-weight:600; transition:color .15s; }
.gei-link:hover { color:#4b6cb7 !important; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:130px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<br>

<!-- Nº de linhas por página -->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
    <form action="" method="post">
        <input type="hidden" name="datai" value="<?php echo htmlspecialchars($di, ENT_QUOTES); ?>">
        <input type="hidden" name="dataf" value="<?php echo htmlspecialchars($df, ENT_QUOTES); ?>">
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
<!-- Sem resultados -->
<div style="padding:30px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não foram encontradas avarias para o período indicado.
</div>

<?php else: ?>

<!-- Tabela de avarias -->
<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Avarias por sala
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Sala</th>
                <th class="js-sort-number">Nº avarias</th>
                <th class="js-sort-number">Nº reparações</th>
            </tr>
        </thead>
        <tbody>
<?php
while ($row = mysqli_fetch_array($result)) {
    $sa = (int) $row['id_sala'];
    $ns = htmlspecialchars($row['nome_sala'] ?? '', ENT_QUOTES, 'UTF-8');

    // Contar reparações
    mysqli_stmt_bind_param($stmt_rep, 'ssi', $di_safe, $df_safe, $sa);
    mysqli_stmt_execute($stmt_rep);
    $rows_rep = mysqli_fetch_row(mysqli_stmt_get_result($stmt_rep));
    $contarep = (int) ($rows_rep[0] ?? 0);

    $link_sala = SVRURL . 'ver_reparacoes_sala.php?x=1'
               . '&&di='  . base64_encode($di_safe)
               . '&&df='  . base64_encode($df_safe)
               . '&&sa='  . base64_encode($sa)
               . '&&ies=' . base64_encode($idescola_safe);
?>
            <tr>
                <td data-label="Sala">
                    <a class="gei-link" href="<?php echo $link_sala; ?>" title="Ver reparações da sala">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <?php echo $ns; ?>
                    </a>
                </td>
                <td data-label="Nº avarias"><span class="gei-badge"><?php echo (int) $row['qta']; ?></span></td>
                <td data-label="Nº reparações"><span class="gei-badge gei-badge-rep"><?php echo $contarep; ?></span></td>
            </tr>
<?php } ?>
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
                   echo SVRURL . 'num_avarias_entredatas.php?x=' . base64_encode(2)
                      . '&&di='  . base64_encode($di_safe)
                      . '&&df='  . base64_encode($df_safe)
                      . '&&ies=' . base64_encode($idescola_safe)
                      . '&&page='. $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>num_avarias_entredatas.php?x=<?php echo base64_encode(2); ?>&amp;&amp;di=<?php echo base64_encode($di_safe); ?>&amp;&amp;df=<?php echo base64_encode($df_safe); ?>&amp;&amp;ies=<?php echo base64_encode($idescola_safe); ?>&amp;&amp;page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'num_avarias_entredatas.php?x=' . base64_encode(2)
                      . '&&di='  . base64_encode($di_safe)
                      . '&&df='  . base64_encode($df_safe)
                      . '&&ies=' . base64_encode($idescola_safe)
                      . '&&page='. $next;
               } ?>">&gt;&gt;</a>
        </li>

        <li class="page-item">
            <?php echo str_repeat("&nbsp;", 5); echo "TOTAL: " . $totalLinhas; ?>
        </li>

    </ul>
</nav>

<?php endif; // totalLinhas ?>

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
