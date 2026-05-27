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

while (ob_get_level()) {{ ob_end_clean(); }}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tipos_equipamento.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['Nome']);

$result = mysqli_query($db, "SELECT nome FROM tipos_equipamento ORDER BY nome");
while ($row = mysqli_fetch_assoc($result)) {{ fputcsv($output, $row); }}

fclose($output);
mysqli_close($db);
exit;
?>