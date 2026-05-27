<?php
// Sessão segura — consistente com todos os outros ficheiros
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

// ── Obter parâmetros ─────────────────────────────────────────────────────────
if (!isset($_GET["x"])) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

if ($_GET["x"] == 0) {
    $sa       = (int) $_POST["sala"];
    $idescola = (int) $_GET["escola"];
} else {
    $sa       = (int) base64_decode($_GET["sa"]);
    $idescola = (int) base64_decode($_GET["ies"]);
}

if ( empty($_GET["di"]) || empty($_GET["df"]) || $sa <= 0 || $idescola <= 0 ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$di = base64_decode($_GET["di"]);
$df = base64_decode($_GET["df"]);

$datai_ts = strtotime($di);
$dataf_ts = strtotime($df);

if ( $datai_ts === false || $dataf_ts === false ) {
    echo '<script>window.location.href = "' . SVRURL . 'lista";</script>';
    exit;
}

$di_safe = date('Y-m-d', $datai_ts);
$df_safe = date('Y-m-d', $dataf_ts);

// ── Autorização horizontal ────────────────────────────────────────────────────
// Uma única query com INNER JOIN confirma simultaneamente que:
//   1. A sala ($sa) existe nesta BD (ligada a $_SESSION['nobd'])
//   2. A sala pertence à escola ($idescola) indicada nos parâmetros
// Se qualquer uma das condições falhar, $rows10 fica vazio e o acesso é negado.
$stmt_sala = mysqli_prepare($db,
    "SELECT s.nome FROM salas s
     INNER JOIN escolas e ON e.id = s.id_escola
     WHERE s.id = ? AND s.id_escola = ?"
);
mysqli_stmt_bind_param($stmt_sala, 'ii', $sa, $idescola);
mysqli_stmt_execute($stmt_sala);
$rows10 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_sala));
$ns = $rows10[0] ?? '';

if (!$rows10) {
    header('Location: ' . SVRURL . 'lista');
    exit;
}

// ── Nome da escola ────────────────────────────────────────────────────────────
$stmt_esc = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_esc, 'i', $idescola);
mysqli_stmt_execute($stmt_esc);
$rows11 = mysqli_fetch_row(mysqli_stmt_get_result($stmt_esc));

if (!$rows11) {
    header('Location: ' . SVRURL . 'lista');
    exit;
}
$ne = $rows11[0];

// ── Paginação ─────────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {

    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}
$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;

