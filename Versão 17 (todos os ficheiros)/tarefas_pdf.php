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

// Função auxiliar: converte UTF-8 → ISO-8859-1 para o FPDF
function enc($str) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $str);
}

// Limpar output buffer
while (ob_get_level()) { ob_end_clean(); }

// ── Parâmetros ────────────────────────────────────────────
$esc = (int)base64_decode($_GET['id'] ?? '');
$z   = (int)base64_decode($_GET['z']  ?? base64_encode(0));

if ($esc <= 0) {
    header('Location: ' . SVRURL . 'configura');
    exit;
}

// ── Nome da escola ────────────────────────────────────────
$stmt_esc = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ? LIMIT 1");
$stmt_esc->bind_param("i", $esc);
$stmt_esc->execute();
$res_esc = $stmt_esc->get_result();
if ($res_esc->num_rows === 0) {
    $stmt_esc->close();
    header('Location: ' . SVRURL . 'configura');
    exit;
}
$ne = $res_esc->fetch_row()[0];
$stmt_esc->close();

// ── Query de tarefas (sem paginação — exporta tudo) ───────
// CORRECÇÃO: usar CAST para comparar como CHAR, contornando o modo
// estrito do MySQL que rejeita '0000-00-00' e '' em colunas DATE.
if ($z == 0) {
    $filtro_desc = 'Não concluídas';
    $sql = "SELECT t.*, s.nome AS nome_sala
            FROM tarefas t
            LEFT JOIN salas s ON s.id = t.id_sala
            WHERE t.id_escola = ?
              AND (t.data_conclusao IS NULL
                   OR CAST(t.data_conclusao AS CHAR) = ''
                   OR CAST(t.data_conclusao AS CHAR) = '0000-00-00')
            ORDER BY t.data_criacao DESC";
} else {
    $filtro_desc = 'Todas';
    $sql = "SELECT t.*, s.nome AS nome_sala
            FROM tarefas t
            LEFT JOIN salas s ON s.id = t.id_sala
            WHERE t.id_escola = ?
            ORDER BY t.data_criacao DESC, t.data_conclusao";
}

$stmt_tar = $db->prepare($sql);
$stmt_tar->bind_param("i", $esc);
$stmt_tar->execute();
$res_tar = $stmt_tar->get_result();
$total   = $res_tar->num_rows;
$rows    = $res_tar->fetch_all(MYSQLI_ASSOC);
$stmt_tar->close();

// ── Logotipo ──────────────────────────────────────────────
$logo_pic  = null;
$stmt_logo = $db->prepare("SELECT logotipo FROM logotipo LIMIT 1");
$stmt_logo->execute();
$res_logo  = $stmt_logo->get_result();
$row_logo  = $res_logo->fetch_assoc();
$stmt_logo->close();
if (!empty($row_logo['logotipo'])) {
    $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row_logo['logotipo']);
}

$db->close();

// ── Classe PDF com header/footer personalizados ───────────
class GEI_PDF extends FPDF {

    public $logo_pic   = null;
    public $doc_title  = '';
    public $doc_filtro = '';
    public $total_rows = 0;

    function Header() {
        // Faixa azul escura no topo
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 297, 22, 'F');

        // Logotipo
        if ($this->logo_pic) {
            $info = getimagesize($this->logo_pic);
            if ($info) {
                $img_w = $info[0]; $img_h = $info[1];
                $max_h = 16; $max_w = 30;
                $ratio = $img_w / $img_h;
                $h = $max_h; $w = $h * $ratio;
                if ($w > $max_w) { $w = $max_w; $h = $w / $ratio; }
                $y = (22 - $h) / 2;
                $this->Image($this->logo_pic, 6, $y, $w, $h, 'png');
            }
        }

        // Título
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 5);
        $this->Cell(297, 8, enc($this->doc_title), 0, 1, 'C');

        // Data + total
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(180, 200, 230);
        $this->SetXY(0, 14);
        $this->Cell(148, 5, enc('Gerado em: ' . date('d/m/Y H:i')), 0, 0, 'C');
        $this->Cell(149, 5, enc('Total de registos: ' . $this->total_rows), 0, 1, 'C');

        // Barra de filtro
        $this->SetFillColor(240, 244, 251);
        $this->Rect(0, 22, 297, 6, 'F');
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(75, 108, 183);
        $this->SetXY(0, 23);
        $this->Cell(297, 4, enc('Filtro: ' . $this->doc_filtro), 0, 1, 'C');

        $this->SetTextColor(30, 42, 69);
        $this->SetY(32);
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetDrawColor(75, 108, 183);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(1);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(123, 136, 160);
        $this->Cell(0, 5, enc('Página ' . $this->PageNo() . ' / {nb}'), 0, 0, 'C');
    }
}

