<?php
// ============================================================
// planta_equip_ajax.php — GEI
// Endpoint AJAX: devolve equipamentos de uma sala em JSON.
// Chamado por planta_salas.php ao clicar numa sala.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

// Só utilizadores autenticados
if (!isset($_SESSION['login_user'])) {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

include_once('config.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json; charset=utf-8');

$sala_id = isset($_GET['sala']) ? (int)$_GET['sala'] : 0;
$esc_id  = isset($_GET['esc'])  ? (int)$_GET['esc']  : 0;

if ($sala_id <= 0 || $esc_id <= 0) {
    echo json_encode([]);
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT
            eq.id,
            eq.id_sala,
            eq.nomeequi,
            eq.tipo,
            COUNT(ar.id) AS avarias
        FROM equipamento eq
        INNER JOIN salas s ON s.id = eq.id_sala
        LEFT JOIN avarias_reparacoes ar
            ON ar.id_equi = eq.id AND ar.datareparacao IS NULL
        WHERE eq.id_sala = ? AND s.id_escola = ?
        GROUP BY eq.id, eq.id_sala, eq.nomeequi, eq.tipo
        ORDER BY eq.nomeequi
    ");
    $stmt->bind_param('ii', $sala_id, $esc_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($rows);

} catch (mysqli_sql_exception $e) {
    error_log('planta_equip_ajax error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
