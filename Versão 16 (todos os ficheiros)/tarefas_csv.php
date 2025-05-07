<?php
session_start();


// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tarefas.csv');
include ("config.php");
// create a file pointer connected to the output stream
ob_clean();
$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, array('Sala', 'Descrição', 'Urgência', 'Criado_por', 'Concluído_por', 'Data_conclusão'
));

$id=base64_decode($_GET['id']);
$sql ="
sELECT  s.nome,t.descricao,t.urgencia,t.criado_por,t.concluido_por,t.data_conclusao
FROM escolas e, salas s, tarefas t
where e.id=t.id_escola and t.id_sala=s.id
and t.id_escola=$id
order by s.nome;";
 
$result = mysqli_query($db,$sql);

// loop over the rows, outputting them
while($row=mysqli_fetch_assoc($result))

fputcsv($output, $row);
?>