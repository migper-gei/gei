<?php
ob_start(); // Captura qualquer output acidental dos includes (evita corrupção do XLSX)

// ── Sessão segura (tem de ser ANTES de qualquer include) ─────────────────────
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

// ── Verificar sessão ANTES de qualquer output ────────────────────────────────
if (empty($_SESSION['nobd']) || empty($_SESSION['serverbd'])) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

include('config.php');

// ── PhpSpreadsheet ───────────────────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// ── Validar parâmetro id ─────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: ' . SVRURL . 'equipamento.php');
    exit();
}

// ── Buscar nome da escola ────────────────────────────────────────────────────
$stmtEsc = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmtEsc->bind_param('i', $id);
$stmtEsc->execute();
$rowEsc = $stmtEsc->get_result()->fetch_assoc();
$nomeEscola = $rowEsc ? $rowEsc['nome_escola'] : 'Escola';
$stmtEsc->close();

// ── Query equipamentos ───────────────────────────────────────────────────────
$stmt = $db->prepare("
    SELECT
        eq.nomeequi, eq.numserie, s.nome AS sala, eq.marca_modelo, eq.tipo,
        eq.processador, eq.memoria, eq.disco, eq.placagrafica, eq.placasom,
        eq.monitor, eq.teclado, eq.tecladointerface, eq.rato, eq.ratointerface,
        eq.colunas, eq.cd_dvd,
        eq.dominio, eq.ip, eq.mascara_rede, eq.gateway,
        eq.dns_principal, eq.dns_alternativo,
        eq.sala_temp, eq.data_compra,
        eq.escola_digital, eq.num_inv_dgest, eq.fornecedor, eq.nif_pessoa, eq.num_rma
    FROM escolas e
    INNER JOIN salas s        ON s.id_escola  = e.id
    INNER JOIN equipamento eq ON eq.id_sala   = s.id
    WHERE e.id = ?
    ORDER BY s.nome, eq.nomeequi
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

$dados = [];
while ($row = $result->fetch_assoc()) {
    $dados[] = $row;
}
$stmt->close();

// ── Agrupar por sala ─────────────────────────────────────────────────────────
$porSala = [];
foreach ($dados as $row) {
    $porSala[$row['sala']][] = $row;
}

// ── Criar Spreadsheet ────────────────────────────────────────────────────────
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator('GEI')
    ->setLastModifiedBy('GEI')
    ->setTitle('Equipamentos — ' . $nomeEscola)
    ->setSubject('Exportação de Equipamentos')
    ->setDescription('Gerado automaticamente pelo GEI em ' . date('d/m/Y H:i'));

// ── Estilos globais ──────────────────────────────────────────────────────────
$corCabecalho   = '1E3A5F';  // azul escuro
$corSubCabecalho = '2E75B6'; // azul médio
$corAlternada   = 'EEF4FB';  // azul muito claro
$corBranco      = 'FFFFFF';
$corTextoClaro  = 'FFFFFF';
$corBorda       = 'BDD7EE';

$colunas = [
    'Nome'              => 20,
    'Nº Série'          => 18,
    'Sala'              => 16,
    'Marca/Modelo'      => 22,
    'Tipo'              => 14,
    'Processador'       => 22,
    'Memória'           => 12,
    'Disco'             => 12,
    'Placa Gráfica'     => 18,
    'Placa Som'         => 14,
    'Monitor'           => 14,
    'Teclado'           => 14,
    'Tecl. Interface'   => 14,
    'Rato'              => 12,
    'Rato Interface'    => 14,
    'Colunas'           => 12,
    'CD/DVD'            => 10,
    'Domínio'           => 16,
    'IP'                => 14,
    'Máscara Rede'      => 14,
    'Gateway'           => 14,
    'DNS Principal'     => 16,
    'DNS Alternativo'   => 16,
    'Sala Temporária'   => 16,
    'Data Compra'       => 13,
    'Escola Digital'    => 14,
    'Nº Inv. DGEST'     => 14,
    'Fornecedor'        => 20,
    'NIF Pessoa'        => 12,
    'Nº RMA'            => 12,
];

$camposOrdem = array_keys($colunas);
$numCols = count($camposOrdem);
$ultimaColLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numCols);

// Helper: converte índice numérico de coluna + linha em referência de célula (ex: 1,3 → "A3")
function colRef(int $col, int $row): string {
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
}

