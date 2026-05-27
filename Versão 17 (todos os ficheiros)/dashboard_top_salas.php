<?php
// ============================================================
// dashboard_top_salas.php
// Endpoint AJAX — devolve as top salas com mais avarias,
// com filtro opcional de ano.
// Responde com JSON: { "items": [ { "sala": "...", "total": N }, ... ] }
// ============================================================

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
}

header('Content-Type: application/json; charset=utf-8');

// Verificar sessão
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    echo json_encode(['items' => []]);
    exit;
}

// Parâmetros
$escola_id = (isset($_GET['esc']) && is_numeric($_GET['esc'])) ? (int)$_GET['esc'] : 1;
$ano_sel   = (isset($_GET['ano']) && is_numeric($_GET['ano'])) ? (int)$_GET['ano'] : 0;

$where_ano = $ano_sel > 0 ? " AND YEAR(ar.dataavaria) = $ano_sel" : "";

// Incluir ligação à BD (igual ao resto do projecto)
require_once("config.php"); // ajuste o caminho se necessário

$sql = "SELECT s.nome AS sala, COUNT(*) AS total
        FROM avarias_reparacoes ar
        JOIN salas s ON ar.id_sala = s.id
        WHERE ar.id_escola = $escola_id
          $where_ano
        GROUP BY s.nome
        ORDER BY total DESC
        LIMIT 6";

$result = @mysqli_query($db, $sql);
$items  = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'sala'  => $row['sala'],
            'total' => (int)$row['total'],
        ];
    }
}

echo json_encode(['items' => $items]);
