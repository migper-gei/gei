<?php
// dashboard_comparacao.php
// Endpoint AJAX — devolve KPIs para o período de comparação
// GET params: esc=N, periodo=[mes_anterior|ano_anterior|periodo_anterior], ano=N, ano_sel=N

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $isHttps,
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    echo json_encode(['erro' => 'sem_sessao']);
    exit;
}

require_once('config.php');

$e       = (isset($_GET['esc']) && is_numeric($_GET['esc'])) ? (int)$_GET['esc'] : 1;
$periodo = $_GET['periodo'] ?? 'ano_anterior';
$ano_sel = (isset($_GET['ano_sel']) && is_numeric($_GET['ano_sel'])) ? (int)$_GET['ano_sel'] : (int)date('Y');

function qval2($db, $sql) {
    $r = @mysqli_query($db, $sql);
    if (!$r) return 0;
    $row = mysqli_fetch_row($r);
    return $row[0] ?? 0;
}

// Definir o intervalo de comparação
$tz = new DateTimeZone('Europe/Lisbon');
$hoje = new DateTime('now', $tz);

switch ($periodo) {
    case 'mes_anterior':
        $dt_inicio = (new DateTime("first day of last month", $tz))->format('Y-m-d');
        $dt_fim    = (new DateTime("last day of last month",  $tz))->format('Y-m-d');
        $label     = 'Mês anterior (' . (new DateTime("first day of last month", $tz))->format('M/Y') . ')';
        break;
    case 'ano_anterior':
        $ano_cmp   = $ano_sel - 1;
        $dt_inicio = "$ano_cmp-01-01";
        $dt_fim    = "$ano_cmp-12-31";
        $label     = "Ano $ano_cmp";
        break;
    case 'periodo_anterior':
    default:
        // "Período anterior" = mesmo número de dias antes do período actual
        // Se ano_sel for o ano actual, toma YTD; senão toma todo o ano
        if ($ano_sel == (int)date('Y')) {
            $inicio_atual = date('Y') . '-01-01';
            $dias = (new DateTime($inicio_atual))->diff($hoje)->days + 1;
            $fim_cmp   = (new DateTime($inicio_atual))->modify('-1 day');
            $ini_cmp   = (clone $fim_cmp)->modify("-{$dias} days")->modify('+1 day');
            $dt_inicio = $ini_cmp->format('Y-m-d');
            $dt_fim    = $fim_cmp->format('Y-m-d');
            $label     = 'Período anterior (' . $ini_cmp->format('d/m/Y') . ' – ' . $fim_cmp->format('d/m/Y') . ')';
        } else {
            $ano_cmp   = $ano_sel - 1;
            $dt_inicio = "$ano_cmp-01-01";
            $dt_fim    = "$ano_cmp-12-31";
            $label     = "Período anterior ($ano_cmp)";
        }
        break;
}

$where_av  = "AND ar.dataavaria  BETWEEN '$dt_inicio' AND '$dt_fim'";
$where_mn  = "AND m.data_manutencao BETWEEN '$dt_inicio' AND '$dt_fim'";

$total_avarias = qval2($db, "SELECT COUNT(*) FROM avarias_reparacoes ar WHERE ar.id_escola=$e $where_av");
$av_abertas    = qval2($db, "SELECT COUNT(*) FROM avarias_reparacoes ar WHERE ar.id_escola=$e AND ar.datareparacao IS NULL $where_av");
$av_resolvidas = $total_avarias - $av_abertas;
$taxa_res      = $total_avarias > 0 ? round($av_resolvidas / $total_avarias * 100) : 0;
$total_manut   = qval2($db,
    "SELECT COUNT(*) FROM manutencao m
     JOIN equipamento eq ON m.id_equi=eq.id
     JOIN salas s ON eq.id_sala=s.id
     WHERE s.id_escola=$e $where_mn");
$tempo_med = qval2($db,
    "SELECT AVG(DATEDIFF(ar.datareparacao, ar.dataavaria))
     FROM avarias_reparacoes ar
     WHERE ar.id_escola=$e AND ar.datareparacao IS NOT NULL $where_av
     AND DATEDIFF(ar.datareparacao, ar.dataavaria) >= 0");

echo json_encode([
    'label'         => $label,
    'total_avarias' => (int)$total_avarias,
    'av_abertas'    => (int)$av_abertas,
    'av_resolvidas' => (int)$av_resolvidas,
    'taxa_res'      => (int)$taxa_res,
    'total_manut'   => (int)$total_manut,
    'tempo_med'     => $tempo_med ? round((float)$tempo_med, 1) : null,
    'dt_inicio'     => $dt_inicio,
    'dt_fim'        => $dt_fim,
]);
