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

// ── 4. Obter tipo de equipamento ─────────────────────────────────────────────
if ( $x === 2 && ( empty($_POST['tipoeq']) || !isset($_POST['tipoeq']) ) ) {
    if ( empty($_GET["tie"]) || !isset($_GET["tie"]) ) {
        echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
        exit;
    }
    $teq = base64_decode($_GET["tie"]);
} elseif ( $x === 0 ) {
    if ( empty($_POST["tipoeq"]) || !isset($_POST["tipoeq"]) ) {
        echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
        exit;
    }
    $teq = $_POST["tipoeq"];
} elseif ( $x === 1 ) {
    if ( empty($_GET["tie"]) || !isset($_GET["tie"]) ) {
        echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
        exit;
    }
    $teq = base64_decode($_GET["tie"]);
} else {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 5. Nome da escola ─────────────────────────────────────────────────────────
$stmt_ne = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_ne, 'i', $idescola);
mysqli_stmt_execute($stmt_ne);
$rows11 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_ne));
if (!$rows11) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ne = $rows11[0];

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
    "SELECT ar.ano_letivo, ar.periodo, COUNT(*) AS c,
            p.data_inicio, p.data_fim
     FROM avarias_reparacoes ar
     INNER JOIN equipamento eq ON eq.id = ar.id_equi
     LEFT JOIN periodos p ON p.ano_lectivo = ar.ano_letivo AND p.num_periodo = ar.periodo
     WHERE eq.tipo = ? AND ar.id_escola = ?
     GROUP BY ar.ano_letivo, ar.periodo, p.data_inicio, p.data_fim
     ORDER BY ar.ano_letivo DESC, ar.periodo
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'siii', $teq, $idescola, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── 8. Total para paginação ──────────────────────────────────────────────────
$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(DISTINCT CONCAT(ar.ano_letivo, ar.periodo)) AS total
     FROM avarias_reparacoes ar
     INNER JOIN equipamento eq ON eq.id = ar.id_equi
     WHERE eq.tipo = ? AND ar.id_escola = ?"
);
mysqli_stmt_bind_param($stmt_total, 'si', $teq, $idescola);
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
                     <li style="color:#1e2a45;">Nº de avarias por tipo de equipamento</li>
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

                  <!-- Card tipo equipamento + instituição -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Tipo de equipamento</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($teq, ENT_QUOTES, 'UTF-8'); ?></span>
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
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-badge-sala { background:#f3e8fe; color:#6a1b9a; border:1.5px solid #d7b8f5; margin:2px 3px 2px 0; display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:5px; font-size:.76rem; font-weight:700; }
.gei-salas-count { font-size:.75rem; font-weight:600; color:#7b88a0; margin-left:2px; }
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
        <input type="hidden" name="tipoeq" value="<?php echo htmlspecialchars($teq, ENT_QUOTES); ?>">
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
    Não foram encontradas avarias para este tipo de equipamento.
</div>

<?php else: ?>

<!-- Tabela de avarias -->
<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Avarias por ano / período de tempo
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Ano</th>
                <th>Período de tempo</th>
                <th class="js-sort-number">Nº avarias</th>
                <th>Salas afetadas</th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = mysqli_fetch_array($result)): ?>
            <tr>
                <td data-label="Ano letivo" style="font-weight:700;"><?php echo htmlspecialchars($row['ano_letivo'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-label="Período / Semestre">
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                        <span class="gei-badge"><?php echo htmlspecialchars($row['periodo'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if (!empty($row['data_inicio']) || !empty($row['data_fim'])): ?>
                        <span style="font-size:.76rem;color:#7b88a0;font-weight:600;">
                            <?php
                            if (!empty($row['data_inicio'])) echo date('d/m/Y', strtotime($row['data_inicio']));
                            if (!empty($row['data_inicio']) && !empty($row['data_fim'])) echo ' &ndash; ';
                            if (!empty($row['data_fim']))    echo date('d/m/Y', strtotime($row['data_fim']));
                            ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </td>
                <td data-label="Nº avarias">
                    <span class="gei-badge"><?php echo (int) $row['c']; ?></span>
                </td>
                <td data-label="Salas afetadas">
                <?php
                    // Salas distintas com contagem — prepared statement
                    $stmt3 = mysqli_prepare($db,
                        "SELECT DISTINCT s.nome, s.id
                         FROM avarias_reparacoes ar
                         INNER JOIN equipamento eq ON eq.id = ar.id_equi
                         INNER JOIN salas s ON s.id = ar.id_sala
                         WHERE eq.tipo = ? AND ar.ano_letivo = ? AND ar.periodo = ? AND ar.id_escola = ?
                         ORDER BY s.nome"
                    );
                    mysqli_stmt_bind_param($stmt3, 'ssii', $teq, $row['ano_letivo'], $row['periodo'], $idescola);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);

                    while ($row3 = mysqli_fetch_array($result3)):
                        // Contagem por sala — prepared statement
                        $stmt4 = mysqli_prepare($db,
                            "SELECT COUNT(*) AS cc
                             FROM avarias_reparacoes ar
                             INNER JOIN equipamento eq ON eq.id = ar.id_equi
                             WHERE eq.tipo = ? AND ar.ano_letivo = ? AND ar.periodo = ? AND ar.id_sala = ? AND ar.id_escola = ?"
                        );
                        mysqli_stmt_bind_param($stmt4, 'ssiii', $teq, $row['ano_letivo'], $row['periodo'], $row3['id'], $idescola);
                        mysqli_stmt_execute($stmt4);
                        $rows4 = mysqli_fetch_row(mysqli_stmt_get_result($stmt4));
                ?>
                    <span class="gei-badge-sala">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <?php echo htmlspecialchars($row3['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        <span class="gei-salas-count">(<?php echo (int) $rows4[0]; ?>)</span>
                    </span>
                <?php endwhile; ?>
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
                   echo SVRURL . 'num_avarias_tipoeq.php?x=' . base64_encode(2)
                      . '&&tie=' . base64_encode($teq)
                      . '&&ies=' . base64_encode($idescola)
                      . '&&page='. $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>num_avarias_tipoeq.php?x=<?php echo base64_encode(2); ?>&&tie=<?php echo base64_encode($teq); ?>&&ies=<?php echo base64_encode($idescola); ?>&&page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'num_avarias_tipoeq.php?x=' . base64_encode(2)
                      . '&&tie=' . base64_encode($teq)
                      . '&&ies=' . base64_encode($idescola)
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
