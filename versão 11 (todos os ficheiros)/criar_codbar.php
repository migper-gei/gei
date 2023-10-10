<html>
<head>
<style>
p.inline {display: inline-block;}
span { font-size: 18px;}
</style>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */

    }
</style>
</head>

<!-- onload="window.print();"-->
<body >
	<div style="margin-left: 5%">
		<?php
		include 'barcode128.php';
		


		
include('config.php');

//include 'barcode128.php';

$id_escola=base64_decode($_GET["escola"]);

$sa=$_POST['salacod'];


$sql = "SELECT e.nomeequi, s.nome, es.nome_escola, l.logotipo
FROM equipamento e, salas s, escolas es, logotipo l
WHERE e.id_sala=s.id and s.id_escola=es.id and es.id=$id_escola and s.id=$sa
order by s.nome";
$result = mysqli_query($db,$sql);

echo ('<br>');

	while($dados = mysqli_fetch_array($result)) {

             $bc=$dados['nomeequi']. ' | '.$dados['nome']; // . ' | '.$dados['nome_escola'];

            //echo $bc;
            
			echo "<p class='inline'><span ><b>".bar128(stripcslashes($bc))." </b><span></p>&nbsp&nbsp&nbsp&nbsp";
		    echo ('<br>');echo ('<br>');echo ('<br>');
	        
		}

		?>
	</div>
</body>
</html>