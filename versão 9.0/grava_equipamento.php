<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
               <h2>Gravar equipamento </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                    
<br>




<?php
if ( !isset($_POST['nome']) || !isset($_POST['localizacao']) || !isset($_POST['eqreq']) )
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 140);
</script>


<?php
}
?>





<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {

$idescola=$_GET["escola"];

//echo $idescola;

if ( $_SESSION['tipo']==1)
{

//echo $_POST["sala"];


   $sql2 = "select count(*) from equipamento where nomeequi='".$_POST["nomeq"]."' and id_sala='".$_POST["sala"]."'";
   $result2 = mysqli_query($db,$sql2); 
   $rows2 =mysqli_fetch_row($result2);
   
   $contaeq = $rows2[0];

 if  ($contaeq==1) 
{
?>

   
<script>
    
swal({
title: 'O nome do equipamento já existe!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inserirequip?x=1&&escola=<?php echo $idescola ?>";
});

</script>

<?php

}
else
{

$dt=$_POST["datacompra"];
//echo $dt;


if ($dt<>"")
{
  
$sql = "insert into equipamento (nomeequi,id_sala,tipo,marca_modelo,numserie,data_compra) 
values ('".$_POST["nomeq"]."',".$_POST["sala"].",'".$_POST["tipoeq"]."','".$_POST["marcamod"]."', 
'".$_POST["nserie"]."',STR_TO_DATE('".$dt."','%Y-%m-%d') )";
}
else
{
   $sql = "insert into equipamento (nomeequi,id_sala,tipo,marca_modelo,numserie) 
   values ('".$_POST["nomeq"]."',".$_POST["sala"].",'".$_POST["tipoeq"]."','".$_POST["marcamod"]."', 
   '".$_POST["nserie"]."')";
}

$result = mysqli_query($db,$sql);


//echo ("aa");
$sql1 = "select max(id) from equipamento";
$result1 = mysqli_query($db,$sql1);
$rows2 =mysqli_fetch_row($result1);

$maxid = $rows2[0];




mysqli_close($db);
?>

<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Os dados técnicos e de rede são opcionais. Caso deseje sair clicar na seta "Voltar", no final da página.',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>dados_tec_redes.php?z=1&id=<?php echo ($maxid);?>&&escola=<?php echo $idescola ?>";
});



</script>

<br><br><br><br><br><br><br><br><br><br>

<?php
}

}
else

{
?>
    <script>
window.setTimeout(function() {
    window.location.href = 'equip';
}, 10);
</script>


<?php

}



}

?>




<br><br><br><br><br><br>


                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>