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

// ── Filtros opcionais ─────────────────────────────────────
$filtro_loc = isset($_GET['filtro_loc']) ? trim($_GET['filtro_loc']) : '';
$filtro_req = isset($_GET['filtro_req']) ? trim($_GET['filtro_req']) : '';

$filtros_label = [];
$params      = [$esc];
$param_types = 'i';

if ($filtro_loc !== '') {
    $filtros_label[] = 'Localização: ' . $filtro_loc;
}
if ($filtro_req !== '') {
    $filtros_label[] = 'Equip. Requisitável: ' . $filtro_req;
}

// ── Query de salas (exporta tudo, sem paginação) ──────────
$sql = "SELECT s.id, s.nome, s.localizacao, s.departamento, s.equip_requisitavel,
               COUNT(DISTINCT e.id)   AS total_equip,
               COUNT(DISTINCT oe.id)  AS total_outro_equip
        FROM salas s
        LEFT JOIN equipamento e        ON e.id_sala  = s.id
        LEFT JOIN outro_equipamento oe ON oe.id_sala = s.id
        WHERE s.id_escola = ?";

if ($filtro_loc !== '') {
    $sql          .= " AND s.localizacao = ?";
    $params[]      = $filtro_loc;
    $param_types  .= 's';
}
if ($filtro_req !== '') {
    $sql          .= " AND s.equip_requisitavel = ?";
    $params[]      = $filtro_req;
    $param_types  .= 's';
}

$sql .= " GROUP BY s.id, s.nome, s.localizacao, s.departamento, s.equip_requisitavel
          ORDER BY s.nome";

$stmt_sal = $db->prepare($sql);
$stmt_sal->bind_param($param_types, ...$params);
$stmt_sal->execute();
$res_sal = $stmt_sal->get_result();
$total   = $res_sal->num_rows;
$rows    = $res_sal->fetch_all(MYSQLI_ASSOC);
$stmt_sal->close();

// ── Logotipo ──────────────────────────────────────────────
$logo_pic    = null;
$stmt_logo   = $db->prepare("SELECT logotipo FROM logotipo LIMIT 1");
$stmt_logo->execute();
$res_logo    = $stmt_logo->get_result();
$row_logo    = $res_logo->fetch_assoc();
$stmt_logo->close();
if (!empty($row_logo['logotipo'])) {
    $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row_logo['logotipo']);
}

$db->close();

// ── Classe PDF com header/footer personalizados ───────────
class GEI_PDF extends FPDF {

    public $logo_pic    = null;
    public $doc_title   = '';
    public $total_rows  = 0;
    public $filter_label = ''; // filtros ativos

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

        // Barra de filtros ativos (se existirem)
        if ($this->filter_label !== '') {
            $this->SetFillColor(235, 242, 255);
            $this->Rect(0, 22, 297, 7, 'F');
            $this->SetFont('Arial', 'I', 7);
            $this->SetTextColor(75, 108, 183);
            $this->SetXY(0, 23);
            $this->Cell(297, 5, enc('Filtros ativos: ' . $this->filter_label), 0, 1, 'C');
            $this->SetTextColor(30, 42, 69);
            $this->SetY(35);
        } else {
            $this->SetTextColor(30, 42, 69);
            $this->SetY(28);
        }
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
$pdf->logo_pic    = $logo_pic;
$pdf->doc_title   = 'SALAS — ' . strtoupper($ne);
$pdf->total_rows  = $total;
$pdf->filter_label = !empty($filtros_label) ? implode('  |  ', $filtros_label) : '';
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('L', 'A4');   // Paisagem
$pdf->SetAutoPageBreak(true, 18);

// ── Cabeçalho da tabela ───────────────────────────────────
// Total útil ~277mm (297 - 10 - 10)
$cols = [
    ['label' => 'NOME',                  'w' => 60],
    ['label' => 'LOCALIZAÇÃO',           'w' => 50],
    ['label' => 'DEPARTAMENTO / GRUPO',  'w' => 70],
    ['label' => 'EQUIP. REQUISITÁVEL',   'w' => 35],
    ['label' => 'EQUIPAMENTOS',          'w' => 30],
    ['label' => 'OUTRO EQUIPAMENTO',     'w' => 32],
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
    $nome        = $row['nome']              ?? '—';
    $localizacao = $row['localizacao']       ?? '—';
    $depart      = $row['departamento']      ?? '—';
    $req         = $row['equip_requisitavel'] ?? '—';
    $total_eq    = $row['total_equip']       ?? 0;
    $total_oe    = $row['total_outro_equip'] ?? 0;

    // Truncar campos longos
    $nome_t   = mb_strlen($nome)   > 35 ? mb_substr($nome,   0, 32) . '...' : $nome;
    $loc_t    = mb_strlen($localizacao) > 28 ? mb_substr($localizacao, 0, 25) . '...' : $localizacao;
    $depart_t = mb_strlen($depart) > 42 ? mb_substr($depart, 0, 39) . '...' : $depart;

    // Fundo alternado
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);

    // Nome
    $pdf->SetTextColor(30, 42, 69);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 7, enc($nome_t), 1, 0, 'L', true);

    // Localização
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(50, 7, enc($loc_t), 1, 0, 'L', true);

    // Departamento
    $pdf->Cell(70, 7, enc($depart_t), 1, 0, 'L', true);

    // Equip. Requisitável — colorido
    $req_lower = strtolower($req);
    if ($req_lower === 'sim') {
        $pdf->SetTextColor(5, 150, 105);   // verde
    } else {
        $pdf->SetTextColor(123, 136, 160); // cinza
    }
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 7, enc($req), 1, 0, 'C', true);

    // Totais de equipamentos
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(30, 42, 69);
    $pdf->Cell(30, 7, (string)$total_eq, 1, 0, 'C', true);
    $pdf->Cell(32, 7, (string)$total_oe, 1, 1, 'C', true);

    $fill = !$fill;
}

// Rodapé da tabela com total
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(277, 8, enc('Total: ' . $total . ' sala' . ($total != 1 ? 's' : '')), 1, 1, 'R', true);

// ── Output ────────────────────────────────────────────────
$filename = 'salas_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $ne) . '_' . date('Ymd_Hi') . '.pdf';
$pdf->Output('I', $filename);
exit;
?>
