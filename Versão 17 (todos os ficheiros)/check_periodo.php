<?php
// Sessão segura
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

// Apenas aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Requere sessão válida (aceita qualquer variável de sessão ativa)
if (empty($_SESSION) || count($_SESSION) === 0) {
    http_response_code(403);
    echo json_encode(['existe' => false, 'erro' => 'não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

include("config.php"); // ligação $db

$acao = trim($_POST['acao'] ?? 'periodo');

// ── Verificação de período duplicado ──────────────────────────────────────────
if ($acao === 'periodo') {
    $ano     = trim($_POST['ano']     ?? '');
    $periodo = trim($_POST['periodo'] ?? '');

    if ($ano === '' || $periodo === '' || !ctype_digit($periodo)) {
        echo json_encode(['existe' => false]);
        exit;
    }

    $periodoInt = (int)$periodo;

    $stmt = $db->prepare("SELECT COUNT(*) FROM periodos WHERE ano_lectivo = ? AND num_periodo = ?");
    $stmt->bind_param("si", $ano, $periodoInt);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    echo json_encode(['existe' => ($count > 0)]);
    exit;
}

// ── Verificação de sobreposição de datas ──────────────────────────────────────
if ($acao === 'datas') {
    $ano   = trim($_POST['ano']   ?? '');
    $datai = trim($_POST['datai'] ?? '');
    $dataf = trim($_POST['dataf'] ?? '');

    // Validar formato de datas
    $diObj = DateTime::createFromFormat('Y-m-d', $datai);
    $dfObj = DateTime::createFromFormat('Y-m-d', $dataf);

    if (!$diObj || !$dfObj) {
        echo json_encode(['conflito_datas' => false]);
        exit;
    }

    $stmt = $db->prepare("
        SELECT num_periodo, ano_lectivo FROM periodos
        WHERE STR_TO_DATE(?, '%Y-%m-%d') <= data_fim
          AND STR_TO_DATE(?, '%Y-%m-%d') >= data_inicio
        LIMIT 1
    ");
    $stmt->bind_param("ss", $datai, $dataf);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        echo json_encode([
            'conflito_datas'  => true,
            'periodo_conflito' => $row['num_periodo'] . 'º período',
            'ano_conflito'    => htmlspecialchars($row['ano_lectivo'], ENT_QUOTES, 'UTF-8'),
        ]);
    } else {
        echo json_encode(['conflito_datas' => false]);
    }
    exit;
}

echo json_encode(['existe' => false]);
