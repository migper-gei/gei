<?php
// ============================================================
// dashboard.php — Dashboard GEI
// Pode ser acedido directamente (standalone) ou incluído
// noutro ficheiro (ex: ecraboas.php) com a flag:
//   define('DASHBOARD_EMBEDDED', true);
// ============================================================

$_dash_embedded = defined('DASHBOARD_EMBEDDED') && DASHBOARD_EMBEDDED;

// Sessão segura — só inicia se ainda não estiver activa
if (!$_dash_embedded) {
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
}

if (!$_dash_embedded):
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include("head.php"); ?>
<?php endif; ?>

    <!-- Fontes carregadas globalmente em head.php — DM Sans e DM Mono já disponíveis via tokens.css -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        /* ── Base & Reset ─────────────────────────────── */
        :root {
            --bg:          #f0f4fb;
            --surface:     #ffffff;
            --surface2:    #f7f9fe;
            --primary:     #4b6cb7;
            --primary-dk:  #182848;
            --accent:      #507feb;
            --accent2:     #36b9cc;
            --success:     #1cc88a;
            --warning:     #f6c23e;
            --danger:      #e74a3b;
            --border:      #e3e8f4;
            --text:        #1e2a45;
            --text-muted:  #7b88a0;
            --radius:      14px;
            --shadow:      0 2px 16px rgba(75,108,183,.10);
            --shadow-lg:   0 8px 32px rgba(75,108,183,.16);
        }

        /* ═══ DARK MODE — remapeia as variáveis locais para as do dark-theme.css ══ */
        [data-theme="dark"] {
            /* Remapeia nomes locais → valores dark do dark-theme.css */
            --bg:         #0f1117;       /* = --bg-page       */
            --surface:    #1a1d27;       /* = --bg-card       */
            --surface2:   #1e2130;       /* = --bg-table-alt  */
            --primary:    #6489f5;       /* = --accent        */
            --primary-dk: #e2e8f0;       /* = --text-heading  */
            --accent:     #7b9bf7;       /* = --accent-hover  */
            --accent2:    #56c4d8;
            --success:    #26d49a;
            --warning:    #f6c23e;
            --danger:     #f07167;
            --border:     #2d3348;       /* = --border-color  */
            --text:       #e2e8f0;       /* = --text-primary  */
            --text-muted: #94a3b8;       /* = --text-secondary*/
            --shadow:     0 2px 16px rgba(0,0,0,.4);
            --shadow-lg:  0 8px 32px rgba(0,0,0,.55);
        }
        /* Elementos específicos do dashboard que o dark-theme.css não cobre */
        html[data-theme="dark"] .about                { background: #0f1117 !important; }
        html[data-theme="dark"] .dash-wrap            { background: #0f1117; }
        html[data-theme="dark"] body.main-layout      { background: #0f1117 !important; }
        [data-theme="dark"] .kpi                      { background: var(--surface); border-left-color: currentColor; }
        [data-theme="dark"] .kpi-label,
        [data-theme="dark"] .kpi-sub                  { color: var(--text-muted); }
        [data-theme="dark"] .panel                    { background: var(--surface); }
        [data-theme="dark"] .panel-head               { border-bottom-color: var(--border); }
        [data-theme="dark"] .panel-head h2            { color: var(--primary-dk); }
        [data-theme="dark"] .school-bar               { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .school-bar select,
        [data-theme="dark"] #filtro-sala,
        [data-theme="dark"] #filtro-ano-salas         { background: var(--surface2); color: var(--text); border-color: var(--border); }
        [data-theme="dark"] .prog-bar                 { background: var(--border); }
        [data-theme="dark"] .avaria-item              { border-bottom-color: var(--border); }
        [data-theme="dark"] .avaria-title             { color: var(--text); }
        [data-theme="dark"] .avaria-meta              { color: var(--text-muted); }
        [data-theme="dark"] .tarefa-row               { border-color: var(--border) !important; }
        [data-theme="dark"] .tarefa-check             { border-color: var(--border); }
        [data-theme="dark"] .tarefa-text              { color: var(--text); }
        [data-theme="dark"] .tarefa-sala              { color: var(--text-muted); }
        [data-theme="dark"] .empty-state              { color: var(--text-muted); }
        [data-theme="dark"] .auto-refresh-track       { background: var(--border); }
        [data-theme="dark"] .last-update              { color: var(--text-muted); }
        [data-theme="dark"] .dash-title h1            { color: var(--primary-dk); }
        /* Badges do dashboard (sobrepõe o dark-theme.css que usa Bootstrap badge-*) */
        [data-theme="dark"] .badge-danger             { background: rgba(240,113,103,.18) !important; color: #f07167 !important; }
        [data-theme="dark"] .badge-warning            { background: rgba(246,194,62,.15)  !important; color: #f6c23e !important; }
        [data-theme="dark"] .badge-success            { background: rgba(38,212,154,.15)  !important; color: #26d49a !important; }
        [data-theme="dark"] .badge-info               { background: rgba(107,141,214,.18) !important; color: #7aa2f7 !important; }
        [data-theme="dark"] .badge-purple             { background: rgba(111,66,193,.22)  !important; color: #b48ef7 !important; }
        /* ══════════════════════════════════════════════════════════════════════════ */

        body.main-layout { background: var(--bg); font-family: var(--font-body); color: var(--text); }

        .dash-wrap { padding: 28px 32px 48px; max-width: 1440px; margin: 0 auto; }

        /* ── Page Title ───────────────────────────────── */
        .dash-title {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 28px;
        }
        .dash-title-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(75,108,183,.35);
        }
        .dash-title h1 { font-size: 1.45rem; font-weight: 700; margin: 0; color: var(--primary-dk); letter-spacing: -.4px; }
        .dash-title p  { margin: 0; font-size: .82rem; color: var(--text-muted); }

        /* ── School Filter ────────────────────────────── */
        .school-bar {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 14px 20px;
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 24px; box-shadow: var(--shadow);
            flex-wrap: wrap;
        }
        .school-bar label { font-size: .82rem; font-weight: 600; color: var(--text-muted); white-space: nowrap; }
        .school-bar select {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 7px 12px; font-family: inherit; font-size: .88rem;
            color: var(--text); background: var(--surface2); cursor: pointer;
            transition: border .2s;
        }
        .school-bar select:focus { outline: none; border-color: var(--accent); }
        .refresh-btn {
            margin-left: auto; background: var(--primary); color: #fff; border: none;
            border-radius: 8px; padding: 8px 18px; font-size: .82rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: background .2s, transform .15s;
        }
        .refresh-btn:hover { background: var(--accent); transform: translateY(-1px); }
        .last-update { font-size: .75rem; color: var(--text-muted); margin-left: 4px; }
        .auto-refresh-wrap {
            display: flex; align-items: center; gap: 8px;
            font-size: .75rem; color: var(--text-muted);
        }
        .auto-refresh-track {
            width: 80px; height: 4px; background: var(--border);
            border-radius: 99px; overflow: hidden;
        }
        .auto-refresh-fill {
            height: 4px; border-radius: 99px;
            background: var(--primary);
            transition: width 1s linear;
            width: 100%;
        }

        /* ── KPI Cards ────────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px; margin-bottom: 24px;
        }
        @media (max-width: 1200px) {
            .kpi-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 768px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        .kpi {
            background: var(--surface); border-radius: var(--radius);
            padding: 10px 11px; box-shadow: var(--shadow);
            border-left: 3px solid transparent;
            display: flex; flex-direction: column; gap: 2px;
            transition: box-shadow .2s, transform .2s;
            position: relative; overflow: hidden;
        }
        .kpi:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
        .kpi.c-primary  { border-color: var(--primary); }
        .kpi.c-accent2  { border-color: var(--accent2); }
        .kpi.c-success  { border-color: var(--success); }
        .kpi.c-warning  { border-color: var(--warning); }
        .kpi.c-danger   { border-color: var(--danger);  }
        .kpi.c-info     { border-color: #6f42c1; }

        .kpi-icon {
            width: 26px; height: 26px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; color: #fff; margin-bottom: 2px;
        }
        .c-primary  .kpi-icon { background: var(--primary); }
        .c-accent2  .kpi-icon { background: var(--accent2); }
        .c-success  .kpi-icon { background: var(--success); }
        .c-warning  .kpi-icon { background: var(--warning); }
        .c-danger   .kpi-icon { background: var(--danger);  }
        .c-info     .kpi-icon { background: #6f42c1; }

        .kpi-val  { font-size: 1.45rem; font-weight: 700; line-height: 1; font-family: var(--font-mono); }
        .kpi-label{ font-size: .68rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .4px; }
        .kpi-sub  { font-size: .65rem; color: var(--text-muted); }

        /* ── Chart / Table Grid ───────────────────────── */
        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px; margin-bottom: 20px;
        }
        .panel-grid.cols3 { grid-template-columns: 2fr 1fr; }
        .panel-grid.full  { grid-template-columns: 1fr; }

        .panel {
            background: var(--surface); border-radius: var(--radius);
            box-shadow: var(--shadow); overflow: hidden;
        }
        .panel-head {
            padding: 16px 20px 12px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .panel-head-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; color: #fff;
        }
        .panel-head h2 { margin: 0; font-size: .95rem; font-weight: 700; color: var(--primary-dk); }
        .panel-body { padding: 18px 20px; }
        .panel-body.no-pad { padding: 0; }
        .chart-wrap { position: relative; height: 240px; }
        .chart-wrap-sm { position: relative; height: 200px; }

        /* ── Tables ───────────────────────────────────── */
        .dash-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
        .dash-table thead th {
            padding: 10px 14px; text-align: left;
            background: var(--surface2); font-weight: 600;
            color: var(--text-muted); font-size: .74rem;
            text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
        }
        .dash-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .dash-table tbody tr:last-child { border-bottom: none; }
        .dash-table tbody tr:hover { background: var(--surface2); }
        .dash-table td { padding: 10px 14px; vertical-align: middle; }

        /* ── Badges ───────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: .72rem; font-weight: 600;
        }
        .badge-danger  { background: #fde8e6; color: var(--danger); }
        .badge-warning { background: #fef7e0; color: #c69500; }
        .badge-success { background: #e0f7f0; color: #13a073; }
        .badge-info    { background: #e8f0fe; color: var(--primary); }
        .badge-purple  { background: #ede8fc; color: #6f42c1; }

        /* ── Progress Bar ─────────────────────────────── */
        .prog-bar { background: var(--border); border-radius: 99px; height: 6px; margin-top: 4px; }
        .prog-fill { height: 6px; border-radius: 99px; background: var(--primary); transition: width 1s ease; }

        /* ── Avarias recentes list ─────────────────────── */
        .avaria-item {
            padding: 10px 0; border-bottom: 1px solid var(--border);
            display: flex; gap: 12px; align-items: flex-start;
        }
        .avaria-item:last-child { border-bottom: none; }
        .avaria-dot {
            width: 8px; height: 8px; border-radius: 50%;
            margin-top: 5px; flex-shrink: 0;
        }
        .avaria-dot.open   { background: var(--danger); }
        .avaria-dot.fixed  { background: var(--success); }
        .avaria-dot.inprog { background: var(--warning); }
        .avaria-title { font-size: .84rem; font-weight: 600; color: var(--text); }
        .avaria-meta  { font-size: .73rem; color: var(--text-muted); margin-top: 1px; }

        /* ── Tarefas section ──────────────────────────── */
        .tarefa-row { display: flex; gap: 10px; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border); }
        .tarefa-row:last-child { border-bottom: none; }
        .tarefa-check { width: 16px; height: 16px; border-radius: 4px; border: 2px solid var(--border); flex-shrink: 0; }
        .tarefa-text  { font-size: .83rem; color: var(--text); }
        .tarefa-sala  { font-size: .72rem; color: var(--text-muted); margin-left: auto; white-space: nowrap; }

        /* ── Empty state ──────────────────────────────── */
        .empty-state { text-align: center; padding: 32px 20px; color: var(--text-muted); font-size: .85rem; }
        .empty-state i { font-size: 2rem; margin-bottom: 8px; display: block; opacity: .35; }

        /* ── Fix tema: .about nao deve cortar o conteudo ─ */
        .about {
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            display: block !important;
        }

        /* ── Comparison Panel ────────────────────────── */
        .comparacao-bar {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 14px 20px;
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 24px; box-shadow: var(--shadow);
            flex-wrap: wrap;
        }
        .comparacao-bar label { font-size: .82rem; font-weight: 600; color: var(--text-muted); white-space: nowrap; }
        .comparacao-bar select {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 7px 12px; font-family: inherit; font-size: .88rem;
            color: var(--text); background: var(--surface2); cursor: pointer;
        }
        .comparacao-bar select:focus { outline: none; border-color: var(--accent); }
        .btn-comparar {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff; border: none; border-radius: 8px; padding: 8px 18px;
            font-size: .82rem; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 7px;
            transition: opacity .2s, transform .15s;
        }
        .btn-comparar:hover { opacity: .88; transform: translateY(-1px); }

        .kpi-cmp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px; margin-bottom: 24px;
        }
        .kpi-cmp {
            background: var(--surface2);
            border: 1.5px dashed var(--border);
            border-radius: var(--radius);
            padding: 16px 20px; box-shadow: none;
            display: flex; flex-direction: column; gap: 4px;
            position: relative;
        }
        .kpi-cmp-badge {
            position: absolute; top: 10px; right: 12px;
            font-size: .68rem; font-weight: 700; border-radius: 20px;
            padding: 2px 8px;
        }
        .kpi-cmp-badge.up   { background: #e0f7f0; color: #13a073; }
        .kpi-cmp-badge.down { background: #fde8e6; color: var(--danger); }
        .kpi-cmp-badge.same { background: var(--border); color: var(--text-muted); }
        .kpi-cmp-val  { font-size: 1.6rem; font-weight: 700; font-family: var(--font-mono); color: var(--text-muted); }
        .kpi-cmp-label{ font-size: .76rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }
        .kpi-cmp-lbl-periodo { font-size: .7rem; color: var(--text-muted); }

        [data-theme="dark"] .kpi-cmp { background: var(--surface2); border-color: var(--border); }
        [data-theme="dark"] .kpi-cmp-badge.up   { background: rgba(38,212,154,.18); color: #26d49a; }
        [data-theme="dark"] .kpi-cmp-badge.down { background: rgba(240,113,103,.18); color: #f07167; }
        [data-theme="dark"] .comparacao-bar select { background: var(--surface2); color: var(--text); border-color: var(--border); }
        [data-theme="dark"] .comparacao-bar { background: var(--surface); border-color: var(--border); }

        /* ── Heatmap ──────────────────────────────────── */
        .heatmap-wrap { overflow-x: auto; }
        .heatmap-table { border-collapse: collapse; width: 100%; min-width: 600px; }
        .heatmap-table th {
            font-size: .72rem; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .5px;
            padding: 6px 8px; white-space: nowrap;
        }
        .heatmap-table td {
            padding: 5px 6px; text-align: center; font-size: .75rem;
            font-family: var(--font-mono); font-weight: 600;
            border-radius: 5px; cursor: default; transition: filter .15s;
        }
        .heatmap-table td:hover { filter: brightness(1.1); }
        .heatmap-sala {
            text-align: left !important; font-family: var(--font-body) !important;
            font-size: .8rem !important; font-weight: 600 !important;
            color: var(--text) !important; white-space: nowrap;
            padding-right: 12px !important;
        }
        .heatmap-legend {
            display: flex; gap: 8px; align-items: center;
            margin-top: 12px; flex-wrap: wrap; font-size: .72rem; color: var(--text-muted);
        }
        .heatmap-legend-swatch {
            width: 18px; height: 12px; border-radius: 3px; display: inline-block;
        }
        #heatmap-ano-select {
            border: 1.5px solid var(--border); border-radius: 7px;
            padding: 4px 10px; font-size: .8rem; font-family: inherit;
            color: var(--text); background: var(--surface2); cursor: pointer;
        }
        [data-theme="dark"] #heatmap-ano-select { background: var(--surface2); color: var(--text); border-color: var(--border); }

        /* ── Export buttons ───────────────────────────── */
        .btn-export {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: 8px; font-size: .82rem;
            font-weight: 600; cursor: pointer; border: 1.5px solid var(--border);
            background: var(--surface); color: var(--text);
            transition: border-color .2s, background .2s, transform .15s;
            text-decoration: none;
        }
        .btn-export:hover { border-color: var(--accent); background: var(--surface2); transform: translateY(-1px); }
        .btn-export.pdf  { border-color: #e74a3b; color: #e74a3b; }
        .btn-export.pdf:hover { background: #fde8e6; }
        .btn-export.word { border-color: #2b579a; color: #2b579a; }
        .btn-export.word:hover { background: #e8f0fe; }
        [data-theme="dark"] .btn-export.pdf:hover  { background: rgba(240,113,103,.15); }
        [data-theme="dark"] .btn-export.word:hover { background: rgba(107,141,214,.15); }

        /* ── Print / PDF styles ───────────────────────── */
        @media print {
            .school-bar, .comparacao-bar,
            .refresh-btn, .auto-refresh-wrap, header, footer,
            .gei-theme-toggle, nav, #loader,
            [id="refresh-bar"], [id="refresh-lbl"] { display: none !important; }
            .dash-wrap { padding: 10px; max-width: 100%; }
            .panel-grid { grid-template-columns: 1fr 1fr !important; }
            .kpi-grid   { grid-template-columns: repeat(4, 1fr) !important; }
            .panel { break-inside: avoid; box-shadow: none; border: 1px solid #ddd; }
            body { background: #fff !important; }
        }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width: 900px) {
            .panel-grid, .panel-grid.cols3 { grid-template-columns: 1fr; }
            .dash-wrap { padding: 16px 14px 40px; }
        }
        @media (max-width: 600px) {
            .kpi-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
<?php if (!$_dash_embedded): ?>
</head>

<body class="main-layout">
    <?php include("loader.php"); ?>

    <?php include("header.php"); ?>
    <?php include("sessao_timeout.php"); ?>
<?php endif; ?>

    <?php
    // ── Verificar sessão (padrão GEI) ───────────────────────
    if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
        echo '<script>window.location.href="' . SVRURL . 'i";</script>';
        exit;
    }

    $tipo_user  = $_SESSION['tipo'];      // 1=Admin 2=Utilizador 3=Reparador 4=Funcionário
    $id_user    = $_SESSION['user_id'];

    // ── Escola seleccionada ──────────────────────────────────
    $escola_id = 1;
    if (isset($_GET['esc']) && is_numeric($_GET['esc'])) {
        $escola_id = (int)$_GET['esc'];
    }

    // ── Sala seleccionada (filtro gráfico Equipamentos por Tipo) ──
    $sala_fil = (isset($_GET['sala']) && is_numeric($_GET['sala'])) ? (int)$_GET['sala'] : 0; // 0 = todas

    // ── Ano seleccionado ─────────────────────────────────────
    $ano_atual = (int)date('Y');
    $ano_sel   = (isset($_GET['ano']) && is_numeric($_GET['ano'])) ? (int)$_GET['ano'] : 0; // 0 = todos
    // Lista de anos disponíveis (últimos 5 anos + ano actual)
    $anos_disp = [];
    for ($y = $ano_atual; $y >= $ano_atual - 5; $y--) { $anos_disp[] = $y; }

    // ── Helpers com prepared statements ─────────────────────
    /**
     * Executa um prepared statement e devolve o valor da primeira célula.
     * $sql  – query com placeholders '?'
     * $types – string de tipos mysqli (ex: "ii", "s", "")
     * $params – array de valores a ligar
     */
    function ps_val($db, $sql, $types = '', $params = []) {
        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) return 0;
        if ($types && $params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_row($result);
        mysqli_stmt_close($stmt);
        return $row[0] ?? 0;
    }

    /**
     * Executa um prepared statement e devolve todas as linhas como array associativo.
     */
    function ps_rows($db, $sql, $types = '', $params = []) {
        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) return [];
        if ($types && $params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $out = [];
        while ($row = mysqli_fetch_assoc($result)) $out[] = $row;
        mysqli_stmt_close($stmt);
        return $out;
    }

    // ─────────────────────────────────────────────────────────
    // KPI QUERIES — prepared statements
    // ─────────────────────────────────────────────────────────

    $total_equip = ps_val($db,
        "SELECT COUNT(*) FROM equipamento eq JOIN salas s ON eq.id_sala=s.id WHERE s.id_escola=?",
        "i", [$escola_id]);

    $total_salas = ps_val($db,
        "SELECT COUNT(*) FROM salas WHERE id_escola=?",
        "i", [$escola_id]);

    // total_avarias e av_abertas (com filtro opcional de ano)
    if ($ano_sel > 0) {
        $total_avarias = ps_val($db,
            "SELECT COUNT(*) FROM avarias_reparacoes WHERE id_escola=? AND YEAR(dataavaria)=?",
            "ii", [$escola_id, $ano_sel]);
        $av_abertas = ps_val($db,
            "SELECT COUNT(*) FROM avarias_reparacoes WHERE id_escola=? AND datareparacao IS NULL AND YEAR(dataavaria)=?",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $total_avarias = ps_val($db,
            "SELECT COUNT(*) FROM avarias_reparacoes WHERE id_escola=?",
            "i", [$escola_id]);
        $av_abertas = ps_val($db,
            "SELECT COUNT(*) FROM avarias_reparacoes WHERE id_escola=? AND datareparacao IS NULL",
            "i", [$escola_id]);
    }
    $av_resolvidas = $total_avarias - $av_abertas;

    if ($ano_sel > 0) {
        $total_manut = ps_val($db,
            "SELECT COUNT(*) FROM manutencao m
             JOIN equipamento eq ON m.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=? AND YEAR(m.data_manutencao)=?",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $total_manut = ps_val($db,
            "SELECT COUNT(*) FROM manutencao m
             JOIN equipamento eq ON m.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=?",
            "i", [$escola_id]);
    }

    $req_pendentes = ps_val($db,
        "SELECT COUNT(*) FROM requisicao r JOIN salas s ON r.id_sala=s.id WHERE s.id_escola=? AND r.dataentrega IS NULL",
        "i", [$escola_id]);

    if ($ano_sel > 0) {
        $tarefas_tot = ps_val($db,
            "SELECT COUNT(*) FROM tarefas WHERE id_escola=? AND YEAR(data_criacao)=?",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $tarefas_tot = ps_val($db,
            "SELECT COUNT(*) FROM tarefas WHERE id_escola=?",
            "i", [$escola_id]);
    }

    $outro_equip = ps_val($db,
        "SELECT COALESCE(SUM(oe.qta),0) FROM outro_equipamento oe JOIN salas s ON oe.id_sala=s.id WHERE s.id_escola=?",
        "i", [$escola_id]);

    // ─────────────────────────────────────────────────────────
    // CHART DATA — prepared statements
    // ─────────────────────────────────────────────────────────

    // 1. Avarias por mês
    if ($ano_sel > 0) {
        $av_mes = ps_rows($db,
            "SELECT DATE_FORMAT(dataavaria,'%Y-%m') as mes, COUNT(*) as total
             FROM avarias_reparacoes
             WHERE id_escola=? AND YEAR(dataavaria)=?
             GROUP BY mes ORDER BY mes ASC",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $av_mes = ps_rows($db,
            "SELECT DATE_FORMAT(dataavaria,'%Y-%m') as mes, COUNT(*) as total
             FROM avarias_reparacoes
             WHERE id_escola=? AND dataavaria >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY mes ORDER BY mes ASC",
            "i", [$escola_id]);
    }
    $av_mes_labels = array_map(function($r) { return $r['mes']; }, $av_mes);
    $av_mes_vals   = array_map(function($r) { return (int)$r['total']; }, $av_mes);

    // 2. Equipamentos por tipo (com filtro opcional de sala)
    if ($sala_fil > 0) {
        $eq_tipo = ps_rows($db,
            "SELECT eq.tipo, COUNT(*) as total
             FROM equipamento eq JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=? AND eq.id_sala=?
             GROUP BY eq.tipo ORDER BY total DESC LIMIT 8",
            "ii", [$escola_id, $sala_fil]);
    } else {
        $eq_tipo = ps_rows($db,
            "SELECT eq.tipo, COUNT(*) as total
             FROM equipamento eq JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=?
             GROUP BY eq.tipo ORDER BY total DESC LIMIT 8",
            "i", [$escola_id]);
    }
    $eq_tipo_labels = array_map(function($r) { return $r['tipo']; }, $eq_tipo);
    $eq_tipo_vals   = array_map(function($r) { return (int)$r['total']; }, $eq_tipo);

    // Lista de salas da escola (para o filtro do gráfico)
    $salas_lista = ps_rows($db,
        "SELECT id, nome FROM salas WHERE id_escola=? ORDER BY nome",
        "i", [$escola_id]);

    // 3. Top salas com mais avarias
    if ($ano_sel > 0) {
        $top_salas = ps_rows($db,
            "SELECT s.nome as sala, COUNT(*) as total
             FROM avarias_reparacoes ar JOIN salas s ON ar.id_sala=s.id
             WHERE ar.id_escola=? AND YEAR(ar.dataavaria)=?
             GROUP BY s.nome ORDER BY total DESC LIMIT 6",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $top_salas = ps_rows($db,
            "SELECT s.nome as sala, COUNT(*) as total
             FROM avarias_reparacoes ar JOIN salas s ON ar.id_sala=s.id
             WHERE ar.id_escola=?
             GROUP BY s.nome ORDER BY total DESC LIMIT 6",
            "i", [$escola_id]);
    }

    // 4. Avarias resolvidas vs abertas (donut) — já calculado acima

    // 5. Manutenções por mês
    if ($ano_sel > 0) {
        $manut_mes = ps_rows($db,
            "SELECT DATE_FORMAT(m.data_manutencao,'%Y-%m') as mes, COUNT(*) as total
             FROM manutencao m JOIN equipamento eq ON m.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=? AND YEAR(m.data_manutencao)=?
             GROUP BY mes ORDER BY mes ASC",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $manut_mes = ps_rows($db,
            "SELECT DATE_FORMAT(m.data_manutencao,'%Y-%m') as mes, COUNT(*) as total
             FROM manutencao m JOIN equipamento eq ON m.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE s.id_escola=? AND m.data_manutencao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY mes ORDER BY mes ASC",
            "i", [$escola_id]);
    }
    $manut_labels = array_map(function($r) { return $r['mes']; }, $manut_mes);
    $manut_vals   = array_map(function($r) { return (int)$r['total']; }, $manut_mes);

    // 6. Últimas avarias (lista)
    if ($ano_sel > 0) {
        $ult_avarias = ps_rows($db,
            "SELECT ar.id, ar.avaria, ar.dataavaria, ar.datareparacao,
                    s.nome as sala, eq.nomeequi as equip
             FROM avarias_reparacoes ar
             JOIN salas s ON ar.id_sala=s.id
             JOIN equipamento eq ON ar.id_equi=eq.id
             WHERE ar.id_escola=? AND YEAR(ar.dataavaria)=?
             ORDER BY ar.dataavaria DESC LIMIT 8",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $ult_avarias = ps_rows($db,
            "SELECT ar.id, ar.avaria, ar.dataavaria, ar.datareparacao,
                    s.nome as sala, eq.nomeequi as equip
             FROM avarias_reparacoes ar
             JOIN salas s ON ar.id_sala=s.id
             JOIN equipamento eq ON ar.id_equi=eq.id
             WHERE ar.id_escola=?
             ORDER BY ar.dataavaria DESC LIMIT 8",
            "i", [$escola_id]);
    }

    // 7. Tarefas pendentes (sem data_conclusao)
    if ($ano_sel > 0) {
        $tarefas = ps_rows($db,
            "SELECT t.descricao, s.nome as sala, t.data_criacao, t.urgencia
             FROM tarefas t JOIN salas s ON t.id_sala=s.id
             WHERE t.id_escola=? AND t.data_conclusao IS NULL AND YEAR(t.data_criacao)=?
             ORDER BY t.data_criacao ASC LIMIT 6",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $tarefas = ps_rows($db,
            "SELECT t.descricao, s.nome as sala, t.data_criacao, t.urgencia
             FROM tarefas t JOIN salas s ON t.id_sala=s.id
             WHERE t.id_escola=? AND t.data_conclusao IS NULL
             ORDER BY t.data_criacao ASC LIMIT 6",
            "i", [$escola_id]);
    }

    // 8. Escolas (para o filtro) — sem parâmetros variáveis, sem risco de injecção
    $escolas = ps_rows($db, "SELECT id, nome_escola FROM escolas ORDER BY nome_escola");

    // 10. Equipamentos por estado: operacional vs avariado
    $equip_operacional = ps_val($db,
        "SELECT COUNT(DISTINCT eq.id)
         FROM equipamento eq
         JOIN salas s ON eq.id_sala=s.id
         WHERE s.id_escola=?
           AND eq.id NOT IN (
               SELECT id_equi FROM avarias_reparacoes
               WHERE id_escola=? AND datareparacao IS NULL
           )",
        "ii", [$escola_id, $escola_id]);

    $equip_avariado = ps_val($db,
        "SELECT COUNT(DISTINCT eq.id)
         FROM equipamento eq
         JOIN salas s ON eq.id_sala=s.id
         WHERE s.id_escola=?
           AND eq.id IN (
               SELECT id_equi FROM avarias_reparacoes
               WHERE id_escola=? AND datareparacao IS NULL
           )",
        "ii", [$escola_id, $escola_id]);

    $pct_operacional = $total_equip > 0 ? round($equip_operacional / $total_equip * 100) : 0;
    $pct_avariado    = $total_equip > 0 ? round($equip_avariado    / $total_equip * 100) : 0;

    // 11. Top 5 equipamentos com mais avarias
    if ($ano_sel > 0) {
        $top5_equip = ps_rows($db,
            "SELECT eq.nomeequi, eq.tipo, s.nome as sala,
                    COUNT(*) as total_avarias,
                    SUM(CASE WHEN ar.datareparacao IS NULL THEN 1 ELSE 0 END) as abertas
             FROM avarias_reparacoes ar
             JOIN equipamento eq ON ar.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE ar.id_escola=? AND YEAR(ar.dataavaria)=?
             GROUP BY ar.id_equi, eq.nomeequi, eq.tipo, s.nome
             ORDER BY total_avarias DESC LIMIT 5",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $top5_equip = ps_rows($db,
            "SELECT eq.nomeequi, eq.tipo, s.nome as sala,
                    COUNT(*) as total_avarias,
                    SUM(CASE WHEN ar.datareparacao IS NULL THEN 1 ELSE 0 END) as abertas
             FROM avarias_reparacoes ar
             JOIN equipamento eq ON ar.id_equi=eq.id
             JOIN salas s ON eq.id_sala=s.id
             WHERE ar.id_escola=?
             GROUP BY ar.id_equi, eq.nomeequi, eq.tipo, s.nome
             ORDER BY total_avarias DESC LIMIT 5",
            "i", [$escola_id]);
    }
    $json_top5_labels = json_encode(array_map(fn($r) => mb_strimwidth($r['nomeequi'], 0, 22, '…'), $top5_equip));
    $json_top5_vals   = json_encode(array_map(fn($r) => (int)$r['total_avarias'], $top5_equip));

    // 9. Equipamentos Escola Digital
    $escola_digital = ps_val($db,
        "SELECT COUNT(*) FROM equipamento eq JOIN salas s ON eq.id_sala=s.id WHERE s.id_escola=? AND eq.escola_digital='Sim'",
        "i", [$escola_id]);

    // Taxa resolução
    $taxa_res = $total_avarias > 0 ? round($av_resolvidas / $total_avarias * 100) : 0;

    // Tempo médio de resolução (dias)
    if ($ano_sel > 0) {
        $tempo_med_raw = ps_val($db,
            "SELECT AVG(DATEDIFF(ar.datareparacao, ar.dataavaria))
             FROM avarias_reparacoes ar
             WHERE ar.id_escola=?
               AND ar.datareparacao IS NOT NULL
               AND ar.dataavaria IS NOT NULL
               AND DATEDIFF(ar.datareparacao, ar.dataavaria) >= 0
               AND YEAR(ar.dataavaria)=?",
            "ii", [$escola_id, $ano_sel]);
    } else {
        $tempo_med_raw = ps_val($db,
            "SELECT AVG(DATEDIFF(ar.datareparacao, ar.dataavaria))
             FROM avarias_reparacoes ar
             WHERE ar.id_escola=?
               AND ar.datareparacao IS NOT NULL
               AND ar.dataavaria IS NOT NULL
               AND DATEDIFF(ar.datareparacao, ar.dataavaria) >= 0",
            "i", [$escola_id]);
    }
    $tempo_med_dias = ($tempo_med_raw !== null && $tempo_med_raw > 0) ? round((float)$tempo_med_raw, 1) : null;
    if ($tempo_med_dias === null) {
        $tempo_med_display = '—';
        $tempo_med_unit    = 'sem dados';
    } elseif ($tempo_med_dias < 1) {
        $tempo_med_display = '< 1';
        $tempo_med_unit    = 'dia em média';
    } else {
        $tempo_med_display = number_format($tempo_med_dias, 1);
        $tempo_med_unit    = $tempo_med_dias == 1.0 ? 'dia em média' : 'dias em média';
    }

    // JSON para charts
    $json_av_mes_labels  = json_encode($av_mes_labels);
    $json_av_mes_vals    = json_encode($av_mes_vals);
    $json_eq_tipo_labels = json_encode($eq_tipo_labels);
    $json_eq_tipo_vals   = json_encode($eq_tipo_vals);
    $json_manut_labels   = json_encode($manut_labels);
    $json_manut_vals     = json_encode($manut_vals);
    ?>

    <div class="about">
    <div class="dash-wrap">

        <!-- Title -->
        <div class="dash-title">
            <div class="dash-title-icon"><i class="fas fa-tachometer-alt"></i></div>
            <div>
                <h1>Dashboard</h1>
                <p>Visão geral dos equipamentos e manutenção</p>
            </div>
        </div>

        <!-- School filter bar -->
        <div class="school-bar">
            <label><i class="fas fa-school"></i> &nbsp;Instituição:</label>
            <select onchange="window.location.href='?esc='+this.value+'&ano=<?= $ano_sel ?>'">
                <?php foreach ($escolas as $esc): ?>
                    <option value="<?= $esc['id'] ?>" <?= $esc['id'] == $escola_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($esc['nome_escola']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label><i class="fas fa-calendar-alt"></i> &nbsp;Ano:</label>
            <select onchange="window.location.href='?esc=<?= $escola_id ?>&ano='+this.value">
                <option value="0" <?= $ano_sel === 0 ? 'selected' : '' ?>>Todos os anos</option>
                <?php foreach ($anos_disp as $y): ?>
                    <option value="<?= $y ?>" <?= $y === $ano_sel ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
            </select>
            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
            <span class="last-update"><i class="far fa-clock"></i> &nbsp;<?php $_tz=new DateTimeZone('Europe/Lisbon');$_dt=new DateTime('now',$_tz);echo $_dt->format('d/m/Y H:i'); ?></span>
            <div class="auto-refresh-wrap">
                <i class="fas fa-redo" style="opacity:.5;font-size:.7rem"></i>
                <span id="refresh-lbl" style="font-variant-numeric:tabular-nums">120s</span>
                <div class="auto-refresh-track">
                    <div class="auto-refresh-fill" id="refresh-bar"></div>
                </div>
            </div>
        </div>

        <!-- Comparison + Export bar -->
        <div class="comparacao-bar" id="comparacao-bar">
            <label><i class="fas fa-exchange-alt"></i> &nbsp;Comparar com:</label>
            <select id="sel-periodo">
                <option value="">— Sem comparação —</option>
                <option value="mes_anterior">Mês anterior</option>
                <option value="ano_anterior">Ano anterior</option>
                <option value="periodo_anterior">Período anterior equivalente</option>
            </select>
            <button class="btn-comparar" onclick="carregarComparacao()">
                <i class="fas fa-chart-bar"></i> Comparar
            </button>
            <span id="cmp-loading" style="display:none;font-size:.8rem;color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> A carregar...</span>
            <button class="btn-export pdf" onclick="exportarPDF()" title="Exportar para PDF" style="margin-left:auto;">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </div>

        <!-- Comparison KPIs (hidden until triggered) -->
        <div id="comparacao-kpis" style="display:none;margin-bottom:24px;">
            <div style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                <i class="fas fa-history"></i> &nbsp;A comparar com: <span id="cmp-periodo-nome" style="color:var(--primary);font-weight:700;"></span>
            </div>
            <div class="kpi-cmp-grid" id="cmp-kpi-grid"></div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi c-primary">
                <div class="kpi-icon"><i class="fas fa-desktop"></i></div>
                <div class="kpi-val"><?= number_format($total_equip) ?></div>
                <div class="kpi-label">Equipamentos</div>
                <div class="kpi-sub">Escola Digital: <?= $escola_digital ?></div>
            </div>
            <div class="kpi c-accent2">
                <div class="kpi-icon"><i class="fas fa-door-open"></i></div>
                <div class="kpi-val"><?= $total_salas ?></div>
                <div class="kpi-label">Salas</div>
                <div class="kpi-sub">Outro equip.: <?= $outro_equip ?> unid.</div>
            </div>
            <div class="kpi c-danger">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-val"><?= $av_abertas ?></div>
                <div class="kpi-label">Avarias Abertas</div>
                <div class="kpi-sub">Total: <?= $total_avarias ?> registadas</div>
            </div>
            <div class="kpi c-success">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-val"><?= $taxa_res ?>%</div>
                <div class="kpi-label">Taxa Resolução</div>
                <div class="kpi-sub"><?= $av_resolvidas ?> resolvidas</div>
            </div>
            <div class="kpi" style="border-color:#0891b2;">
                <div class="kpi-icon" style="background:#0891b2;"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-val" style="color:#0891b2;"><?= $tempo_med_display ?></div>
                <div class="kpi-label">Tempo Médio Resolução</div>
                <div class="kpi-sub"><?= $tempo_med_unit ?></div>
            </div>
            <div class="kpi c-warning">
                <div class="kpi-icon"><i class="fas fa-tools"></i></div>
                <div class="kpi-val"><?= $total_manut ?></div>
                <div class="kpi-label">Manutenções</div>
                <div class="kpi-sub">Tarefas: <?= $tarefas_tot ?></div>
            </div>
            <div class="kpi c-info">
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="kpi-val"><?= $req_pendentes ?></div>
                <div class="kpi-label">Requisições Pend.</div>
                <div class="kpi-sub">Aguardam entrega</div>
            </div>
        </div>

        <!-- Row 1: Avarias por mês + Donut estado -->
        <div class="panel-grid cols3">
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--danger)"><i class="fas fa-chart-line"></i></div>
                    <h2>Avarias por Mês <?= $ano_sel > 0 ? "($ano_sel)" : "(últimos 12 meses)" ?></h2>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap">
                        <canvas id="chartAvMes"></canvas>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--accent2)"><i class="fas fa-chart-pie"></i></div>
                    <h2>Estado das Avarias <?= $ano_sel > 0 ? "($ano_sel)" : "" ?></h2>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap-sm">
                        <canvas id="chartEstado"></canvas>
                    </div>
                    <div style="text-align:center;margin-top:12px;">
                        <span class="badge badge-danger"><i class="fas fa-circle"></i> Abertas: <?= $av_abertas ?></span>
                        &nbsp;
                        <span class="badge badge-success"><i class="fas fa-circle"></i> Resolvidas: <?= $av_resolvidas ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Equipamentos por tipo + Manutenções -->
        <div class="panel-grid">
            <div class="panel">
                <div class="panel-head" style="flex-wrap:wrap;gap:8px;">
                    <div class="panel-head-icon" style="background:var(--primary)"><i class="fas fa-chart-bar"></i></div>
                    <h2>Equipamentos por Tipo</h2>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
                        <label for="filtro-sala" style="font-size:.75rem;font-weight:600;color:var(--text-muted);white-space:nowrap;"><i class="fas fa-door-open"></i> Sala:</label>
                        <select id="filtro-sala"
                            style="border:1.5px solid var(--border);border-radius:7px;padding:4px 10px;font-size:.8rem;font-family:inherit;color:var(--text);background:var(--surface2);cursor:pointer;transition:border .2s;"
                            onchange="filtrarTiposPorSala(this.value)">
                            <option value="0" <?= $sala_fil === 0 ? 'selected' : '' ?>>Todas as salas</option>
                            <?php foreach ($salas_lista as $sl): ?>
                            <option value="<?= $sl['id'] ?>" <?= $sl['id'] == $sala_fil ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sl['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <span id="tipos-loading" style="display:none;font-size:.75rem;color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap">
                        <canvas id="chartTipos"></canvas>
                    </div>
                    <div id="tipos-empty" style="display:none;" class="empty-state"><i class="fas fa-box-open"></i>Sem equipamentos nesta sala</div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--warning)"><i class="fas fa-wrench"></i></div>
                    <h2>Manutenções por Mês</h2>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap">
                        <canvas id="chartManut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2b: Estado dos equipamentos + Top 5 equipamentos c/ mais avarias -->
        <div class="panel-grid">
            <!-- Estado: Operacional vs Avariado -->
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--success)"><i class="fas fa-heartbeat"></i></div>
                    <h2>Estado dos Equipamentos</h2>
                </div>
                <div class="panel-body">
                    <!-- Barra de progresso dupla -->
                    <div style="margin-bottom:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span style="font-size:.82rem;font-weight:600;color:var(--text);">Total: <?= number_format($total_equip) ?> equipamentos</span>
                        </div>
                        <div style="height:18px;border-radius:99px;overflow:hidden;display:flex;background:var(--border);">
                            <?php if ($equip_operacional > 0): ?>
                            <div style="width:<?= $pct_operacional ?>%;background:var(--success);transition:width 1s ease;" title="Operacional: <?= $equip_operacional ?>"></div>
                            <?php endif; ?>
                            <?php if ($equip_avariado > 0): ?>
                            <div style="width:<?= $pct_avariado ?>%;background:var(--danger);transition:width 1s ease;" title="Avariado: <?= $equip_avariado ?>"></div>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex;gap:16px;margin-top:8px;">
                            <span style="font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:5px;">
                                <span style="width:10px;height:10px;border-radius:50%;background:var(--success);display:inline-block;"></span>
                                Operacional: <?= $pct_operacional ?>%
                            </span>
                            <span style="font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:5px;">
                                <span style="width:10px;height:10px;border-radius:50%;background:var(--danger);display:inline-block;"></span>
                                Avariado: <?= $pct_avariado ?>%
                            </span>
                        </div>
                    </div>
                    <!-- Dois KPI inline -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:8px;">
                        <div style="background:var(--surface2);border:1.5px solid var(--border);border-left:4px solid var(--success);border-radius:10px;padding:16px 18px;">
                            <div style="font-size:1.7rem;font-weight:700;font-family: var(--font-mono);color:var(--success);"><?= number_format($equip_operacional) ?></div>
                            <div style="font-size:.74rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-top:4px;">
                                <i class="fas fa-check-circle" style="color:var(--success);margin-right:4px;"></i>Operacional
                            </div>
                        </div>
                        <div style="background:var(--surface2);border:1.5px solid var(--border);border-left:4px solid var(--danger);border-radius:10px;padding:16px 18px;">
                            <div style="font-size:1.7rem;font-weight:700;font-family: var(--font-mono);color:var(--danger);"><?= number_format($equip_avariado) ?></div>
                            <div style="font-size:.74rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-top:4px;">
                                <i class="fas fa-exclamation-triangle" style="color:var(--danger);margin-right:4px;"></i>Avariado
                            </div>
                        </div>
                    </div>
                    <!-- Gráfico donut estado equipamentos -->
                    <div style="position:relative;height:180px;margin-top:22px;">
                        <canvas id="chartEstadoEquip"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top 5 equipamentos com mais avarias -->
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:#e85d04"><i class="fas fa-fire-alt"></i></div>
                    <h2>Top 5 — Mais Avarias</h2>
                </div>
                <div class="panel-body">
                    <?php if (empty($top5_equip)): ?>
                        <div class="empty-state"><i class="fas fa-check-circle"></i>Sem avarias registadas</div>
                    <?php else:
                        $max5 = max(array_column($top5_equip, 'total_avarias'));
                        $cores = ['#e63946','#e85d04','#f4a261','#2a9d8f','#4361ee'];
                        foreach ($top5_equip as $idx => $eq):
                            $pct5 = $max5 > 0 ? round($eq['total_avarias'] / $max5 * 100) : 0;
                            $cor  = $cores[$idx] ?? '#4b6cb7';
                    ?>
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                            <span style="font-size:.72rem;font-weight:700;color:#fff;background:<?= $cor ?>;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $idx+1 ?></span>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.83rem;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= htmlspecialchars($eq['nomeequi']) ?>"><?= htmlspecialchars(mb_strimwidth($eq['nomeequi'], 0, 28, '…')) ?></div>
                                <div style="font-size:.71rem;color:var(--text-muted);"><?= htmlspecialchars($eq['tipo']) ?> &bull; <?= htmlspecialchars($eq['sala']) ?></div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:2px;flex-shrink:0;">
                                <span class="badge" style="background:<?= $cor ?>22;color:<?= $cor ?>;font-size:.77rem;font-weight:700;"><?= $eq['total_avarias'] ?> avarías</span>
                                <?php if ($eq['abertas'] > 0): ?>
                                <span class="badge badge-danger" style="font-size:.68rem;"><?= $eq['abertas'] ?> abertas</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="height:7px;border-radius:99px;background:var(--border);overflow:hidden;">
                            <div style="height:7px;border-radius:99px;background:<?= $cor ?>;width:<?= $pct5 ?>%;transition:width 1s ease;"></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>

                </div>
            </div>
        </div>
        <!-- Fim Row 2b -->

        <!-- Row 3: Top salas avarias + Últimas avarias -->
        <div class="panel-grid cols3">
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--danger)"><i class="fas fa-exclamation-circle"></i></div>
                    <h2>Últimas Avarias</h2>
                </div>
                <div class="panel-body no-pad">
                    <?php if (empty($ult_avarias)): ?>
                        <div class="empty-state"><i class="fas fa-check-circle"></i>Sem avarias registadas</div>
                    <?php else: ?>
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Equipamento</th>
                                    <th>Sala</th>
                                    <th>Data</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ult_avarias as $av): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600;font-size:.83rem"><?= htmlspecialchars($av['equip']) ?></div>
                                        <div style="font-size:.72rem;color:var(--text-muted)"><?= mb_strimwidth(htmlspecialchars($av['avaria']), 0, 45, '…') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($av['sala']) ?></td>
                                    <td style="white-space:nowrap;font-family: var(--font-mono);font-size:.77rem">
                                        <?= $av['dataavaria'] ? date('d/m/Y', strtotime($av['dataavaria'])) : '—' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($av['datareparacao'])): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Resolvida</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-clock"></i> Aberta</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top salas -->
            <div class="panel">
                <div class="panel-head" style="flex-wrap:wrap;gap:8px;">
                    <div class="panel-head-icon" style="background:#6f42c1"><i class="fas fa-map-marker-alt"></i></div>
                    <h2>Top Salas c/ Avarias</h2>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
                        <label for="filtro-ano-salas" style="font-size:.75rem;font-weight:600;color:var(--text-muted);white-space:nowrap;"><i class="fas fa-calendar-alt"></i> Ano:</label>
                        <select id="filtro-ano-salas"
                            style="border:1.5px solid var(--border);border-radius:7px;padding:4px 10px;font-size:.8rem;font-family:inherit;color:var(--text);background:var(--surface2);cursor:pointer;transition:border .2s;"
                            onchange="filtrarTopSalasPorAno(this.value)">
                            <option value="0" <?= $ano_sel === 0 ? 'selected' : '' ?>>Todos</option>
                            <?php foreach ($anos_disp as $y): ?>
                            <option value="<?= $y ?>" <?= $y === $ano_sel ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="salas-loading" style="display:none;font-size:.75rem;color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
                <div class="panel-body" id="top-salas-body">
                    <?php if (empty($top_salas)): ?>
                        <div class="empty-state"><i class="fas fa-door-open"></i>Sem dados</div>
                    <?php else:
                        $max_av = max(array_column($top_salas, 'total'));
                        foreach ($top_salas as $ts):
                            $pct = $max_av > 0 ? round($ts['total'] / $max_av * 100) : 0;
                    ?>
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:.83rem;font-weight:600"><?= htmlspecialchars($ts['sala']) ?></span>
                            <span class="badge badge-purple"><?= $ts['total'] ?></span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:<?= $pct ?>%;background:#6f42c1"></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Row Heatmap: Avarias por sala ao longo do ano -->
        <div class="panel-grid full" style="margin-bottom:20px;">
            <div class="panel">
                <div class="panel-head" style="flex-wrap:wrap;gap:8px;">
                    <div class="panel-head-icon" style="background:#0891b2"><i class="fas fa-th"></i></div>
                    <h2>Heatmap de Avarias por Sala</h2>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
                        <label for="heatmap-ano-select" style="font-size:.75rem;font-weight:600;color:var(--text-muted);"><i class="fas fa-calendar-alt"></i> Ano:</label>
                        <select id="heatmap-ano-select" onchange="carregarHeatmap(this.value)">
                            <?php foreach ($anos_disp as $y): ?>
                            <option value="<?= $y ?>" <?= ($ano_sel > 0 ? $y === $ano_sel : $y === $ano_atual) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="heatmap-loading" style="display:none;font-size:.75rem;color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="heatmap-wrap" id="heatmap-container">
                        <div class="empty-state"><i class="fas fa-spinner fa-spin"></i> A carregar heatmap...</div>
                    </div>
                    <div class="heatmap-legend" id="heatmap-legend" style="display:none;">
                        <span>Menos</span>
                        <span class="heatmap-legend-swatch" style="background:#f0f4fb;border:1px solid #e3e8f4;"></span>
                        <span class="heatmap-legend-swatch" style="background:#bfdbfe;"></span>
                        <span class="heatmap-legend-swatch" style="background:#60a5fa;"></span>
                        <span class="heatmap-legend-swatch" style="background:#2563eb;"></span>
                        <span class="heatmap-legend-swatch" style="background:#1e3a8a;"></span>
                        <span>Mais</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Tarefas pendentes -->
        <?php if (!empty($tarefas)): ?>
        <div class="panel-grid full">
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-head-icon" style="background:var(--warning)"><i class="fas fa-tasks"></i></div>
                    <h2>Tarefas de Manutenção Pendentes</h2>
                </div>
                <div class="panel-body">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:10px;">
                    <?php foreach ($tarefas as $t): ?>
                        <div class="tarefa-row" style="border:1px solid var(--border);border-radius:8px;padding:10px 14px;flex-direction:column;align-items:flex-start;gap:4px">
                            <div style="display:flex;align-items:center;gap:8px;width:100%">
                                <div class="tarefa-check"></div>
                                <span class="tarefa-text" style="font-weight:600"><?= htmlspecialchars($t['descricao']) ?></span>
                                <span class="tarefa-sala" style="margin-left:auto">Sala <?= htmlspecialchars($t['sala']) ?></span>
                            </div>
                            <?php if (!empty($t['data_criacao'])): ?>
                            <div style="padding-left:24px;font-size:.72rem;color:var(--text-muted)">
                                <i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($t['data_criacao'])) ?>
                                <?php if (!empty($t['urgencia'])): ?>
                                &nbsp;<span class="badge <?= $t['urgencia']=='Alta' ? 'badge-danger' : ($t['urgencia']=='Média' ? 'badge-warning' : 'badge-info') ?>"><?= htmlspecialchars($t['urgencia']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
          
        </div>

    </div><!-- /.dash-wrap -->
    </div><!-- /.about -->

    <?php if (!$_dash_embedded): include("footer.php"); endif; ?>

    <!-- ── Charts JS ─────────────────────────────────────── -->
    <script>
    Chart.defaults.font.family = getComputedStyle(document.documentElement).getPropertyValue('--font-body').trim();
    const _isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
    Chart.defaults.color = _isDark() ? '#94a3b8' : '#7b88a0';

    const COLORS = ['#4b6cb7','#36b9cc','#1cc88a','#f6c23e','#e74a3b','#6f42c1','#fd7e14','#20c9a6'];

    // Plugin inline para mostrar valores no topo das barras (só usado no chartTipos)
    const barValuesPlugin = {
        id: 'barValues',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                meta.data.forEach((bar, idx) => {
                    const val = dataset.data[idx];
                    if (val === null || val === undefined) return;
                    ctx.save();
                    ctx.font = "600 11px 'DM Mono', monospace"; // canvas API requer string literal
                    ctx.fillStyle = document.documentElement.getAttribute('data-theme') === 'dark' ? '#e2e8f0' : '#1e2a45';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillText(val, bar.x, bar.y - 4);
                    ctx.restore();
                });
            });
        }
    };

    // 1. Avarias por mês
    new Chart(document.getElementById('chartAvMes'), {
        type: 'line',
        data: {
            labels: <?= $json_av_mes_labels ?>,
            datasets: [{
                label: 'Avarias',
                data: <?= $json_av_mes_vals ?>,
                borderColor: '#e74a3b',
                backgroundColor: 'rgba(231,74,59,.10)',
                borderWidth: 2.5,
                pointBackgroundColor: '#e74a3b',
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: _isDark() ? '#2d3348' : '#f0f4fb' }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Estado avarias (donut)
    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: {
            labels: ['Abertas', 'Resolvidas'],
            datasets: [{
                data: [<?= $av_abertas ?>, <?= $av_resolvidas ?>],
                backgroundColor: ['#e74a3b','#1cc88a'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
            }
        }
    });

    // 3. Equipamentos por tipo
    // ── Gráfico Equipamentos por Tipo com filtro de sala ──
    const chartTiposInst = new Chart(document.getElementById('chartTipos'), {
        type: 'bar',
        plugins: [barValuesPlugin],
        data: {
            labels: <?= $json_eq_tipo_labels ?>,
            datasets: [{
                label: 'Equipamentos',
                data: <?= $json_eq_tipo_vals ?>,
                backgroundColor: COLORS,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: _isDark() ? '#2d3348' : '#f0f4fb' }, ticks: { stepSize: 1 } },
                x: { grid: { display: false }, ticks: { maxRotation: 35, minRotation: 20 } }
            },
            layout: { padding: { top: 22 } }
        }
    });

    function filtrarTiposPorSala(salaId) {
        const loading  = document.getElementById('tipos-loading');
        const empty    = document.getElementById('tipos-empty');
        const canvas   = document.getElementById('chartTipos');
        const escId    = <?= $escola_id ?>;
        const anoSel   = <?= $ano_sel ?>;

        loading.style.display = 'inline';

        fetch('dashboard_sala_tipos.php?esc=' + escId + '&sala=' + salaId + '&ano=' + anoSel)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';
                const temDados = data.labels.length > 0;
                canvas.style.display  = temDados ? 'block' : 'none';
                empty.style.display   = temDados ? 'none'  : 'block';

                chartTiposInst.data.labels = data.labels;
                chartTiposInst.data.datasets[0].data = data.vals;
                chartTiposInst.data.datasets[0].backgroundColor = COLORS.slice(0, data.labels.length);
                chartTiposInst.update();
            })
            .catch(() => { loading.style.display = 'none'; });
    }

    // 4. Manutenções por mês
    new Chart(document.getElementById('chartManut'), {
        type: 'bar',
        data: {
            labels: <?= $json_manut_labels ?>,
            datasets: [{
                label: 'Manutenções',
                data: <?= $json_manut_vals ?>,
                backgroundColor: '#f6c23e',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: _isDark() ? '#2d3348' : '#f0f4fb' }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
    // 5. Estado dos equipamentos (donut)
    (function() {
        const el = document.getElementById('chartEstadoEquip');
        if (!el) return;
        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: ['Operacional', 'Avariado'],
                datasets: [{
                    data: [<?= $equip_operacional ?>, <?= $equip_avariado ?>],
                    backgroundColor: ['#1cc88a', '#e74a3b'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 14, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a,b) => a+b, 0);
                                const pct   = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                                return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    })();

    // 6. Top 5 equipamentos — gráfico de barras horizontais
    // ── Top Salas — filtro por ano (AJAX) ───────────────
    function filtrarTopSalasPorAno(ano) {
        const loading = document.getElementById('salas-loading');
        const body    = document.getElementById('top-salas-body');
        const escId   = <?= $escola_id ?>;

        loading.style.display = 'inline';

        fetch('dashboard_top_salas.php?esc=' + escId + '&ano=' + ano)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';

                if (!data.items || data.items.length === 0) {
                    body.innerHTML = '<div class="empty-state"><i class="fas fa-door-open"></i>Sem dados</div>';
                    return;
                }

                const max = Math.max(...data.items.map(i => i.total));
                body.innerHTML = data.items.map(i => {
                    const pct = max > 0 ? Math.round(i.total / max * 100) : 0;
                    return `<div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:.83rem;font-weight:600">${i.sala}</span>
                            <span class="badge badge-purple">${i.total}</span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:${pct}%;background:#6f42c1;transition:width .6s ease"></div>
                        </div>
                    </div>`;
                }).join('');

                // Animar as barras após injeção no DOM
                requestAnimationFrame(() => {
                    body.querySelectorAll('.prog-fill').forEach(el => {
                        const w = el.style.width;
                        el.style.width = '0';
                        requestAnimationFrame(() => { el.style.width = w; });
                    });
                });
            })
            .catch(() => { loading.style.display = 'none'; });
    }

    // ── Auto-refresh ────────────────────────────────────
    (function() {
        const INTERVAL = 120; // segundos
        const bar = document.getElementById('refresh-bar');
        const lbl = document.getElementById('refresh-lbl');
        let remaining = INTERVAL;

        function tick() {
            remaining--;
            if (lbl) lbl.textContent = remaining + 's';
            if (bar) bar.style.width = ((remaining / INTERVAL) * 100) + '%';
            if (remaining <= 0) {
                const url = new URL(window.location.href);
                if (!url.searchParams.has('ano')) url.searchParams.set('ano', '0');
                window.location.href = url.href;
            }
        }
        setInterval(tick, 1000);
    })();

    // ══════════════════════════════════════════════════════
    // COMPARAÇÃO DE PERÍODOS
    // ══════════════════════════════════════════════════════
    const _escId  = <?= $escola_id ?>;
    const _anoSel = <?= $ano_sel > 0 ? $ano_sel : $ano_atual ?>;

    // KPIs actuais para calcular delta
    const _kpisActuais = {
        total_avarias: <?= $total_avarias ?>,
        av_abertas:    <?= $av_abertas ?>,
        av_resolvidas: <?= $av_resolvidas ?>,
        taxa_res:      <?= $taxa_res ?>,
        total_manut:   <?= $total_manut ?>,
    };

    function carregarComparacao() {
        const periodo = document.getElementById('sel-periodo').value;
        if (!periodo) {
            document.getElementById('comparacao-kpis').style.display = 'none';
            return;
        }
        const loading = document.getElementById('cmp-loading');
        loading.style.display = 'inline';

        fetch(`dashboard_comparacao.php?esc=${_escId}&periodo=${periodo}&ano_sel=${_anoSel}`)
            .then(r => r.json())
            .then(d => {
                loading.style.display = 'none';
                document.getElementById('cmp-periodo-nome').textContent = d.label;

                const kpis = [
                    { label: 'Total Avarias',   atual: _kpisActuais.total_avarias, cmp: d.total_avarias, formato: 'n', menor_melhor: false },
                    { label: 'Avarias Abertas', atual: _kpisActuais.av_abertas,    cmp: d.av_abertas,    formato: 'n', menor_melhor: true  },
                    { label: 'Resolvidas',       atual: _kpisActuais.av_resolvidas, cmp: d.av_resolvidas, formato: 'n', menor_melhor: false },
                    { label: 'Taxa Resolução',   atual: _kpisActuais.taxa_res,      cmp: d.taxa_res,      formato: '%', menor_melhor: false },
                    { label: 'Manutenções',      atual: _kpisActuais.total_manut,   cmp: d.total_manut,   formato: 'n', menor_melhor: false },
                ];

                const grid = document.getElementById('cmp-kpi-grid');
                grid.innerHTML = kpis.map(k => {
                    const diff = k.atual - k.cmp;
                    const pct  = k.cmp > 0 ? Math.abs(Math.round(diff / k.cmp * 100)) : null;
                    let badgeClass = 'same', badgeLabel = '=';
                    if (diff !== 0) {
                        const melhorou = k.menor_melhor ? diff < 0 : diff > 0;
                        badgeClass = melhorou ? 'up' : 'down';
                        const arrow = diff > 0 ? '▲' : '▼';
                        badgeLabel = arrow + ' ' + (pct !== null ? pct + '%' : Math.abs(diff));
                    }
                    const val = k.formato === '%' ? k.cmp + '%' : k.cmp;
                    return `<div class="kpi-cmp">
                        <span class="kpi-cmp-badge ${badgeClass}">${badgeLabel}</span>
                        <div class="kpi-cmp-val">${val}</div>
                        <div class="kpi-cmp-label">${k.label}</div>
                        <div class="kpi-cmp-lbl-periodo">${d.label}</div>
                    </div>`;
                }).join('');

                document.getElementById('comparacao-kpis').style.display = 'block';
            })
            .catch(() => { document.getElementById('cmp-loading').style.display = 'none'; });
    }

    // ══════════════════════════════════════════════════════
    // HEATMAP DE AVARIAS POR SALA
    // ══════════════════════════════════════════════════════
    function heatmapColor(val, max) {
        if (val === 0) return _isDark() ? '#1e2130' : '#f0f4fb';
        const ratio = val / max;
        if (_isDark()) {
            // Dark mode: low opacity blue → intense blue
            const stops = ['#1e3a5f','#1d4ed8','#2563eb','#3b82f6','#93c5fd'];
            const i = Math.min(Math.floor(ratio * stops.length), stops.length - 1);
            return stops[i];
        }
        const stops = ['#dbeafe','#93c5fd','#3b82f6','#1d4ed8','#1e3a8a'];
        const i = Math.min(Math.floor(ratio * stops.length), stops.length - 1);
        return stops[i];
    }
    function heatmapTextColor(val, max) {
        if (val === 0) return _isDark() ? '#94a3b8' : '#cbd5e1';
        const ratio = val / max;
        return ratio > 0.4 ? '#fff' : (_isDark() ? '#e2e8f0' : '#1e3a8a');
    }

    function renderHeatmap(data) {
        const container = document.getElementById('heatmap-container');
        const legend    = document.getElementById('heatmap-legend');

        if (!data.dados || data.dados.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-th"></i>Sem dados de avarias para este ano</div>';
            legend.style.display = 'none';
            return;
        }

        const meses = data.meses;
        const maxVal = Math.max(...data.dados.flatMap(d => d.valores));

        let html = '<table class="heatmap-table"><thead><tr><th style="text-align:left">Sala</th>';
        meses.forEach(m => { html += `<th>${m}</th>`; });
        html += '<th>Total</th></tr></thead><tbody>';

        data.dados.forEach(row => {
            const total = row.valores.reduce((a, b) => a + b, 0);
            html += `<tr><td class="heatmap-sala">${row.sala.length > 22 ? row.sala.substring(0, 20) + '…' : row.sala}</td>`;
            row.valores.forEach(v => {
                const bg  = heatmapColor(v, maxVal);
                const col = heatmapTextColor(v, maxVal);
                const tip = v > 0 ? ` title="${v} avaria${v > 1 ? 's' : ''}"` : '';
                html += `<td style="background:${bg};color:${col};min-width:32px;"${tip}>${v > 0 ? v : ''}</td>`;
            });
            html += `<td style="font-weight:700;color:var(--text);">${total}</td></tr>`;
        });
        html += '</tbody></table>';

        container.innerHTML = html;
        legend.style.display = 'flex';
    }

    function carregarHeatmap(ano) {
        const loading   = document.getElementById('heatmap-loading');
        const container = document.getElementById('heatmap-container');
        loading.style.display = 'inline';

        fetch(`dashboard_heatmap.php?esc=${_escId}&ano=${ano}`)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';
                renderHeatmap(data);
            })
            .catch(() => {
                loading.style.display = 'none';
                container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i> Erro ao carregar heatmap</div>';
            });
    }

    // Carregar heatmap ao iniciar
    document.addEventListener('DOMContentLoaded', function() {
        const anoHeatmap = document.getElementById('heatmap-ano-select').value;
        carregarHeatmap(anoHeatmap);
    });

    // ══════════════════════════════════════════════════════
    // EXPORT PDF / WORD
    // ══════════════════════════════════════════════════════

    // Helper: resolve CSS variables em inline styles (html2canvas não resolve vars)
    function _resolveCssVars(root) {
        const computed = getComputedStyle(document.documentElement);
        const varMap = {};
        // Colecionar todas as variáveis CSS usadas no documento
        ['--text','--text-muted','--surface','--surface2','--border',
         '--primary','--accent','--accent2','--success','--warning','--danger',
         '--bg','--radius'].forEach(v => {
            varMap[v] = computed.getPropertyValue(v).trim();
        });
        const backups = [];
        root.querySelectorAll('*').forEach(el => {
            const orig = el.getAttribute('style') || '';
            if (orig.includes('var(')) {
                let fixed = orig;
                Object.entries(varMap).forEach(([k, val]) => {
                    fixed = fixed.replaceAll(`var(${k})`, val);
                });
                backups.push({ el, orig });
                el.setAttribute('style', fixed);
            }
        });
        return backups;
    }

    // Helper: capturar um elemento como canvas (com resolução de vars CSS e canvas→img)
    // pdfContentW_px: largura do conteúdo do PDF em píxeis de ecrã (para forçar wrap correcto do grid)
    async function _captureElement(el, scale, pdfContentW_px) {
        scale = scale || 2;
        const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#ffffff';

        // ── 1. Forçar largura igual à largura do PDF para que o grid faça wrap ───
        const origWidth     = el.style.width;
        const origMaxWidth  = el.style.maxWidth;
        const origBoxSizing = el.style.boxSizing;
        if (pdfContentW_px) {
            el.style.width     = pdfContentW_px + 'px';
            el.style.maxWidth  = pdfContentW_px + 'px';
            el.style.boxSizing = 'border-box';
        }

        // ── 2. Substituir <canvas> por <img> ─────────────────────────────────────
        const canvasEls = el.querySelectorAll('canvas');
        const canvasSwaps = [];
        canvasEls.forEach(c => {
            try {
                const img = document.createElement('img');
                img.src = c.toDataURL('image/png', 1.0);
                img.style.width  = c.offsetWidth  + 'px';
                img.style.height = c.offsetHeight + 'px';
                img.style.display = 'block';
                c.parentNode.insertBefore(img, c);
                c.style.display = 'none';
                canvasSwaps.push({ c, img });
            } catch(e) {}
        });

        // ── 3. Resolver vars CSS inline ───────────────────────────────────────────
        const varBackups = _resolveCssVars(el);

        // ── 4. Expandir overflow scrollável ───────────────────────────────────────
        const overflowEls = [];
        el.querySelectorAll('*').forEach(child => {
            const cs = getComputedStyle(child);
            if (cs.overflow === 'auto' || cs.overflow === 'scroll' ||
                cs.overflowX === 'auto' || cs.overflowX === 'scroll') {
                overflowEls.push({ el: child, orig: child.style.cssText });
                child.style.overflow  = 'visible';
                child.style.overflowX = 'visible';
                child.style.overflowY = 'visible';
                child.style.maxWidth  = 'none';
            }
        });

        // Aguardar dois frames para o layout ser recalculado com a nova largura
        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

        const captured = await html2canvas(el, {
            scale,
            useCORS: true,
            allowTaint: true,
            backgroundColor: bgColor,
            logging: false,
            scrollX: -window.scrollX,
            scrollY: -window.scrollY,
            width:  el.scrollWidth,
            height: el.scrollHeight,
            windowWidth:  Math.max(document.documentElement.scrollWidth,  el.scrollWidth  + 200),
            windowHeight: Math.max(document.documentElement.scrollHeight, el.scrollHeight + 200),
        });

        // ── 5. Restaurar tudo ─────────────────────────────────────────────────────
        overflowEls.forEach(({ el: e, orig }) => { e.style.cssText = orig; });
        varBackups.forEach(({ el: e, orig }) => e.setAttribute('style', orig));
        canvasSwaps.forEach(({ c, img }) => { c.style.display = ''; img.remove(); });
        if (pdfContentW_px) {
            el.style.width     = origWidth;
            el.style.maxWidth  = origMaxWidth;
            el.style.boxSizing = origBoxSizing;
        }

        return captured;
    }

    async function exportarPDF() {
        const btn = document.querySelector('.btn-export.pdf');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A gerar PDF…';
        btn.disabled = true;

        try {
            const { jsPDF } = window.jspdf;
            const escola = document.querySelector('.school-bar select option:checked')?.textContent?.trim() || 'Escola';

            const pdf      = new jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' });
            const pageW    = pdf.internal.pageSize.getWidth();
            const pageH    = pdf.internal.pageSize.getHeight();
            const marginX  = 6;   // mm margem lateral
            const marginB  = 8;   // mm margem inferior
            const contentW = pageW - marginX * 2;

            // Y inicial do conteúdo (depois do cabeçalho)
            const HEADER_Y_FIRST = 26;  // 1ª página (cabeçalho grande)
            const HEADER_Y_REST  = 13;  // páginas seguintes (cabeçalho mini)

            // Desenhar cabeçalho da 1ª página
            (function drawFirstHeader() {
                const agora = new Date().toLocaleDateString('pt-PT', { year: 'numeric', month: 'long', day: 'numeric' });
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(14);
                pdf.setTextColor(75, 108, 183);
                pdf.text('Dashboard GEI \u2014 ' + escola, 14, 14);
                pdf.setFont('helvetica', 'normal');
                pdf.setFontSize(9);
                pdf.setTextColor(120, 136, 160);
                pdf.text(agora, 14, 20);
                pdf.setDrawColor(75, 108, 183);
                pdf.setLineWidth(0.4);
                pdf.line(14, 22, pageW - 14, 22);
            })();

            function drawPageHeader() {
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(9);
                pdf.setTextColor(75, 108, 183);
                pdf.text('Dashboard GEI \u2014 ' + escola, 14, 7);
                pdf.setDrawColor(200, 210, 230);
                pdf.setLineWidth(0.3);
                pdf.line(14, 9, pageW - 14, 9);
            }

            // Colecionar blocos do dashboard pela ordem do DOM
            const dashWrap = document.querySelector('.dash-wrap') || document.body;
            const allBlocks = [];
            dashWrap.querySelectorAll(':scope > .kpi-grid, :scope > .panel-grid').forEach(el => {
                if (el.closest('#comparacao-section')) return;
                allBlocks.push(el);
            });

            if (allBlocks.length === 0) {
                throw new Error('Nenhum bloco encontrado no dashboard para exportar.');
            }

            // Largura do PDF em px de ecra (96dpi)
            const MM_TO_PX       = 96 / 25.4;
            const pdfContentW_px = Math.round(contentW * MM_TO_PX);

            let curY        = HEADER_Y_FIRST;
            let isFirstPage = true;

            for (let i = 0; i < allBlocks.length; i++) {
                const block = allBlocks[i];

                let captured;
                try {
                    captured = await _captureElement(block, 2, pdfContentW_px);
                } catch (captureErr) {
                    console.warn('Bloco ignorado (erro na captura):', captureErr);
                    continue;
                }

                if (!captured || captured.width === 0 || captured.height === 0) continue;

                // Altura proporcional em mm para a largura do conteúdo
                const blockH_mm = (captured.height / captured.width) * contentW;

                // Espaço disponível na página actual
                const availH = pageH - curY - marginB;

                // Se o bloco não couber E não for o primeiro da página → nova página
                const isFirstOfPage = (isFirstPage && i === 0) || (curY === HEADER_Y_REST);
                if (blockH_mm > availH && !isFirstOfPage) {
                    pdf.addPage();
                    isFirstPage = false;
                    drawPageHeader();
                    curY = HEADER_Y_REST;
                }

                // Calcular dimensões finais mantendo proporção — nunca cortar
                const maxH  = pageH - curY - marginB;
                let finalW  = contentW;
                let finalH  = blockH_mm;
                if (finalH > maxH) {
                    // Escalar para caber sem cortar
                    const ratio = maxH / finalH;
                    finalH = maxH;
                    finalW = contentW * ratio;
                }

                const imgData = captured.toDataURL('image/jpeg', 0.93);
                const offsetX = marginX + (contentW - finalW) / 2;
                pdf.addImage(imgData, 'JPEG', offsetX, curY, finalW, finalH);

                curY += finalH + 5; // 5 mm entre blocos
            }

            // --- Abrir / descarregar PDF ---
            const pdfBlob = pdf.output('blob');
            const url = URL.createObjectURL(pdfBlob);
            const win = window.open(url, '_blank');
            if (!win) {
                const a = document.createElement('a');
                a.href = url;
                a.download = 'dashboard_gei_' + new Date().toISOString().slice(0, 10) + '.pdf';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 1000);
                alert('O popup foi bloqueado. O PDF foi transferido directamente.');
            } else {
                setTimeout(() => URL.revokeObjectURL(url), 60000);
            }

        } catch (err) {
            console.error('Erro ao gerar PDF:', err);
            alert('Erro ao gerar o PDF: ' + err.message);
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    function exportarWord() {
        // Gerar HTML simplificado para Word (MHTML approach)
        const escola = document.querySelector('.school-bar select option:checked')?.textContent?.trim() || 'Escola';
        const agora  = new Date().toLocaleDateString('pt-PT', { year: 'numeric', month: 'long', day: 'numeric' });

        const kpiEls = document.querySelectorAll('.kpi');
        let kpiHtml = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;margin-bottom:20px;font-family:Calibri,sans-serif;">';
        kpiHtml += '<tr style="background:#4b6cb7;color:#fff;"><th>Indicador</th><th>Valor</th><th>Detalhe</th></tr>';
        kpiEls.forEach(kpi => {
            const label = kpi.querySelector('.kpi-label')?.textContent?.trim() || '';
            const val   = kpi.querySelector('.kpi-val')?.textContent?.trim()   || '';
            const sub   = kpi.querySelector('.kpi-sub')?.textContent?.trim()   || '';
            kpiHtml += `<tr><td><b>${label}</b></td><td style="text-align:center;font-size:18px;font-weight:bold;">${val}</td><td style="color:#666;">${sub}</td></tr>`;
        });
        kpiHtml += '</table>';

        // Heatmap
        const heatTable = document.querySelector('#heatmap-container table');
        const heatHtml  = heatTable ? heatTable.outerHTML.replace(/style="[^"]*"/g, '') : '<p><em>Sem dados de heatmap</em></p>';

        const html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="utf-8">
<style>
body    { font-family: Calibri, sans-serif; font-size: 11pt; color: #222; }
h1      { color: #4b6cb7; border-bottom: 2px solid #4b6cb7; padding-bottom: 6px; }
h2      { color: #182848; margin-top: 20px; }
table   { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
th, td  { border: 1px solid #ddd; padding: 6px 10px; }
th      { background: #4b6cb7; color: #fff; }
tr:nth-child(even) { background: #f0f4fb; }
</style>
</head>
<body>
<h1>Dashboard GEI — ${escola}</h1>
<p><b>Data de exportação:</b> ${agora}</p>
<hr/>
<h2>Indicadores Principais</h2>
${kpiHtml}
<h2>Heatmap de Avarias por Sala</h2>
${heatHtml}
</body></html>`;

        const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `dashboard_gei_${new Date().toISOString().slice(0,10)}.doc`;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 500);
    }
    </script>
    <!-- ═══ TEMA ESCURO — fix stopPropagation dashboard ═══ -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopImmediatePropagation();
                window.GEITheme.toggle();
            }, true); // capture phase — corre antes de qualquer stopPropagation
        });
    });
    </script>
    <!-- ══════════════════════════════════════════════════ -->
<?php if (!$_dash_embedded): ?>
      <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>
<?php endif; ?>
