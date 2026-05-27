<?php
// ============================================================
// equipamento_timeline_pdf.php
// Exporta o histórico de avarias de um equipamento em PDF
// Chamada: equipamento_timeline_pdf.php?id_equip=X
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_name('gei_session');
    session_start();
}

include('config.php');
require('fpdf/fpdf.php');

while (ob_get_level()) { ob_end_clean(); }

// ── Validação ─────────────────────────────────────────────
$id_equip = (int)($_GET['id_equip'] ?? 0);
if ($id_equip <= 0) {
    http_response_code(400);
    exit('ID de equipamento inválido.');
}

// ── Dados do equipamento ──────────────────────────────────
$res_eq = mysqli_query($db,
    "SELECT e.nomeequi, e.tipo, s.nome AS sala
     FROM equipamento e
     JOIN salas s ON e.id_sala = s.id
     WHERE e.id = $id_equip
     LIMIT 1"
);
if (!$res_eq || mysqli_num_rows($res_eq) === 0) {
    http_response_code(404);
    exit('Equipamento não encontrado.');
}
$equip = mysqli_fetch_assoc($res_eq);

// ── Avarias ───────────────────────────────────────────────
$res = mysqli_query($db,
    "SELECT
         ar.id,
         ar.dataavaria,
         ar.datareparacao,
         ar.avaria            AS descricao,
         ar.reparacao         AS solucao,
         ar.rep_efectuada_por AS tecnico,
         DATEDIFF(ar.datareparacao, ar.dataavaria) AS dias_resolucao
     FROM avarias_reparacoes ar
     WHERE ar.id_equi = $id_equip
     ORDER BY ar.dataavaria ASC, ar.id ASC"
);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}
$total = count($rows);

// ── Estatísticas ──────────────────────────────────────────
$abertas    = 0;
$resolvidas = 0;
$soma_dias  = 0;
$n_dias     = 0;

foreach ($rows as $r) {
    if ($r['datareparacao'] !== null && (int)$r['dias_resolucao'] >= 0) {
        $resolvidas++;
        $soma_dias += (int)$r['dias_resolucao'];
        $n_dias++;
    } else {
        $abertas++;
    }
}
$tempo_medio = $n_dias > 0 ? round($soma_dias / $n_dias, 1) : null;
$taxa_res    = $total > 0  ? round($resolvidas / $total * 100) : 0;

// ── Logotipo ──────────────────────────────────────────────
$logo_pic = null;
$result2  = mysqli_query($db, "SELECT logotipo FROM logotipo LIMIT 1");
if ($result2) {
    $row2 = mysqli_fetch_assoc($result2);
    if (!empty($row2['logotipo'])) {
        $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row2['logotipo']);
    }
}

mysqli_close($db);

// ── Helper: formatar data ─────────────────────────────────
function fmt_data(?string $d): string {
    if (!$d || $d === '0000-00-00') return '-';
    $dt = DateTime::createFromFormat('Y-m-d', substr($d, 0, 10));
    return $dt ? $dt->format('d/m/Y') : $d;
}

// ── Classe PDF ────────────────────────────────────────────
class GEI_PDF extends FPDF {

    public $logo_pic   = null;
    public $doc_title  = '';
    public $doc_sub    = '';
    public $total_rows = 0;

    function Header() {
        // Faixa azul escura
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 210, 22, 'F');

        // Logotipo
        if ($this->logo_pic) {
            $info = getimagesize($this->logo_pic);
            if ($info) {
                $ratio = $info[0] / $info[1];
                $h = 16; $w = $h * $ratio;
                if ($w > 30) { $w = 30; $h = $w / $ratio; }
                $this->Image($this->logo_pic, 6, (22 - $h) / 2, $w, $h, 'png');
            }
        }

        // Título
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 5);
        $this->Cell(210, 8, utf8_decode($this->doc_title), 0, 1, 'C');

        // Subtítulo
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(180, 200, 230);
        $this->SetXY(0, 14);
        $this->Cell(105, 5, utf8_decode('Gerado em: ' . date('d/m/Y H:i')), 0, 0, 'C');
        $this->Cell(105, 5, utf8_decode('Total de avarias: ' . $this->total_rows), 0, 1, 'C');

        // Faixa info equipamento
        $this->SetFillColor(240, 244, 251);
        $this->Rect(0, 22, 210, 6, 'F');
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(75, 108, 183);
        $this->SetXY(0, 23);
        $this->Cell(210, 4, utf8_decode($this->doc_sub), 0, 1, 'C');

        $this->SetTextColor(30, 42, 69);
        $this->SetY(32);
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
$pdf->logo_pic   = $logo_pic;
$pdf->doc_title  = 'HISTÓRICO DE AVARIAS';
$pdf->doc_sub    = utf8_decode($equip['nomeequi'] . '   ·   ' . $equip['tipo'] . '   ·   Sala: ' . $equip['sala']);
$pdf->total_rows = $total;
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P', 'A4');
$pdf->SetAutoPageBreak(true, 18);

