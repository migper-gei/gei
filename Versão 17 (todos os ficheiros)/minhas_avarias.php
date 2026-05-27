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

// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            window.location.href = '<?php echo SVRURL ?>eliminaavaria/' + n;
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<?php

// ── 1. Validar op ─────────────────────────────────────────────────────────────
$op = $_GET["op"] ?? '';
if ( !in_array($op, ['t', 'al']) ) {
    echo '<script>window.location.href = "' . SVRURL . 'avaria";</script>';
    exit;
}

$op2 = ($op === 't') ? 'Todas as avarias' : 'Avarias do ano letivo';
$em  = $_SESSION['email'];

// ── 2. Ano letivo mais recente ────────────────────────────────────────────────
$result3 = mysqli_query($db, "SELECT MAX(ano_lectivo) FROM periodos");
$rows3   = mysqli_fetch_row($result3);
$ano_max = $rows3[0] ?? null;

// ── 3. Paginação ─────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {
    // Validação CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Token inválido.');
    }
    $_SESSION['records-limit'] = (int) $_POST['records-limit'];
}
$limit           = isset($_SESSION['records-limit']) ? (int) $_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$page            = max(1, $page);
$paginationStart = ($page - 1) * $limit;

// ── 4. Query principal ───────────────────────────────────────────────────────
if ($op === 't') {
    $stmt = mysqli_prepare($db,
        "SELECT * FROM avarias_reparacoes
         WHERE autoravaria = ?
         ORDER BY dataavaria DESC
         LIMIT ?, ?"
    );
    mysqli_stmt_bind_param($stmt, 'sii', $em, $paginationStart, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stmt_total = mysqli_prepare($db,
        "SELECT COUNT(*) FROM avarias_reparacoes WHERE autoravaria = ?"
    );
    mysqli_stmt_bind_param($stmt_total, 's', $em);
    mysqli_stmt_execute($stmt_total);
    $row_total   = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
    $totalLinhas = (int) ($row_total[0] ?? 0);
} else {
    $stmt = mysqli_prepare($db,
        "SELECT * FROM avarias_reparacoes
         WHERE autoravaria = ? AND ano_letivo = ?
         ORDER BY dataavaria DESC
         LIMIT ?, ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssii', $em, $ano_max, $paginationStart, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stmt_total = mysqli_prepare($db,
        "SELECT COUNT(*) FROM avarias_reparacoes WHERE autoravaria = ? AND ano_letivo = ?"
    );
    mysqli_stmt_bind_param($stmt_total, 'ss', $em, $ano_max);
    mysqli_stmt_execute($stmt_total);
    $row_total   = mysqli_fetch_row(mysqli_stmt_get_result($stmt_total));
    $totalLinhas = (int) ($row_total[0] ?? 0);
}

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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <a href="<?php echo SVRURL ?>avaria" style="color:#4b6cb7;text-decoration:none;">Avarias</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Minhas avarias</li>
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

                  <!-- Card filtro ativo -->
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Filtro</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo $op2; ?></span>
                     </div>
                     <?php if ($op === 'al' && $ano_max): ?>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex;flex-direction:column;">
                        <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Ano letivo</span>
                        <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ano_max, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <?php endif; ?>
                  </div>

                  <!-- Botões de filtro -->
                  <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
                     <a href="<?php echo SVRURL ?>myavarias?op=t"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:7px;font-size:.82rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='t' ? '#4b6cb7' : '#c7d4f0'; ?>;background:<?php echo $op==='t' ? '#4b6cb7' : '#f4f6fb'; ?>;color:<?php echo $op==='t' ? '#fff' : '#4b6cb7'; ?>;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        Todas
                     </a>
                     <a href="<?php echo SVRURL ?>myavarias?op=al"
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
.gei-table td { padding:10px 14px; vertical-align:top; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-badge-rep  { background:#e6f9f0; color:#1a7f4b; border-color:#a8e6c8; }
.gei-badge-warn { background:#fff8e1; color:#b07d00; border-color:#ffe082; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-img { height:120px; width:200px; object-fit:cover; border-radius:6px; border:1px solid #e3e8f4; cursor:zoom-in; transition:transform .25s ease; }
.gei-img:hover { transform:scale(2.5); z-index:10; position:relative; }
.gei-action-btn { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:7px; font-size:.78rem; font-weight:700; text-decoration:none !important; border:1.5px solid; transition:background .15s; }
.gei-btn-edit  { border-color:#4b6cb7 !important; background:#e8f0fe !important; color:#4b6cb7 !important; }
.gei-btn-edit:hover  { background:#4b6cb7 !important; color:#fff !important; }
.gei-btn-del   { border-color:#c0392b !important; background:#fdecea !important; color:#c0392b !important; }
.gei-btn-del:hover   { background:#c0392b !important; color:#fff !important; }
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
    <form action="<?php echo SVRURL ?>myavarias?op=<?php echo $op; ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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
    Não foram encontradas avarias registadas.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Minhas avarias
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th style="width:22%">Localização / Equipamento</th>
                <th style="width:34%">Avaria</th>
                <th style="width:34%">Reparação</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = mysqli_fetch_array($result)):
    $n     = $row['id'];
    $ideq  = $row['id_equi'];
    $idsa  = $row['id_sala'];
    $idesc = $row['id_escola'];

    // Equipamento
    $stmt11 = mysqli_prepare($db, "SELECT nomeequi FROM equipamento WHERE id = ?");
    mysqli_stmt_bind_param($stmt11, 'i', $ideq);
    mysqli_stmt_execute($stmt11);
    $r11  = mysqli_fetch_row(mysqli_stmt_get_result($stmt11));
    $neq  = $r11[0] ?? '—';

    // Sala
    $stmt12 = mysqli_prepare($db, "SELECT nome FROM salas WHERE id = ?");
    mysqli_stmt_bind_param($stmt12, 'i', $idsa);
    mysqli_stmt_execute($stmt12);
    $r12  = mysqli_fetch_row(mysqli_stmt_get_result($stmt12));
    $nsa  = $r12[0] ?? '—';

    // Escola
    $stmt13 = mysqli_prepare($db, "SELECT nome_escola FROM escolas WHERE id = ?");
    mysqli_stmt_bind_param($stmt13, 'i', $idesc);
    mysqli_stmt_execute($stmt13);
    $r13   = mysqli_fetch_row(mysqli_stmt_get_result($stmt13));
    $noesc = $r13[0] ?? '—';

    $reparado = ($row['datareparacao'] !== null);
?>
            <tr>
                <td data-label="Localização / Equipamento">
                    <span class="gei-label">Instituição</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($noesc, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Sala</span>
                    <span><?php echo htmlspecialchars($nsa, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Equipamento</span>
                    <span><?php echo htmlspecialchars($neq, ENT_QUOTES, 'UTF-8'); ?></span>
                    <div style="margin-top:8px;">
                        <?php if ($reparado): ?>
                            <span class="gei-badge gei-badge-rep">&#10003; Reparado</span>
                        <?php else: ?>
                            <span class="gei-badge gei-badge-warn">&#9679; Por reparar</span>
                        <?php endif; ?>
                    </div>
                </td>

                <td data-label="Avaria">
                    <span class="gei-label">Data</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['dataavaria'])); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <span><?php echo htmlspecialchars($row['avaria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if (!empty($row['imgavaria'])): ?>
                        <?php
                        // FIX: detectar MIME real dos bytes (evita falha com PNG/GIF/BMP)
                        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
                        $imgMime = finfo_buffer($finfo, $row['imgavaria']) ?: 'image/jpeg';
                        finfo_close($finfo);
                        ?>
                        <div style="margin-top:8px;">
                            <img class="gei-img"
                                 src="data:<?php echo htmlspecialchars($imgMime, ENT_QUOTES, 'UTF-8'); ?>;base64,<?php echo base64_encode($row['imgavaria']); ?>"
                                 alt="Imagem avaria">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($row['video'])): ?>
                        <div style="margin-top:8px;">
                            <!-- FIX: usar streamvideo.php autónomo em vez de base64 inline -->
                            <video width="200" height="150" controls style="border-radius:6px;border:1px solid #e3e8f4;">
                                <source src="streamvideo.php?id=<?php echo base64_encode($row['id']); ?>" type="video/mp4">
                                O seu browser não suporta a reprodução de vídeo.
                            </video>
                        </div>
                    <?php endif; ?>
                </td>

                <td data-label="Reparação">
                    <?php if ($reparado): ?>
                        <span class="gei-label">Data</span>
                        <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['datareparacao'])); ?></span>
                        <span class="gei-label" style="margin-top:6px;">Descrição</span>
                        <span><?php echo htmlspecialchars($row['reparacao'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="gei-label" style="margin-top:6px;">Reparado por</span>
                        <span style="font-weight:600;"><?php echo htmlspecialchars($row['rep_efectuada_por'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php else: ?>
                        <span style="color:#c5cde0;font-size:.82rem;">—</span>
                    <?php endif; ?>
                </td>

                <td data-label="Ações" style="vertical-align:middle;">
                    <?php if (!$reparado): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <a class="gei-action-btn gei-btn-edit"
                           href="<?php echo SVRURL ?>atualavaria/<?php echo base64_encode($n); ?>"
                           title="Atualizar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Atualizar
                        </a>
                        <a class="gei-action-btn gei-btn-del"
                           onclick="confirmarEliminar(<?php echo $n; ?>);"
                           href="<?php echo SVRURL ?>eliminaavaria/<?php echo $n; ?>"
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
                   echo SVRURL . 'myavarias?op=' . $op . '&&page=' . $prev;
               } ?>">&lt;&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php echo SVRURL; ?>myavarias?op=<?php echo $op; ?>&&page=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a style="color:black;" class="page-link"
               href="<?php if ($page >= $totalPages) { echo '#'; } else {
                   echo SVRURL . 'myavarias?op=' . $op . '&&page=' . $next;
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

<a href="<?php echo SVRURL ?>avaria">
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