// ════════════════════════════════════════════════════════════════════════════
// FOLHA 1 — RESUMO GERAL (todos os equipamentos)
// ════════════════════════════════════════════════════════════════════════════
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Todos os Equipamentos');

// Linha 1 — Título principal
$sheet->mergeCells('A1:' . $ultimaColLetra . '1');
$sheet->setCellValue('A1', 'Equipamentos — ' . $nomeEscola);
$sheet->getStyle('A1')->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF' . $corTextoClaro);
$sheet->getRowDimension(1)->setRowHeight(30);

// Linha 2 — Subtítulo com data e total
$sheet->mergeCells('A2:' . $ultimaColLetra . '2');
$sheet->setCellValue('A2', 'Gerado em ' . date('d/m/Y H:i') . '   |   Total: ' . count($dados) . ' equipamentos');
$sheet->getStyle('A2')->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corSubCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);
$sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true)->getColor()->setARGB('FF' . $corTextoClaro);
$sheet->getRowDimension(2)->setRowHeight(18);

// Linha 3 — Cabeçalhos das colunas
$col = 1;
foreach ($camposOrdem as $cabecalho) {
    $sheet->setCellValue(colRef($col, 3), $cabecalho);
    $col++;
}
$sheet->getStyle('A3:' . $ultimaColLetra . '3')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . $corTextoClaro]],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF4472C4']]],
]);
$sheet->getRowDimension(3)->setRowHeight(22);

// Dados
$linha = 4;
foreach ($dados as $i => $row) {
    $vals = [
        $row['nomeequi'], $row['numserie'], $row['sala'], $row['marca_modelo'], $row['tipo'],
        $row['processador'], $row['memoria'], $row['disco'], $row['placagrafica'], $row['placasom'],
        $row['monitor'], $row['teclado'], $row['tecladointerface'], $row['rato'], $row['ratointerface'],
        $row['colunas'], $row['cd_dvd'],
        $row['dominio'], $row['ip'], $row['mascara_rede'], $row['gateway'],
        $row['dns_principal'], $row['dns_alternativo'],
        $row['sala_temp'], $row['data_compra'],
        $row['escola_digital'], $row['num_inv_dgest'], $row['fornecedor'], $row['nif_pessoa'], $row['num_rma'],
    ];

    $corFundo = ($i % 2 === 0) ? $corBranco : $corAlternada;
    $col = 1;
    foreach ($vals as $val) {
        $sheet->setCellValue(colRef($col, $linha), $val ?? '');
        $col++;
    }
    $sheet->getStyle('A' . $linha . ':' . $ultimaColLetra . $linha)->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corFundo]],
        'font'      => ['size' => 9],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF' . $corBorda]]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getRowDimension($linha)->setRowHeight(16);
    $linha++;
}

// Larguras das colunas
$col = 1;
foreach ($colunas as $largura) {
    $sheet->getColumnDimensionByColumn($col)->setWidth($largura);
    $col++;
}

// Filtros automáticos na linha de cabeçalho
$sheet->setAutoFilter('A3:' . $ultimaColLetra . $linha);

// Congelar painel (cabeçalho + 2 linhas de título fixas)
$sheet->freezePane('A4');

// ════════════════════════════════════════════════════════════════════════════
// FOLHA 2 — RESUMO POR SALA (contagem de equipamentos)
// ════════════════════════════════════════════════════════════════════════════
$sheetResumo = $spreadsheet->createSheet();
$sheetResumo->setTitle('Resumo por Sala');

$sheetResumo->mergeCells('A1:D1');
$sheetResumo->setCellValue('A1', 'Resumo de Equipamentos por Sala — ' . $nomeEscola);
$sheetResumo->getStyle('A1')->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheetResumo->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setARGB('FF' . $corTextoClaro);
$sheetResumo->getRowDimension(1)->setRowHeight(28);

$sheetResumo->mergeCells('A2:D2');
$sheetResumo->setCellValue('A2', 'Gerado em ' . date('d/m/Y H:i'));
$sheetResumo->getStyle('A2')->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corSubCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);
$sheetResumo->getStyle('A2')->getFont()->setSize(9)->setItalic(true)->getColor()->setARGB('FF' . $corTextoClaro);
$sheetResumo->getRowDimension(2)->setRowHeight(16);

