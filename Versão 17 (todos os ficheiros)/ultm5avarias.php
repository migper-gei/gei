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

// Gerar token CSRF se ainda não existir
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
     <?php
include("sessao_timeout.php");
?>

<script>
function validarDataRep() {
    var dre = document.getElementsByName("datarep")[0].value;
    var dav = document.getElementsByName("dav")[0].value;
    if (dre < dav) {
        swal({ title: 'A data de reparação deve ser igual ou superior à data da avaria!', icon: 'error' });
        return false;
    }
    return true;
}
</script>

<?php

// ── Validar id escola ─────────────────────────────────────────────────────────
$sql2a = "SELECT MAX(id) AS me FROM escolas";
$result2a = mysqli_query($db, $sql2a);
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = $rows2a[0];

if (base64_decode($_GET["aves"]) > $maxesc) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>avaria'; }, 10);</script>
<?php
}

$idescola = (int)base64_decode($_GET["aves"]);

if (!isset($idescola) || empty($idescola) || !is_numeric($idescola)) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>avaria'; }, 10);</script>
<?php
}

// Nome da escola
$sql11   = "SELECT nome_escola FROM escolas WHERE id=$idescola";
$result11 = mysqli_query($db, $sql11);
$rows11   = mysqli_fetch_row($result11);
$ne       = $rows11[0];

