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
               <h2>Gravar outro equipamento </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                    
<br>




<?php
$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];
//echo $maxesc;

$idescola=base64_decode($_GET["ies"]);

//echo $idescola;

if (  !isset($_POST['sala']) || !isset($_POST['nomeq']) || !isset($_GET['ies'])  
   ||  empty($_POST['sala']) || empty($_POST['nomeq']) || empty($idescola)   
   || !is_numeric($idescola)  || base64_decode($_GET["ies"])>$maxesc
   )
{

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(0) ?>&ies=<?php echo base64_encode($idescola);?>';
}, 10);
</script>


<?php
} 

?>



<?php

$noe=$_POST["nomeq"];
$ids=$_POST["sala"];

   $sql2 = $db->prepare("select count(*) from outro_equipamento 
   where nomeoutro=? and id_sala=? ");
   
   $sql2->bind_param("si", $noe, $ids);
   $sql2->execute();
 
 
   $rows2 = $sql2->get_result()->fetch_row();


   $contaeq = $rows2[0];

//echo $contaeq;

 if ($contaeq==1) 
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
window.location = "<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?>";
});

</script>

<?php

}
   
//echo $contaeq;


if ($contaeq==0) 
{


 
$sql = "insert into outro_equipamento (nomeoutro,id_sala,qta,observacoes) 
values ('".$_POST["nomeq"]."',".$_POST["sala"].",'".$_POST["qta"]."','".$_POST["obs"]."'
   )";


$result = mysqli_query($db,$sql);




?>

<script>
    
    swal({
title: 'Os dados foram guardados!',
//text: 'Os dados técnicos e de rede são opcionais. Caso deseje sair clicar na seta "Voltar", no final da página.',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?> ";
});



</script>

<br><br><br><br><br><br><br><br><br><br>

<?php

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