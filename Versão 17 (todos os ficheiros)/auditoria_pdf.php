<?php
// ============================================================
// GEI — Exportação PDF do log de auditoria
// Usa FPDF (já incluído no projeto em fpdf/fpdf.php)
// ============================================================
ob_start(); // Captura qualquer output acidental dos includes

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
}
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i'); exit();
}
if ((int)($_SESSION['tipo'] ?? 0) !== 1) {
    header('Location: ' . SVRURL . 'dashboard'); exit();
}

include('sessao_timeout.php');
include('config.php');
require_once('fpdf/fpdf.php');
require_once('gei_audit.php');

// ── Conversão UTF-8 → Latin-1 (FPDF não suporta UTF-8 nativamente) ───────────
function u($str) {
    return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $str ?? '');
}

// ── Filtros (mesmos parâmetros do auditoria.php) ──────────────────────────────
$f_acao     = trim($_GET['acao']     ?? '');
$f_entidade = trim($_GET['entidade'] ?? '');
$f_user     = trim($_GET['user']     ?? '');
$f_ip       = trim($_GET['ip']       ?? '');
$f_de       = trim($_GET['de']       ?? '');
$f_ate      = trim($_GET['ate']      ?? '');

$where  = [];
$params = [];
$types  = '';

if ($f_acao)     { $where[] = 'acao = ?';            $params[] = $f_acao;      $types .= 's'; }
if ($f_entidade) { $where[] = 'entidade = ?';        $params[] = $f_entidade;  $types .= 's'; }
if ($f_ip)       { $where[] = 'ip LIKE ?';           $params[] = "%$f_ip%";    $types .= 's'; }
if ($f_user)     { $where[] = '(user_nome LIKE ? OR user_email LIKE ?)';
                   $params[] = "%$f_user%"; $params[] = "%$f_user%"; $types .= 'ss'; }
if ($f_de)       { $where[] = 'timestamp >= ?';      $params[] = $f_de . ' 00:00:00'; $types .= 's'; }
if ($f_ate)      { $where[] = 'timestamp <= ?';      $params[] = $f_ate . ' 23:59:59'; $types .= 's'; }

$sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Máximo 5000 registos no PDF (protecção de memória)
$stmt = $db->prepare("
    SELECT timestamp, user_nome, user_email, acao, entidade, entidade_id, detalhe, ip
    FROM auditoria
    $sql_where
    ORDER BY timestamp DESC
    LIMIT 5000
");
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$registos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Registar exportação

gei_audit($db, 'exportar', 'auditoria', null,
    'PDF: ' . count($registos) . ' registos exportados'
    . ($f_acao     ? " | acao=$f_acao"         : '')
    . ($f_entidade ? " | entidade=$f_entidade" : '')
    . ($f_de       ? " | de=$f_de"             : '')
    . ($f_ate      ? " | ate=$f_ate"           : '')
);


// ── FPDF ─────────────────────────────────────────────────────────────────────

class AuditoriaPDF extends FPDF {
    function Header() {
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor(27, 40, 72);
        $this->Cell(0, 8, u('GEI - Log de Auditoria'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(120, 136, 160);
        $this->Cell(0, 5, u('Gerado em ' . date('d/m/Y \às H:i:s') . ' por ' . ($_SESSION['login_user'] ?? '')), 0, 1, 'C');
        $this->Ln(2);
        // Cabeçalho da tabela
        $this->SetFillColor(247, 249, 254);
        $this->SetDrawColor(227, 232, 244);
        $this->SetTextColor(123, 136, 160);
        $this->SetFont('Helvetica', 'B', 7);
        $this->SetLineWidth(0.3);
        $this->Cell(32, 7, u('Data / Hora'),  1, 0, 'C', true);
        $this->Cell(38, 7, u('Utilizador'),   1, 0, 'C', true);
        $this->Cell(22, 7, u('Acao'),         1, 0, 'C', true);
        $this->Cell(28, 7, u('Entidade'),     1, 0, 'C', true);
        $this->Cell(10, 7, u('ID'),           1, 0, 'C', true);
        $this->Cell(60, 7, u('Detalhe'),      1, 0, 'C', true);
        $this->Cell(27, 7, u('IP'),           1, 1, 'C', true);
        $this->SetTextColor(30, 42, 69);
    }

    function Footer() {
        $this->SetY(-13);
        $this->SetFont('Helvetica', 'I', 7);
        $this->SetTextColor(160, 160, 160);
        $this->Cell(0, 10, u('Pagina ' . $this->PageNo() . ' / {nb}'), 0, 0, 'C');
    }
}

$pdf = new AuditoriaPDF('L', 'mm', 'A4'); // Landscape
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 18);
$pdf->SetFont('Helvetica', '', 7.5);

$acoes_cor = [
    'login_ok'     => [212, 237, 218],
    'login_falhou' => [248, 215, 218],
    'logout'       => [226, 232, 240],
    'criar'        => [204, 229, 255],
    'editar'       => [255, 243, 205],
    'eliminar'     => [248, 215, 218],
    'exportar'     => [209, 236, 241],
    'config'       => [226, 217, 243],
];

foreach ($registos as $r) {
    if ($pdf->GetY() > 185) { $pdf->AddPage(); }

    $ts       = u(date('d/m/Y H:i:s', strtotime($r['timestamp'])));
    $user     = u(mb_strimwidth($r['user_nome']  ?: '-', 0, 24, '...'));
    $acao     = u($r['acao']);
    $entidade = u(mb_strimwidth($r['entidade'] ?: '-', 0, 20, '...'));
    $eid      = u((string)($r['entidade_id'] ?? '-'));
    $detalhe  = u(mb_strimwidth($r['detalhe']  ?? '', 0, 55, '...'));
    $ip       = u($r['ip']);

    // Cor de fundo da linha consoante ação
    $cor = $acoes_cor[$r['acao']] ?? [255, 255, 255];
    $pdf->SetFillColor(...$cor);
    $pdf->SetDrawColor(227, 232, 244);

    $pdf->Cell(32, 6, $ts,       1, 0, 'L', true);
    $pdf->Cell(38, 6, $user,     1, 0, 'L', true);
    $pdf->Cell(22, 6, $acao,     1, 0, 'C', true);
    $pdf->Cell(28, 6, $entidade, 1, 0, 'L', true);
    $pdf->Cell(10, 6, $eid,      1, 0, 'C', true);
    $pdf->Cell(60, 6, $detalhe,  1, 0, 'L', true);
    $pdf->Cell(27, 6, $ip,       1, 1, 'L', true);
}

// Rodapé com totais
$pdf->Ln(3);
$pdf->SetFont('Helvetica', 'I', 8);
$pdf->SetTextColor(120, 136, 160);
$pdf->Cell(0, 6, u(count($registos) . ' registos' . (count($registos) >= 5000 ? ' (limitado a 5000)' : '')), 0, 1, 'R');

// Descartar todo o output acumulado antes de enviar o PDF
ob_end_clean();

$filename = 'auditoria_gei_' . date('Ymd_His') . '.pdf';
$pdf->Output('D', $filename);
exit;
