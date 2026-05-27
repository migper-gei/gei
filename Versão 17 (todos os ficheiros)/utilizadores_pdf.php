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

// ── Filtros da sessão (os mesmos que utilizadores.php guarda) ──
$filtro_tipo  = isset($_SESSION['filtro-tipo'])  ? (int)$_SESSION['filtro-tipo']  : 0;
$filtro_ativo = isset($_SESSION['filtro-ativo']) ? $_SESSION['filtro-ativo']       : 'todos';

// Construir WHERE igual ao utilizadores.php
$conditions = [];
if ($filtro_tipo > 0)            { $conditions[] = "tipo = $filtro_tipo"; }
if ($filtro_ativo === 'ativo')   { $conditions[] = "COALESCE(ativo,1) = 1"; }
if ($filtro_ativo === 'inativo') { $conditions[] = "COALESCE(ativo,1) = 0"; }
$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// ── Query principal com filtros ───────────────────────────────────────
$sql    = "SELECT nome, email, tipo FROM utilizadores $where ORDER BY nome";
$result = mysqli_query($db, $sql);
$total  = mysqli_num_rows($result);
$rows   = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

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

// Mapa de tipos
$tipos = [1=>'Administrador', 2=>'Utilizador', 3=>'Reparador', 4=>'Funcionário'];

// Subtítulo dinâmico consoante filtros ativos
$filtro_desc_parts = [];
if ($filtro_tipo > 0)             { $filtro_desc_parts[] = $tipos[$filtro_tipo] ?? 'Tipo '.$filtro_tipo; }
if ($filtro_ativo === 'ativo')    { $filtro_desc_parts[] = 'Ativos'; }
if ($filtro_ativo === 'inativo')  { $filtro_desc_parts[] = 'Desativados'; }
$filtro_desc = count($filtro_desc_parts) > 0 ? implode(' · ', $filtro_desc_parts) : 'Todos';

// ── Classe PDF com header/footer personalizados ───────────
class GEI_PDF extends FPDF {

    public $logo_pic    = null;
    public $doc_title   = '';
    public $doc_filtro  = '';
    public $total_rows  = 0;

    function Header() {
        // Faixa azul escura no topo
        $this->SetFillColor(24, 40, 72);
        $this->Rect(0, 0, 210, 22, 'F');

        // Logotipo — dimensões proporcionais, altura máx 16mm
        if ($this->logo_pic) {
            $info = getimagesize($this->logo_pic);
            if ($info) {
                $img_w = $info[0];
                $img_h = $info[1];
                $max_h = 16;
                $max_w = 30;
                $ratio = $img_w / $img_h;
                $h = $max_h;
                $w = $h * $ratio;
                if ($w > $max_w) {
                    $w = $max_w;
                    $h = $w / $ratio;
                }
                $y = (22 - $h) / 2;
                $this->Image($this->logo_pic, 6, $y, $w, $h, 'png');
            }
        }

        // Título
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 5);
        $this->Cell(210, 8, utf8_decode($this->doc_title), 0, 1, 'C');

        // Subtítulo: data + total
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(180, 200, 230);
        $this->SetXY(0, 14);
        $this->Cell(105, 5, utf8_decode('Gerado em: ' . date('d/m/Y H:i')), 0, 0, 'C');
        $this->Cell(105, 5, utf8_decode('Total de registos: ' . $this->total_rows), 0, 1, 'C');

        // Linha de filtro ativo (abaixo da faixa)
        $this->SetFillColor(240, 244, 251);
        $this->Rect(0, 22, 210, 6, 'F');
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(75, 108, 183);
        $this->SetXY(0, 23);
        $this->Cell(210, 4, utf8_decode('Filtro: ' . $this->doc_filtro), 0, 1, 'C');

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
$pdf->doc_title  = 'LISTA DE UTILIZADORES';
$pdf->doc_filtro = $filtro_desc;
$pdf->total_rows = $total;
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P', 'A4');
$pdf->SetAutoPageBreak(true, 18);

// ── Cabeçalho da tabela ───────────────────────────────────
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(200, 210, 230);
$pdf->SetLineWidth(0.3);

$pdf->Cell(65, 9, 'NOME',  1, 0, 'C', true);
$pdf->Cell(95, 9, 'EMAIL', 1, 0, 'C', true);
$pdf->Cell(30, 9, 'TIPO',  1, 1, 'C', true);

// ── Linhas de dados ───────────────────────────────────────
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(30, 42, 69);
$fill = false;

foreach ($rows as $row) {
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 244 : 255, $fill ? 251 : 255);
    $tipo_label = $tipos[(int)$row['tipo']] ?? '-';
    $pdf->Cell(65, 8, utf8_decode($row['nome']),  1, 0, 'L', true);
    $pdf->Cell(95, 8, utf8_decode($row['email']), 1, 0, 'L', true);
    $pdf->Cell(30, 8, utf8_decode($tipo_label),   1, 1, 'C', true);
    $fill = !$fill;
}

// Rodapé da tabela com total
$pdf->SetFillColor(75, 108, 183);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 8, utf8_decode('Total: ' . $total . ' utilizadores'), 1, 1, 'R', true);

// ── Output ────────────────────────────────────────────────
$pdf->Output('I', 'Utilizadores.pdf');
exit;
?>
