
<?php

include ("config.php");

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=equipamentos.csv');

// create a file pointer connected to the output stream
ob_clean();
$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, array('Nome', 'Nº_série', 'Sala', 'Marca/Modelo','Tipo',
'Processador','Memória','disco','Placa_gráfica','Placa_som','Monitor','Teclado',
'Rato', 'Colunas','CD/DVD','Dominio','IP', 'Máscara_rede','Gateway','DNS_principal',
'DNS_alternativo','Sala temporária','Data compra',
'Escola_digital','Nº_inv_dgest','Fornecedor','NIF_pessoa','Nº_RMA',
'Nome escola'
));

$id=base64_decode($_GET['id']);
$sql ="
sELECT  eq.nomeequi,eq.numserie,s.nome,eq.marca_modelo,eq.tipo,
eq.processador,eq.memoria,eq.disco,eq.placagrafica,eq.placasom,
eq.monitor,eq.teclado,eq.rato, eq.colunas,eq.cd_dvd,
eq.dominio,eq.ip,eq.mascara_rede,eq.gateway,eq.dns_principal,eq.dns_alternativo,
eq.sala_temp,eq.data_compra,
eq.escola_digital,eq.num_inv_dgest, eq.fornecedor,eq.nif_pessoa,eq.num_rma,
e.nome_escola
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id
and id_escola=$id
order by e.nome_escola,s.nome,eq.nomeequi";
 
$result = mysqli_query($db,$sql);

// loop over the rows, outputting them
while($row=mysqli_fetch_assoc($result))

fputcsv($output, $row);
?>