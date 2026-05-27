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

// ── 1. Validar presença de parâmetros ────────────────────────────────────────
if ( empty($_GET["si"])  || !isset($_GET["si"])  ||
     empty($_GET["ies"]) || !isset($_GET["ies"]) ||
     empty($_GET["al"])  || !isset($_GET["al"])  ||
     empty($_GET["p"])   || !isset($_GET["p"])   ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 2. Descodificar parâmetros ───────────────────────────────────────────────
// NOTA: $al é string (ex: "2024/2025") — NÃO fazer cast (int)
$sa       = (int)    base64_decode($_GET["si"]);
$al       =          base64_decode($_GET["al"]);   // string, ano letivo
$per      = (int)    base64_decode($_GET["p"]);
$idescola = (int)    base64_decode($_GET["ies"]);

// ── 3. Máximo de escolas ─────────────────────────────────────────────────────
$result2a = mysqli_query($db, "SELECT MAX(id) AS me FROM escolas");
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = (int) $rows2a[0];

// ── 4. Validações básicas ────────────────────────────────────────────────────
if ( $sa <= 0 || $idescola <= 0 || $idescola > $maxesc || $per <= 0 || empty($al) ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

// ── 5. Nome da escola e da sala ───────────────────────────────────────────────
$stmt_ne = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_ne, 'i', $idescola);
mysqli_stmt_execute($stmt_ne);
$rows11 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_ne));
if (!$rows11) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ne = $rows11[0];

$stmt_sa = mysqli_prepare($db, "SELECT nome FROM salas WHERE id = ?");
mysqli_stmt_bind_param($stmt_sa, 'i', $sa);
mysqli_stmt_execute($stmt_sa);
$rows12 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_sa));
if (!$rows12) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}
$ns = $rows12[0];

// ── 6. Paginação ─────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}
$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;

