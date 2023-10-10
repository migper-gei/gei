
<?php

include ("config.php");

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=utilizadores.csv');

// create a file pointer connected to the output stream
ob_clean();
$output = fopen('php://output', 'w');

// output the column headings
//fputcsv($output, array('Nome', 'Email', 'Tipo (1-admin    2-utilizador    3-reparador)'));
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, array('Nome', 'Email'));


$sql ="select nome,email from utilizadores order by nome";
$result = mysqli_query($db,$sql);

// loop over the rows, outputting them
while($row=mysqli_fetch_assoc($result))

fputcsv($output, $row);
?>