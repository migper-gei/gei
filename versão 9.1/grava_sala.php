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





$sql2 = "select max(id) from escolas";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];

//echo $_GET['id'];

if ( !isset($_POST['eqreq']) || !isset($_POST['nome']) || !isset($_POST['localizacao']) 
|| !isset($_GET['ie']) || $_GET['ie']>$conta  || $_GET['ie']<1
|| empty($_POST['eqreq'])  || empty($_POST['nome'])  || empty($_POST['localizacao']) 
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>salas?x=0';
}, 140);
</script>


<?php
}
?>



     <?php
//session_start();



include("sessao_timeout.php");

$idescola=base64_decode($_GET['ie']);

base64_decode($idescola);

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
               <div class="titlepage">
                     <h2>Inserir sala <br> <?php echo $rows11[0] ?></h2>
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

if (isset($_SESSION['token']) 
&& (isset($_POST['token'])) 
&& $_POST['token'] == $_SESSION['token'])
{





if ( $_SESSION['tipo']==1)
{




$sql = "insert into salas (nome,localizacao,departamento,id_escola,equip_requisitavel) 
values ('".$_POST["nome"]."','".$_POST["localizacao"]."','".$_POST["departamento"]."',".$idescola.",'".$_POST["eqreq"]."')";

//$result = mysqli_query($db,$sql);


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
window.location = "<?php echo SVRURL ?>salas?x=1&&esi=<?php echo base64_encode($idescola) ?>";
});


</script>

<?php
}

else

{
?>


<script>
    
swal({
title: 'Não pode inserir!',
text: 'Não tem permisssão!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>salas";
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