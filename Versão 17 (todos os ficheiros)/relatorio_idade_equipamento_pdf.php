<?php
// ============================================================
// relatorio_idade_equipamento_pdf.php — GEI
// Exportação PDF do relatório de equipamentos por idade
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

include('svrurl.php');

if (empty($_SESSION['nobd']) || empty($_SESSION['serverbd'])) {
    header('Location: ' . SVRURL . 'i');
    exit();
}
if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

include('config.php');
require('fpdf/fpdf.php');

while (ob_get_level()) { ob_end_clean(); }

// ── Parâmetros ────────────────────────────────────────────
$anos_min = isset($_GET['anos']) ? max(0, (int)$_GET['anos']) : 5;
$escola   = isset($_GET['esc'])  ? (int)$_GET['esc'] : 0;
$tipo_sel = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$sala_sel = isset($_GET['sala']) ? (int)$_GET['sala'] : 0;
$ordem    = isset($_GET['ordem']) && in_array($_GET['ordem'], ['nome','sala','tipo','idade','data_compra'])
            ? $_GET['ordem'] : 'idade';

// ── Nome da escola ────────────────────────────────────────
$nome_escola = '';
$res_esc = mysqli_query($db, "SELECT nome_escola FROM escolas WHERE id = " . (int)$escola . " LIMIT 1");
if ($res_esc && $row_esc = mysqli_fetch_assoc($res_esc)) {
    $nome_escola = $row_esc['nome_escola'];
}

// ── Nome da sala ──────────────────────────────────────────
$nome_sala = '';
if ($sala_sel > 0) {
    $res_sl = mysqli_query($db, "SELECT nome FROM salas WHERE id = " . (int)$sala_sel . " LIMIT 1");
    if ($res_sl && $row_sl = mysqli_fetch_assoc($res_sl)) {
        $nome_sala = $row_sl['nome'];
    }
}

// ── Desativar strict mode ─────────────────────────────────
mysqli_query($db, "SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

// ── Query principal ───────────────────────────────────────
$where_tipo = '';
$where_sala = '';
$params     = [];
$types      = 'ii';

$order_map = [
    'nome'        => 'eq.nomeequi ASC',
    'sala'        => 's.nome ASC',
    'tipo'        => 'eq.tipo ASC',
    'idade'       => 'idade DESC',
    'data_compra' => 'eq.data_compra ASC',
];
$order_sql = $order_map[$ordem];

if ($tipo_sel !== '') {
    $where_tipo = ' AND eq.tipo = ?';
    $params[]   = $tipo_sel;
    $types     .= 's';
}
if ($sala_sel > 0) {
    $where_sala = ' AND s.id = ?';
    $params[]   = $sala_sel;
    $types     .= 'i';
}

$stmt = $db->prepare("
    SELECT
        eq.nomeequi,
        eq.tipo,
        s.nome            AS sala,
        eq.marca_modelo,
        eq.num_inv_dgest,
        eq.data_compra,
        TIMESTAMPDIFF(YEAR,  NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) AS idade,
        TIMESTAMPDIFF(MONTH, NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) AS meses
    FROM equipamento eq
    INNER JOIN salas s ON s.id = eq.id_sala
    WHERE s.id_escola = ?
      AND eq.data_compra IS NOT NULL
      AND eq.data_compra != '0000-00-00'
      AND TIMESTAMPDIFF(YEAR, NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) >= ?
      $where_tipo
      $where_sala
    ORDER BY $order_sql
");

array_unshift($params, $escola, $anos_min);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$rows   = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();
$total = count($rows);

// ── Logotipo ──────────────────────────────────────────────
$logo_pic = null;
$res_logo = mysqli_query($db, "SELECT logotipo FROM logotipo LIMIT 1");
if ($res_logo) {
    $row_logo = mysqli_fetch_assoc($res_logo);
    if (!empty($row_logo['logotipo'])) {
        $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row_logo['logotipo']);
    }
}

mysqli_close($db);

// ── Classe PDF ────────────────────────────────────────────
class GEI_PDF extends FPDF {

    public $logo_pic   = null;
    public $doc_title  = '';
    public $subtitulo  = '';
    public $total_rows = 0;

    function Header() {
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 297, 22, 'F');

        if ($this->logo_pic) {
            $info = @getimagesize($this->logo_pic);
            if ($info) {
                $ratio = $info[0] / $info[1];
                $h = 16; $w = $h * $ratio;
                if ($w > 30) { $w = 30; $h = $w / $ratio; }
                $y = (22 - $h) / 2;
                $this->Image($this->logo_pic, 6, $y, $w, $h, 'png');
            }
        }

        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 4);
        $this->Cell(297, 8, utf8_decode($this->doc_title), 0, 1, 'C');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(180, 200, 230);
        $this->SetXY(0, 13);
        $this->Cell(150, 5, utf8_decode($this->subtitulo), 0, 0, 'C');
        $this->Cell(147, 5, utf8_decode('Gerado em: ' . date('d/m/Y H:i') . '   |   Total: ' . $this->total_rows . ' equipamentos'), 0, 1, 'C');

        $this->SetTextColor(30, 42, 69);
        $this->SetY(28);
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetDrawColor(75, 108, 183);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(1);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(123, 136, 160);
        $this->Cell(0, 5, utf8_decode('Pagina ' . $this->PageNo() . ' / {nb}'), 0, 0, 'C');
    }
}

