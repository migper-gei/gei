
<?php

include ("config.php");

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tipos_manutenvcao.csv');

// create a file pointer connected to the output stream
ob_clean();
$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, array('Nome'));


$sql ="select nome from tipos_manutencao order by nome";
$result = mysqli_query($db,$sql);

// loop over the rows, outputting them
while($row=mysqli_fetch_assoc($result))

fputcsv($output, $row);
?>