<?php
  session_start();
  session_regenerate_id();
  ?>
<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS >> REQUISIÇÕES >> INSERIR</a>
               <div class="titlepage">
                     <h2>Grava requisicao</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>
                   



<?php



//echo $hfim;


//if (!empty($_POST['horafim'])) 
//isset($_POST['g'])

//if (empty($_POST['horafim']) || empty($_POST['horainicio']))


 $idesc=base64_decode($_GET["rei"]);
 $dr=base64_decode($_GET['dr']);
//echo $dr;
//echo $idesc;
//|| !isset($_POST['eqrequi[]']) || !isset($_POST['eqdisp[]'])

if (!isset($_POST['horafim']) || !isset($_POST['horainicio']) || !isset($_POST['sala'])  
|| empty($_POST['horafim']) || empty($_POST['horainicio']) || empty($_POST['sala'])
|| !isset($idesc) || !isset($dr) || empty($idesc) || empty($dr) 
 || !is_numeric($idesc) 
 || empty($_POST['eqrequi']) || !isset($_POST['eqrequi'])
)
{
    
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(1) ?>&&rei=<?php echo base64_encode($idesc) ?>&&dr=<?php echo base64_encode($dr) ?>';
}, 10);
</script>

<?php

}
else
{

?>



<?php


//echo($_POST["data"]);
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['horafim']))
{






 //echo $dr;

$a=$_POST["horainicio"];
//echo $a;

//echo '<br>';
$hi1 = strtotime($a);
$hi= date('H:i:s', $hi1);

//echo $hi;
//echo '<br>';



$sql = "SELECT count(*) as c1
from requisicao 
where datautil=STR_TO_DATE('".$_POST["datautil"]."','%Y-%m-%d') 
and horafim>'".$hi."'
and id_sala='".$_POST["sala"]."';";
$result = mysqli_query($db,$sql);
$rows2 =mysqli_fetch_row($result);
$conta = $rows2[0];

//echo $conta;
//echo '<br>';

//echo  $_SESSION['email'];


//se sala, datautil e horas disponíveis
if ($conta==0)
{

    $escolhas= $_POST['eqrequi'];

    if(isset($escolhas)) {
    //echo 'Selecionou os seguintes equipamentos para requisitar:' . '<br>' . '<br>';

     $ceq=0;
    foreach ($escolhas as $key => $value)
    {
    //echo $value . '<br>';
    $ideq=$value;
    
    //echo $ideq;


    $sql3 = "
    SELECT count(*) as c3
    from requisicao r, equip_requisitado er
    where r.id=er.id_req
    and r.datautil=STR_TO_DATE('".$_POST["datautil"]."','%Y-%m-%d')
    and r.horafim>'".$hi."'
    and er.id_equip=$ideq 
    and r.dataentrega is null;";
    
    $result3 = mysqli_query($db,$sql3);
    $rows3 =mysqli_fetch_row($result3);
    $contaideq = $rows3[0];


//echo $contaideq;

    if ($contaideq==1)
    {
    $ceq=$ceq+1;
    }


    }
    }

    //echo $ceq;
    //echo '<br>';


//echo $idesc;

    if ($ceq>0)
    {
    ?>
        <script>
    
        swal({
    title: 'A requisição não foi efetuada!',
    text: 'Algum equipamento que selecionou está requisitado. Consulte a tabela das requisições para o dia.',
    icon: 'error',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(1) ?>&&rei=<?php echo base64_encode($idesc) ?>&&dr=<?php echo base64_encode($dr);?>";
    });
    
    
    </script>


<?php
    }
    elseif($ceq==0)
    {


$sql = "insert into requisicao (email_util,datarequi,datautil,horainicio,horafim,id_sala) 
values ('".$_SESSION['email']."',
STR_TO_DATE('".$_POST["datareq"]."','%Y-%m-%d'),
STR_TO_DATE('".$_POST["datautil"]."','%Y-%m-%d'),
'".$_POST["horainicio"]."','".$_POST["horafim"]."','".$_POST["sala"]."'
)";

$result = mysqli_query($db,$sql);



$sql1 = "select max(id) from requisicao";
$result1 = mysqli_query($db,$sql1); 
$rows1 =mysqli_fetch_row($result1);

$maxid = $rows1[0];
//echo $maxid;

foreach ($escolhas as $key => $value)
    {
   
    $ideq=$value;
   

    
$sql2 = "insert into equip_requisitado (id_req,id_equip) 
values (".$maxid.",".$ideq.")";

$result2 = mysqli_query($db,$sql2);
    }

        



mysqli_close($db);

    ?>

<script>
    
        swal({
    title: 'A requisição foi efetuada!',
    //text: 'Algum equipamento já requisitado para essa data, horas e sala!',
    icon: 'success',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "<?php echo SVRURL ?>equip";
    });
    
    
    </script>


<?php
    }



}//fim do conta==0


elseif($conta==1)
{
    
?>

<script>
    
    swal({
title: 'A requisição não foi efetuada!',
text: 'Verificar data da utilização, horas e sala. Consulte a tabela das requisições para o dia.',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(1) ?>&&rei=<?php echo base64_encode($idesc) ?>&&dr=<?php echo base64_encode($dr)?>";
});


</script>


<?php


}

?>


<?php

?>



<?php
}

}





?>


<br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>