// ── Instanciar PDF ────────────────────────────────────────
$pdf = new GEI_PDF();
$pdf->AliasNbPages();
$pdf->logo_pic   = $logo_pic;
$pdf->doc_title  = 'RELATORIO DE EQUIPAMENTOS POR IDADE';
$pdf->total_rows = $total;

$subtitulo_parts = [$nome_escola];
if ($nome_sala) $subtitulo_parts[] = 'Sala: ' . $nome_sala;
if ($tipo_sel)  $subtitulo_parts[] = 'Tipo: ' . $tipo_sel;
$subtitulo_parts[] = 'Idade minima: ' . $anos_min . ' anos';
$pdf->subtitulo = implode('   |   ', $subtitulo_parts);

$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('L', 'A4');
$pdf->SetAutoPageBreak(true, 18);

// ── Cabeçalho da tabela ───────────────────────────────────
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(200, 210, 230);
$pdf->SetLineWidth(0.3);

// Total larguras = 277mm (A4 landscape 297 - 20 margens)
$pdf->Cell(70, 9, 'NOME',         1, 0, 'C', true);
$pdf->Cell(35, 9, 'TIPO',         1, 0, 'C', true);
$pdf->Cell(45, 9, 'SALA',         1, 0, 'C', true);
$pdf->Cell(45, 9, 'MARCA/MODELO', 1, 0, 'C', true);
$pdf->Cell(32, 9, 'N INV.',       1, 0, 'C', true);
$pdf->Cell(26, 9, 'DATA COMPRA',  1, 0, 'C', true);
$pdf->Cell(24, 9, 'IDADE',        1, 1, 'C', true);

// ── Linhas de dados ───────────────────────────────────────
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(30, 42, 69);
$fill = false;

foreach ($rows as $row) {
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);

    $idade     = (int)$row['idade'];
    $meses     = (int)$row['meses'] % 12;
    $idade_str = $idade . ' ano' . ($idade !== 1 ? 's' : '');
    if ($meses > 0) $idade_str .= ' e ' . $meses . ($meses === 1 ? ' mes' : ' meses');
    $data_fmt  = !empty($row['data_compra']) ? date('d/m/Y', strtotime($row['data_compra'])) : '-';

    $pdf->Cell(70, 8, utf8_decode($row['nomeequi']),                1, 0, 'L', true);
    $pdf->Cell(35, 8, utf8_decode($row['tipo']         ?: '-'),     1, 0, 'L', true);
    $pdf->Cell(45, 8, utf8_decode($row['sala']),                    1, 0, 'L', true);
    $pdf->Cell(45, 8, utf8_decode($row['marca_modelo'] ?: '-'),     1, 0, 'L', true);
    $pdf->Cell(32, 8, utf8_decode($row['num_inv_dgest'] ?: '-'),    1, 0, 'C', true);
    $pdf->Cell(26, 8, $data_fmt,                                    1, 0, 'C', true);
    $pdf->Cell(24, 8, utf8_decode($idade_str),                      1, 1, 'C', true);

    $fill = !$fill;
}

// ── Rodapé da tabela ──────────────────────────────────────
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(277, 8, utf8_decode('Total: ' . $total . ' equipamentos'), 1, 1, 'R', true);

// ── Output ────────────────────────────────────────────────
$pdf->Output('I', 'Equipamentos_Idade.pdf');
exit;
?>
