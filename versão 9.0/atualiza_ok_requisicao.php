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
               <div class="titlepage">
                     <h2>Atualiza requisicao</h2>
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
if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
    
<script>
window.setTimeout(function() {
   // window.location.href = '<?php echo SVRURL ?>equip';
}, 10);
</script>

<?php
}
?>


<?php

   
   //else $tmp="";

//echo($filename);

//echo($_POST["data"]);
if ($_SERVER["REQUEST_METHOD"] == "POST" )
{

    //$sql0 = "delete from requisicao 
    //where id=".$url[0]."";
   // $result0 = mysqli_query($db,$sql0);
    
    
  



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
and id_sala='".$_POST["sala"]."'
and id<>".$url[0]."
";
$result = mysqli_query($db,$sql);
$rows2 =mysqli_fetch_row($result);
$conta = $rows2[0];

//echo $conta;
//echo '<br>';

//echo  $_SESSION['email'];


//se sala, datautil e horas disponíveis (conta=0)
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

    if ($contaideq==1)
    {
    $ceq=$ceq+1;
    }


    }
    }

    //echo $ceq;
    //echo '<br>';


    if ($ceq>0)
    {
    ?>
        <script>
    
        swal({
    title: 'A requisição não foi efetuada!',
    text: 'Algum equipamento que selecionou está requisitado.  Consulte a tabela das requisições para o dia.',
    icon: 'error',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "<?php echo SVRURL ?>myrequi";
    });
    
    
    </script>


<?php
    }
    elseif($ceq==0)
    {

      
      $sql01 = "delete from equip_requisitado
      where id_req=".$url[0]."";
      
      $result01 = mysqli_query($db,$sql01);
      
      

      $sql = "update requisicao
      set datautil=STR_TO_DATE('".$_POST["datautil"]."','%Y-%m-%d'),
      horainicio='".$_POST["horainicio"]."',
      horafim='".$_POST["horafim"]."',
      id_sala=".$_POST["sala"]."
       where id=".$url[0]."";


       $result = mysqli_query($db,$sql);





foreach ($escolhas as $key => $value)
    {
   
    $ideq=$value;
 
  //echo $ideq; 

    
$sql2 = "insert into equip_requisitado (id_req,id_equip) 
values (".$url[0]." ,".$ideq.")";

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
    window.location = "<?php echo SVRURL ?>myrequi";
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
text: 'Verificar data da utilização, horas e sala.',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>myrequi";
});


</script>


<?php


}


?>



<?php
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