<?php
// Iniciar sessão
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
require('fpdf/fpdf.php');

while (ob_get_level()) { ob_end_clean(); }

// ── Validação ─────────────────────────────────────────────
$sql2a    = "SELECT MAX(id) AS me FROM escolas";
$result2a = mysqli_query($db, $sql2a);
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = $rows2a[0];

$x        = (int)base64_decode($_GET["x"]   ?? '');
$idescola = (int)base64_decode($_GET["ies"] ?? '');

if (empty($_GET["x"]) || empty($_GET["ies"])
    || $idescola > $maxesc || $idescola < 1
    || $x > 1 || $x < 0
) {
    header('Location: ' . SVRURL . 'lista');
    exit;
}

// ── Nome da escola ────────────────────────────────────────
$sql11    = "SELECT nome_escola FROM escolas WHERE id=$idescola";
$result11 = mysqli_query($db, $sql11);
$rows11   = mysqli_fetch_row($result11);
$ne       = $rows11[0] ?? '';
if (!$ne) { header('Location: ' . SVRURL . 'lista'); exit; }

// ── Equipamento por tipo ──────────────────────────────────
$sql = "SELECT tipo, COUNT(*) AS qta
        FROM equipamento e, salas s
        WHERE e.id_sala=s.id AND s.id_escola=$idescola
        GROUP BY tipo ORDER BY tipo ASC";
$result = mysqli_query($db, $sql);
$tipos_rows = [];
$total_equip = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $tipos_rows[] = $row;
    $total_equip += $row['qta'];
}

// ── Avariados ─────────────────────────────────────────────
$sql_av = "SELECT e.tipo AS ti, COUNT(DISTINCT a.id_equi) AS c2
           FROM avarias_reparacoes a, equipamento e
           WHERE a.datareparacao IS NULL AND a.id_equi=e.id AND a.id_escola=$idescola
           GROUP BY e.tipo ORDER BY ti ASC";
$result_av  = mysqli_query($db, $sql_av);
$avariados  = [];
while ($row_av = mysqli_fetch_assoc($result_av)) {
    // nomes avariados deste tipo
    $sql_n = "SELECT DISTINCT e.nomeequi AS n
              FROM equipamento e, avarias_reparacoes a
              WHERE a.id_equi=e.id AND a.id_escola=$idescola
              AND a.datareparacao IS NULL AND e.tipo='" . mysqli_real_escape_string($db, $row_av['ti']) . "'
              ORDER BY e.nomeequi ASC";
    $result_n = mysqli_query($db, $sql_n);
    $nomes = [];
    while ($rn = mysqli_fetch_assoc($result_n)) { $nomes[] = $rn['n']; }
    $avariados[] = ['tipo' => $row_av['ti'], 'c2' => $row_av['c2'], 'nomes' => $nomes];
}

// ── Outro equipamento ─────────────────────────────────────
$sql5    = "SELECT oe.nomeoutro, SUM(oe.qta) AS so
            FROM outro_equipamento oe, salas s
            WHERE oe.id_sala=s.id AND s.id_escola=$idescola
            GROUP BY oe.nomeoutro ORDER BY oe.nomeoutro";
$result5 = mysqli_query($db, $sql5);
$outro   = [];
while ($row5 = mysqli_fetch_assoc($result5)) { $outro[] = $row5; }

// ── Logotipo ──────────────────────────────────────────────
$logo_pic    = null;
$result_logo = mysqli_query($db, "SELECT logotipo FROM logotipo LIMIT 1");
if ($result_logo) {
    $row_logo = mysqli_fetch_assoc($result_logo);
    if (!empty($row_logo['logotipo'])) {
        $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row_logo['logotipo']);
    }
}

mysqli_close($db);

// ── Classe PDF ────────────────────────────────────────────
class GEI_PDF extends FPDF {
    public $logo_pic  = null;
    public $doc_title = '';
    public $subtitulo = '';

