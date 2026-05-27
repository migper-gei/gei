<?php
// dashboard_heatmap.php
// Endpoint AJAX — devolve JSON com avarias por sala por mês para o heatmap
// GET params: esc=N, ano=N

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
    echo json_encode(['salas' => [], 'meses' => [], 'dados' => []]);
    exit;
}

require_once('config.php');

$escola_id = (isset($_GET['esc']) && is_numeric($_GET['esc'])) ? (int)$_GET['esc'] : 1;
$ano_sel   = (isset($_GET['ano']) && is_numeric($_GET['ano']))  ? (int)$_GET['ano']  : (int)date('Y');

// Avarias por sala por mês para o ano seleccionado
$sql = "SELECT s.nome AS sala, MONTH(ar.dataavaria) AS mes, COUNT(*) AS total
        FROM avarias_reparacoes ar
        JOIN salas s ON ar.id_sala = s.id
        WHERE ar.id_escola = $escola_id AND YEAR(ar.dataavaria) = $ano_sel
        GROUP BY s.id, s.nome, MONTH(ar.dataavaria)
        ORDER BY s.nome ASC, mes ASC";

$result = @mysqli_query($db, $sql);

$raw = [];
$salas_set = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sala = $row['sala'];
        $mes  = (int)$row['mes'];
        $tot  = (int)$row['total'];
        if (!isset($raw[$sala])) $raw[$sala] = [];
        $raw[$sala][$mes] = $tot;
        $salas_set[$sala] = true;
    }
}

$salas = array_keys($salas_set);
$meses = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

// Construir matriz salas x meses
$dados = [];
foreach ($salas as $sala) {
    $row_data = [];
    for ($m = 1; $m <= 12; $m++) {
        $row_data[] = $raw[$sala][$m] ?? 0;
    }
    $dados[] = ['sala' => $sala, 'valores' => $row_data];
}

echo json_encode([
    'salas' => $salas,
    'meses' => $meses,
    'dados' => $dados,
    'ano'   => $ano_sel
]);