// Recolher todos os tipos existentes em todas as salas (ordenados)
$todosTipos = [];
foreach ($porSala as $equips) {
    foreach ($equips as $eq) {
        if (!empty($eq['tipo'])) {
            $todosTipos[$eq['tipo']] = true;
        }
    }
}
ksort($todosTipos);
$todosTipos = array_keys($todosTipos);

// Colunas fixas: A=Sala, B=Total; depois uma col por tipo; depois Marcas distintas
$colTipoInicio = 3; // coluna C (índice 3)
$numTipos = count($todosTipos);
$colMarcas = $colTipoInicio + $numTipos; // coluna após os tipos
$ultimaColR = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colMarcas);

// Actualizar merge do título e subtítulo
$sheetResumo->unmergeCells('A1:D1');
$sheetResumo->mergeCells('A1:' . $ultimaColR . '1');
$sheetResumo->unmergeCells('A2:D2');
$sheetResumo->mergeCells('A2:' . $ultimaColR . '2');

// Cabeçalhos fixos
$sheetResumo->setCellValue('A3', 'Sala');
$sheetResumo->setCellValue('B3', 'Total Equipamentos');

// Cabeçalhos dinâmicos por tipo
foreach ($todosTipos as $idx => $tipo) {
    $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colTipoInicio + $idx);
    $sheetResumo->setCellValue($colLetra . '3', $tipo);
}

// Cabeçalho Marcas distintas
$sheetResumo->setCellValue($ultimaColR . '3', 'Marcas distintas');

$sheetResumo->getStyle('A3:' . $ultimaColR . '3')->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corCabecalho]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF4472C4']]],
]);
$sheetResumo->getStyle('A3:' . $ultimaColR . '3')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF' . $corTextoClaro);
$sheetResumo->getRowDimension(3)->setRowHeight(20);

$linhaR = 4;
foreach ($porSala as $sala => $equips) {
    $marcas   = count(array_unique(array_column($equips, 'marca_modelo')));
    $corFundo = ($linhaR % 2 === 0) ? $corBranco : $corAlternada;

    $sheetResumo->setCellValue('A' . $linhaR, $sala);
    $sheetResumo->setCellValue('B' . $linhaR, count($equips));

    // Contagem por tipo
    $contagemTipos = array_count_values(array_column($equips, 'tipo'));
    foreach ($todosTipos as $idx => $tipo) {
        $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colTipoInicio + $idx);
        $sheetResumo->setCellValue($colLetra . $linhaR, $contagemTipos[$tipo] ?? 0);
    }

    $sheetResumo->setCellValue($ultimaColR . $linhaR, $marcas);

    $sheetResumo->getStyle('A' . $linhaR . ':' . $ultimaColR . $linhaR)->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corFundo]],
        'font'      => ['size' => 10],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF' . $corBorda]]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);
    $sheetResumo->getStyle('B' . $linhaR . ':' . $ultimaColR . $linhaR)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheetResumo->getRowDimension($linhaR)->setRowHeight(18);
    $linhaR++;
}

// Linha de totais
$sheetResumo->setCellValue('A' . $linhaR, 'TOTAL');
$sheetResumo->setCellValue('B' . $linhaR, '=SUM(B4:B' . ($linhaR - 1) . ')');
foreach ($todosTipos as $idx => $tipo) {
    $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colTipoInicio + $idx);
    $sheetResumo->setCellValue($colLetra . $linhaR, '=SUM(' . $colLetra . '4:' . $colLetra . ($linhaR - 1) . ')');
}
$sheetResumo->setCellValue($ultimaColR . $linhaR, '');
$sheetResumo->getStyle('A' . $linhaR . ':' . $ultimaColR . $linhaR)->applyFromArray([
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corSubCabecalho]],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . $corCabecalho]]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheetResumo->getStyle('A' . $linhaR . ':' . $ultimaColR . $linhaR)->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF' . $corTextoClaro);
$sheetResumo->getRowDimension($linhaR)->setRowHeight(20);

$sheetResumo->setAutoFilter('A3:' . $ultimaColR . $linhaR);
$sheetResumo->freezePane('A4');
$sheetResumo->getColumnDimension('A')->setWidth(28);
$sheetResumo->getColumnDimension('B')->setWidth(18);
foreach ($todosTipos as $idx => $tipo) {
    $colLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colTipoInicio + $idx);
    $sheetResumo->getColumnDimension($colLetra)->setWidth(max(14, mb_strlen($tipo) + 2));
}
$sheetResumo->getColumnDimension($ultimaColR)->setWidth(18);