// ── Query: últimas 5 avarias por reparar desta escola ────────────────────────
$stmt = mysqli_prepare($db,
    "SELECT ar.*, e.nomeequi, s.nome AS nomsala, esc.nome_escola
     FROM avarias_reparacoes ar
     INNER JOIN equipamento e   ON e.id   = ar.id_equi
     INNER JOIN salas s         ON s.id   = ar.id_sala
     INNER JOIN escolas esc     ON esc.id = ar.id_escola
     WHERE ar.datareparacao IS NULL
       AND esc.id = ?
     ORDER BY ar.dataavaria DESC
     LIMIT 5"
);
mysqli_stmt_bind_param($stmt, 'i', $idescola);
mysqli_stmt_execute($stmt);
$result      = mysqli_stmt_get_result($stmt);
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <a href="<?php echo SVRURL ?>avaria" style="color:#4b6cb7;text-decoration:none;">Avarias</a>
                    
                    </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Últimas 5 registadas</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

               <!-- Cabeçalho com info da escola -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>

                  <!-- Nota informativa -->
                  <div style="display:flex;align-items:center;gap:8px;margin:10px 0 18px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                     <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                     Ao clicar em <img width="15" height="15" src="<?php echo SVRURL ?>images/checkbox.svg" style="vertical-align:middle;margin:0 4px;">, o autor da avaria recebe um email com os dados da reparação.
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
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-img { height:120px; width:200px; object-fit:cover; border-radius:6px; border:1px solid #e3e8f4; cursor:zoom-in; transition:transform .25s ease; }
.gei-img:hover { transform:scale(2.5); z-index:10; position:relative; }
.gei-badge-warn { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#fff8e1; color:#b07d00; border:1.5px solid #ffe082; }
.gei-rep-input { width:100%; padding:5px 8px; border:1.5px solid #c7d4f0; border-radius:6px; font-size:.83rem; color:#1e2a45; background:#f8faff; margin-bottom:6px; }
.gei-rep-input:focus { outline:none; border-color:#4b6cb7; background:#fff; }
.gei-rep-textarea { width:100%; padding:6px 8px; border:1.5px solid #c7d4f0; border-radius:6px; font-size:.83rem; color:#1e2a45; background:#f8faff; resize:vertical; }
.gei-rep-textarea:focus { outline:none; border-color:#4b6cb7; background:#fff; }
.gei-btn-rep { display:inline-flex; align-items:center; gap:6px; padding:6px 16px; border-radius:7px; font-size:.82rem; font-weight:700; border:none; background:#1a7f4b; color:#fff !important; cursor:pointer; transition:background .15s; }
.gei-btn-rep:hover { background:#145f38; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:120px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<?php if ($totalLinhas === 0): ?>
<div style="padding:30px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não existem avarias por reparar.
</div>

<?php else: ?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Últimas 5 avarias por reparar — <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th style="width:22%">Sala / Equipamento</th>
                <th style="width:30%">Avaria</th>
                <th style="width:38%">Registar reparação</th>
                <th style="width:10%">Ação</th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = mysqli_fetch_array($result)):
    $n  = $row['id'];
    $em = $row['autoravaria'];

    // Nome do autor
    $stmt2 = mysqli_prepare($db, "SELECT nome FROM utilizadores WHERE email = ?");
    mysqli_stmt_bind_param($stmt2, 's', $em);
    mysqli_stmt_execute($stmt2);
    $r2        = mysqli_fetch_row(mysqli_stmt_get_result($stmt2));
    $nomeautor = $r2[0] ?? '—';
?>
            <tr>
                <td data-label="Sala / Equipamento">
                    <span class="gei-label">Sala</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row['nomsala'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Equipamento</span>
                    <span><?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <div style="margin-top:8px;">
                        <span class="gei-badge-warn">&#9679; Por reparar</span>
                    </div>
                </td>

                <td data-label="Avaria">
                    <span class="gei-label">Autor</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($nomeautor, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:4px;">Email</span>
                    <span style="font-size:.78rem;color:#7b88a0;"><?php echo htmlspecialchars($em, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Data da avaria</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['dataavaria'])); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <span><?php echo htmlspecialchars($row['avaria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if (!empty($row['imgavaria'])): ?>
                        <?php
                        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
                        $imgMime = finfo_buffer($finfo, $row['imgavaria']) ?: 'image/jpeg';
                        finfo_close($finfo);
                        ?>
                        <div style="margin-top:8px;">
                            <img class="gei-img" src="data:<?php echo htmlspecialchars($imgMime, ENT_QUOTES, 'UTF-8'); ?>;base64,<?php echo base64_encode($row['imgavaria']); ?>" alt="Imagem avaria">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($row['video'])): ?>
                        <div style="margin-top:8px;">
                            <video width="200" height="150" controls style="border-radius:6px;border:1px solid #e3e8f4;">
                                <source src="<?php echo SVRURL ?>streamvideo.php?id=<?php echo base64_encode($row['id']); ?>" type="video/mp4">
                                O seu browser não suporta a reprodução de vídeo.
                            </video>
                        </div>
                    <?php endif; ?>
                </td>

                <form onsubmit="return validarDataRep();" action="<?php echo SVRURL ?>repara_avaria.php?ia=<?php echo base64_encode($n); ?>" method="post">
                <input type="hidden" name="dav" value="<?php echo htmlspecialchars($row['dataavaria'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <td data-label="Registar reparação">
                    <span class="gei-label">Data de reparação</span>
                    <input class="gei-rep-input" required type="date" name="datarep" value="<?php echo date('Y-m-d'); ?>">
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <textarea class="gei-rep-textarea" required rows="4" name="reparacao"></textarea>
                    <span class="gei-label" style="margin-top:6px;">Reparado por</span>
                    <input class="gei-rep-input" required type="text" name="repar_por">
                </td>

                <?php if (isset($_SESSION['tipo']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3)): ?>
                <td data-label="Ação" style="vertical-align:middle;">
                    <div style="display:flex;flex-direction:column;align-items:flex-start;gap:10px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#4b6cb7;cursor:pointer;" title="Enviar email ao autor da avaria">
                            <input type="checkbox" name="my_check" value="yes" style="width:16px;height:16px;cursor:pointer;">
                            Email autor
                        </label>
                        <button type="submit" class="gei-btn-rep" title="Registar reparação">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Reparar
                        </button>
                    </div>
                </td>
                <?php else: ?>
                <td></td>
                <?php endif; ?>

                </form>
            </tr>
<?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include ("jquery_bootstrap.php"); ?>

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

      <?php
      mysqli_close($db);
      include ("footer.php"); ?>

   </body>
</html>
