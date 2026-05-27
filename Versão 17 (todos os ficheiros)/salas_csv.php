<?php
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

include('svrurl.php');
include('config.php');

while (ob_get_level()) { ob_end_clean(); }

header('Content-Type: text/csv; charset=utf-8');

$id = (int)base64_decode($_GET['id']);

if ($id <= 0) {
    header('Content-Disposition: attachment; filename=salas.csv');
    $output = fopen('php://output', 'w');
    fclose($output);
    mysqli_close($db);
    exit;
}

// Filtros opcionais
$filtro_loc = isset($_GET['filtro_loc']) ? trim($_GET['filtro_loc']) : '';
$filtro_req = isset($_GET['filtro_req']) ? trim($_GET['filtro_req']) : '';

// Construir nome de ficheiro com filtros
$fname_parts = ['salas'];
if ($filtro_loc !== '') $fname_parts[] = 'loc_' . preg_replace('/[^a-zA-Z0-9]/', '_', $filtro_loc);
if ($filtro_req !== '') $fname_parts[] = 'req_' . preg_replace('/[^a-zA-Z0-9]/', '_', $filtro_req);
$fname_parts[] = date('Ymd_Hi');
$csv_filename  = implode('_', $fname_parts) . '.csv';

header('Content-Disposition: attachment; filename=' . $csv_filename);

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeçalho com indicação dos filtros ativos
$filtros_ativos = [];
if ($filtro_loc !== '') $filtros_ativos[] = 'Localização: ' . $filtro_loc;
if ($filtro_req !== '') $filtros_ativos[] = 'Equip. Requisitável: ' . $filtro_req;
if (!empty($filtros_ativos)) {
    fputcsv($output, ['# Filtros ativos: ' . implode(' | ', $filtros_ativos)]);
}

fputcsv($output, ['Nome', 'Localização', 'Departamento', 'Equipamento_requisitável']);

// Construir WHERE com filtros
$where = 'id_escola = ?';
$types = 'i';
$params = [$id];

if ($filtro_loc !== '') {
    $where .= ' AND localizacao = ?';
    $types .= 's';
    $params[] = $filtro_loc;
}
if ($filtro_req !== '') {
    $where .= ' AND equip_requisitavel = ?';
    $types .= 's';
    $params[] = $filtro_req;
}

$stmt = $db->prepare("SELECT nome, localizacao, departamento, equip_requisitavel FROM salas WHERE $where ORDER BY nome");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_assoc($result)) { fputcsv($output, $row); }
$stmt->close();

fclose($output);
mysqli_close($db);
exit;
?>
