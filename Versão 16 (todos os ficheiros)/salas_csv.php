
<?php
session_start();


// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=salas.csv');
include ("config.php");
// create a file pointer connected to the output stream
ob_clean();
$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, array('Nome', 'Localização', 'Departamento', 'Equipamento_requisitável'));

$id=base64_decode($_GET['id']);
$sql ="select nome,localizacao,departamento,equip_requisitavel from salas where id_escola=$id order by nome";
$result = mysqli_query($db,$sql);

// loop over the rows, outputting them
while($row=mysqli_fetch_assoc($result))

fputcsv($output, $row);
?>