// ── Query principal ──────────────────────────────────────────────────────────
$stmt = mysqli_prepare($db,
    "SELECT ar.*, s.nome, eq.nomeequi
     FROM avarias_reparacoes ar
     INNER JOIN salas s ON s.id = ar.id_sala
     INNER JOIN equipamento eq ON eq.id = ar.id_equi
     WHERE ar.id_sala = ?
     AND ar.dataavaria BETWEEN ? AND ?
     ORDER BY ar.dataavaria DESC
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, 'issii', $sa, $di_safe, $df_safe, $paginationStart, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── Total de registos ────────────────────────────────────────────────────────
$stmt_total = mysqli_prepare($db,
    "SELECT COUNT(*) FROM avarias_reparacoes
     WHERE id_sala = ? AND dataavaria BETWEEN ? AND ?"
);
mysqli_stmt_bind_param($stmt_total, 'iss', $sa, $di_safe, $df_safe);
mysqli_stmt_execute($stmt_total);
$rows_total  = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
$totalLinhas = (int) ($rows_total[0] ?? 0);
$totalPages  = ($limit > 0) ? (int) ceil($totalLinhas / $limit) : 1;

$prev = $page - 1;
$next = $page + 1;

// ── Prepared statement para nome do autor ────────────────────────────────────
$stmt_autor = mysqli_prepare($db, "SELECT nome FROM utilizadores WHERE email = ?");
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="display:flex;align-items:center;gap:4px;">
                        <a href="<?php echo SVRURL ?>num_avarias_entredatas.php?x=<?php echo base64_encode(1); ?>&&di=<?php echo base64_encode($di_safe); ?>&&df=<?php echo base64_encode($df_safe); ?>&&ies=<?php echo base64_encode($idescola); ?>" style="color:#4b6cb7;text-decoration:none;">Nº de avarias entre datas</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Avarias da sala</li>
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

                  <!-- Card instituição + sala + período -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Sala</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?></span>
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
.gei-table td, .gei-table th { padding:10px 14px; vertical-align:top; color:#1e2a45; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-img { border-radius:6px; border:1px solid #e3e8f4; cursor:zoom-in; transition:transform .25s ease; max-width:220px; height:auto; margin-top:8px; display:block; }
.gei-img:hover { transform:scale(2.5); z-index:999; position:relative; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td, .gei-table th { display:block; padding:6px 2px; border:none; }
}
</style>

<br>

<!-- Nº de linhas -->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
    <form action="<?php echo SVRURL ?>ver_reparacoes_sala.php?x=1&amp;&amp;sa=<?php echo base64_encode($sa); ?>&amp;&amp;di=<?php echo base64_encode($di_safe); ?>&amp;&amp;df=<?php echo base64_encode($df_safe); ?>&amp;&amp;ies=<?php echo base64_encode($idescola); ?>" method="post">
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
    Não foram encontradas avarias para esta sala no período indicado.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Avarias da sala — <?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th>Equipamento</th>
                <th>Avaria</th>
                <th>Reparação</th>
            </tr>
        </thead>
        <tbody>
<?php
while ($row = mysqli_fetch_array($result)) {

    // Nome do autor
    $em_autor = $row['autoravaria'];
    mysqli_stmt_bind_param($stmt_autor, 's', $em_autor);
    mysqli_stmt_execute($stmt_autor);
    $rows2    = mysqli_fetch_row(mysqli_stmt_get_result($stmt_autor));
    $nome_autor = $rows2[0] ?? '';
?>
            <tr>
                <td>
                    <span class="gei-badge"><?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td>
                    <span class="gei-label">Autor</span>
                    <?php echo htmlspecialchars($nome_autor, ENT_QUOTES, 'UTF-8'); ?>
                    <span class="gei-label" style="margin-top:6px;">Email</span>
                    <?php echo htmlspecialchars($row['autoravaria'], ENT_QUOTES, 'UTF-8'); ?>
                    <span class="gei-label" style="margin-top:6px;">Data avaria</span>
                    <?php echo htmlspecialchars($row['dataavaria'], ENT_QUOTES, 'UTF-8'); ?>
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <?php echo nl2br(htmlspecialchars($row['avaria'], ENT_QUOTES, 'UTF-8')); ?>
                    <?php if (!empty($row['imgavaria'])): ?>
                        <img class="gei-img" src="data:image/jpeg;base64,<?php echo base64_encode($row['imgavaria']); ?>" alt="Imagem da avaria">
                    <?php endif; ?>
                    <?php if (!empty($row['video'])): ?>
                        <span class="gei-label" style="margin-top:8px;">Vídeo da avaria</span>
                        <video controls style="width:240px;max-width:100%;border-radius:6px;border:1px solid #e3e8f4;margin-top:4px;display:block;">
                            <source src="data:video/mp4;base64,<?php echo base64_encode($row['video']); ?>" type="video/mp4">
                            O seu browser não suporta a reprodução de vídeo.
                        </video>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="gei-label">Data reparação</span>
                    <?php echo htmlspecialchars($row['datareparacao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <?php echo nl2br(htmlspecialchars($row['reparacao'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
                    <span class="gei-label" style="margin-top:6px;">Reparado por</span>
                    <?php echo htmlspecialchars($row['rep_efectuada_por'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</div>

<!-- Paginação -->
<nav aria-label="Paginação">
    <ul class="pagination justify-content-center">

        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page <= 1) { echo '#'; } else {
                   echo SVRURL . 'ver_reparacoes_sala.php?x=1'
                      . '&&sa='  . base64_encode($sa)
                      . '&&di='  . base64_encode($di_safe)
                      . '&&df='  . base64_encode($df_safe)
                      . '&&ies=' . base64_encode($idescola)
                      . '&&page='. $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>ver_reparacoes_sala.php?x=1&amp;&amp;sa=<?php echo base64_encode($sa); ?>&amp;&amp;di=<?php echo base64_encode($di_safe); ?>&amp;&amp;df=<?php echo base64_encode($df_safe); ?>&amp;&amp;ies=<?php echo base64_encode($idescola); ?>&amp;&amp;page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'ver_reparacoes_sala.php?x=1'
                      . '&&sa='  . base64_encode($sa)
                      . '&&di='  . base64_encode($di_safe)
                      . '&&df='  . base64_encode($df_safe)
                      . '&&ies=' . base64_encode($idescola)
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

<a href="<?php echo SVRURL ?>num_avarias_entredatas.php?x=<?php echo base64_encode(1); ?>&&di=<?php echo base64_encode($di_safe); ?>&&df=<?php echo base64_encode($df_safe); ?>&&ies=<?php echo base64_encode($idescola); ?>">
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
