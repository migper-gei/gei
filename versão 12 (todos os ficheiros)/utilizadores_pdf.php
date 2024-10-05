<?php  session_start();

include('config.php');

//$result = $db->runQuery("select nome,email,tipo from utilizadores");

$sql = "select nome,email from utilizadores order by nome";
$result = mysqli_query($db,$sql);

$total=mysqli_num_rows($result);

/*
$sql1 = "select UCASE(`COLUMN_NAME`) 
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='geidb' 
AND `TABLE_NAME`='utilizadores'
and `COLUMN_NAME` in ('nome','email')";
*/
$sql1 = "select nome,email
FROM utilizadores order by nome";

$header = mysqli_query($db,$sql1);



require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage("P","A4");
//$pdf->$header('NOME','EMAIL' , 'TIPO');
$pdf->SetFont('Arial','B',10);



$sql2 = " select logotipo from logotipo";
$result2 = mysqli_query($db,$sql2);
$row2=mysqli_fetch_array($result2);

if ($row2['logotipo']<>"")
{


    
    // prepare a base64 encoded "data url"
    $pic = 'data:image/jpeg;base64,' . base64_encode($row2['logotipo']);
    // extract dimensions from image
    $info = getimagesize($pic);
    
    $pdf->Image($pic, 10, 10, 20, -300,'png');
   
    //$pdf->Image($pic, 10, 10, $info[0], $info[1], 'png');

    
}
//$pdf->Image('images/logo.png',10,10,-300);


$pdf->title = 'UTILIZADORES - Total (' . $total.')';

//$pdf->Cell(70,10,'Page Heading',1,0,'C');
$pdf->Cell(0,10,$pdf->title, 0, 1, 'C');
$pdf->SetFont('Arial','B',8);
$pdf->Ln(8);

$width_cell=array(90,90);
$pdf->SetFillColor(193,229,252); // Background color of header 
// Header starts /// 
$pdf->Cell($width_cell[0],10,'NOME',1,0,'C',true); // First header column 
$pdf->Cell($width_cell[1],10,'EMAIL',1,0,'C',true); // Second header column
//$pdf->Cell($width_cell[2],10,'TIPO (1-admin    2-utilizador    3-reparador)',1,0,'C',true); // Third header column 



foreach($header as $heading) {
    foreach($heading as $column_heading)
              $pdf->SetFillColor 	(220,220,220);
             //$pdf->Cell(80,10,$column_heading,1,0, 'L', True);
    }
foreach($result as $row) {
    $pdf->SetFont('Arial','',8);	
	$pdf->Ln();
foreach($row as $column)
		$pdf->Cell(90,10,utf8_decode($column),1);      
}

mysqli_close($db);

$pdf->Output('I','Utilizadores');
//$pdf->Output('F', 'Utilizadore.pdf');



?>