    function Header() {
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 210, 22, 'F');
        if ($this->logo_pic) {
            $info = @getimagesize($this->logo_pic);
            if ($info) {
                $ratio = $info[0] / $info[1];
                $h = 16; $w = $h * $ratio;
                if ($w > 30) { $w = 30; $h = $w / $ratio; }
                $this->Image($this->logo_pic, 6, (22 - $h) / 2, $w, $h, 'png');
            }
        }
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 4);
        $this->Cell(210, 8, utf8_decode($this->doc_title), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(180, 200, 230);
        $this->SetXY(0, 12);
        $this->Cell(105, 5, utf8_decode($this->subtitulo), 0, 0, 'C');
        $this->Cell(105, 5, utf8_decode('Gerado em: ' . date('d/m/Y H:i')), 0, 1, 'C');
        $this->SetTextColor(30, 42, 69);
        $this->SetY(28);
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetDrawColor(75, 108, 183);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(1);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(123, 136, 160);
        $this->Cell(0, 5, utf8_decode('Página ' . $this->PageNo() . ' / {nb}'), 0, 0, 'C');
    }

    function SectionLabel($label) {
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(123, 136, 160);
        $this->Cell(0, 6, utf8_decode(strtoupper($label)), 0, 1, 'L');
    }

    function TableHeader($cols) {
        $this->SetFillColor(75, 108, 183);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        $this->SetDrawColor(200, 210, 230);
        $this->SetLineWidth(0.3);
        foreach ($cols as $col) {
            $this->Cell($col[0], 8, utf8_decode($col[1]), 1, 0, 'C', true);
        }
        $this->Ln();
    }
}

// ── Instanciar PDF ────────────────────────────────────────
$pdf = new GEI_PDF();
$pdf->AliasNbPages();
$pdf->logo_pic  = $logo_pic;
$pdf->doc_title = 'QUANTIDADE TOTAL DE EQUIPAMENTO';
$pdf->subtitulo = $ne;
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P', 'A4');
$pdf->SetAutoPageBreak(true, 18);

// ── Secção: Equipamento por tipo ──────────────────────────
$pdf->SectionLabel('Equipamento por tipo');
$pdf->TableHeader([[150, 'TIPO'], [40, 'QUANTIDADE']]);

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(30, 42, 69);
$fill = false;
foreach ($tipos_rows as $row) {
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
    $pdf->Cell(150, 7, utf8_decode($row['tipo']), 1, 0, 'L', true);
    $pdf->Cell(40,  7, $row['qta'],               1, 1, 'C', true);
    $fill = !$fill;
}
// Total
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 7, utf8_decode('Total: ' . $total_equip), 1, 1, 'R', true);

// ── Secção: Avariados ─────────────────────────────────────
if (count($avariados) > 0) {
    $pdf->SectionLabel('⚠ Avariados (sem reparação)');
    $pdf->TableHeader([[70, 'TIPO'], [30, 'AVARIADOS'], [90, 'EQUIPAMENTO']]);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(30, 42, 69);
    $fill = false;
    foreach ($avariados as $av) {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
        $nomes_str = implode('  ', $av['nomes']);
        $pdf->Cell(70, 7, utf8_decode($av['tipo']),  1, 0, 'L', true);
        $pdf->Cell(30, 7, $av['c2'],                 1, 0, 'C', true);
        $pdf->Cell(90, 7, utf8_decode($nomes_str),   1, 1, 'L', true);
        $fill = !$fill;
    }
}

// ── Secção: Outro equipamento ─────────────────────────────
if (count($outro) > 0) {
    $pdf->SectionLabel('Outro equipamento');
    $pdf->TableHeader([[150, 'NOME'], [40, 'QUANTIDADE']]);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(30, 42, 69);
    $fill = false;
    foreach ($outro as $oe) {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
        $pdf->Cell(150, 7, utf8_decode($oe['nomeoutro']), 1, 0, 'L', true);
        $pdf->Cell(40,  7, $oe['so'],                     1, 1, 'C', true);
        $fill = !$fill;
    }
}

// ── Output ────────────────────────────────────────────────
$pdf->Output('I', 'Equipamentos_total.pdf');
exit;
?>