// ── Instanciar PDF ────────────────────────────────────────
$pdf = new GEI_PDF();
$pdf->AliasNbPages();
$pdf->logo_pic   = $logo_pic;
$pdf->doc_title  = 'TAREFAS — ' . strtoupper($ne);
$pdf->doc_filtro = $filtro_desc;
$pdf->total_rows = $total;
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('L', 'A4');   // Paisagem — mais colunas
$pdf->SetAutoPageBreak(true, 18);

// ── Cabeçalho da tabela ───────────────────────────────────
// Larguras: total útil ~277mm (297 - 10 - 10)
$cols = [
    ['label' => 'SALA',           'w' => 28],
    ['label' => 'DESCRIÇÃO',      'w' => 80],
    ['label' => 'URGÊNCIA',       'w' => 22],
    ['label' => 'ESTADO',         'w' => 24],
    ['label' => 'CRIADO POR',     'w' => 35],
    ['label' => 'DATA CRIAÇÃO',   'w' => 22],
    ['label' => 'CONCLUÍDO POR',  'w' => 35],
    ['label' => 'DATA CONCLUSÃO', 'w' => 31],
];

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(200, 210, 230);
$pdf->SetLineWidth(0.3);

foreach ($cols as $col) {
    $pdf->Cell($col['w'], 9, enc($col['label']), 1, 0, 'C', true);
}
$pdf->Ln();

// ── Linhas de dados ───────────────────────────────────────
$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(30, 42, 69);
$fill = false;

foreach ($rows as $row) {
    // CORRECÇÃO: considerar '0000-00-00' como não concluída
    $data_conclusao = $row['data_conclusao'] ?? '';
    $concluida      = !empty($data_conclusao) && $data_conclusao !== '0000-00-00';

    $urg        = $row['urgencia']      ?? '';
    $urg_lower  = strtolower($urg);
    $nomesala   = $row['nome_sala']     ?? '—';
    $descricao  = $row['descricao']     ?? '';
    $criado_por = $row['criado_por']    ?? '—';
    $conc_por   = $row['concluido_por'] ?? '—';
    $data_cria  = !empty($row['data_criacao']) ? date('d/m/Y', strtotime($row['data_criacao'])) : '—';
    $data_conc  = $concluida ? date('d/m/Y', strtotime($data_conclusao)) : '—';
    $estado     = $concluida ? 'Concluída' : 'Pendente';

    // Fundo alternado
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);

    // Sala
    $pdf->SetTextColor(30, 42, 69);
    $pdf->Cell(28, 7, enc($nomesala), 1, 0, 'L', true);

    // Descrição truncada
    $desc_trunc = mb_strlen($descricao) > 60 ? mb_substr($descricao, 0, 57) . '...' : $descricao;
    $pdf->Cell(80, 7, enc($desc_trunc), 1, 0, 'L', true);

    // Urgência colorida
    if ($urg_lower === 'alta') {
        $pdf->SetTextColor(192, 57, 43);
    } elseif ($urg_lower === 'média' || $urg_lower === 'media') {
        $pdf->SetTextColor(125, 78, 0);
    } else {
        $pdf->SetTextColor(75, 108, 183);
    }
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(22, 7, enc($urg), 1, 0, 'C', true);

    // Estado colorido
    $pdf->SetTextColor($concluida ? 26 : 192, $concluida ? 122 : 57, $concluida ? 80 : 43);
    $pdf->Cell(24, 7, enc($estado), 1, 0, 'C', true);

    // Restantes colunas
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(30, 42, 69);
    $pdf->Cell(35, 7, enc($criado_por), 1, 0, 'L', true);
    $pdf->Cell(22, 7, $data_cria,               1, 0, 'C', true);
    $pdf->Cell(35, 7, enc($conc_por),   1, 0, 'L', true);
    $pdf->Cell(31, 7, $data_conc,               1, 1, 'C', true);

    $fill = !$fill;
}

// Rodapé da tabela com total
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(277, 8, enc('Total: ' . $total . ' tarefa' . ($total != 1 ? 's' : '')), 1, 1, 'R', true);

// ── Output ────────────────────────────────────────────────
$filename = 'tarefas_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $ne) . '_' . date('Ymd_Hi') . '.pdf';
$pdf->Output('I', $filename);
exit;
?>
