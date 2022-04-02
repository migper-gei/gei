<?php
require_once("fpdf/fpdf.php");

include('config.php');

//include 'barcode128.php';


$id_escola=$_GET['escola'];
$sa=$_POST['sala'];




$sql = "SELECT e.nomeequi, s.nome, es.nome_escola, l.logotipo
FROM equipamento e, salas s, escolas es, logotipo l
WHERE e.id_sala=s.id and s.id_escola=es.id and es.id=$id_escola and s.id=$sa
order by  s.nome";
$result = mysqli_query($db,$sql);





// Variaveis de Tamanho

$mesq = "10"; // Margem Esquerda (mm)
$mdir = "10"; // Margem Direita (mm)
$msup = "12"; // Margem Superior (mm)
$leti = "70"; // Largura da Etiqueta (mm)
$aeti = "27"; // Altura da Etiqueta (mm)
$ehet = "3";//"3.2"; // Espaço horizontal entre as Etiquetas (mm)


$pdf = new FPDF();
$pdf->AddPage("P","A4");

//$pdf->Open(); // inicia documento

//$pdf->AddPage(); // adiciona a primeira pagina
//$pdf->SetMargins('5','12.7'); // Define as margens do documento
$pdf->SetAuthor(""); // Define o autor
$pdf->SetFont('helvetica',"",8); // Define o tipo e tamanho da letra
$pdf->SetDisplayMode('fullpage'); //Fullpage

$coluna = 0;

$linha = 0;



while($dados = mysqli_fetch_array($result)) {

$nomeequi = $dados["nomeequi"];
$nome = $dados["nome"];
$nome_escola = $dados["nome_escola"];

$bc=$nomeequi ." | "  .$nome." | "  .$nome_escola;

//echo $barcode;

if($linha == "10") {
$pdf->AddPage();
$linha = 0;
}

if($coluna == 3) { // Se for a terceira coluna
$coluna = 0; // $coluna volta para o valor inicial
$linha = $linha +1; // $linha é igual ela mesma +1
}

if($linha == "10") { // Se for a última linha da página
$pdf->AddPage(); // Adiciona uma nova página
$linha = 0; // $linha volta ao seu valor inicial
}

$posicaoV = $linha*$aeti;
$posicaoH = $coluna*$leti;

if($coluna == "0") { // Se a coluna for 0
$somaH = $mesq; // Soma Horizontal é apenas a margem da esquerda inicial
} else { // Senão
$somaH = $mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
}

if($linha =="0") { // Se a linha for 0
$somaV = $msup; // Soma Vertical é apenas a margem superior inicial
} else { // Senão
$somaV = $msup+$posicaoV; // Soma Vertical é a margem superior inicial mais a posiçãoV
}

if ($dados['logotipo']<>"")
{

  // prepare a base64 encoded "data url"
  $pic = 'data:image/jpeg;base64,' . base64_encode($dados['logotipo']);
  
  // extract dimensions from image
  //$info = getimagesize($pic);
  
$pdf->Image($pic, $somaH, $somaV, 15, -300,'png'); // Imprime o logotipo
}


$pdf->Text($somaH,$somaV+12,$nomeequi); // Imprime o nome do equipamento
$pdf->Text($somaH,$somaV+16,$nome); // Imprime o nome da sala
$pdf->Text($somaH,$somaV+20,$nome_escola); // Imprime o nome da escola



$coluna = $coluna+1;
}

$pdf->Output();

?>