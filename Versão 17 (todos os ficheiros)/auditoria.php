<?php
// ============================================================
// GEI — Painel de auditoria (apenas administradores)
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
}
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i'); exit();
}
// Apenas administradores (tipo 1)
if ((int)($_SESSION['tipo'] ?? 0) !== 1) {
    header('Location: ' . SVRURL . 'dashboard'); exit();
}

include('sessao_timeout.php');
require_once('gei_audit.php');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <style>
        :root {
            --primary:#4b6cb7; --primary-dk:#182848; --accent:#507feb;
            --success:#1cc88a; --warning:#f6c23e; --danger:#e74a3b;
            --bg:#f0f4fb; --surface:#ffffff; --border:#e3e8f4;
            --text:#1e2a45; --muted:#7b88a0; --radius:10px;
            --shadow:0 2px 12px rgba(75,108,183,.10);
        }
        [data-theme="dark"] {
            --bg:#0f1117; --surface:#1a1d27; --primary:#6489f5;
            --primary-dk:#e2e8f0; --accent:#7b9bf7; --border:#2d3348;
            --text:#e2e8f0; --muted:#94a3b8; --shadow:0 2px 12px rgba(0,0,0,.4);
        }
        [data-theme="dark"] .filter-card,
        [data-theme="dark"] .audit-table-wrap { background:var(--surface); border-color:var(--border); }
        [data-theme="dark"] input,[data-theme="dark"] select { background:#1e2130!important; color:var(--text)!important; border-color:var(--border)!important; }
        [data-theme="dark"] .audit-table thead th { background:#252836; }
        [data-theme="dark"] .audit-table tbody tr:hover { background:#1e2130; }

        .audit-wrap { padding:28px 32px 60px; max-width:1400px; margin:0 auto; }

        .page-title { display:flex; align-items:center; gap:14px; margin-bottom:24px; }
        .page-title-icon {
            width:46px; height:46px; border-radius:12px;
            background:linear-gradient(135deg,var(--primary),var(--accent));
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:1.2rem; flex-shrink:0;
            box-shadow:0 4px 12px rgba(75,108,183,.35);
        }
        .page-title h1 { font-size:1.35rem; font-weight:700; margin:0; color:var(--primary-dk); }
        .page-title p  { margin:0; font-size:.82rem; color:var(--muted); }

        /* ── Filtros ── */
        .filter-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius); padding:18px 22px;
            margin-bottom:20px; box-shadow:var(--shadow);
            display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
        }
        .filter-group { display:flex; flex-direction:column; gap:5px; justify-content:flex-end; }
        .filter-group label { font-size:.75rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
        .filter-group input,
        .filter-group select {
            border:1.5px solid var(--border); border-radius:8px;
            padding:7px 11px; font-family:inherit; font-size:.86rem;
            color:var(--text); background:#f7f9fe; width:130px;
            height:36px; box-sizing:border-box; line-height:1;
        }
        .filter-group input:focus,
        .filter-group select:focus { outline:none; border-color:var(--accent); }
        .filter-actions { display:flex; gap:8px; align-items:center; flex-wrap:nowrap; }
        .filter-actions .btn { height:36px; box-sizing:border-box; padding:0 18px; }
        .btn {
            padding:8px 18px; border-radius:8px; font-size:.84rem; font-weight:700;
            cursor:pointer; border:none; transition:opacity .15s;
            display:inline-flex; align-items:center; gap:7px; text-decoration:none;
        }
        .btn:hover { opacity:.85; }
        .btn-primary  { background:var(--primary); color:#fff; }
        .btn-outline  { background:#f0f4fb; color:var(--primary-dk); border:1.5px solid var(--border); }
        .btn-danger   { background:var(--danger); color:#fff; }
        [data-theme="dark"] .btn-outline { background:#1e2130; }

        /* ── Tabela ── */
        .audit-table-wrap {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius); box-shadow:var(--shadow); overflow-x:auto;
        }
        .audit-table { width:100%; border-collapse:collapse; font-size:.84rem; }
        .audit-table thead th {
            background:#f7f9fe; color:var(--muted);
            font-size:.72rem; font-weight:700; text-transform:uppercase;
            letter-spacing:.4px; padding:11px 14px;
            border-bottom:1.5px solid var(--border); white-space:nowrap;
            text-align:left;
        }
        .audit-table tbody td { padding:10px 14px; border-bottom:1px solid var(--border); vertical-align:middle; }
        .audit-table tbody tr:last-child td { border-bottom:none; }
        .audit-table tbody tr:hover { background:#f7f9fe; }

        .badge {
            display:inline-flex; align-items:center; gap:5px;
            font-size:.7rem; font-weight:700; padding:3px 9px; border-radius:5px;
        }
        .badge-login_ok     { background:#d4edda; color:#155724; }
        .badge-login_falhou { background:#f8d7da; color:#721c24; }
        .badge-logout       { background:#e2e8f0; color:#4a5568; }
        .badge-criar        { background:#cce5ff; color:#004085; }
        .badge-editar       { background:#fff3cd; color:#856404; }
        .badge-eliminar     { background:#f8d7da; color:#721c24; }
        .badge-exportar     { background:#d1ecf1; color:#0c5460; }
        .badge-config       { background:#e2d9f3; color:#4a235a; }

        .ts { font-size:.77rem; color:var(--muted); white-space:nowrap; }
        .ip-cell { font-family:monospace; font-size:.77rem; color:var(--muted); }
        .detalhe-cell { max-width:280px; word-break:break-word; font-size:.8rem; color:var(--muted); }
        .user-cell { font-weight:600; color:var(--text); }
        .user-email { font-size:.72rem; color:var(--muted); }

        .pager { display:flex; gap:6px; justify-content:flex-end; padding:14px 16px; flex-wrap:wrap; align-items:center; }
        .pager a, .pager span {
            padding:5px 11px; border-radius:7px; font-size:.82rem; font-weight:600;
            border:1.5px solid var(--border); color:var(--text); text-decoration:none;
        }
        .pager a:hover { background:var(--accent); color:#fff; border-color:var(--accent); }
        .pager .active { background:var(--primary); color:#fff; border-color:var(--primary); }
        .pager-info { font-size:.78rem; color:var(--muted); margin-right:auto; }

        .empty-state { text-align:center; padding:48px; color:var(--muted); }
        .empty-state i { font-size:2rem; display:block; margin-bottom:10px; }
    </style>
</head>
<body class="main-layout">
<?php include('loader.php'); ?>
<?php include('header.php'); ?>

<?php
include('sessao_timeout.php');

// ── Filtros ───────────────────────────────────────────────────────────────────
$f_acao     = trim($_GET['acao']     ?? '');
$f_entidade = trim($_GET['entidade'] ?? '');
$f_user     = trim($_GET['user']     ?? '');
$f_ip       = trim($_GET['ip']       ?? '');
$f_de       = trim($_GET['de']       ?? '');
$f_ate      = trim($_GET['ate']      ?? '');
$pagina     = max(1, (int)($_GET['p'] ?? 1));
$por_pagina = 50;

// ── Query com filtros ─────────────────────────────────────────────────────────
$where  = [];
$params = [];
$types  = '';

if ($f_acao)     { $where[] = 'acao = ?';            $params[] = $f_acao;      $types .= 's'; }
if ($f_entidade) { $where[] = 'entidade = ?';        $params[] = $f_entidade;  $types .= 's'; }
if ($f_ip)       { $where[] = 'ip LIKE ?';           $params[] = "%$f_ip%";    $types .= 's'; }
if ($f_user)     { $where[] = '(user_nome LIKE ? OR user_email LIKE ?)';
                   $params[] = "%$f_user%"; $params[] = "%$f_user%"; $types .= 'ss'; }
if ($f_de)       { $where[] = 'timestamp >= ?';      $params[] = $f_de . ' 00:00:00'; $types .= 's'; }
if ($f_ate)      { $where[] = 'timestamp <= ?';      $params[] = $f_ate . ' 23:59:59'; $types .= 's'; }

$sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Contar total
$stmt_c = $db->prepare("SELECT COUNT(*) FROM auditoria $sql_where");
if ($params) { $stmt_c->bind_param($types, ...$params); }
$stmt_c->execute();
$total = (int)$stmt_c->get_result()->fetch_row()[0];
$stmt_c->close();

$total_pags  = max(1, (int)ceil($total / $por_pagina));
$pagina      = min($pagina, $total_pags);
$offset      = ($pagina - 1) * $por_pagina;

// Registos da página
$stmt_r = $db->prepare("
    SELECT id, timestamp, user_id, user_nome, user_email, acao, entidade, entidade_id, detalhe, ip
    FROM auditoria
    $sql_where
    ORDER BY timestamp DESC
    LIMIT ? OFFSET ?
");
$p2 = $params; $t2 = $types;
$p2[] = $por_pagina; $t2 .= 'i';
$p2[] = $offset;     $t2 .= 'i';
$stmt_r->bind_param($t2, ...$p2);
$stmt_r->execute();
$registos = $stmt_r->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_r->close();

// Listas para dropdowns
$acoes     = ['login_ok','login_falhou','logout','criar','editar','eliminar','exportar','config'];
$entidades = $db->query("SELECT DISTINCT entidade FROM auditoria ORDER BY entidade")->fetch_all(MYSQLI_ASSOC);

// Registar acesso ao painel de auditoria
gei_audit($db, 'exportar', 'auditoria', null, 'Acesso ao painel de auditoria');

// ── Helper: URL dos filtros ───────────────────────────────────────────────────
function filtros_url(array $override = []): string {
    $base = ['acao'=>'','entidade'=>'','user'=>'','ip'=>'','de'=>'','ate'=>'','p'=>1];
    foreach ($_GET as $k => $v) { if (isset($base[$k])) $base[$k] = $v; }
    foreach ($override as $k => $v) { $base[$k] = $v; }
    return '?' . http_build_query(array_filter($base, fn($v) => $v !== ''));
}
?>

<div class="about">
  <div class="container-fluid">


    <div class="audit-wrap">
      <div class="page-title">
        <div class="page-title-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
          <h1>Logs de Auditoria</h1>
          <p>Registo de todas as ações realizadas no sistema</p>
        </div>
      </div>

      <!-- Filtros -->
      <form method="GET" action="">
      <div class="filter-card">

        <div class="filter-group">
          <label>Ação</label>
          <select name="acao">
            <option value="">Todas</option>
            <?php foreach ($acoes as $a): ?>
              <option value="<?= htmlspecialchars($a) ?>" <?= $f_acao===$a?'selected':'' ?>><?= htmlspecialchars($a) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label>Entidade</label>
          <select name="entidade">
            <option value="">Todas</option>
            <?php foreach ($entidades as $e): ?>
              <option value="<?= htmlspecialchars($e['entidade']) ?>" <?= $f_entidade===$e['entidade']?'selected':'' ?>>
                <?= htmlspecialchars($e['entidade']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label>Utilizador</label>
          <input type="text" name="user" value="<?= htmlspecialchars($f_user) ?>" placeholder="Nome ou email">
        </div>

        <div class="filter-group">
          <label>IP</label>
          <input type="text" name="ip" value="<?= htmlspecialchars($f_ip) ?>" placeholder="ex: 192.168">
        </div>

        <div class="filter-group">
          <label>De</label>
          <input type="date" name="de" value="<?= htmlspecialchars($f_de) ?>">
        </div>

        <div class="filter-group">
          <label>Até</label>
          <input type="date" name="ate" value="<?= htmlspecialchars($f_ate) ?>">
        </div>

        <div class="filter-actions">
          <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
          <a href="auditoria.php" class="btn btn-outline"><i class="fa fa-xmark"></i> Limpar</a>
          <a href="auditoria_pdf.php<?= htmlspecialchars(filtros_url()) ?>"
             class="btn btn-danger" target="_blank">
            <i class="fa fa-file-pdf"></i> Exportar PDF
          </a>
        </div>

      </div>
      </form>

      <!-- Tabela -->
      <div class="audit-table-wrap">
        <?php if (empty($registos)): ?>
          <div class="empty-state">
            <i class="fa fa-shield-halved"></i>
            <p>Nenhum registo encontrado com os filtros aplicados.</p>
          </div>
        <?php else: ?>
          <table class="audit-table">
            <thead>
              <tr>
                <th>Data / Hora</th>
                <th>Utilizador</th>
                <th>Ação</th>
                <th>Entidade</th>
                <th>ID</th>
                <th>Detalhe</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($registos as $r): ?>
              <tr>
                <td class="ts"><?= date('d/m/Y H:i:s', strtotime($r['timestamp'])) ?></td>
                <td>
                  <div class="user-cell"><?= htmlspecialchars($r['user_nome'] ?: '—') ?></div>
                  <div class="user-email"><?= htmlspecialchars($r['user_email']) ?></div>
                </td>
                <td>
                  <span class="badge badge-<?= htmlspecialchars($r['acao']) ?>">
                    <?= htmlspecialchars($r['acao']) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($r['entidade'] ?: '—') ?></td>
                <td style="text-align:center;color:var(--muted);font-size:.78rem;">
                  <?= $r['entidade_id'] ? htmlspecialchars($r['entidade_id']) : '—' ?>
                </td>
                <td class="detalhe-cell"><?= htmlspecialchars($r['detalhe'] ?? '') ?></td>
                <td class="ip-cell"><?= htmlspecialchars($r['ip']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Paginação -->
          <div class="pager">
            <span class="pager-info">
              <?= number_format($total) ?> registos — página <?= $pagina ?> de <?= $total_pags ?>
            </span>
            <?php if ($pagina > 1): ?>
              <a href="<?= htmlspecialchars(filtros_url(['p'=>1])) ?>">«</a>
              <a href="<?= htmlspecialchars(filtros_url(['p'=>$pagina-1])) ?>">‹</a>
            <?php endif; ?>
            <?php
            $inicio = max(1, $pagina - 2);
            $fim    = min($total_pags, $pagina + 2);
            for ($i = $inicio; $i <= $fim; $i++):
            ?>
              <?php if ($i === $pagina): ?>
                <span class="active"><?= $i ?></span>
              <?php else: ?>
                <a href="<?= htmlspecialchars(filtros_url(['p'=>$i])) ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagina < $total_pags): ?>
              <a href="<?= htmlspecialchars(filtros_url(['p'=>$pagina+1])) ?>">›</a>
              <a href="<?= htmlspecialchars(filtros_url(['p'=>$total_pags])) ?>">»</a>
            <?php endif; ?>
          </div>

        <?php endif; ?>
      </div><!-- /audit-table-wrap -->

    </div><!-- /audit-wrap -->
  </div>
</div>

<?php include('footer.php'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
    });
});
</script>
<script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
</body>
</html>