// ── Bloco de estatísticas ─────────────────────────────────
$pdf->SetFillColor(240, 244, 251);
$pdf->SetDrawColor(75, 108, 183);
$pdf->SetLineWidth(0.3);

$stat_w = 38;
$stats = [
    ['Total',          $total],
    ['Em aberto',      $abertas],
    ['Resolvidas',     $resolvidas],
    ['Taxa resolução', $taxa_res . '%'],
    ['Tempo médio',    $tempo_medio !== null ? $tempo_medio . ' dias' : '-'],
];

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(75, 108, 183);
foreach ($stats as $s) {
    $pdf->Cell($stat_w, 6, utf8_decode($s[0]), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(24, 40, 72);
foreach ($stats as $s) {
    $pdf->Cell($stat_w, 8, utf8_decode((string)$s[1]), 1, 0, 'C', true);
}
$pdf->Ln(14);

// ── Cabeçalho da tabela ───────────────────────────────────
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(200, 210, 230);

$pdf->Cell(6,  9, '#',          1, 0, 'C', true);
$pdf->Cell(22, 9, 'Data Avaria',1, 0, 'C', true);
$pdf->Cell(22, 9, 'Data Repar.',1, 0, 'C', true);
$pdf->Cell(10, 9, 'Dias',       1, 0, 'C', true);
$pdf->Cell(30, 9, 'Tecnico',    1, 0, 'C', true);
$pdf->Cell(55, 9, 'Descricao',  1, 0, 'C', true);
$pdf->Cell(45, 9, 'Solucao',    1, 1, 'C', true);

// ── Linhas de dados ───────────────────────────────────────
$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(30, 42, 69);
$fill = false;

foreach ($rows as $i => $r) {
    $resolvida = $r['datareparacao'] !== null && (int)$r['dias_resolucao'] >= 0;
    $estado    = $resolvida ? 'Resolvida' : 'Em aberto';
    $dias_txt  = $resolvida ? (string)(int)$r['dias_resolucao'] : '-';
    $tecnico   = $r['tecnico']   ?? '-';
    $descricao = $r['descricao'] ?? '-';
    $solucao   = $r['solucao']   ?? '-';

    // Calcular altura da linha (multiline)
    $lines_desc = $pdf->GetStringWidth(utf8_decode($descricao)) > 54 ? 2 : 1;
    $lines_sol  = $pdf->GetStringWidth(utf8_decode($solucao))   > 44 ? 2 : 1;
    $row_h = max($lines_desc, $lines_sol) * 5 + 2;

    // Cor alternada; vermelho suave se em aberto
    if (!$resolvida) {
        $pdf->SetFillColor(255, 240, 240);
    } else {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Verificar quebra de página manual
    if ($y + $row_h > $pdf->GetPageHeight() - 20) {
        $pdf->AddPage();
        $y = $pdf->GetY();
    }

    // Desenhar células com altura fixa (conteúdo longo é truncado — simples e limpo)
    $pdf->Cell(6,  $row_h, (string)($i + 1),              1, 0, 'C', true);
    $pdf->Cell(22, $row_h, fmt_data($r['dataavaria']),     1, 0, 'C', true);
    $pdf->Cell(22, $row_h, fmt_data($r['datareparacao']),  1, 0, 'C', true);
    $pdf->Cell(10, $row_h, $dias_txt,                      1, 0, 'C', true);
    $pdf->Cell(30, $row_h, utf8_decode($tecnico),          1, 0, 'L', true);
    $pdf->Cell(55, $row_h, utf8_decode(mb_strimwidth($descricao, 0, 60, '...')), 1, 0, 'L', true);
    $pdf->Cell(45, $row_h, utf8_decode(mb_strimwidth($solucao,   0, 50, '...')), 1, 1, 'L', true);

    $fill = !$fill;
}

// ── Rodapé da tabela ──────────────────────────────────────
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 8, utf8_decode('Total: ' . $total . ' avaria' . ($total === 1 ? '' : 's')), 1, 1, 'R', true);

// ── Output ────────────────────────────────────────────────
$nome_ficheiro = 'Avarias_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $equip['nomeequi']) . '.pdf';
$pdf->Output('I', $nome_ficheiro);
exit;
?>