// ════════════════════════════════════════════════════════════════════════════
// FOLHAS POR SALA — uma folha por cada sala
// ════════════════════════════════════════════════════════════════════════════
foreach ($porSala as $sala => $equips) {
    // Sanitizar nome da folha (máx 31 chars, sem caracteres especiais)
    $nomeFolha = mb_substr(preg_replace('/[\/\\\?\*\[\]:]/u', '', $sala), 0, 31);
    if ($nomeFolha === '') $nomeFolha = 'Sala';

    $sheetSala = $spreadsheet->createSheet();
    $sheetSala->setTitle($nomeFolha);

    // Título
    $sheetSala->mergeCells('A1:' . $ultimaColLetra . '1');
    $sheetSala->setCellValue('A1', '' . $nomeEscola . ' — Sala: ' . $sala . '   (' . count($equips) . ' equipamentos)');
    $sheetSala->getStyle('A1')->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corCabecalho]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);
    $sheetSala->getStyle('A1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF' . $corTextoClaro);
    $sheetSala->getRowDimension(1)->setRowHeight(26);

    // Cabeçalhos
    $col = 1;
    foreach ($camposOrdem as $cabecalho) {
        $sheetSala->setCellValue(colRef($col, 2), $cabecalho);
        $col++;
    }
    $sheetSala->getStyle('A2:' . $ultimaColLetra . '2')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . $corTextoClaro]],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corSubCabecalho]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF4472C4']]],
    ]);
    $sheetSala->getRowDimension(2)->setRowHeight(20);

    // Dados
    $linhaSala = 3;
    foreach ($equips as $i => $row) {
        $vals = [
            $row['nomeequi'], $row['numserie'], $row['sala'], $row['marca_modelo'], $row['tipo'],
            $row['processador'], $row['memoria'], $row['disco'], $row['placagrafica'], $row['placasom'],
            $row['monitor'], $row['teclado'], $row['tecladointerface'], $row['rato'], $row['ratointerface'],
            $row['colunas'], $row['cd_dvd'],
            $row['dominio'], $row['ip'], $row['mascara_rede'], $row['gateway'],
            $row['dns_principal'], $row['dns_alternativo'],
            $row['sala_temp'], $row['data_compra'],
            $row['escola_digital'], $row['num_inv_dgest'], $row['fornecedor'], $row['nif_pessoa'], $row['num_rma'],
        ];
        $corFundo = ($i % 2 === 0) ? $corBranco : $corAlternada;
        $col = 1;
        foreach ($vals as $val) {
            $sheetSala->setCellValue(colRef($col, $linhaSala), $val ?? '');
            $col++;
        }
        $sheetSala->getStyle('A' . $linhaSala . ':' . $ultimaColLetra . $linhaSala)->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $corFundo]],
            'font'    => ['size' => 9],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF' . $corBorda]]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheetSala->getRowDimension($linhaSala)->setRowHeight(16);
        $linhaSala++;
    }

    // Larguras e filtros
    $col = 1;
    foreach ($colunas as $largura) {
        $sheetSala->getColumnDimensionByColumn($col)->setWidth($largura);
        $col++;
    }
    $sheetSala->setAutoFilter('A2:' . $ultimaColLetra . $linhaSala);
    $sheetSala->freezePane('A3');
}

// ── Activar primeira folha ───────────────────────────────────────────────────
$spreadsheet->setActiveSheetIndex(0);

// ── Enviar ficheiro ──────────────────────────────────────────────────────────
$nomeArquivo = 'Equipamentos_' . preg_replace('/\s+/', '_', $nomeEscola) . '_' . date('Ymd_Hi') . '.xlsx';

// Gravar primeiro num ficheiro temporário — evita qualquer corrupção por output prematuro
$tmpFile = tempnam(sys_get_temp_dir(), 'gei_xlsx_');
$writer = new Xlsx($spreadsheet);
$writer->save($tmpFile);

// Só agora limpar buffer e enviar headers + ficheiro limpo
while (ob_get_level()) { ob_end_clean(); }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: max-age=0');
header('Pragma: public');
header('Expires: 0');

readfile($tmpFile);
unlink($tmpFile);

$stmt && $stmt->close();
mysqli_close($db);
exit;
?>
