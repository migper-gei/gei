<?php
// Iniciar sessão com o mesmo nome que o resto da aplicação
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

// Limpar output buffer
while (ob_get_level()) { ob_end_clean(); }

// ── Validação de parâmetros ───────────────────────────────
$sql2a   = "SELECT MAX(id) AS me FROM escolas";
$result2a = mysqli_query($db, $sql2a);
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = $rows2a[0];

$x        = (int)base64_decode($_GET["x"]  ?? '');
$idescola = (int)base64_decode($_GET["ies"] ?? '');

if (empty($_GET["x"]) || empty($_GET["ies"])
    || $idescola > $maxesc || $idescola < 1
    || $x > 1 || $x < 0
) {
    header('Location: ' . SVRURL . 'lista');
    exit;
}

// ── Nome da escola ────────────────────────────────────────
$sql11   = "SELECT nome_escola FROM escolas WHERE id=$idescola";
$result11 = mysqli_query($db, $sql11);
$rows11   = mysqli_fetch_row($result11);
$ne       = $rows11[0] ?? '';

if (!$ne) {
    header('Location: ' . SVRURL . 'lista');
    exit;
}

// ── Recolher dados: salas com equipamento ─────────────────
$sql0 = "SELECT DISTINCT s.id, s.nome
         FROM salas s
         WHERE s.id_escola=$idescola
         AND (
             EXISTS (SELECT 1 FROM equipamento e WHERE e.id_sala=s.id)
             OR EXISTS (SELECT 1 FROM outro_equipamento oe WHERE oe.id_sala=s.id)
         )
         ORDER BY s.nome ASC";
$result0 = mysqli_query($db, $sql0);

$salas = [];
while ($row0 = mysqli_fetch_assoc($result0)) {
    $idsa = $row0['id'];
    $nos  = $row0['nome'];

    $linhas = [];

    // Equipamento por tipo
    $sql01   = "SELECT tipo, COUNT(*) AS qta
                FROM equipamento e, salas s
                WHERE e.id_sala=s.id AND s.id_escola=$idescola AND s.id=$idsa
                GROUP BY tipo ORDER BY tipo ASC";
    $result01 = mysqli_query($db, $sql01);
    while ($row = mysqli_fetch_assoc($result01)) {
        $linhas[] = ['tipo' => $row['tipo'], 'qta' => $row['qta']];
    }

    // Outro equipamento
    $sql5   = "SELECT oe.nomeoutro, SUM(oe.qta) AS so
               FROM outro_equipamento oe, salas s
               WHERE oe.id_sala=s.id AND s.id_escola=$idescola AND s.id=$idsa
               GROUP BY oe.nomeoutro ORDER BY oe.nomeoutro";
    $result5 = mysqli_query($db, $sql5);
    while ($row5 = mysqli_fetch_assoc($result5)) {
        $linhas[] = ['tipo' => $row5['nomeoutro'], 'qta' => $row5['so']];
    }

    if (count($linhas) > 0) {
        $salas[] = ['nome' => $nos, 'linhas' => $linhas];
    }
}

// ── Logotipo ──────────────────────────────────────────────
$logo_pic = null;
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
        // Faixa azul escura no topo
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 210, 22, 'F');

        // Logotipo
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

        // Título
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 4);
        $this->Cell(210, 8, utf8_decode($this->doc_title), 0, 1, 'C');

        // Subtítulo (nome da escola)
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
}

// ── Instanciar PDF ────────────────────────────────────────
$pdf = new GEI_PDF();
$pdf->AliasNbPages();
$pdf->logo_pic  = $logo_pic;
$pdf->doc_title = 'QUANTIDADE DE EQUIPAMENTO POR SALA';
$pdf->subtitulo = $ne;
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P', 'A4');
$pdf->SetAutoPageBreak(true, 18);

// ── Renderizar cada sala ──────────────────────────────────
foreach ($salas as $sala) {

    // Cabeçalho da sala
    $pdf->SetFillColor(24, 40, 72);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(190, 8, utf8_decode('SALA: ' . $sala['nome']), 1, 1, 'L', true);

    // Cabeçalho da tabela
    $pdf->SetFillColor(75, 108, 183);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetDrawColor(200, 210, 230);
    $pdf->SetLineWidth(0.3);
    $pdf->Cell(140, 8, utf8_decode('TIPO / OUTRO EQUIPAMENTO'), 1, 0, 'C', true);
    $pdf->Cell(50,  8, 'QUANTIDADE', 1, 1, 'C', true);

    // Linhas de dados
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(30, 42, 69);
    $fill = false;
    $total_sala = 0;

    foreach ($sala['linhas'] as $linha) {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
        $pdf->Cell(140, 7, utf8_decode($linha['tipo']), 1, 0, 'L', true);
        $pdf->Cell(50,  7, $linha['qta'],               1, 1, 'C', true);
        $total_sala += $linha['qta'];
        $fill = !$fill;
    }

    // Rodapé da sala com total
    $pdf->SetFillColor(75, 108, 183);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(190, 7, utf8_decode('Total: ' . $total_sala), 1, 1, 'R', true);

    $pdf->Ln(4);
}

// ── Output ────────────────────────────────────────────────
$pdf->Output('I', 'Equipamentos_por_sala.pdf');
exit;
?>
