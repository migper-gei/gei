<!DOCTYPE html>
<html lang="pt">
   <head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <meta name="keywords" content="GEI">
      <meta name="description" content="Gestão do Equipamento Informático">
</head>

<body >



<?php



//fetch_user.php
header("Content-type: text/plain; charset=utf-8");
session_start();

include('database_connection.php');





if (!isset($_SESSION['user_id']))
{
?>
<script>
    window.close();
	</script>


<?php

}
else
{
	
$query = "
SELECT * FROM utilizadores
WHERE id != '".$_SESSION['user_id']."' order by sessao_ativa desc, nome
";


$statement = $connect->prepare($query);

$statement->execute();

//$result = $statement->fetchAll();

$result = $statement->get_result();



//<th width="20%">Status</td>
$output = '
<table class="table table-bordered table-striped">
	<tr>
		<th width="30%">Utilizador</td>
		<th width="20%">Estado</td>
		<th width="10%">Acção</td>
	</tr>
';





foreach($result as $row)
{
	
	//echo utf8_decode($row['nome']);
   
	$no=($row['nome']);



	if ($row['sessao_ativa']==1)
    $status = '<span class="label label-success">Online</span>';   

	else   
	$status = '<span class="label label-danger">Offline</span>';
   //<td>'.$status.'</td>
	$output .= '
	<tr>
		<td>'.$no.' '.count_unseen_message($row['id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['id'], $connect).'</td>
		<td>
		'.$status.'
		</td>
		<td><button type="button" class="btn btn-info btn-xs start_chat" data-touserid="'.$row['id'].'" data-tousername="'.$row['nome'].'">Iniciar Chat</button></td>
	</tr>
	';
}

$output .= '</table>';

echo $output;

}

?>


</body >
</html>