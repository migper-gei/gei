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

// ── 1. Validar presença de op e ies ──────────────────────────────────────────
if ( empty($_GET["op"]) || !isset($_GET["op"]) || empty($_GET["ies"]) || !isset($_GET["ies"]) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$op       = $_GET["op"];
$idescola = (int) base64_decode($_GET["ies"]);

// ── 2. Máximo de escolas ─────────────────────────────────────────────────────
$result2a = mysqli_query($db, "SELECT MAX(id) AS me FROM escolas");
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = (int) $rows2a[0];

// ── 3. Validar op e idescola ─────────────────────────────────────────────────
if ( $idescola <= 0 || $idescola > $maxesc || !in_array($op, ['t', 'al']) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 4. Nome da escola ─────────────────────────────────────────────────────────
$stmt_ne = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_ne, 'i', $idescola);
mysqli_stmt_execute($stmt_ne);
$rows11 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_ne));
if (!$rows11) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ne = $rows11[0];

// ── 5. Ano letivo mais recente na tabela periodos ─────────────────────────────
$result3  = mysqli_query($db, "SELECT MAX(ano_lectivo) FROM periodos");
$rows3    = mysqli_fetch_row($result3);
$ano_max  = $rows3[0] ?? null;

$op2 = ($op === 't') ? 'Todos os anos letivos' : 'Ano: ' . htmlspecialchars($ano_max, ENT_QUOTES, 'UTF-8');

// ── 6. Query principal ───────────────────────────────────────────────────────
if ($op === 't') {
    $stmt = mysqli_prepare($db,
        "SELECT ar.id_equi, MAX(ar.ano_letivo) AS ano_letivo, COUNT(*) AS c, e.nomeequi, s.nome AS nome_sala, s.id AS id_sala
         FROM avarias_reparacoes ar
         INNER JOIN equipamento e ON e.id = ar.id_equi
         INNER JOIN salas s ON s.id = e.id_sala
         WHERE ar.id_escola = ?
         GROUP BY ar.id_equi, e.nomeequi, s.nome, s.id
         ORDER BY c DESC, ar.id_equi
         LIMIT 10"
    );
    mysqli_stmt_bind_param($stmt, 'i', $idescola);
} else {
    $stmt = mysqli_prepare($db,
        "SELECT ar.id_equi, ar.ano_letivo, COUNT(*) AS c, e.nomeequi, s.nome AS nome_sala, s.id AS id_sala
         FROM avarias_reparacoes ar
         INNER JOIN equipamento e ON e.id = ar.id_equi
         INNER JOIN salas s ON s.id = e.id_sala
         WHERE ar.ano_letivo = ? AND ar.id_escola = ?
         GROUP BY ar.id_equi, ar.ano_letivo, e.nomeequi, s.nome, s.id
         ORDER BY c DESC, ar.id_equi
         LIMIT 10"
    );
    mysqli_stmt_bind_param($stmt, 'si', $ano_max, $idescola);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$totalLinhas = mysqli_num_rows($result);
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
                     <li style="color:#1e2a45;">Equipamentos com mais avarias (Top 10)</li>
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

                  <!-- Card instituição + filtro ativo -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Filtro</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo $op2; ?></span>
                     </div>
                  </div>

                  <!-- Botões de filtro -->
                  <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
                     <a href="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($idescola); ?>"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:7px;font-size:.82rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='t' ? '#4b6cb7' : '#c7d4f0'; ?>;background:<?php echo $op==='t' ? '#4b6cb7' : '#f4f6fb'; ?>;color:<?php echo $op==='t' ? '#fff' : '#4b6cb7'; ?>;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        Todos os anos
                     </a>
                     <a href="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=al&&ies=<?php echo base64_encode($idescola); ?>"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:7px;font-size:.82rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='al' ? '#4b6cb7' : '#c7d4f0'; ?>;background:<?php echo $op==='al' ? '#4b6cb7' : '#f4f6fb'; ?>;color:<?php echo $op==='al' ? '#fff' : '#4b6cb7'; ?>;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Ano: <?php echo htmlspecialchars($ano_max ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                     </a>
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
.gei-rank { display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; font-size:.75rem; font-weight:800; background:#182848; color:#fff; flex-shrink:0; }
.gei-rank-1 { background:#f5a623; }
.gei-rank-2 { background:#9b9b9b; }
.gei-rank-3 { background:#c07b4a; }
.gei-link { color:#1e2a45 !important; text-decoration:none !important; display:flex; align-items:center; gap:8px; font-weight:600; transition:color .15s; }
.gei-link:hover { color:#4b6cb7 !important; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:110px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<?php if ($totalLinhas === 0): ?>
<div style="padding:30px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não foram encontradas avarias para esta instituição.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Top 10 — equipamentos com mais avarias
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Equipamento</th>
                <th>Sala</th>
                <th>Ano</th>
                <th class="js-sort-number">Nº avarias</th>
            </tr>
        </thead>
        <tbody>
<?php
$rank = 0;
while ($row = mysqli_fetch_array($result)):
    $rank++;
    $rankClass = $rank <= 3 ? ' gei-rank-' . $rank : '';
?>
            <tr>
                <td data-label="#">
                    <span class="gei-rank<?php echo $rankClass; ?>"><?php echo $rank; ?></span>
                </td>
                <td data-label="Equipamento" style="font-weight:700;">
                    <?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td data-label="Sala">
                    <a href="<?php echo SVRURL ?>dashboard_sala.php?si=<?php echo base64_encode($row['id_sala']); ?>&ies=<?php echo base64_encode($idescola); ?>"
                       style="display:inline-flex;align-items:center;gap:5px;color:#4b6cb7;font-weight:600;text-decoration:none;font-size:.82rem;"
                       title="Ver dashboard da sala">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        <?php echo htmlspecialchars($row['nome_sala'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
                <td data-label="Ano letivo">
                    <span class="gei-badge"><?php echo htmlspecialchars($row['ano_letivo'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td data-label="Nº avarias">
                    <a class="gei-link" title="Ver avarias/reparações do equipamento"
                       href="<?php echo SVRURL ?>num_avarias_equipamento.php?x=<?php echo base64_encode(2); ?>&&eq=<?php echo base64_encode($row['id_equi']); ?>&&ies=<?php echo base64_encode($idescola); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span class="gei-badge"><?php echo (int) $row['c']; ?></span>
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
