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
      <!-- loader  -->
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
                     <h2>Eliminar sala</h2>
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

//include("config.php");
//include ("svrurl.php");

//$nome = $_GET["nome"];


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$url2 = explode('/',$_GET['url2']);

$idescola=$url2[0];
//echo $url2[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
}

$id=$url[0];


$sql2 = $db->prepare("select count(*), s.nome as sa 
from equipamento e, salas s 
where s.id=e.id_sala and
 s.id=? ");
 $sql2->bind_param("i", $id);
 $sql2->execute();

 $rows = $sql2->get_result()->fetch_row();


  
//echo $rows[0];
//echo $rows[1];

//echo $_SESSION['tipo'];

if ($rows[0]==0 && $_SESSION['tipo']==1  )


{

$sql = $db->prepare("delete from salas where id=?");
$sql->bind_param("i", $id);

$sql->execute();




?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola)?>';
          },10);
          </script>

<?php
}
else
{
?>
    <script>

    swal({
title: 'Não pode eliminar a sala '+'<?php echo $rows[1]?>!',
text: 'A sala tem equipamento associado ou não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>sala";
});



</script>

<?php
}

?>

          <?php
          mysqli_close($db);
          ?>


<br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>