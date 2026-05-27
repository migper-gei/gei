<?php
// dashboard_sala_tipos.php
// Endpoint AJAX — devolve JSON com equipamentos por tipo filtrados por sala.
// Chamado pelo gráfico "Equipamentos por Tipo" no dashboard.

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $isHttps,
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

// Segurança: apenas utilizadores autenticados
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    http_response_code(403);
    echo json_encode(['labels' => [], 'vals' => []]);
    exit;
}

// Inclui configuração da BD (define $db e SVRURL)
include('config.php'); // ajustar caminho se necessário

header('Content-Type: application/json; charset=utf-8');

$escola_id = (isset($_GET['esc'])  && is_numeric($_GET['esc']))  ? (int)$_GET['esc']  : 1;
$sala_id   = (isset($_GET['sala']) && is_numeric($_GET['sala'])) ? (int)$_GET['sala'] : 0;
$ano_sel   = (isset($_GET['ano'])  && is_numeric($_GET['ano']))  ? (int)$_GET['ano']  : 0;

// Validar que a sala pertence à escola (evitar dados cruzados)
if ($sala_id > 0) {
    $stmt = $db->prepare("SELECT id FROM salas WHERE id = ? AND id_escola = ? LIMIT 1");
    $stmt->bind_param("ii", $sala_id, $escola_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        echo json_encode(['labels' => [], 'vals' => []]);
        exit;
    }
    $stmt->close();
}

// Construir query com prepared statement
// O filtro por sala OU por escola e o filtro opcional por ano são resolvidos
// com parâmetros — evita qualquer concatenação de variáveis no SQL.
$params      = [];
$param_types = '';

if ($sala_id > 0) {
    $sql = "SELECT eq.tipo, COUNT(*) AS total
            FROM equipamento eq
            JOIN salas s ON eq.id_sala = s.id
            WHERE eq.id_sala = ?";
    $params[]     = $sala_id;
    $param_types .= 'i';
} else {
    $sql = "SELECT eq.tipo, COUNT(*) AS total
            FROM equipamento eq
            JOIN salas s ON eq.id_sala = s.id
            WHERE s.id_escola = ?";
    $params[]     = $escola_id;
    $param_types .= 'i';
}

if ($ano_sel > 0) {
    $sql        .= " AND YEAR(eq.data_compra) = ?";
    $params[]    = $ano_sel;
    $param_types .= 'i';
}

$sql .= " GROUP BY eq.tipo ORDER BY total DESC LIMIT 8";

$stmt = $db->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$vals   = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['tipo'];
    $vals[]   = (int)$row['total'];
}

$stmt->close();

echo json_encode(['labels' => $labels, 'vals' => $vals]);
