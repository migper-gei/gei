<?php
// ── Sessão segura ─────────────────────────────────────────────────────────────
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

// ── Só administradores (tipo 1) ───────────────────────────────────────────────
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i'); exit();
}
if ((int)($_SESSION['tipo'] ?? 0) !== 1) {
    require_once('gei_audit.php');
    gei_audit($db, 'acesso_negado', 'pagina', null, 'avarias_utilizadores_removidos.php');
    header('Location: ' . SVRURL . 'dashboard'); exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include("head.php"); ?>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>

<?php
include("sessao_timeout.php");
require_once('gei_audit.php');

// ── Eliminação em massa ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_selecionados'])) {
    $ids_raw = $_POST['ids'] ?? [];
    $ids = array_filter(array_map('intval', $ids_raw));
    if (!empty($ids)) {
        $eliminados = 0;
        foreach ($ids as $del_id) {
            // Snapshot antes de apagar
            $stmt_snap = $db->prepare("
                SELECT ar.id, ar.avaria, ar.autoravaria, s.nome AS sala_nome, e.nome_escola AS escola_nome
                FROM avarias_reparacoes ar
                LEFT JOIN salas   s ON s.id = ar.id_sala
                LEFT JOIN escolas e ON e.id = ar.id_escola
                WHERE ar.id = ? LIMIT 1
            ");
            $stmt_snap->bind_param('i', $del_id);
            $stmt_snap->execute();
            $snap = $stmt_snap->get_result()->fetch_assoc();
            $stmt_snap->close();

            $detalhe = '(não encontrado)';
            if ($snap) {
                $partes = [
                    'id='     . $snap['id'],
                    'avaria=' . mb_strimwidth($snap['avaria'] ?? '', 0, 80, '…'),
                    'autor='  . ($snap['autoravaria'] ?? ''),
                    'sala='   . ($snap['sala_nome']   ?? ''),
                    'escola=' . ($snap['escola_nome'] ?? ''),
                ];
                $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
            }

            $stmt_del = $db->prepare("DELETE FROM avarias_reparacoes WHERE id = ?");
            $stmt_del->bind_param('i', $del_id);
            $stmt_del->execute();
            if ($stmt_del->affected_rows > 0) { $eliminados++; }
            $stmt_del->close();

            gei_audit($db, 'eliminar', 'avaria', $del_id, '[massa] ' . $detalhe);
        }
        $_SESSION['msg_massa'] = $eliminados . ' avaria(s) eliminada(s) com sucesso.';
    }
    header('Location: ' . SVRURL . 'avarias_utilizadores_removidos.php'); exit();
}

// ── Paginação ─────────────────────────────────────────────────────────────────
if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit-removidos'] = (int)$_POST['records-limit'];
}
$limit  = isset($_SESSION['records-limit-removidos']) ? (int)$_SESSION['records-limit-removidos'] : 10;
$page   = (isset($_GET['page']) && is_numeric($_GET['page'])) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ── Buscar avarias cujo autoravaria não existe na tabela utilizadores ──────────
// LEFT JOIN: se utilizadores.email for NULL, o autor foi removido
$stmt = $db->prepare("
    SELECT
        ar.id,
        ar.autoravaria,
        ar.avaria,
        ar.dataavaria,
        ar.ano_letivo,
        ar.datareparacao,
        s.nome        AS sala_nome,
        e.nome_escola AS escola_nome
    FROM avarias_reparacoes ar
    LEFT JOIN salas      s  ON s.id  = ar.id_sala
    LEFT JOIN escolas    e  ON e.id  = ar.id_escola
    LEFT JOIN utilizadores u ON u.email = ar.autoravaria
    WHERE u.email IS NULL
      AND ar.datareparacao IS NULL
    ORDER BY ar.dataavaria DESC
    LIMIT ?, ?
");
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$avarias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Contar total ──────────────────────────────────────────────────────────────
$stmt_cnt = $db->prepare("
    SELECT COUNT(*)
    FROM avarias_reparacoes ar
    LEFT JOIN utilizadores u ON u.email = ar.autoravaria
    WHERE u.email IS NULL
      AND ar.datareparacao IS NULL
");
$stmt_cnt->execute();
$total       = (int)$stmt_cnt->get_result()->fetch_row()[0];
$stmt_cnt->close();
$totalPages  = max(1, (int)ceil($total / $limit));
$page        = min($page, $totalPages);
?>

<style>
:root {
    --primary:#4b6cb7; --primary-dk:#182848; --accent:#507feb;
    --danger:#e74a3b;  --warn-bg:#fff8e1;    --warn-border:#ffe082;
    --bg:#f0f4fb;      --surface:#ffffff;    --border:#e3e8f4;
    --text:#1e2a45;    --muted:#7b88a0;      --radius:10px;
    --shadow:0 2px 12px rgba(75,108,183,.10);
}
.page-wrap      { padding:28px 32px 60px; max-width:1300px; margin:0 auto; }
.page-title     { display:flex; align-items:center; gap:14px; margin-bottom:20px; }
.page-title-icon{ width:46px; height:46px; border-radius:12px;
                  background:linear-gradient(135deg,#e74a3b,#c0392b);
                  display:flex; align-items:center; justify-content:center;
                  color:#fff; font-size:1.2rem; flex-shrink:0;
                  box-shadow:0 4px 12px rgba(231,74,59,.35); }
.page-title h1  { font-size:1.25rem; font-weight:700; margin:0; color:var(--primary-dk); }
.page-title p   { margin:0; font-size:.82rem; color:var(--muted); }

.info-banner    { display:flex; align-items:center; gap:10px; padding:12px 16px;
                  background:#fff8e1; border:1px solid #ffe082; border-radius:8px;
                  font-size:.83rem; color:#856404; font-weight:600; margin-bottom:18px; }

.table-wrap     { background:var(--surface); border:1px solid var(--border);
                  border-radius:var(--radius); box-shadow:var(--shadow); overflow-x:auto; margin-bottom:16px; }
.table-header   { padding:10px 16px; background:var(--primary-dk); color:#fff;
                  font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
table.av-table  { width:100%; border-collapse:collapse; font-size:.84rem; }
table.av-table thead th { padding:10px 14px; background:#253d6e; color:#fff;
                          font-size:.75rem; font-weight:700; text-transform:uppercase;
                          letter-spacing:.5px; border:none; text-align:left; }
table.av-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
table.av-table tbody tr:last-child { border-bottom:none; }
table.av-table tbody tr:hover { background:#f7f0f0; }
table.av-table td { padding:11px 14px; vertical-align:top; color:var(--text); }

.lbl  { font-size:.72rem; font-weight:700; text-transform:uppercase;
         letter-spacing:.4px; color:var(--muted); display:block; margin-bottom:3px; }
.email-removido { display:inline-flex; align-items:center; gap:5px; font-size:.78rem;
                  font-weight:700; background:#fdecea; color:#c0392b;
                  border:1.5px solid #f5c6cb; border-radius:5px; padding:2px 8px; }
.badge-porReparar { display:inline-flex; align-items:center; padding:2px 10px;
                    border-radius:5px; font-size:.78rem; font-weight:700;
                    background:var(--warn-bg); color:#b07d00; border:1.5px solid var(--warn-border); }

.btn-del { display:inline-flex; align-items:center; gap:6px; padding:6px 14px;
           border-radius:7px; font-size:.82rem; font-weight:700; border:none;
           background:var(--danger); color:#fff; cursor:pointer; transition:background .15s; }
.btn-del:hover { background:#c0392b; }

.pager { display:flex; gap:6px; justify-content:flex-end; padding:12px 16px;
         flex-wrap:wrap; align-items:center; }
.pager a, .pager span { padding:5px 11px; border-radius:7px; font-size:.82rem;
                         font-weight:600; border:1.5px solid var(--border);
                         color:var(--text); text-decoration:none; }
.pager a:hover  { background:var(--accent); color:#fff; border-color:var(--accent); }
.pager .active  { background:var(--primary); color:#fff; border-color:var(--primary); }
.pager-info     { font-size:.78rem; color:var(--muted); margin-right:auto; }

.empty-state    { text-align:center; padding:48px; color:var(--muted); }
.check-all-wrap { display:flex; align-items:center; gap:10px; padding:10px 16px;
                  background:#f7f9fe; border-bottom:1px solid var(--border); font-size:.83rem; }
.mass-bar       { display:none; align-items:center; gap:12px; padding:10px 16px;
                  background:#fdecea; border:1px solid #f5c6cb; border-radius:8px;
                  margin-bottom:12px; font-size:.83rem; font-weight:600; color:#c0392b; }
.mass-bar.visible { display:flex; }
.cb-row         { width:16px; height:16px; cursor:pointer; accent-color:var(--danger); }
</style>

<div class="about">
  <div class="container-fluid">
    <div class="page-wrap">

      <!-- Breadcrumb -->
      <nav style="margin-bottom:16px;">
        <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
          <li><a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a></li>
          <li style="color:#c5cde0;font-size:.9rem;">›</li>
          <li style="color:#1e2a45;">Avarias de utilizadores removidos</li>
        </ol>
      </nav>

      <!-- Mensagem de sucesso -->
      <?php if (!empty($_SESSION['msg_massa'])): ?>
      <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#d4edda;border:1px solid #c3e6cb;border-radius:8px;font-size:.83rem;color:#155724;font-weight:600;margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#155724" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <?= htmlspecialchars($_SESSION['msg_massa'], ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php unset($_SESSION['msg_massa']); endif; ?>

      <!-- Título -->
      <div class="page-title">
        <div class="page-title-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
        </div>
        <div>
          <h1>Avarias de utilizadores removidos</h1>
          <p>Avarias por reparar cujo autor já não existe no sistema</p>
        </div>
      </div>

      <!-- Aviso informativo -->
      <div class="info-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Estas avarias foram reportadas por utilizadores entretanto removidos do sistema. Só podem ser eliminadas — não é possível notificar o autor.
      </div>

      <!-- Linhas por página -->
      <div class="d-flex flex-row-reverse mb-3">
        <form method="post" action="<?php echo SVRURL ?>avarias_utilizadores_removidos.php">
          <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
            Linhas por página:
            <select name="records-limit" onchange="this.form.submit()"
              style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
              <?php foreach ([5,10,20,30,50,100] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </form>
      </div>

      <?php if (empty($avarias)): ?>
        <div class="empty-state">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <p>Não existem avarias de utilizadores removidos por reparar.</p>
        </div>

      <?php else: ?>

        <form method="post" action="<?php echo SVRURL ?>avarias_utilizadores_removidos.php" id="form-massa">
        <input type="hidden" name="eliminar_selecionados" value="1">

        <!-- Barra de ações em massa -->
        <div class="mass-bar" id="mass-bar">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
          <span id="mass-count">0</span> avaria(s) selecionada(s)
          <button type="button" onclick="confirmarMassa()" class="btn-del" style="margin-left:8px;">
            Eliminar selecionadas
          </button>
          <button type="button" onclick="desselecionar()" style="background:none;border:none;color:#c0392b;font-weight:700;cursor:pointer;font-size:.82rem;margin-left:4px;">
            Cancelar
          </button>
        </div>

        <div class="table-wrap">
          <div class="table-header">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
            <?= $total ?> avaria<?= $total != 1 ? 's' : '' ?> de utilizadores removidos
          </div>
          <table class="av-table">
            <thead>
              <tr>
                <th style="width:3%;text-align:center;">
                  <input type="checkbox" id="check-all" class="cb-row" title="Selecionar todos">
                </th>
                <th style="width:5%">#</th>
                <th style="width:20%">Autor removido</th>
                <th style="width:20%">Localização</th>
                <th style="width:35%">Avaria</th>
                <th style="width:10%">Data</th>
                <th style="width:10%">Ação</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($avarias as $r): ?>
              <tr>
                <td style="text-align:center;vertical-align:middle;">
                  <input type="checkbox" name="ids[]" value="<?= (int)$r['id'] ?>" class="cb-row cb-item">
                </td>
                <td style="color:var(--muted);font-size:.78rem;font-family:monospace;"><?= (int)$r['id'] ?></td>

                <td>
                  <span class="lbl">Email</span>
                  <span class="email-removido">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
                    <?= htmlspecialchars($r['autoravaria'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                  <span class="lbl" style="margin-top:6px;">Ano letivo</span>
                  <span style="font-size:.8rem;"><?= htmlspecialchars($r['ano_letivo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                </td>

                <td>
                  <span class="lbl">Escola</span>
                  <span style="font-weight:600;"><?= htmlspecialchars($r['escola_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="lbl" style="margin-top:5px;">Sala</span>
                  <span><?= htmlspecialchars($r['sala_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                </td>

                <td>
                  <span class="badge-porReparar" style="margin-bottom:6px;">&#9679; Por reparar</span>
                  <span style="display:block;font-size:.83rem;"><?= htmlspecialchars($r['avaria'], ENT_QUOTES, 'UTF-8') ?></span>
                </td>

                <td style="font-family:monospace;font-size:.8rem;color:var(--muted);white-space:nowrap;">
                  <?= date('d/m/Y', strtotime($r['dataavaria'])) ?>
                </td>

                <td style="vertical-align:middle;">
                  <button class="btn-del"
                    onclick="confirmarEliminar(event, '<?php echo SVRURL ?>elimina_avaria.php?url=<?= (int)$r['id'] ?>', <?= (int)$r['id'] ?>)"
                    title="Eliminar avaria">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    Eliminar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Paginação -->
          <div class="pager">
            <span class="pager-info"><?= $total ?> registos — página <?= $page ?> de <?= $totalPages ?></span>
            <?php if ($page > 1): ?>
              <a href="?page=1">«</a>
              <a href="?page=<?= $page - 1 ?>">‹</a>
            <?php endif; ?>
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
              <?php if ($i === $page): ?>
                <span class="active"><?= $i ?></span>
              <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
              <a href="?page=<?= $page + 1 ?>">›</a>
              <a href="?page=<?= $totalPages ?>">»</a>
            <?php endif; ?>
          </div>

        </div><!-- /table-wrap -->
        </form>

      <?php endif; ?>

      <a href="<?php echo SVRURL ?>avaria">
        <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
      </a>
      <br><br>

    </div><!-- /page-wrap -->
  </div>
</div>

<?php include("jquery_bootstrap.php"); ?>
<?php mysqli_close($db); ?>
<?php include("footer.php"); ?>

<script>
// Checkboxes
document.addEventListener('DOMContentLoaded', function() {
    var checkAll  = document.getElementById('check-all');
    var massBar   = document.getElementById('mass-bar');
    var massCount = document.getElementById('mass-count');

    function updateBar() {
        var checked = document.querySelectorAll('.cb-item:checked');
        massCount.textContent = checked.length;
        if (checked.length > 0) {
            massBar.classList.add('visible');
            if (checkAll) checkAll.indeterminate = checked.length < document.querySelectorAll('.cb-item').length;
        } else {
            massBar.classList.remove('visible');
            if (checkAll) checkAll.indeterminate = false;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.cb-item').forEach(function(cb) { cb.checked = checkAll.checked; });
            updateBar();
        });
    }
    document.querySelectorAll('.cb-item').forEach(function(cb) {
        cb.addEventListener('change', updateBar);
    });
});

function desselecionar() {
    document.querySelectorAll('.cb-item, #check-all').forEach(function(cb) { cb.checked = false; });
    document.getElementById('mass-bar').classList.remove('visible');
    document.getElementById('mass-count').textContent = '0';
}

function confirmarMassa() {
    var n = document.querySelectorAll('.cb-item:checked').length;
    swal({
        title: 'Eliminar ' + n + ' avaria(s)?',
        text: 'Esta ação é irreversível. Os registos serão apagados permanentemente.',
        icon: 'warning',
        buttons: {
            cancelar: { text: 'Cancelar', value: false },
            eliminar: { text: 'Eliminar ' + n, value: true, className: 'swal-button--danger' }
        },
        dangerMode: true,
    }, function(confirmar) {
        if (confirmar) { document.getElementById('form-massa').submit(); }
    });
}

function confirmarEliminar(e, url, id) {
    e.preventDefault();
    swal({
        title: 'Eliminar avaria #' + id + '?',
        text: 'Esta ação é irreversível. O registo será apagado permanentemente.',
        icon: 'warning',
        buttons: {
            cancelar: { text: 'Cancelar', value: false },
            eliminar: { text: 'Eliminar', value: true, className: 'swal-button--danger' }
        },
        dangerMode: true,
    }, function(confirmar) {
        if (confirmar) { window.location.href = url; }
    });
}
</script>

   </body>
</html>
