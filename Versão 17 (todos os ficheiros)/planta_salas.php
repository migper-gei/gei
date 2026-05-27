<?php
// ============================================================
// Vista em planta interactiva da escola com estado das salas
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

include('sessao_timeout.php');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <style>
        :root {
            --primary:    #4b6cb7;
            --primary-dk: #182848;
            --accent:     #507feb;
            --success:    #1cc88a;
            --warning:    #f6c23e;
            --danger:     #e74a3b;
            --bg:         #f0f4fb;
            --surface:    #ffffff;
            --border:     #e3e8f4;
            --text:       #1e2a45;
            --muted:      #7b88a0;
            --radius:     10px;
            --shadow:     0 2px 12px rgba(75,108,183,.10);
        }

        /* ═══ DARK MODE — remapeia variáveis locais ══════════════ */
        [data-theme="dark"] {
            --bg:         #0f1117;
            --surface:    #1a1d27;
            --primary:    #6489f5;
            --primary-dk: #e2e8f0;
            --accent:     #7b9bf7;
            --success:    #26d49a;
            --warning:    #f6c23e;
            --danger:     #f07167;
            --border:     #2d3348;
            --text:       #e2e8f0;
            --muted:      #94a3b8;
            --shadow:     0 2px 12px rgba(0,0,0,.4);
        }
        [data-theme="dark"] .filter-card        { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .filter-card select { background: #1e2130; color: var(--text); border-color: var(--border); }
        [data-theme="dark"] .kpi                { background: var(--surface); }
        [data-theme="dark"] .kpi-val            { color: var(--primary-dk); }
        [data-theme="dark"] .planta-canvas      { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .piso-block         { background: #1e2130; border-color: var(--border); }
        [data-theme="dark"] .corredor           { background: #252836; }
        [data-theme="dark"] .sala-nome          { color: var(--text); }
        [data-theme="dark"] .sala-loc           { color: var(--muted); }
        [data-theme="dark"] .painel-wrap        { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .painel-title       { color: var(--text); }
        [data-theme="dark"] .painel-sub         { color: var(--muted); }
        /* ════════════════════════════════════════════════════════ */

        .planta-wrap { padding: 28px 32px 48px; max-width: 1300px; margin: 0 auto; }

        .page-title {
            display: flex; align-items: center; gap: 14px; margin-bottom: 24px;
        }
        .page-title-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(75,108,183,.35);
        }
        .page-title h1 { font-size: 1.35rem; font-weight: 700; margin: 0; color: var(--primary-dk); }
        .page-title p  { margin: 0; font-size: .82rem; color: var(--muted); }

        /* ── Filtros ── */
        .filter-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 16px 20px;
            margin-bottom: 20px; box-shadow: var(--shadow);
            display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .filter-card label { font-size: .78rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
        .filter-card select {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 7px 12px; font-family: inherit; font-size: .88rem;
            color: var(--text); background: #f7f9fe; cursor: pointer; transition: border .2s;
        }
        .filter-card select:focus { outline: none; border-color: var(--accent); }

        .refresh-info { margin-left: auto; font-size: .75rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }
        .refresh-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--success); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        /* ── KPIs ── */
        .kpi-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px; margin-bottom: 20px;
        }
        .kpi {
            background: var(--surface); border-radius: var(--radius);
            padding: 16px 18px; box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
        }
        .kpi.c-danger  { border-color: var(--danger); }
        .kpi.c-warning { border-color: var(--warning); }
        .kpi.c-success { border-color: var(--success); }
        .kpi-val { font-size: 1.8rem; font-weight: 800; color: var(--primary-dk); line-height: 1; }
        .kpi.c-danger  .kpi-val { color: var(--danger); }
        .kpi.c-warning .kpi-val { color: #c8860a; }
        .kpi.c-success .kpi-val { color: #148f5a; }
        .kpi-lbl { font-size: .73rem; color: var(--muted); margin-top: 4px; }

        /* ── Legenda ── */
        .legend {
            display: flex; gap: 16px; flex-wrap: wrap;
            margin-bottom: 20px; font-size: .78rem; color: var(--muted);
        }
        .leg { display: flex; align-items: center; gap: 6px; }
        .leg-box { width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0; }
        .leg-ok    { background: #EAF3DE; border: 1.5px solid #3B6D11; }
        .leg-av    { background: #FCEBEB; border: 1.5px solid #A32D2D; }
        .leg-mn    { background: #FAEEDA; border: 1.5px solid #854F0B; }
        .leg-misto { background: #FBEAF0; border: 1.5px solid #993556; }

        /* ── Planta ── */
        .planta-canvas {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            padding: 20px; overflow-x: auto;
        }

        .piso-label {
            font-size: .72rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .6px;
            margin-bottom: 8px; margin-top: 4px;
        }
        .piso-block {
            background: #f7f9fe; border: 1px solid var(--border);
            border-radius: 8px; padding: 14px; margin-bottom: 14px;
        }
        .corredor {
            height: 16px; background: #e8edf8; border-radius: 4px;
            margin: 10px 0 8px; display: flex; align-items: center;
            padding: 0 10px;
        }
        .corredor span { font-size: .68rem; color: var(--muted); font-style: italic; }
        .salas-row { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ── Sala ── */
        .sala {
            width: 90px; height: 70px; border-radius: 7px; border: 2px solid transparent;
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; cursor: pointer; padding: 5px;
            transition: transform .15s, box-shadow .15s; text-align: center;
            position: relative;
        }
        .sala:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .sala.ok    { background: #EAF3DE; border-color: #3B6D11; }
        .sala.avaria{ background: #FCEBEB; border-color: #A32D2D; }
        .sala.manut { background: #FAEEDA; border-color: #854F0B; }
        .sala.misto { background: #FBEAF0; border-color: #993556; }
        .sala.hidden{ display: none; }

        /* ── Heatmap ── */
        .sala.heat-mode {
            border-width: 2px;
            transition: background .4s, border-color .4s, transform .15s, box-shadow .15s;
        }
        /* Escala verde→amarelo→laranja→vermelho via hue */
        .sala.heat-mode { background: hsl(calc(115 - (var(--heat,0) * 115)), 70%, 88%); border-color: hsl(calc(115 - (var(--heat,0) * 115)), 55%, 40%); }
        .sala.heat-mode .sala-badges { display: none; }
        .sala.heat-mode .heat-count  { display: flex; }
        .heat-count {
            display: none; margin-top: 4px;
            font-size: .72rem; font-weight: 800; color: #7a1a1a;
            background: rgba(255,255,255,.55); border-radius: 4px;
            padding: 1px 7px;
        }

        /* Legenda heatmap */
        .heat-legend-bar {
            display: none; align-items: center; gap: 10px;
            margin-bottom: 20px; font-size: .76rem; color: var(--muted);
            padding: 10px 14px; background: var(--surface);
            border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .heat-legend-bar.visible { display: flex; flex-wrap: wrap; }
        .heat-gradient {
            width: 180px; height: 13px; border-radius: 4px; flex-shrink: 0;
            background: linear-gradient(to right, hsl(115,70%,80%), hsl(57,80%,78%), hsl(30,85%,75%), hsl(0,70%,80%));
            border: 1px solid #ccc;
        }
        .heat-periodo-label {
            margin-left: auto; font-size: .7rem; color: var(--muted);
            background: #f0f4fb; border: 1px solid var(--border);
            border-radius: 5px; padding: 2px 8px;
        }
        [data-theme="dark"] .heat-periodo-label { background: #1e2130; }
        [data-theme="dark"] .heat-legend-bar { background: var(--surface); border-color: var(--border); }
        .sala.sala-selected {
            outline: 3px solid var(--accent);
            outline-offset: 2px;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(80,127,235,.25);
        }

        .sala-nome { font-size: .72rem; font-weight: 700; color: var(--text); line-height: 1.2; }
        .sala-loc  { font-size: .62rem; color: var(--muted); margin-top: 1px; }
        .sala-badges { display: flex; gap: 2px; margin-top: 4px; flex-wrap: wrap; justify-content: center; }
        .sbadge {
            font-size: .6rem; font-weight: 700; padding: 1px 5px; border-radius: 3px;
        }
        .sbadge.av { background: #F09595; color: #501313; }
        .sbadge.mn { background: #FAC775; color: #412402; }
        .sbadge.eq { background: #B5D4F4; color: #042C53; }

        /* ── Painel lateral ── */
        .painel-wrap {
            display: none;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px 24px;
            margin-bottom: 20px;
        }
        .painel-wrap.open { display: block; }
        .painel-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .painel-icon {
            width: 38px; height: 38px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; flex-shrink: 0; color: #fff;
        }
        .painel-icon.ok    { background: #3B6D11; }
        .painel-icon.avaria{ background: #A32D2D; }
        .painel-icon.manut { background: #854F0B; }
        .painel-icon.misto { background: #993556; }
        .painel-title { font-size: 1rem; font-weight: 700; color: var(--text); }
        .painel-sub   { font-size: .75rem; color: var(--muted); }
        .painel-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 7px 0; border-bottom: 1px solid var(--border); font-size: .85rem;
        }
        .painel-row:last-of-type { border-bottom: none; }
        .painel-lbl { color: var(--muted); }
        .painel-val { font-weight: 700; color: var(--text); }
        .painel-val.danger  { color: var(--danger); }
        .painel-val.warning { color: #c8860a; }
        .painel-val.success { color: #148f5a; }
        .painel-actions { display: flex; gap: 8px; margin-top: 16px; }
        .btn-painel {
            padding: 8px 16px; border-radius: 8px; font-size: .82rem;
            font-weight: 700; cursor: pointer; border: none; transition: opacity .15s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-painel:hover { opacity: .85; }
        .btn-painel.primary   { background: var(--primary); color: #fff; }
        .btn-painel.secondary { background: #f0f4fb; color: var(--primary-dk); border: 1px solid var(--border); }

        .empty-state {
            text-align: center; padding: 40px; color: var(--muted);
        }
        .empty-state i { font-size: 2rem; display: block; margin-bottom: 10px; }

        /* ── Lista de equipamentos no painel ── */
        .equip-list { margin-top: 16px; }
        .equip-list-title {
            font-size: .72rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
        }
        .equip-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 8px; margin-bottom: 4px;
            background: #f7f9fe; border: 1px solid var(--border);
            font-size: .82rem; transition: background .15s;
        }
        .equip-item:hover { background: #eef1f9; }
        .equip-dot {
            width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0;
        }
        .equip-dot.ok     { background: #1cc88a; }
        .equip-dot.avaria { background: var(--danger); }
        .equip-nome { flex: 1; font-weight: 600; color: var(--text); }
        .equip-tipo { font-size: .72rem; color: var(--muted); }
        .equip-badge-av {
            font-size: .65rem; font-weight: 700; padding: 2px 6px;
            border-radius: 4px; background: #fde8e8; color: #a32d2d; white-space: nowrap;
        }
        .equip-link {
            color: #fff; text-decoration: none; font-size: .78rem;
            font-weight: 700; padding: 4px 10px; border-radius: 6px;
            border: none; background: var(--primary); white-space: nowrap;
            display: inline-flex; align-items: center; gap: 5px;
            box-shadow: 0 1px 4px rgba(75,108,183,.35);
            transition: background .15s, transform .1s;
            margin-left: auto; flex-shrink: 0;
        }
        .equip-link:hover { background: #182848; color: #fff; transform: translateY(-1px); }
        .equip-list-loading {
            text-align: center; padding: 20px; color: var(--muted); font-size: .82rem;
        }
        .equip-list-loading i { display: block; font-size: 1.2rem; margin-bottom: 6px; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .painel-wrap { transition: all .2s; }
    </style>
</head>

<body class="main-layout">
    <?php include('loader.php'); ?>
    <?php include('header.php'); ?>

    <?php
    include('sessao_timeout.php');

    // ── Escolas disponíveis ───────────────────────────────────────────────────
    $escolas = [];
    $res_esc = mysqli_query($db, "SELECT id, nome_escola FROM escolas ORDER BY id");
    while ($row = mysqli_fetch_assoc($res_esc)) { $escolas[] = $row; }

    $esc_id = 0;
    if (!empty($_POST['escola'])) {
        $esc_id = (int)$_POST['escola'];
    } elseif (!empty($_GET['esc'])) {
        $esc_id = (int)base64_decode($_GET['esc']);
    }
    if ($esc_id === 0 && !empty($escolas)) {
        $esc_id = (int)$escolas[0]['id'];
    }

    $filtro_estado = $_POST['filtro_estado'] ?? $_GET['fe'] ?? 'all';

    // ── Query principal ───────────────────────────────────────────────────────
    $stmt = $db->prepare("
        SELECT
            s.id,
            s.nome,
            s.localizacao,
            COUNT(DISTINCT eq.id)                                                AS total_equip,
            COUNT(DISTINCT CASE WHEN ar.datareparacao IS NULL THEN ar.id_equi END)  AS avarias_abertas,
            COUNT(DISTINCT CASE WHEN t.data_conclusao IS NULL THEN t.id END)     AS tarefas_pendentes
        FROM salas s
        LEFT JOIN equipamento eq ON eq.id_sala = s.id
        LEFT JOIN avarias_reparacoes ar ON ar.id_sala = s.id AND ar.datareparacao IS NULL
        LEFT JOIN tarefas t ON t.id_sala = s.id AND t.data_conclusao IS NULL
        WHERE s.id_escola = ?
        GROUP BY s.id, s.nome, s.localizacao
        ORDER BY s.localizacao, s.nome
    ");
    $stmt->bind_param('i', $esc_id);
    $stmt->execute();
    $salas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // ── Heatmap — avarias históricas por período ──────────────────────────────
    $periodo = isset($_POST['periodo']) ? (int)$_POST['periodo'] : (isset($_GET['p']) ? (int)$_GET['p'] : 30);
    $periodos_validos = [7, 30, 90, 365];
    if (!in_array($periodo, $periodos_validos)) $periodo = 30;

    $stmt_heat = $db->prepare("
        SELECT ar.id_sala, COUNT(DISTINCT ar.id_equi) AS total_avarias
        FROM avarias_reparacoes ar
        INNER JOIN salas s ON s.id = ar.id_sala
        WHERE s.id_escola = ?
          AND ar.dataavaria >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY ar.id_sala
    ");
    $stmt_heat->bind_param('ii', $esc_id, $periodo);
    $stmt_heat->execute();
    $heat_rows = $stmt_heat->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_heat->close();

    $heat_map = [];
    foreach ($heat_rows as $row) {
        $heat_map[$row['id_sala']] = (int)$row['total_avarias'];
    }
    $heat_max = $heat_map ? max($heat_map) : 1;

    // ── KPIs ──────────────────────────────────────────────────────────────────
    $total    = count($salas);
    $com_av   = count(array_filter($salas, function($s) { return $s['avarias_abertas'] > 0; }));
    $com_mn   = count(array_filter($salas, function($s) { return $s['tarefas_pendentes'] > 0; }));
    $sem_prob = count(array_filter($salas, function($s) { return $s['avarias_abertas'] == 0 && $s['tarefas_pendentes'] == 0; }));

    // Agrupar por localização
    $grupos = [];
    foreach ($salas as $s) {
        $loc = trim($s['localizacao']) ?: 'Sem localização';
        $grupos[$loc][] = $s;
    }
    ksort($grupos);

    // Nome da escola seleccionada
    $nome_escola_sel = '';
    foreach ($escolas as $e) {
        if ((int)$e['id'] === $esc_id) { $nome_escola_sel = $e['nome_escola']; break; }
    }
    ?>

    <div class="about">
      <div class="container-fluid">
  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
               
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        
                 <a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a>
            <li style="color:#c5cde0;">&#8250;</li>
                <li style="color:#1e2a45;">Planta - Salas</li>
</ol>
        <div class="planta-wrap">

          <div class="page-title">
            <div class="page-title-icon"><i class="fa-solid fa-map-location-dot"></i></div>
               
            <div>
        
              <h1>Estado em tempo real das salas — avarias, manutenções e equipamentos</h1>
            </div>
          </div>

          <!-- Filtros -->
          <form method="POST" action="" id="frmFiltro">
          <div class="filter-card">

            <div>
              <label>Instituição</label><br>
              <select name="escola" onchange="document.getElementById('frmFiltro').submit()">
                <?php foreach ($escolas as $e): ?>
                  <option value="<?php echo (int)$e['id']; ?>"
                    <?php echo ((int)$e['id'] === $esc_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($e['nome_escola']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label>Estado</label><br>
              <select name="filtro_estado" onchange="filtrarEstado(this.value)">
                <option value="all"    <?php echo $filtro_estado==='all'    ?'selected':''; ?>>Todas as salas</option>
                <option value="avaria" <?php echo $filtro_estado==='avaria' ?'selected':''; ?>>Com avarias</option>
                <option value="manut"  <?php echo $filtro_estado==='manut'  ?'selected':''; ?>>Com manutenções</option>
                <option value="ok"     <?php echo $filtro_estado==='ok'     ?'selected':''; ?>>Sem problemas</option>
              </select>
            </div>

            <div class="refresh-info">
              <div class="refresh-dot"></div>
              Actualizado em <?php echo (new DateTime('now', new DateTimeZone('Europe/Lisbon')))->format('H:i'); ?>
            </div>

            <!-- Heatmap toggle -->
            <div style="margin-left:8px;">
              <label>Visualização</label><br>
              <button type="button" id="btnHeat" onclick="toggleHeatmap()"
                      class="btn-painel secondary" style="height:34px;">
                <i class="fa fa-fire"></i> Heatmap
              </button>
            </div>

            <!-- Período (só visível em modo heatmap) -->
            <div id="heatPeriodoWrap" style="display:none;">
              <label>Período</label><br>
              <select name="periodo" onchange="document.getElementById('frmFiltro').submit()">
                <option value="7"   <?php echo $periodo==7   ?'selected':''; ?>>Últimos 7 dias</option>
                <option value="30"  <?php echo $periodo==30  ?'selected':''; ?>>Últimos 30 dias</option>
                <option value="90"  <?php echo $periodo==90  ?'selected':''; ?>>Últimos 90 dias</option>
                <option value="365" <?php echo $periodo==365 ?'selected':''; ?>>Último ano</option>
              </select>
            </div>

          </div>
          </form>

          <!-- Legenda heatmap (oculta por defeito) -->
          <div class="heat-legend-bar" id="heatLegendaBar">
            <i class="fa fa-fire" style="color:#e74a3b;"></i>
            <strong style="font-size:.75rem;color:var(--text);">Heatmap de avarias</strong>
            <span>0</span>
            <div class="heat-gradient"></div>
            <span id="heatMaxLabel">máx</span>
            <span class="heat-periodo-label" id="heatPeriodoLabel"></span>
          </div>

          <!-- KPIs -->
          <div class="kpi-row">
            <div class="kpi">
              <div class="kpi-val"><?php echo $total; ?></div>
              <div class="kpi-lbl">Salas totais</div>
            </div>
            <div class="kpi c-danger">
              <div class="kpi-val"><?php echo $com_av; ?></div>
              <div class="kpi-lbl">Com avarias abertas</div>
            </div>
            <div class="kpi c-warning">
              <div class="kpi-val"><?php echo $com_mn; ?></div>
              <div class="kpi-lbl">Com tarefas pendentes</div>
            </div>
            <div class="kpi c-success">
              <div class="kpi-val"><?php echo $sem_prob; ?></div>
              <div class="kpi-lbl">Sem problemas</div>
            </div>
          </div>

          <!-- Legenda -->
          <div class="legend">
            <div class="leg"><div class="leg-box leg-ok"></div> Sem problemas</div>
            <div class="leg"><div class="leg-box leg-av"></div> Avaria aberta</div>
            <div class="leg"><div class="leg-box leg-mn"></div> Tarefa pendente</div>
            <div class="leg"><div class="leg-box leg-misto"></div> Avaria + Manutenção</div>
          </div>

          <!-- Planta -->
          <div class="planta-canvas">
            <?php if (empty($salas)): ?>
              <div class="empty-state">
                <i class="fa fa-map"></i>
                <p>Nenhuma sala encontrada para esta escola.</p>
              </div>
            <?php else: ?>
              <?php foreach ($grupos as $loc => $grupo_salas): ?>
                <div class="piso-label">
                  <?php echo htmlspecialchars($loc); ?>
                  <span style="font-weight:400; color:var(--muted); font-size:.68rem; margin-left:6px;">
                    (<?php echo count($grupo_salas); ?> sala<?php echo count($grupo_salas) !== 1 ? 's' : ''; ?>)
                  </span>
                </div>
                <div class="piso-block">
                  <div class="corredor"><span>Salas</span></div>
                  <div class="salas-row">
                    <?php foreach ($grupo_salas as $s):
                      $av  = (int)$s['avarias_abertas'];
                      $mn  = (int)$s['tarefas_pendentes'];
                      $eq  = (int)$s['total_equip'];
                      if ($av > 0 && $mn > 0) $est = 'misto';
                      elseif ($av > 0)         $est = 'avaria';
                      elseif ($mn > 0)         $est = 'manut';
                      else                     $est = 'ok';

                      if ($est === 'avaria')      { $icon = 'fa-triangle-exclamation'; }
                      elseif ($est === 'manut')   { $icon = 'fa-wrench'; }
                      elseif ($est === 'misto')   { $icon = 'fa-circle-exclamation'; }
                      else                        { $icon = 'fa-circle-check'; }
                    ?>
                    <div class="sala <?php echo $est; ?>"
                         data-estado="<?php echo $est; ?>"
                         data-sala-id="<?php echo $s['id']; ?>"
                         onclick="abrirModal(<?php echo $s['id']; ?>, '<?php echo addslashes(htmlspecialchars($s['nome'])); ?>', '<?php echo addslashes(htmlspecialchars($loc)); ?>', '<?php echo $est; ?>', <?php echo $av; ?>, <?php echo $mn; ?>, <?php echo $eq; ?>, '<?php echo $esc_id; ?>')"
                         title="<?php echo htmlspecialchars($s['nome']); ?>">
                      <div class="sala-nome"><?php echo htmlspecialchars($s['nome']); ?></div>
                      <div class="sala-badges">
                        <?php if ($av > 0): ?><span class="sbadge av"><?php echo $av; ?> av.</span><?php endif; ?>
                        <?php if ($mn > 0): ?><span class="sbadge mn"><?php echo $mn; ?> tar.</span><?php endif; ?>
                        <span class="sbadge eq"><?php echo $eq; ?> eq.</span>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Painel de detalhe inline -->
          <div class="painel-wrap" id="painelWrap">
            <div id="painelContent"></div>
          </div>

        </div>
      </div>
    </div>

    <script>
    const SVRURL = '<?php echo SVRURL; ?>';
    const ESC_ID = '<?php echo base64_encode($esc_id); ?>';

    // ── Heatmap data ──────────────────────────────────────────────────────────
    const HEAT_DATA    = <?php echo json_encode($heat_map); ?>;
    const HEAT_MAX     = <?php echo max(1, $heat_max); ?>;
    const HEAT_PERIODO = <?php echo $periodo; ?>;
    const periodoLabels = { 7:'Últimos 7 dias', 30:'Últimos 30 dias', 90:'Últimos 90 dias', 365:'Último ano' };

    let heatmapAtivo = false;

    function toggleHeatmap() {
        heatmapAtivo = !heatmapAtivo;
        const btn      = document.getElementById('btnHeat');
        const periodoW = document.getElementById('heatPeriodoWrap');
        const legenda  = document.getElementById('heatLegendaBar');

        btn.innerHTML = heatmapAtivo
            ? '<i class="fa fa-map"></i> Modo Normal'
            : '<i class="fa fa-fire"></i> Heatmap';
        btn.classList.toggle('primary',   heatmapAtivo);
        btn.classList.toggle('secondary', !heatmapAtivo);

        periodoW.style.display = heatmapAtivo ? '' : 'none';
        legenda.classList.toggle('visible', heatmapAtivo);

        document.getElementById('heatMaxLabel').textContent    = HEAT_MAX + ' av.';
        document.getElementById('heatPeriodoLabel').textContent = periodoLabels[HEAT_PERIODO] || (HEAT_PERIODO + ' dias');

        document.querySelectorAll('.sala').forEach(el => {
            const id    = parseInt(el.dataset.salaId);
            const count = HEAT_DATA[id] || 0;
            const ratio = HEAT_MAX > 0 ? count / HEAT_MAX : 0;

            if (heatmapAtivo) {
                el.classList.add('heat-mode');
                el.style.setProperty('--heat', ratio.toFixed(3));
                el.title = `${el.querySelector('.sala-nome').textContent.trim()} — ${count} avaria(s) nos últimos ${HEAT_PERIODO} dias`;
            } else {
                el.classList.remove('heat-mode');
                el.style.removeProperty('--heat');
                el.title = el.querySelector('.sala-nome').textContent.trim();
            }
        });

        if (heatmapAtivo) fecharModal();
    }

    const labels = {
        ok:     'Sem problemas',
        avaria: 'Avaria aberta',
        manut:  'Manutenção pendente',
        misto:  'Avaria + Manutenção'
    };
    const icons = {
        ok:     'fa-circle-check',
        avaria: 'fa-triangle-exclamation',
        manut:  'fa-wrench',
        misto:  'fa-circle-exclamation'
    };

    let salaAtiva = null;

    function abrirModal(id, nome, loc, estado, av, mn, eq, esc) {

        // Se clicar na mesma sala aberta → fechar
        if (salaAtiva === id) {
            fecharModal();
            return;
        }
        salaAtiva = id;

        // Realçar sala seleccionada
        document.querySelectorAll('.sala').forEach(el => el.classList.remove('sala-selected'));
        document.querySelector(`.sala[data-sala-id="${id}"]`)?.classList.add('sala-selected');

        const valAv = av > 0 ? `<span class="painel-val danger">${av}</span>` : `<span class="painel-val success">0</span>`;
        const valMn = mn > 0 ? `<span class="painel-val warning">${mn}</span>` : `<span class="painel-val success">0</span>`;

        const escB64  = btoa(String(esc));
        const salaB64 = btoa(String(id));
        const urlSala = `${SVRURL}ver_equipamentos_sala.php?x=${btoa('2')}&&si=${salaB64}&&ies=${escB64}`;

        document.getElementById('painelContent').innerHTML = `
            <div class="painel-header">
                <div class="painel-icon ${estado}"><i class="fa ${icons[estado]}"></i></div>
                <div style="flex:1;">
                    <div class="painel-title">${nome}</div>
                    <div class="painel-sub">${loc}</div>
                </div>
                <button class="btn-painel secondary" onclick="fecharModal()" title="Fechar" style="margin-left:auto;padding:5px 10px;">
                    <i class="fa fa-xmark"></i>
                </button>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                <div style="flex:1;min-width:100px;background:#f7f9fe;border:1px solid var(--border);border-radius:8px;padding:10px 14px;text-align:center;">
                    <div style="font-size:1.4rem;font-weight:800;color:var(--primary-dk);">${eq}</div>
                    <div style="font-size:.7rem;color:var(--muted);">Equipamentos</div>
                </div>
                <div style="flex:1;min-width:100px;background:#fde8e8;border:1px solid #f5c6c6;border-radius:8px;padding:10px 14px;text-align:center;">
                    <div style="font-size:1.4rem;font-weight:800;color:var(--danger);">${av}</div>
                    <div style="font-size:.7rem;color:#a32d2d;">Avarias abertas</div>
                </div>
                <div style="flex:1;min-width:100px;background:#faeeda;border:1px solid #e8c98a;border-radius:8px;padding:10px 14px;text-align:center;">
                    <div style="font-size:1.4rem;font-weight:800;color:#854f0b;">${mn}</div>
                    <div style="font-size:.7rem;color:#854f0b;">Tarefas pendentes</div>
                </div>
            </div>

            <div class="equip-list">
                <div class="equip-list-title">
                    <i class="fa fa-desktop"></i> Equipamentos nesta sala
                </div>
                <div id="equipListBody">
                    <div class="equip-list-loading">
                        <i class="fa fa-spinner"></i> A carregar equipamentos…
                    </div>
                </div>
            </div>

            <div class="painel-actions" style="margin-top:14px;">
                <a href="${urlSala}" class="btn-painel primary">
                    <i class="fa fa-arrow-up-right-from-square"></i> Ver sala completa
                </a>
                <button class="btn-painel secondary" onclick="fecharModal()">Fechar</button>
            </div>
        `;

        const painel = document.getElementById('painelWrap');
        painel.classList.add('open');
        painel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Carregar lista de equipamentos via AJAX
        carregarEquipamentos(id, esc, eq);
    }

    function carregarEquipamentos(salaId, escId, totalEq) {
        const params = new URLSearchParams({ sala: salaId, esc: escId });
        fetch(`${SVRURL}planta_equip_ajax.php?${params}`)
            .then(r => r.json())
            .then(data => renderEquipamentos(data, escId))
            .catch(() => {
                document.getElementById('equipListBody').innerHTML =
                    `<div style="color:var(--muted);font-size:.82rem;padding:10px;">
                        Não foi possível carregar os equipamentos.
                     </div>`;
            });
    }

    function renderEquipamentos(equips, escId) {
        const el = document.getElementById('equipListBody');
        if (!el) return;

        if (!equips || equips.length === 0) {
            el.innerHTML = `<div style="color:var(--muted);font-size:.82rem;padding:10px;">Sem equipamentos registados.</div>`;
            return;
        }

        el.innerHTML = equips.map(eq => {
            const avariado = parseInt(eq.avarias) > 0;
            const dot      = avariado ? 'avaria' : 'ok';
            const badgeAv  = avariado ? `<span class="equip-badge-av"><i class="fa fa-triangle-exclamation"></i> ${eq.avarias} av.</span>` : '';
            const idB64    = btoa(String(eq.id));
            const salaB64  = btoa(String(eq.id_sala));
            const escB64   = btoa(String(escId));
            const urlFicha = `${SVRURL}ficha_equipamento.php?ide=${idB64}&&sai=${salaB64}&&ies=${escB64}`;

            return `
                <div class="equip-item">
                    <span class="equip-dot ${dot}"></span>
                    <span class="equip-nome">${eq.nomeequi}</span>
                    <span class="equip-tipo">${eq.tipo || ''}</span>
                    ${badgeAv}
                    <a href="${urlFicha}" class="equip-link" title="Ver ficha">
                        <i class="fa fa-file-lines"></i> Ficha
                    </a>
                </div>`;
        }).join('');
    }

    function fecharModal() {
        document.getElementById('painelWrap').classList.remove('open');
        document.querySelectorAll('.sala').forEach(el => el.classList.remove('sala-selected'));
        salaAtiva = null;
    }

    function filtrarEstado(val) {
        document.querySelectorAll('.sala').forEach(el => {
            const est = el.dataset.estado;
            const mostrar = val === 'all'
                || est === val
                || (val === 'avaria' && est === 'misto')
                || (val === 'manut'  && est === 'misto');
            el.classList.toggle('hidden', !mostrar);
        });
    }

    // Aplicar filtro inicial se vier do POST
    filtrarEstado('<?php echo addslashes($filtro_estado); ?>');

    // Auto-refresh a cada 60 segundos (só se painel fechado)
    setTimeout(() => { if (!salaAtiva) location.reload(); }, 60000);
    </script>

    <?php include('footer.php'); ?>

    <!-- ═══ TEMA ESCURO — fix stopPropagation ═══ -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                window.GEITheme.toggle();
            }, true);
        });
    });
    </script>
    <!-- ══════════════════════════════════════════ -->
      <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>
