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


include("sessao_timeout.php");

$idescola=base64_decode($_GET['ie']);

//echo $idescola;

$sql2 = "select max(id) from escolas";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];
/*
echo $conta;
echo $idescola;
echo $_GET['ie'];



echo $_POST['eqreq'];
echo $_POST['nome'];
echo $_POST['localizacao'];
echo $_POST['departamento'];
*/



if ( !isset($_POST['eqreq']) || !isset($_POST['nome']) || !isset($_POST['localizacao']) 
|| !isset($idescola) || $idescola>$conta  || $idescola<1
|| empty($_POST['eqreq'])  || empty($_POST['nome'])  || empty($_POST['localizacao']) 
)
{

   echo ('zzz');
?>



<script>
    
function() {
//window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(0)?>";
};

</script>



<?php
}
?>



     <?php
//session_start();






//echo base64_decode($idescola);

$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);
 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> SALAS >> INSERIR</a>
               <div class="titlepage">
                    <h2>  <?php echo $rows11[0] ?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>


<?php
$sql0 = "select count(*) as cs from salas
where nome='".$_POST["nome"]."' and id_escola=$idescola";

$result = mysqli_query($db,$sql0);
$rows0 =mysqli_fetch_row($result);
$cs = $rows0[0];

//echo '<br>'.$cs;
//echo $_SESSION['tipo'];
?>




<?php
//&& $_POST['token'] == $_SESSION['token']

if (isset($_SESSION['token']) 
&& (isset($_POST['token'])) 
)
{





if ($_SESSION['tipo']==1 && $cs==0 )
{




$sql = "insert into salas (nome,localizacao,departamento,id_escola,equip_requisitavel) 
values ('".$_POST["nome"]."','".$_POST["localizacao"]."','".$_POST["departamento"]."',".$idescola.",'".$_POST["eqreq"]."')";

$result = mysqli_query($db,$sql);


mysqli_close($db);

 
?>


<script>
    
swal({
title: 'Os dados foram guardados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola) ?>";
});


</script>

<?php
}

else

{
?>


<script>
    
swal({
title: 'Não tem permissão ou o nome da sala já existe!',
//text: 'Não tem permisssão!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>salas?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola) ?>";
});


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