<?php
include('config.php');

//$result = $db->runQuery("select nome,email,tipo from utilizadores");

$sql = "select nome,email,tipo from utilizadores order by tipo,nome";
$result = mysqli_query($db,$sql);

$total=mysqli_num_rows($result);

$sql1 = "select UCASE(`COLUMN_NAME`) 
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='gsidb' 
AND `TABLE_NAME`='utilizadores'
and `COLUMN_NAME` in ('nome','email','tipo')";
$header = mysqli_query($db,$sql1);


require('fpdf/fpdf.php');
$pdf = new FPDF();



$pdf->AddPage("P","A4");

//$pdf->$header('NOME','EMAIL' , 'TIPO');


$pdf->SetFont('Arial','B',10);

$pdf->title = 'Utilizadores - Total (' . $total.')';
//$pdf->Cell(70,10,'Page Heading',1,0,'C');


$pdf->Cell(0,10,$pdf->title, 0, 1, 'C');

$pdf->SetFont('Arial','B',8);


$pdf->Ln(4);

$width_cell=array(65,65,65);
$pdf->SetFillColor(193,229,252); // Background color of header 

// Header starts /// 
$pdf->Cell($width_cell[0],10,'NOME',1,0,'C',true); // First header column 
$pdf->Cell($width_cell[1],10,'EMAIL',1,0,'C',true); // Second header column
$pdf->Cell($width_cell[2],10,'TIPO',1,0,'C',true); // Third header column 




foreach($header as $heading) {
    foreach($heading as $column_heading)
      
        $pdf->SetFillColor 	(220,220,220);
        $pdf->Cell(65,10,$column_heading,1,0, 'L', True);
    }
foreach($result as $row) {
    $pdf->SetFont('Arial','',8);	
	$pdf->Ln();
	foreach($row as $column)
		$pdf->Cell(65,10,$column,1);
}


 // Position at 1.5 cm from bottom
 //$pdf->SetY(-1);
 // Arial italic 8
 //$pdf->SetFont('Arial','I',8);
 // Page number

 //$pdf->Cell(0,10,'Página '.$pdf->PageNo(),0,0,'C');


// $pdf->Cell(0,10,"date d'impression: ".date('d/m/Y'), 0, 0, 'R');



$pdf->Output('I','Utilizadores');

?>