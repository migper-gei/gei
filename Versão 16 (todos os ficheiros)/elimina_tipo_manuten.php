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
                     <h2>Eliminar tipos manutenção</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>


<?php



//$nome = $_GET["nome"];



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
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}

$id=$url[0];
$sql3 =$db->prepare( "select count(*) from tipos_manutencao where id=?");
$sql3->bind_param("i", $id);
$sql3->execute();

$rows3 = $sql3->get_result()->fetch_row();

//$rows3 =mysqli_fetch_row($result3);

//echo $rows3[0];


//echo $rows[0];
//echo $rows[1];

//echo $_SESSION['tipo'];

if ($rows3[0]<>0 && $_SESSION['tipo']==1)


{
$id=$url[0];
$sql = $db->prepare("delete from tipos_manutencao where id=?");
$sql->bind_param("i", $id);
$sql->execute();


mysqli_close($db);
?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tiposmanuten';
          },10);
          </script>


<?php
}
else
{
?>
    

    <script>

swal({
title: 'Não pode eliminar!',
text: 'O tipo tem manutenção associada ou não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>tiposmanuten";
});



</script>


<?php
}

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