// ── 7. Query principal com prepared statement ────────────────────────────────
$stmt = mysqli_prepare($db,
    "SELECT ar.*, e.nomeequi
     FROM avarias_reparacoes ar
     INNER JOIN equipamento e ON ar.id_equi = e.id
     WHERE ar.id_sala = ? AND ar.ano_letivo = ? AND ar.periodo = ? AND ar.id_escola = ?
     ORDER BY ar.dataavaria DESC
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'isiiii', $sa, $al, $per, $idescola, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── 8. Total de registos ─────────────────────────────────────────────────────
$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(*) FROM avarias_reparacoes
     WHERE id_sala = ? AND ano_letivo = ? AND periodo = ? AND id_escola = ?"
);
mysqli_stmt_bind_param($stmt_total, 'isii', $sa, $al, $per, $idescola);
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
                     <li>
                        <a href="<?php echo SVRURL ?>num_avarias_sala.php?x=<?php echo base64_encode(2); ?>&&si=<?php echo base64_encode($sa); ?>&&ies=<?php echo base64_encode($idescola); ?>"
                           style="color:#4b6cb7;text-decoration:none;">Nº avarias da sala</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Avarias</li>
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

                  <!-- Card sala + instituição + período -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Sala</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Ano / Período</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($al, ENT_QUOTES, 'UTF-8'); ?> &nbsp;/&nbsp; <?php echo (int)$per; ?></span>
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
.gei-badge-rep { background:#e8f5e9; color:#2e7d32; border:1.5px solid #b2dfdb; }
.gei-badge-warn { background:#fff3cd; color:#7d4e00; border:1.5px solid #ffe082; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-img { border-radius:6px; border:1px solid #e3e8f4; margin-top:6px; display:block; transition:transform .25s ease; cursor:zoom-in; }
.gei-img:hover { transform:scale(2.5); }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:110px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<br>

<!-- Nº de linhas por página -->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
    <form action="<?php echo SVRURL ?>avarias_sala.php?si=<?php echo base64_encode($sa); ?>&&al=<?php echo base64_encode($al); ?>&&p=<?php echo base64_encode($per); ?>&&ies=<?php echo base64_encode($idescola); ?>" method="post">
        <?php include("num_linhas.php"); ?>
    </form>
</div>

<?php if ($totalLinhas === 0): ?>
<div style="padding:30px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não foram encontradas avarias para este período.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Avarias da sala
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th style="width:18%">Equipamento</th>
                <th style="width:12%">Ano / Período</th>
                <th style="width:35%">Avaria</th>
                <th style="width:35%">Reparação</th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = mysqli_fetch_array($result)): ?>
            <tr>
                <td data-label="Equipamento" style="font-weight:700;">
                    <?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td data-label="Ano / Período">
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row['ano_letivo'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <br>
                    <span class="gei-badge" style="margin-top:4px;"><?php echo htmlspecialchars($row['periodo'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>

                <td data-label="Avaria">
                    <span class="gei-label">Data</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo htmlspecialchars($row['dataavaria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:8px;">Descrição</span>
                    <span><?php echo nl2br(htmlspecialchars($row['avaria'], ENT_QUOTES, 'UTF-8')); ?></span>
                    <?php if (!empty($row['imgavaria'])): ?>
                        <span class="gei-label" style="margin-top:8px;">Imagem</span>
                        <img class="gei-img" height="120" width="200"
                             src="data:image/jpeg;base64,<?php echo base64_encode($row['imgavaria']); ?>"
                             alt="Imagem da avaria">
                    <?php endif; ?>
                    <?php if (!empty($row['video'])): ?>
                        <span class="gei-label" style="margin-top:8px;">Vídeo</span>
                        <video controls style="width:240px;max-width:100%;border-radius:6px;border:1px solid #e3e8f4;margin-top:4px;display:block;">
                            <source src="data:video/mp4;base64,<?php echo base64_encode($row['video']); ?>" type="video/mp4">
                            O seu browser não suporta a reprodução de vídeo.
                        </video>
                    <?php endif; ?>
                </td>

                <td data-label="Reparação">
                    <?php if (!empty($row['datareparacao'])): ?>
                        <span class="gei-label">Data</span>
                        <span style="font-family:monospace;font-size:.82rem;"><?php echo htmlspecialchars($row['datareparacao'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="gei-label" style="margin-top:8px;">Descrição</span>
                        <span><?php echo nl2br(htmlspecialchars($row['reparacao'], ENT_QUOTES, 'UTF-8')); ?></span>
                        <span class="gei-label" style="margin-top:8px;">Reparado por</span>
                        <span><?php echo htmlspecialchars($row['rep_efectuada_por'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <br><span class="gei-badge gei-badge-rep" style="margin-top:8px;">Reparado</span>
                    <?php else: ?>
                        <span class="gei-badge gei-badge-warn">Por reparar</span>
                    <?php endif; ?>
                </td>
            </tr>
<?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Paginação -->
<nav aria-label="Paginação">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page <= 1) { echo '#'; } else {
                   echo SVRURL . 'avarias_sala.php?si=' . base64_encode($sa)
                      . '&&al='  . base64_encode($al)
                      . '&&p='   . base64_encode($per)
                      . '&&ies=' . base64_encode($idescola)
                      . '&&page='. $prev;
               } ?>">&lt;&lt;</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>avarias_sala.php?si=<?php echo base64_encode($sa); ?>&&al=<?php echo base64_encode($al); ?>&&p=<?php echo base64_encode($per); ?>&&ies=<?php echo base64_encode($idescola); ?>&&page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'avarias_sala.php?si=' . base64_encode($sa)
                      . '&&al='  . base64_encode($al)
                      . '&&p='   . base64_encode($per)
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

<a href="<?php echo SVRURL ?>num_avarias_sala.php?x=<?php echo base64_encode(2); ?>&&si=<?php echo base64_encode($sa); ?>&&ies=<?php echo base64_encode($idescola); ?>">
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
