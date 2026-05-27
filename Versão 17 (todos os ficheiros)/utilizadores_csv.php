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
header('Content-Disposition: attachment; filename=utilizadores.csv');

// Ler filtros da sessão (os mesmos que utilizadores.php guarda)
$filtro_tipo  = isset($_SESSION['filtro-tipo'])  ? (int)$_SESSION['filtro-tipo']  : 0;
$filtro_ativo = isset($_SESSION['filtro-ativo']) ? $_SESSION['filtro-ativo']       : 'todos';

// Construir WHERE igual ao utilizadores.php
$conditions = [];
if ($filtro_tipo > 0)            { $conditions[] = "tipo = $filtro_tipo"; }
if ($filtro_ativo === 'ativo')   { $conditions[] = "COALESCE(ativo,1) = 1"; }
if ($filtro_ativo === 'inativo') { $conditions[] = "COALESCE(ativo,1) = 0"; }
$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// Mapa de tipo numérico para label
$tipo_labels = [
    1 => 'Administrador',
    2 => 'Utilizador',
    3 => 'Reparador',
    4 => 'Funcionário',
];

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

fputcsv($output, ['Tipo', 'Nome', 'Email']);

$result = mysqli_query($db, "SELECT tipo, nome, email FROM utilizadores $where ORDER BY nome");
while ($row = mysqli_fetch_assoc($result)) {
    $tipo_label = $tipo_labels[(int)$row['tipo']] ?? $row['tipo'];
    fputcsv($output, [$tipo_label, $row['nome'], $row['email']]);
}

fclose($output);
mysqli_close($db);
exit;
?>
