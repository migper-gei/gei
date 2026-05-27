<?php
// Sessão — igual ao utilizadores_pdf.php
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

// Limpar output buffer — igual ao utilizadores_pdf.php
while (ob_get_level()) { ob_end_clean(); }

$id_escola = (int)base64_decode($_GET["escola"]);
$sa        = (int)$_POST['salaet'];

// ── Query equipamentos ────────────────────────────────────
$sql = "SELECT e.nomeequi, s.nome, es.nome_escola
        FROM equipamento e, salas s, escolas es
        WHERE e.id_sala=s.id AND s.id_escola=es.id
        AND es.id=$id_escola AND s.id=$sa
        ORDER BY e.nomeequi";
$result = mysqli_query($db, $sql);
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// ── Logotipo — query separada ─────────────────────────────
$logo_pic = null;
$result2  = mysqli_query($db, "SELECT logotipo FROM logotipo LIMIT 1");
if ($result2) {
    $row2 = mysqli_fetch_assoc($result2);
    if (!empty($row2['logotipo'])) {
        $logo_pic = 'data:image/jpeg;base64,' . base64_encode($row2['logotipo']);
    }
}

mysqli_close($db);

// ── PDF ───────────────────────────────────────────────────
$mesq = 10;
$msup = 12;
$leti = 70;
$aeti = 27;

$pdf = new FPDF();
$pdf->AddPage("P", "A4");
$pdf->SetFont('Arial', '', 8);
$pdf->SetDisplayMode('fullpage');

$coluna = 0;
$linha  = 0;

foreach ($rows as $dados) {

    if ($coluna == 3) {
        $coluna = 0;
        $linha++;
    }

    if ($linha == 10) {
        $pdf->AddPage();
        $linha = 0;
    }

    $somaH = $mesq + ($coluna * $leti);
    $somaV = $msup + ($linha  * $aeti);

    if ($logo_pic) {
        $info = getimagesize($logo_pic);
        if ($info) {
            $pdf->Image($logo_pic, $somaH, $somaV, 15, 0, 'png');
        }
    }

    $textH = $logo_pic ? $somaH + 17 : $somaH; // deslocar texto se houver logotipo
    $pdf->Text($textH, $somaV + 7,  utf8_decode($dados['nomeequi']));
    $pdf->Text($textH, $somaV + 13, utf8_decode($dados['nome']));
    $pdf->Text($textH, $somaV + 19, utf8_decode($dados['nome_escola']));

    $coluna++;
}

$pdf->Output('I', 'etiquetas.pdf');
exit;
?>
