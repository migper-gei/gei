<?php
// ── Sessão segura (tem de ser ANTES de qualquer include) ─────────────────────
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

// ── Verificar sessão ANTES de qualquer output ────────────────────────────────
if (empty($_SESSION['nobd']) || empty($_SESSION['serverbd'])) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

include('config.php');

// ── Validar parâmetro id ─────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: ' . SVRURL . 'equipamento.php');
    exit();
}

// ── Limpar buffer e enviar headers CSV ──────────────────────────────────────
while (ob_get_level()) { ob_end_clean(); }

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=equipamentos.csv');

$output = fopen('php://output', 'w');

// BOM UTF-8 para compatibilidade com Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, [
    'Nome', 'Nº série', 'Sala', 'Marca/Modelo', 'Tipo',
    'Processador', 'Memória', 'Disco', 'Placa gráfica', 'Placa som',
    'Monitor', 'Teclado', 'Teclado interface',
    'Rato', 'Rato interface', 'Colunas', 'CD/DVD',
    'Domínio', 'IP', 'Máscara rede', 'Gateway',
    'DNS principal', 'DNS alternativo',
    'Sala temporária', 'Data compra',
    'Escola digital', 'Nº inv. Dgest', 'Fornecedor', 'NIF pessoa', 'Nº RMA',
    'Nome escola'
]);

// ── Query com prepared statement ─────────────────────────────────────────────
$stmt = $db->prepare("
    SELECT
        eq.nomeequi, eq.numserie, s.nome, eq.marca_modelo, eq.tipo,
        eq.processador, eq.memoria, eq.disco, eq.placagrafica, eq.placasom,
        eq.monitor, eq.teclado, eq.tecladointerface, eq.rato, eq.ratointerface,
        eq.colunas, eq.cd_dvd,
        eq.dominio, eq.ip, eq.mascara_rede, eq.gateway,
        eq.dns_principal, eq.dns_alternativo,
        eq.sala_temp, eq.data_compra,
        eq.escola_digital, eq.num_inv_dgest, eq.fornecedor, eq.nif_pessoa, eq.num_rma,
        e.nome_escola
    FROM escolas e
    INNER JOIN salas s       ON s.id_escola = e.id
    INNER JOIN equipamento eq ON eq.id_sala  = s.id
    WHERE e.id = ?
    ORDER BY e.nome_escola, s.nome, eq.nomeequi
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

$stmt->close();
fclose($output);
mysqli_close($db);
exit;
?>
