<?php
// ============================================================
// dashboard_sala.php — Dashboard Individual de Sala — GEI
// URL: dashboard_sala.php?si=<base64(id_sala)>&ies=<base64(id_escola)>
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

// Garantir que a ligação à BD está disponível (normalmente incluída no head.php)
if (!isset($db)) {
    foreach (['config.php', 'conexao.php', 'db.php', 'database.php'] as $_dbf) {
        if (file_exists(__DIR__ . '/' . $_dbf)) { require_once __DIR__ . '/' . $_dbf; break; }
    }
}

// ── AJAX: dados do gráfico por ano (deve ficar ANTES de qualquer output HTML) ─
if (isset($_GET['ajax_grafico']) && isset($_GET['ano']) && !empty($_GET['si']) && !empty($_GET['ies'])) {
    $id_sala_ajax  = (int) base64_decode($_GET['si']);
    $id_escola_ajax = (int) base64_decode($_GET['ies']);
    $ano_ajax = (int)$_GET['ano'];
    if ($id_sala_ajax > 0 && $ano_ajax > 2000) {
        $stmt = $db->prepare("
            SELECT DATE_FORMAT(dataavaria,'%Y-%m') AS mes, COUNT(*) AS total
            FROM avarias_reparacoes
            WHERE id_sala = ? AND YEAR(dataavaria) = ?
            GROUP BY mes ORDER BY mes ASC
        ");
        $stmt->bind_param("ii", $id_sala_ajax, $ano_ajax); $stmt->execute();
        $raw_ajax = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
        $meses_pt_ajax = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        $out_labels = []; $out_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%04d-%02d', $ano_ajax, $m);
            $out_labels[] = $meses_pt_ajax[$m - 1];
            $val = 0;
            foreach ($raw_ajax as $row) { if ($row['mes'] === $key) { $val = (int)$row['total']; break; } }
            $out_data[] = $val;
        }
        header('Content-Type: application/json');
        echo json_encode(['labels' => $out_labels, 'data' => $out_data, 'ano' => $ano_ajax, 'total' => array_sum($out_data)]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include("head.php"); ?>
<!-- Fontes carregadas globalmente em head.php via tokens.css -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php include("sessao_timeout.php"); ?>

<?php
// ── Validar parâmetros ────────────────────────────────────────────────────────
if (empty($_GET['si']) || empty($_GET['ies'])) {
    echo '<script>window.location.href="' . SVRURL . 'lista";</script>'; exit;
}

$id_sala   = (int) base64_decode($_GET['si']);
$id_escola = (int) base64_decode($_GET['ies']);


if ($id_sala <= 0 || $id_escola <= 0) {
    echo '<script>window.location.href="' . SVRURL . 'lista";</script>'; exit;
}

// ── Dados da sala ─────────────────────────────────────────────────────────────
$stmt = $db->prepare("SELECT s.nome, s.localizacao, s.departamento, e.nome_escola
                      FROM salas s JOIN escolas e ON s.id_escola = e.id
                      WHERE s.id = ? AND s.id_escola = ?");
$stmt->bind_param("ii", $id_sala, $id_escola);
$stmt->execute();
$sala = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sala) {
    echo '<script>window.location.href="' . SVRURL . 'lista";</script>'; exit;
}

// ── Ano letivo atual (período mais recente) ───────────────────────────────────
$r_per = $db->query("SELECT ano_lectivo, num_periodo FROM periodos ORDER BY id DESC LIMIT 1");
$periodo_atual = $r_per ? $r_per->fetch_assoc() : ['ano_lectivo' => date('Y') . '/' . (date('Y')+1), 'num_periodo' => 1];

// ── KPIs: equipamentos principais ────────────────────────────────────────────
$stmt = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ?");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$total_equip = $stmt->get_result()->fetch_row()[0]; $stmt->close();

// Com avaria aberta (sem data de reparação)
$stmt = $db->prepare("SELECT COUNT(DISTINCT id_equi) FROM avarias_reparacoes
                      WHERE id_sala = ? AND datareparacao IS NULL");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$total_avaria = $stmt->get_result()->fetch_row()[0]; $stmt->close();

// Reparados (com data de reparação, ano letivo atual)
$stmt = $db->prepare("SELECT COUNT(DISTINCT id_equi) FROM avarias_reparacoes
                      WHERE id_sala = ? AND datareparacao IS NOT NULL AND ano_letivo = ?");
$stmt->bind_param("is", $id_sala, $periodo_atual['ano_lectivo']); $stmt->execute();
$total_reparado = $stmt->get_result()->fetch_row()[0]; $stmt->close();

// Operacionais
$total_operacional = max(0, $total_equip - $total_avaria);

// Taxa de disponibilidade
$taxa_disponibilidade = ($total_equip > 0)
    ? round(($total_operacional / $total_equip) * 100, 1) : 100;

// ── Outros equipamentos ───────────────────────────────────────────────────────
$stmt = $db->prepare("SELECT nomeoutro, qta FROM outro_equipamento WHERE id_sala = ? ORDER BY nomeoutro");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$outros_equip = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// ── Lista de equipamentos com estado ─────────────────────────────────────────
$stmt = $db->prepare("
    SELECT e.id, e.nomeequi, e.tipo, e.marca_modelo, e.numserie, e.ip, e.data_compra,
           (SELECT COUNT(*) FROM avarias_reparacoes ar
            WHERE ar.id_equi = e.id AND ar.datareparacao IS NULL) AS avarias_abertas,
           (SELECT COUNT(*) FROM avarias_reparacoes ar2
            WHERE ar2.id_equi = e.id) AS total_avarias,
           (SELECT MAX(m.data_manutencao) FROM manutencao m WHERE m.id_equi = e.id) AS ultima_manutencao
    FROM equipamento e
    WHERE e.id_sala = ?
    ORDER BY avarias_abertas DESC, e.nomeequi ASC
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$equipamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// ── Histórico de incidentes (últimos 20) ─────────────────────────────────────
$stmt = $db->prepare("
    SELECT ar.id, ar.dataavaria, ar.datareparacao, ar.avaria, ar.reparacao,
           ar.autoravaria, ar.rep_efectuada_por, ar.ano_letivo, ar.periodo,
           e.nomeequi, e.tipo
    FROM avarias_reparacoes ar
    JOIN equipamento e ON ar.id_equi = e.id
    WHERE ar.id_sala = ?
    ORDER BY ar.dataavaria DESC
    LIMIT 20
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$historico = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// ── Próximas manutenções (tarefas pendentes) ──────────────────────────────────
$stmt = $db->prepare("
    SELECT id, descricao, urgencia, criado_por, data_criacao
    FROM tarefas
    WHERE id_sala = ? AND id_escola = ? AND data_conclusao IS NULL
    ORDER BY FIELD(urgencia,'Alta','Media','Baixa'), data_criacao DESC
    LIMIT 10
");
$stmt->bind_param("ii", $id_sala, $id_escola); $stmt->execute();
$tarefas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// ── Manutenções realizadas (últimas 10) ───────────────────────────────────────
$stmt = $db->prepare("
    SELECT m.codigo, m.data_manutencao, m.descricao, m.pessoa, m.observacoes, e.nomeequi
    FROM manutencao m
    JOIN equipamento e ON m.id_equi = e.id
    WHERE e.id_sala = ?
    ORDER BY m.data_manutencao DESC
    LIMIT 10
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$manutencoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// ── Anos disponíveis no histórico de avarias desta sala ──────────────────────
$stmt = $db->prepare("
    SELECT DISTINCT YEAR(dataavaria) AS ano
    FROM avarias_reparacoes
    WHERE id_sala = ? AND dataavaria IS NOT NULL
    ORDER BY ano DESC
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$anos_disponiveis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// Ano selecionado (via GET ou ano atual)
$ano_grafico_sel = isset($_GET['ano_grafico']) ? (int)$_GET['ano_grafico'] : (int)date('Y');
// Garantir que o ano selecionado existe; se não, usar o mais recente
$anos_lista = array_column($anos_disponiveis, 'ano');
if (!in_array($ano_grafico_sel, $anos_lista) && !empty($anos_lista)) {
    $ano_grafico_sel = $anos_lista[0];
}

// ── Evolução mensal de avarias (ano selecionado) para gráfico ────────────────
$stmt = $db->prepare("
    SELECT DATE_FORMAT(dataavaria,'%Y-%m') AS mes, COUNT(*) AS total
    FROM avarias_reparacoes
    WHERE id_sala = ? AND YEAR(dataavaria) = ?
    GROUP BY mes ORDER BY mes ASC
");
$stmt->bind_param("ii", $id_sala, $ano_grafico_sel); $stmt->execute();
$grafico_raw = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// Preencher todos os 12 meses do ano selecionado com 0 onde não há dados
$meses_labels = []; $meses_data = [];
$meses_pt = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
for ($m = 1; $m <= 12; $m++) {
    $key = sprintf('%04d-%02d', $ano_grafico_sel, $m);
    $meses_labels[] = $meses_pt[$m - 1];
    $valor = 0;
    foreach ($grafico_raw as $row) { if ($row['mes'] === $key) { $valor = (int)$row['total']; break; } }
    $meses_data[] = $valor;
}

// Total de avarias do ano selecionado
$total_avarias_ano_grafico = array_sum($meses_data);

// ── Distribuição por tipo de equipamento ─────────────────────────────────────
$stmt = $db->prepare("SELECT tipo, COUNT(*) AS n FROM equipamento WHERE id_sala = ? GROUP BY tipo ORDER BY n DESC");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$tipos_dist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// Equipamentos com mais avarias
$stmt = $db->prepare("
    SELECT e.nomeequi, e.tipo, COUNT(ar.id) AS n_avarias
    FROM equipamento e
    LEFT JOIN avarias_reparacoes ar ON ar.id_equi = e.id
    WHERE e.id_sala = ?
    GROUP BY e.id ORDER BY n_avarias DESC LIMIT 5
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$top_avarias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();

// Tempo médio de reparação
$stmt = $db->prepare("
    SELECT AVG(DATEDIFF(datareparacao, dataavaria)) AS media_dias
    FROM avarias_reparacoes
    WHERE id_sala = ? AND datareparacao IS NOT NULL AND DATEDIFF(datareparacao, dataavaria) >= 0
");
$stmt->bind_param("i", $id_sala); $stmt->execute();
$media_rep = round((float)$stmt->get_result()->fetch_row()[0] ?? 0, 1); $stmt->close();

// Total avarias este ano letivo
$stmt = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes WHERE id_sala = ? AND ano_letivo = ?");
$stmt->bind_param("is", $id_sala, $periodo_atual['ano_lectivo']); $stmt->execute();
$avarias_ano = $stmt->get_result()->fetch_row()[0]; $stmt->close();
?>

<style>
/* ══════════════════════════════════════════════════════════════
   DASHBOARD SALA — estilos próprios
   Segue as variáveis do dark-theme.css do GEI
   ══════════════════════════════════════════════════════════════ */

:root {
    --ds-bg:       #f0f4fb;
    --ds-surface:  #ffffff;
    --ds-surface2: #f7f9fe;
    --ds-primary:  #4b6cb7;
    --ds-pri-dk:   #182848;
    --ds-accent:   #507feb;
    --ds-success:  #1cc88a;
    --ds-warning:  #f6c23e;
    --ds-danger:   #e74a3b;
    --ds-purple:   #6f42c1;
    --ds-border:   #e3e8f4;
    --ds-text:     #1e2a45;
    --ds-muted:    #7b88a0;
    --ds-radius:   12px;
    --ds-shadow:   0 2px 14px rgba(75,108,183,.10);
    --ds-shadow-lg:0 6px 28px rgba(75,108,183,.16);
    --ds-font:     var(--font-body, 'DM Sans', sans-serif);
}
[data-theme="dark"] {
    --ds-bg:       #0f1117;
    --ds-surface:  #1a1d27;
    --ds-surface2: #1e2130;
    --ds-primary:  #6489f5;
    --ds-pri-dk:   #e2e8f0;
    --ds-accent:   #7b9bf7;
    --ds-success:  #26d49a;
    --ds-warning:  #f6c23e;
    --ds-danger:   #f07167;
    --ds-border:   #2d3348;
    --ds-text:     #e2e8f0;
    --ds-muted:    #94a3b8;
    --ds-shadow:   0 2px 14px rgba(0,0,0,.4);
    --ds-shadow-lg:0 8px 30px rgba(0,0,0,.55);
}

/* ── Layout base ── */
.ds-wrap {
    font-family: var(--font-body);
    background: var(--ds-bg);
    min-height: 100vh;
    padding: 28px 32px 56px;
    max-width: 1500px;
    margin: 0 auto;
    color: var(--ds-text);
}
@media (max-width: 768px) { .ds-wrap { padding: 16px 14px 40px; } }

/* ── Hero header ── */
.ds-hero {
    background: linear-gradient(135deg, var(--ds-pri-dk) 0%, var(--ds-primary) 100%);
    border-radius: 16px;
    padding: 32px 36px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 32px rgba(75,108,183,.30);
}
.ds-hero::before {
    content:''; position:absolute; top:-60px; right:-60px;
    width:220px; height:220px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.ds-hero::after {
    content:''; position:absolute; bottom:-70px; left:-30px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.04); pointer-events:none;
}
.ds-hero-info { position:relative; z-index:1; }
.ds-hero-badge {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(255,255,255,.14); color:rgba(255,255,255,.9);
    border-radius:20px; padding:4px 12px; font-size:.75rem; font-weight:600;
    letter-spacing:.4px; text-transform:uppercase; margin-bottom:10px;
}
.ds-hero h1 {
    font-size:2rem; font-weight:700; color:#fff; margin:0 0 6px;
    letter-spacing:-.5px;
}
.ds-hero-sub {
    color:rgba(255,255,255,.7); font-size:.88rem; display:flex; gap:16px; flex-wrap:wrap;
}
.ds-hero-sub span { display:flex; align-items:center; gap:5px; }
.ds-hero-actions { display:flex; gap:10px; flex-wrap:wrap; position:relative; z-index:1; }
.ds-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 18px; border-radius:9px; font-size:.84rem; font-weight:600;
    border:none; cursor:pointer; text-decoration:none; transition:all .2s;
    white-space:nowrap;
}
.ds-btn-white { background:#fff; color:#182848 !important; font-weight:700; }
.ds-btn-white:hover { background:#f0f4ff; transform:translateY(-1px); }
.ds-btn-outline { background:rgba(255,255,255,.12); color:#fff; border:1.5px solid rgba(255,255,255,.3); }
.ds-btn-outline:hover { background:rgba(255,255,255,.22); }

/* ── Breadcrumb ── */
.ds-breadcrumb {
    display:flex; align-items:center; gap:6px;
    font-size:.78rem; color:var(--ds-muted); margin-bottom:20px; flex-wrap:wrap;
}
.ds-breadcrumb a { color:var(--ds-primary); text-decoration:none; font-weight:500; }
.ds-breadcrumb a:hover { text-decoration:underline; }
.ds-breadcrumb .sep { color:var(--ds-border); }

/* ── KPI strip ── */
.ds-kpi-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap:16px;
    margin-bottom:28px;
}
.ds-kpi {
    background:var(--ds-surface); border-radius:var(--ds-radius);
    padding:20px 22px; box-shadow:var(--ds-shadow);
    border-left:4px solid transparent;
    transition:transform .2s, box-shadow .2s;
    position:relative; overflow:hidden;
}
.ds-kpi:hover { transform:translateY(-2px); box-shadow:var(--ds-shadow-lg); }
.ds-kpi::after {
    content:''; position:absolute; right:-16px; top:-16px;
    width:70px; height:70px; border-radius:50%;
    background:currentColor; opacity:.05;
}
.ds-kpi-icon { font-size:1.4rem; margin-bottom:8px; display:block; }
.ds-kpi-val { font-size:2rem; font-weight:700; line-height:1; font-family: var(--font-mono); }
.ds-kpi-label { font-size:.75rem; font-weight:600; color:var(--ds-muted);
                text-transform:uppercase; letter-spacing:.5px; margin-top:4px; }
.ds-kpi-sub { font-size:.72rem; color:var(--ds-muted); margin-top:3px; }
.kpi-ok    { border-color:var(--ds-success); color:var(--ds-success); }
.kpi-warn  { border-color:var(--ds-warning); color:var(--ds-warning); }
.kpi-danger{ border-color:var(--ds-danger);  color:var(--ds-danger);  }
.kpi-info  { border-color:var(--ds-primary); color:var(--ds-primary); }
.kpi-purple{ border-color:var(--ds-purple);  color:var(--ds-purple);  }

/* ── Disponibilidade ring ── */
.ds-avail-card {
    background:var(--ds-surface); border-radius:var(--ds-radius);
    padding:22px 24px; box-shadow:var(--ds-shadow);
    display:flex; align-items:center; gap:24px; flex-wrap:wrap;
}
.ds-ring-wrap { position:relative; width:100px; height:100px; flex-shrink:0; }
.ds-ring-wrap svg { transform:rotate(-90deg); }
.ds-ring-val {
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    font-size:1.1rem; font-weight:700; font-family: var(--font-mono);
    color:var(--ds-text);
}
.ds-avail-info h3 { font-size:1rem; font-weight:700; margin:0 0 4px; }
.ds-avail-info p  { font-size:.82rem; color:var(--ds-muted); margin:0 0 12px; }
.ds-prog-row { display:flex; align-items:center; gap:8px; margin-bottom:6px; font-size:.8rem; }
.ds-prog-bar { flex:1; height:6px; background:var(--ds-border); border-radius:3px; overflow:hidden; }
.ds-prog-fill { height:100%; border-radius:3px; transition:width .6s ease; }

/* ── Grid principal ── */
.ds-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
.ds-grid-3 { display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px; }
@media (max-width:1100px) { .ds-grid-2, .ds-grid-3 { grid-template-columns:1fr; } }

/* ── Painel genérico ── */
.ds-panel {
    background:var(--ds-surface); border-radius:var(--ds-radius);
    box-shadow:var(--ds-shadow); overflow:hidden;
}
.ds-panel-head {
    padding:16px 20px; border-bottom:1px solid var(--ds-border);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
}
.ds-panel-head h2 {
    font-size:.92rem; font-weight:700; margin:0;
    display:flex; align-items:center; gap:8px; color:var(--ds-text);
}
.ds-panel-head h2 i { color:var(--ds-primary); font-size:.95rem; }
.ds-panel-head .ds-badge {
    font-size:.7rem; font-weight:700; padding:2px 8px; border-radius:10px;
    background:var(--ds-surface2);
}
.ds-panel-body { padding:0; }

/* ── Tabela de equipamentos ── */
.ds-equip-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.ds-equip-table th {
    padding:9px 12px; text-align:left; font-size:.7rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.5px; color:var(--ds-muted);
    background:var(--ds-surface2); border-bottom:1px solid var(--ds-border);
    white-space:nowrap;
}
.ds-equip-table td {
    padding:10px 12px; border-bottom:1px solid var(--ds-border);
    vertical-align:middle; color:var(--ds-text);
}
.ds-equip-table tr:last-child td { border-bottom:none; }
.ds-equip-table tr:hover td { background:var(--ds-surface2); }
.ds-equip-name { font-weight:600; font-size:.85rem; }
.ds-equip-meta { font-size:.72rem; color:var(--ds-muted); margin-top:2px; }

/* ── Status badges ── */
.ds-status {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:20px; font-size:.71rem; font-weight:700;
    white-space:nowrap;
}
.ds-status-ok      { background:rgba(28,200,138,.12); color:#0e9c67; }
.ds-status-avaria  { background:rgba(231,74,59,.12);  color:#c73828; }
.ds-status-manut   { background:rgba(246,194,62,.15); color:#9a7200; }
[data-theme="dark"] .ds-status-ok     { background:rgba(38,212,154,.15); color:#26d49a; }
[data-theme="dark"] .ds-status-avaria { background:rgba(240,113,103,.18); color:#f07167; }
[data-theme="dark"] .ds-status-manut  { background:rgba(246,194,62,.15);  color:#f6c23e; }
.ds-dot { width:7px; height:7px; border-radius:50%; background:currentColor; flex-shrink:0; }

/* ── Timeline de incidentes ── */
.ds-timeline { padding:16px 20px; }
.ds-tl-item {
    display:flex; gap:14px; padding-bottom:18px; position:relative;
}
.ds-tl-item:not(:last-child)::before {
    content:''; position:absolute; left:13px; top:26px; bottom:0;
    width:2px; background:var(--ds-border);
}
.ds-tl-dot {
    width:26px; height:26px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.72rem; font-weight:700; margin-top:1px;
}
.tl-aberta  { background:rgba(231,74,59,.15);  color:var(--ds-danger); }
.tl-fechada { background:rgba(28,200,138,.15); color:var(--ds-success); }
.ds-tl-content { flex:1; min-width:0; }
.ds-tl-title { font-size:.84rem; font-weight:600; margin-bottom:3px;
               white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ds-tl-meta  { font-size:.72rem; color:var(--ds-muted); display:flex; gap:10px; flex-wrap:wrap; }
.ds-tl-desc  { font-size:.78rem; color:var(--ds-muted); margin-top:4px;
               display:-webkit-box; -webkit-line-clamp:2;
               -webkit-box-orient:vertical; overflow:hidden; }
.ds-tl-repair{
    font-size:.75rem; color:var(--ds-success); margin-top:4px;
    background:rgba(28,200,138,.08); padding:3px 8px; border-radius:6px;
    display:inline-block;
}
[data-theme="dark"] .ds-tl-repair { background:rgba(38,212,154,.12); }

/* ── Tarefas pendentes ── */
.ds-tarefa {
    display:flex; align-items:flex-start; gap:12px;
    padding:12px 20px; border-bottom:1px solid var(--ds-border);
}
.ds-tarefa:last-child { border-bottom:none; }
.ds-tarefa-urg {
    width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px;
}
.urg-Alta   { background:var(--ds-danger); }
.urg-Media  { background:var(--ds-warning); }
.urg-Baixa  { background:var(--ds-success); }
.ds-tarefa-text { font-size:.83rem; font-weight:500; margin-bottom:3px; }
.ds-tarefa-meta { font-size:.72rem; color:var(--ds-muted); }
.ds-tarefa-badge {
    margin-left:auto; font-size:.68rem; font-weight:700; padding:2px 8px;
    border-radius:10px; white-space:nowrap; flex-shrink:0;
}
.tb-Alta   { background:rgba(231,74,59,.12);  color:var(--ds-danger); }
.tb-Media  { background:rgba(246,194,62,.15); color:#9a7200; }
.tb-Baixa  { background:rgba(28,200,138,.12); color:#0e9c67; }
[data-theme="dark"] .tb-Alta  { background:rgba(240,113,103,.18); color:var(--ds-danger); }
[data-theme="dark"] .tb-Media { background:rgba(246,194,62,.15);  color:var(--ds-warning); }
[data-theme="dark"] .tb-Baixa { background:rgba(38,212,154,.15);  color:var(--ds-success); }

/* ── Gráficos ── */
.ds-chart-wrap { padding:18px 20px; }
.ds-chart-wrap canvas { max-height:220px; }
.ds-chart-container { position:relative; height:220px; width:100%; }

/* ── Manutenções lista ── */
.ds-manut-item {
    padding:12px 20px; border-bottom:1px solid var(--ds-border);
    display:flex; gap:14px; align-items:flex-start;
}
.ds-manut-item:last-child { border-bottom:none; }
.ds-manut-date {
    font-size:.72rem; font-weight:700; color:var(--ds-primary);
    background:rgba(75,108,183,.08); padding:3px 8px; border-radius:6px;
    white-space:nowrap; flex-shrink:0; margin-top:2px;
    font-family: var(--font-mono);
}
.ds-manut-desc { font-size:.83rem; font-weight:500; margin-bottom:2px; }
.ds-manut-meta { font-size:.72rem; color:var(--ds-muted); }

/* ── Filtro histórico ── */
.ds-hist-filter {
    padding:10px 16px; border-bottom:1px solid var(--ds-border);
    display:flex; gap:8px; align-items:center; flex-wrap:wrap;
}
.ds-hist-search {
    flex:1; min-width:140px;
    padding:6px 12px 6px 32px;
    border:1.5px solid var(--ds-border); border-radius:8px;
    background:var(--ds-surface2); color:var(--ds-text);
    font-size:.8rem; font-family: var(--font-body);
    outline:none; transition:border-color .2s;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%237b88a0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:10px center;
}
.ds-hist-search:focus { border-color:var(--ds-primary); }
.ds-hist-search::placeholder { color:var(--ds-muted); }
.ds-hist-select {
    padding:6px 10px; border:1.5px solid var(--ds-border); border-radius:8px;
    background:var(--ds-surface2); color:var(--ds-text);
    font-size:.78rem; font-family: var(--font-body);
    outline:none; cursor:pointer; transition:border-color .2s;
}
.ds-hist-select:focus { border-color:var(--ds-primary); }
.ds-hist-empty {
    padding:20px; text-align:center; color:var(--ds-muted);
    font-size:.82rem; display:none;
}

/* ── Outros equipamentos ── */
.ds-outros-grid {
    display:flex; flex-wrap:wrap; gap:10px; padding:16px 20px;
}
.ds-outro-item {
    background:var(--ds-surface2); border:1px solid var(--ds-border);
    border-radius:8px; padding:8px 14px;
    display:flex; align-items:center; gap:8px;
    font-size:.8rem; font-weight:500;
}
.ds-outro-item .qta {
    background:var(--ds-primary); color:#fff;
    border-radius:5px; padding:1px 7px; font-size:.72rem; font-weight:700;
    font-family: var(--font-mono);
}

/* ── Top avarias ── */
.ds-top-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 20px; border-bottom:1px solid var(--ds-border);
}
.ds-top-item:last-child { border-bottom:none; }
.ds-top-rank {
    width:22px; height:22px; border-radius:50%; background:var(--ds-primary);
    color:#fff; font-size:.65rem; font-weight:800; display:flex;
    align-items:center; justify-content:center; flex-shrink:0;
    font-family: var(--font-mono);
}
.ds-top-info { flex:1; min-width:0; }
.ds-top-name { font-size:.83rem; font-weight:600;
               white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ds-top-tipo { font-size:.72rem; color:var(--ds-muted); }
.ds-top-n {
    font-size:.9rem; font-weight:700; color:var(--ds-danger);
    font-family: var(--font-mono); white-space:nowrap;
}

/* ── Estado vazio ── */
.ds-empty {
    padding:36px 20px; text-align:center; color:var(--ds-muted); font-size:.85rem;
}
.ds-empty-icon { font-size:2.4rem; display:block; margin-bottom:10px; opacity:.5; }

/* ── Refresh badge ── */
.ds-refresh {
    display:flex; align-items:center; gap:8px;
    font-size:.75rem; color:var(--ds-muted); margin-bottom:20px;
}
.ds-refresh-dot {
    width:8px; height:8px; border-radius:50%; background:var(--ds-success);
    animation:pulse-dot 2s infinite;
}
@keyframes pulse-dot {
    0%,100%{opacity:1; transform:scale(1)}
    50%{opacity:.5; transform:scale(.8)}
}

/* ── Auto-refresh toggle ── */
.ds-ar-label { display:flex; align-items:center; gap:8px; cursor:pointer; user-select:none; }
.ds-ar-track {
    width:34px; height:18px; background:var(--ds-border); border-radius:9px;
    position:relative; transition:background .2s;
}
.ds-ar-track.on { background:var(--ds-success); }
.ds-ar-knob {
    position:absolute; top:2px; left:2px;
    width:14px; height:14px; border-radius:50%; background:#fff;
    transition:transform .2s; box-shadow:0 1px 4px rgba(0,0,0,.2);
}
.ds-ar-track.on .ds-ar-knob { transform:translateX(16px); }

/* ── Scroll fino ── */
.ds-scroll { max-height:420px; overflow-y:auto; }
.ds-scroll::-webkit-scrollbar { width:4px; }
.ds-scroll::-webkit-scrollbar-track { background:transparent; }
.ds-scroll::-webkit-scrollbar-thumb { background:var(--ds-border); border-radius:2px; }
</style>

<div class="about">
 <div class="ds-wrap">



  <!-- Hero -->
  <div class="ds-hero">
    <div class="ds-hero-info">
      <div class="ds-hero-badge">
        <span>🏫</span>
        <?= htmlspecialchars($sala['nome_escola']) ?>
      </div>
      <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <h1 style="margin:0;">
          <?php
            $icon_sala = '🖥️';
            if (!empty($sala['departamento'])) $icon_sala = '🏛️';
            echo $icon_sala . ' ' . htmlspecialchars($sala['nome']);
          ?>
        </h1>
        <div class="ds-hero-sub" style="margin:0;">
          <?php if ($sala['localizacao']): ?>
            <span>📍 <?= htmlspecialchars($sala['localizacao']) ?></span>
          <?php endif; ?>
          <?php if ($sala['departamento']): ?>
            <span>🏢 <?= htmlspecialchars($sala['departamento']) ?></span>
          <?php endif; ?>
          <span>🖥️ <?= $total_equip ?> equipamento<?= $total_equip != 1 ? 's' : '' ?></span>
        </div>
      </div>
    </div>
    <div class="ds-hero-actions">
      <a href="<?= SVRURL ?>inserir_avaria.php?aves=<?= base64_encode($id_escola) ?>&sala=<?= $id_sala ?>"
         class="ds-btn ds-btn-white">
        ⚠️ Registar Avaria
      </a>
      <a href="<?= SVRURL ?>ver_equipamentos_sala.php?x=<?= base64_encode(1) ?>&si=<?= base64_encode($id_sala) ?>&ies=<?= base64_encode($id_escola) ?>"
         class="ds-btn ds-btn-outline">
        📋 Ver Equipamentos
      </a>
      <button class="ds-btn ds-btn-outline" onclick="var w=window.open(window.location.href,'_blank'); w.onload=function(){ w.print(); };">
        🖨️ Imprimir
      </button>
    </div>
  </div>



  <!-- KPIs -->
  <div class="ds-kpi-grid">

    <div class="ds-kpi kpi-ok">
      <span class="ds-kpi-icon">✅</span>
      <div class="ds-kpi-val"><?= $total_operacional ?></div>
      <div class="ds-kpi-label">Operacionais</div>
      <div class="ds-kpi-sub">de <?= $total_equip ?> no total</div>
    </div>

    <div class="ds-kpi kpi-danger">
      <span class="ds-kpi-icon">🔴</span>
      <div class="ds-kpi-val"><?= $total_avaria ?></div>
      <div class="ds-kpi-label">Com avaria</div>
      <div class="ds-kpi-sub">avarias em aberto</div>
    </div>

    <div class="ds-kpi kpi-warn">
      <span class="ds-kpi-icon">📋</span>
      <div class="ds-kpi-val"><?= $avarias_ano ?></div>
      <div class="ds-kpi-label">Avarias <?= $periodo_atual['ano_lectivo'] ?></div>
      <div class="ds-kpi-sub"><?= $total_reparado ?> reparadas</div>
    </div>

    <div class="ds-kpi kpi-info">
      <span class="ds-kpi-icon">⏱️</span>
      <div class="ds-kpi-val"><?= $media_rep > 0 ? $media_rep : '—' ?></div>
      <div class="ds-kpi-label">Dias p/ reparar</div>
      <div class="ds-kpi-sub">tempo médio</div>
    </div>

    <div class="ds-kpi kpi-purple">
      <span class="ds-kpi-icon">📌</span>
      <div class="ds-kpi-val"><?= count($tarefas) ?></div>
      <div class="ds-kpi-label">Tarefas pendentes</div>
      <div class="ds-kpi-sub">por resolver</div>
    </div>

    <div class="ds-kpi kpi-info">
      <span class="ds-kpi-icon">📦</span>
      <div class="ds-kpi-val"><?= count($manutencoes) > 0 ? htmlspecialchars($manutencoes[0]['data_manutencao']) : '—' ?></div>
      <div class="ds-kpi-label">Última manutenção</div>
      <div class="ds-kpi-sub"><?= count($manutencoes) > 0 ? htmlspecialchars($manutencoes[0]['nomeequi']) : 'sem registos' ?></div>
    </div>

  </div>

  <!-- Disponibilidade + gráfico avarias -->
  <div class="ds-grid-2" style="margin-bottom:20px;">

    <!-- Disponibilidade ring -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-tachometer"></i> Taxa de Disponibilidade</h2>
      </div>
      <div class="ds-panel-body" style="padding:20px 24px;">
        <div class="ds-avail-card" style="padding:0;box-shadow:none;border-radius:0;">
          <?php
            $pct = $taxa_disponibilidade;
            $circumference = 2 * M_PI * 38;
            $offset = $circumference * (1 - $pct / 100);
            $ring_color = $pct >= 90 ? '#1cc88a' : ($pct >= 70 ? '#f6c23e' : '#e74a3b');
          ?>
          <div class="ds-ring-wrap">
            <svg width="100" height="100" viewBox="0 0 100 100">
              <circle cx="50" cy="50" r="38" fill="none"
                      stroke="var(--ds-border)" stroke-width="9"/>
              <circle cx="50" cy="50" r="38" fill="none"
                      stroke="<?= $ring_color ?>" stroke-width="9"
                      stroke-dasharray="<?= round($circumference, 2) ?>"
                      stroke-dashoffset="<?= round($offset, 2) ?>"
                      stroke-linecap="round"
                      style="transition:stroke-dashoffset 1s ease"/>
            </svg>
            <div class="ds-ring-val" style="color:<?= $ring_color ?>">
              <?= $taxa_disponibilidade ?>%
            </div>
          </div>
          <div class="ds-avail-info">
            <h3><?= $taxa_disponibilidade >= 90 ? '✅ Bom estado' : ($taxa_disponibilidade >= 70 ? '⚠️ Atenção' : '🔴 Crítico') ?></h3>
            <p><?= $total_operacional ?> de <?= $total_equip ?> equipamentos operacionais</p>
            <?php if ($total_equip > 0): ?>
              <div class="ds-prog-row">
                <span style="width:80px;font-size:.75rem;color:var(--ds-muted)">Operacional</span>
                <div class="ds-prog-bar">
                  <div class="ds-prog-fill" style="width:<?= round($total_operacional/$total_equip*100) ?>%;background:var(--ds-success)"></div>
                </div>
                <span style="font-size:.72rem;font-weight:700;color:var(--ds-success);width:30px;text-align:right"><?= $total_operacional ?></span>
              </div>
              <div class="ds-prog-row">
                <span style="width:80px;font-size:.75rem;color:var(--ds-muted)">Com avaria</span>
                <div class="ds-prog-bar">
                  <div class="ds-prog-fill" style="width:<?= round($total_avaria/$total_equip*100) ?>%;background:var(--ds-danger)"></div>
                </div>
                <span style="font-size:.72rem;font-weight:700;color:var(--ds-danger);width:30px;text-align:right"><?= $total_avaria ?></span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Distribuição por tipo -->
        <?php if (!empty($tipos_dist)): ?>
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--ds-border);width:100%">
          <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--ds-muted);margin-bottom:10px">Distribuição por tipo</div>
          <?php foreach ($tipos_dist as $td):
            $pct_t = $total_equip > 0 ? round($td['n']/$total_equip*100) : 0;
          ?>
          <div class="ds-prog-row">
            <span style="width:120px;font-size:.75rem;color:var(--ds-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              <?= htmlspecialchars($td['tipo']) ?>
            </span>
            <div class="ds-prog-bar">
              <div class="ds-prog-fill" style="width:<?= $pct_t ?>%;background:var(--ds-primary)"></div>
            </div>
            <span style="font-size:.72rem;font-weight:700;color:var(--ds-primary);width:24px;text-align:right"><?= $td['n'] ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Gráfico de evolução de avarias -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-line-chart"></i> Avarias — <?= $ano_grafico_sel ?></h2>
        <span id="badge-total-avarias" class="ds-badge" style="background:var(--ds-danger);color:#fff;" title="Total de avarias no ano">
          <?= $total_avarias_ano_grafico ?>
        </span>
        <?php if (!empty($anos_disponiveis)): ?>
        <select id="sel-ano-grafico" class="ds-hist-select" style="font-size:.75rem;padding:4px 8px;">
          <?php foreach ($anos_disponiveis as $a): ?>
            <option value="<?= $a['ano'] ?>" <?= $a['ano'] == $ano_grafico_sel ? 'selected' : '' ?>>
              <?= $a['ano'] ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
      </div>
      <div class="ds-panel-body ds-chart-wrap">
        <div class="ds-chart-container">
          <canvas id="chartAvarias"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Equipamentos + Tarefas -->
  <div class="ds-grid-3" style="margin-bottom:20px;">

    <!-- Lista equipamentos -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-desktop"></i> Estado dos Equipamentos</h2>
        <span class="ds-badge"><?= $total_equip ?></span>
      </div>
      <div class="ds-panel-body ds-scroll">
        <?php if (empty($equipamentos)): ?>
          <div class="ds-empty"><span class="ds-empty-icon">🖥️</span>Sem equipamentos registados nesta sala.</div>
        <?php else: ?>
          <table class="ds-equip-table">
            <thead>
              <tr>
                <th>Equipamento</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Avarias</th>
                <th>Últ. Manutenção</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($equipamentos as $eq):
              $estado = $eq['avarias_abertas'] > 0 ? 'avaria' : 'ok';
              $estado_label = $eq['avarias_abertas'] > 0 ? 'Com avaria' : 'Operacional';
              $estado_class = $eq['avarias_abertas'] > 0 ? 'ds-status-avaria' : 'ds-status-ok';
              $dot_color = $eq['avarias_abertas'] > 0 ? 'ds-status-avaria' : 'ds-status-ok';
            ?>
            <tr>
              <td>
                <div class="ds-equip-name"><?= htmlspecialchars($eq['nomeequi']) ?></div>
                <div class="ds-equip-meta">
                  <?= $eq['marca_modelo'] ? htmlspecialchars($eq['marca_modelo']) : '' ?>
                  <?= ($eq['marca_modelo'] && $eq['numserie']) ? ' · ' : '' ?>
                  <?= $eq['numserie'] ? 'S/N: ' . htmlspecialchars($eq['numserie']) : '' ?>
                </div>
              </td>
              <td><span style="font-size:.78rem;color:var(--ds-muted)"><?= htmlspecialchars($eq['tipo']) ?></span></td>
              <td>
                <span class="ds-status <?= $estado_class ?>">
                  <span class="ds-dot"></span>
                  <?= $estado_label ?>
                </span>
              </td>
              <td>
                <span style="font-family: var(--font-mono);font-size:.82rem;font-weight:700;
                      color:<?= $eq['total_avarias'] > 0 ? 'var(--ds-danger)' : 'var(--ds-muted)' ?>">
                  <?= $eq['total_avarias'] ?>
                </span>
              </td>
              <td style="font-size:.76rem;color:var(--ds-muted);white-space:nowrap">
                <?= $eq['ultima_manutencao'] ? date('d/m/Y', strtotime($eq['ultima_manutencao'])) : '—' ?>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tarefas pendentes -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-tasks"></i> Tarefas Pendentes</h2>
        <?php if (count($tarefas) > 0): ?>
          <span class="ds-badge" style="background:rgba(231,74,59,.12);color:var(--ds-danger)"><?= count($tarefas) ?></span>
        <?php endif; ?>
      </div>
      <div class="ds-panel-body ds-scroll">
        <?php if (empty($tarefas)): ?>
          <div class="ds-empty"><span class="ds-empty-icon">✅</span>Sem tarefas pendentes.</div>
        <?php else: ?>
          <?php foreach ($tarefas as $t): ?>
          <div class="ds-tarefa">
            <span class="ds-tarefa-urg urg-<?= htmlspecialchars($t['urgencia']) ?>"></span>
            <div style="flex:1;min-width:0">
              <div class="ds-tarefa-text"><?= htmlspecialchars(mb_strimwidth($t['descricao'], 0, 80, '…')) ?></div>
              <div class="ds-tarefa-meta">
                👤 <?= htmlspecialchars($t['criado_por']) ?> ·
                📅 <?= date('d/m/Y', strtotime($t['data_criacao'])) ?>
              </div>
            </div>
            <span class="ds-tarefa-badge tb-<?= htmlspecialchars($t['urgencia']) ?>">
              <?= htmlspecialchars($t['urgencia']) ?>
            </span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Top equipamentos com mais avarias -->
      <div class="ds-panel-head" style="margin-top:4px">
        <h2><i class="fa fa-exclamation-triangle"></i> Mais Avarias</h2>
      </div>
      <?php if (empty($top_avarias) || $top_avarias[0]['n_avarias'] == 0): ?>
        <div class="ds-empty"><span class="ds-empty-icon">🏆</span>Nenhuma avaria registada.</div>
      <?php else: ?>
        <?php foreach ($top_avarias as $idx => $ta): if ($ta['n_avarias'] == 0) continue; ?>
        <div class="ds-top-item">
          <div class="ds-top-rank"><?= $idx + 1 ?></div>
          <div class="ds-top-info">
            <div class="ds-top-name"><?= htmlspecialchars($ta['nomeequi']) ?></div>
            <div class="ds-top-tipo"><?= htmlspecialchars($ta['tipo']) ?></div>
          </div>
          <div class="ds-top-n"><?= $ta['n_avarias'] ?>×</div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Histórico + Manutenções realizadas -->
  <div class="ds-grid-2" style="margin-bottom:20px;">

    <!-- Histórico de incidentes -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-history"></i> Histórico de Incidentes</h2>
        <?php if (!empty($historico)): ?>
          <span class="ds-badge" id="hist-count"><?= count($historico) ?></span>
        <?php endif; ?>
      </div>
      <?php if (!empty($historico)): ?>
      <div class="ds-hist-filter">
        <input type="text" id="hist-search" class="ds-hist-search"
               placeholder="Filtrar por equipamento…" autocomplete="off">
        <select id="hist-estado" class="ds-hist-select">
          <option value="">Todos os estados</option>
          <option value="aberto">⚠️ Em aberto</option>
          <option value="fechado">✅ Resolvidos</option>
        </select>
      </div>
      <?php endif; ?>
      <div class="ds-panel-body ds-scroll">
        <?php if (empty($historico)): ?>
          <div class="ds-empty"><span class="ds-empty-icon">📋</span>Sem incidentes registados.</div>
        <?php else: ?>
          <div class="ds-timeline" id="hist-timeline">
          <?php foreach ($historico as $inc): ?>
          <div class="ds-tl-item"
               data-equi="<?= htmlspecialchars(mb_strtolower($inc['nomeequi']), ENT_QUOTES) ?>"
               data-estado="<?= $inc['datareparacao'] ? 'fechado' : 'aberto' ?>">
            <div class="ds-tl-dot <?= $inc['datareparacao'] ? 'tl-fechada' : 'tl-aberta' ?>">
              <?= $inc['datareparacao'] ? '✓' : '!' ?>
            </div>
            <div class="ds-tl-content">
              <div class="ds-tl-title"><?= htmlspecialchars($inc['nomeequi']) ?></div>
              <div class="ds-tl-meta">
                <span>📅 <?= date('d/m/Y', strtotime($inc['dataavaria'])) ?></span>
                <span>🏷️ <?= htmlspecialchars($inc['tipo']) ?></span>
                <span>👤 <?= htmlspecialchars($inc['autoravaria']) ?></span>
              </div>
              <div class="ds-tl-desc"><?= htmlspecialchars($inc['avaria']) ?></div>
              <?php if ($inc['datareparacao']): ?>
                <div class="ds-tl-repair">
                  ✅ Reparado a <?= date('d/m/Y', strtotime($inc['datareparacao'])) ?>
                  <?= $inc['rep_efectuada_por'] ? ' · ' . htmlspecialchars($inc['rep_efectuada_por']) : '' ?>
                </div>
                <?php if (!empty($inc['reparacao'])): ?>
                <div class="ds-tl-desc" style="margin-top:4px;color:var(--ds-text);opacity:.8;">
                  🔧 <?= htmlspecialchars($inc['reparacao']) ?>
                </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
          </div>
          <div class="ds-hist-empty" id="hist-no-results">
            📭 Nenhum incidente corresponde ao filtro aplicado.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Manutenções realizadas -->
    <div class="ds-panel">
      <div class="ds-panel-head">
        <h2><i class="fa fa-wrench"></i> Manutenções Realizadas</h2>
      </div>
      <div class="ds-panel-body ds-scroll">
        <?php if (empty($manutencoes)): ?>
          <div class="ds-empty"><span class="ds-empty-icon">🔧</span>Sem manutenções registadas.</div>
        <?php else: ?>
          <?php foreach ($manutencoes as $m): ?>
          <div class="ds-manut-item">
            <div class="ds-manut-date"><?= date('d/m/y', strtotime($m['data_manutencao'])) ?></div>
            <div>
              <div class="ds-manut-desc"><?= htmlspecialchars($m['nomeequi']) ?></div>
              <div class="ds-manut-meta">
                <?= htmlspecialchars(mb_strimwidth($m['descricao'] ?? '', 0, 60, '…')) ?>
              </div>
              <div class="ds-manut-meta">👤 <?= htmlspecialchars($m['pessoa']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Outros equipamentos da sala -->
      <?php if (!empty($outros_equip)): ?>
      <div class="ds-panel-head" style="margin-top:4px">
        <h2><i class="fa fa-cube"></i> Outros Equipamentos</h2>
      </div>
      <div class="ds-outros-grid">
        <?php foreach ($outros_equip as $oe): ?>
          <div class="ds-outro-item">
            📦 <?= htmlspecialchars($oe['nomeoutro']) ?>
            <span class="qta">×<?= $oe['qta'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

 </div><!-- /ds-wrap -->
</div><!-- /about -->

<?php include("footer.php"); ?>

<!-- ══ Charts & Auto-refresh ═══════════════════════════════════════════════ -->
<script>
(function () {
  // ── Gráfico de evolução de avarias ─────────────────────────────────────
  const labels  = <?= json_encode($meses_labels) ?>;
  const data    = <?= json_encode($meses_data) ?>;
  const isDark  = document.documentElement.getAttribute('data-theme') === 'dark';
  const gridClr = isDark ? '#2d3348' : '#e3e8f4';
  const textClr = isDark ? '#94a3b8' : '#7b88a0';

  const ctx = document.getElementById('chartAvarias');
  if (ctx) {
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Avarias',
          data,
          backgroundColor: data.map(v => v > 0
            ? (isDark ? 'rgba(240,113,103,.55)' : 'rgba(231,74,59,.4)')
            : (isDark ? 'rgba(100,137,245,.2)'  : 'rgba(75,108,183,.15)')),
          borderColor: data.map(v => v > 0
            ? (isDark ? '#f07167' : '#e74a3b')
            : (isDark ? '#6489f5' : '#4b6cb7')),
          borderWidth: 1.5,
          borderRadius: 5,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: {
            label: ctx => ctx.parsed.y + ' avaria' + (ctx.parsed.y !== 1 ? 's' : '')
          }}
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, color: textClr, font: { size: 10 } },
            grid:  { color: gridClr }
          },
          x: {
            ticks: { color: textClr, font: { size: 10 } },
            grid:  { display: false }
          }
        }
      }
    });

    // Reagir a mudança de tema
    document.addEventListener('gei-theme-changed', () => {
      const dark = document.documentElement.getAttribute('data-theme') === 'dark';
      chart.data.datasets[0].backgroundColor = chart.data.datasets[0].data.map(v =>
        v > 0 ? (dark ? 'rgba(240,113,103,.55)' : 'rgba(231,74,59,.4)')
              : (dark ? 'rgba(100,137,245,.2)'  : 'rgba(75,108,183,.15)'));
      chart.data.datasets[0].borderColor = chart.data.datasets[0].data.map(v =>
        v > 0 ? (dark ? '#f07167' : '#e74a3b') : (dark ? '#6489f5' : '#4b6cb7'));
      chart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#7b88a0';
      chart.options.scales.y.grid.color  = dark ? '#2d3348' : '#e3e8f4';
      chart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#7b88a0';
      chart.update();
    });

    // ── Filtro por ano do gráfico ──────────────────────────────────────────
    const selAno = document.getElementById('sel-ano-grafico');
    const chartTitle = ctx.closest('.ds-panel')?.querySelector('.ds-panel-head h2');
    if (selAno) {
      selAno.addEventListener('change', async function () {
        const ano = this.value;
        const params = new URLSearchParams(window.location.search);
        params.set('ajax_grafico', '1');
        params.set('ano', ano);
        try {
          const resp = await fetch('?' + params.toString());
          const json = await resp.json();
          const dark = document.documentElement.getAttribute('data-theme') === 'dark';
          chart.data.labels = json.labels;
          chart.data.datasets[0].data = json.data;
          chart.data.datasets[0].backgroundColor = json.data.map(v =>
            v > 0 ? (dark ? 'rgba(240,113,103,.55)' : 'rgba(231,74,59,.4)')
                  : (dark ? 'rgba(100,137,245,.2)'  : 'rgba(75,108,183,.15)'));
          chart.data.datasets[0].borderColor = json.data.map(v =>
            v > 0 ? (dark ? '#f07167' : '#e74a3b') : (dark ? '#6489f5' : '#4b6cb7'));
          chart.update();
          if (chartTitle) {
            chartTitle.innerHTML = '<i class="fa fa-line-chart"></i> Avarias \u2014 ' + json.ano;
          }
          const badge = document.getElementById('badge-total-avarias');
          if (badge) badge.textContent = json.total;
        } catch (e) { console.error('Erro ao carregar dados do gráfico:', e); }
      });
    }
  }

  // ── Auto-refresh ────────────────────────────────────────────────────────
  let arOn    = false;
  let arTimer = null;
  const track = document.getElementById('ds-ar-track');

  function toggleAR() {
    arOn = !arOn;
    track.classList.toggle('on', arOn);
    if (arOn) {
      arTimer = setInterval(() => window.location.reload(), 30000);
    } else {
      clearInterval(arTimer);
    }
  }
  if (track) track.addEventListener('click', toggleAR);

  // Hora de atualização
  const upd = document.getElementById('ds-last-update');
  if (upd) {
    const now = new Date();
    upd.textContent = now.toLocaleTimeString('pt-PT');
  }

  // Observer para tema — dispara evento quando data-theme muda
  new MutationObserver(() => {
    document.dispatchEvent(new Event('gei-theme-changed'));
  }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

  // ── Filtro do Histórico de Incidentes ──────────────────────────────────
  (function () {
    const searchInput = document.getElementById('hist-search');
    const estadoSelect = document.getElementById('hist-estado');
    const timeline     = document.getElementById('hist-timeline');
    const noResults    = document.getElementById('hist-no-results');
    const countBadge   = document.getElementById('hist-count');

    if (!searchInput || !timeline) return;

    function applyFilter() {
      const query  = searchInput.value.trim().toLowerCase();
      const estado = estadoSelect ? estadoSelect.value : '';
      const items  = timeline.querySelectorAll('.ds-tl-item');
      let visible  = 0;

      items.forEach(item => {
        const equiName = item.getAttribute('data-equi') || '';
        const itemEstado = item.getAttribute('data-estado') || '';
        const matchName  = !query  || equiName.includes(query);
        const matchEstado = !estado || itemEstado === estado;

        if (matchName && matchEstado) {
          item.style.display = '';
          visible++;
        } else {
          item.style.display = 'none';
        }
      });

      if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
      if (countBadge) countBadge.textContent = visible;
    }

    searchInput.addEventListener('input', applyFilter);
    if (estadoSelect) estadoSelect.addEventListener('change', applyFilter);
  })();

})();
</script>

</body>
</html>
