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
                     <h2>Eliminar todos os equipamentos</h2>
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

//echo $id;



$sql = "SELECT eq.id as eid,eq.nomeequi,s.id,s.nome 
FROM equipamento eq, salas s
WHERE eq.id_sala=s.id
and s.id_escola=$id;";
$result = mysqli_query($db,$sql);




if ( $_SESSION['tipo']==1  )

{


while($row=mysqli_fetch_array($result)) 
{
    $sid=$row['eid'];
//echo $sid;
//echo ('<br>');

                    $sql2 = "delete from equipamento where id=$sid ";
                    $result2 = mysqli_query($db,$sql2); 
              

                    $sql2a = "delete from avarias_reparacoes where id_equi=$sid ";
                    $result2a = mysqli_query($db,$sql2a); 

              

}
?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>

<?php
}
else
{
?>
    <script>

    swal({
title: 'Não pode eliminar o equipamento informático! ',
text: 'Não tem permissão.',
icon: 'error',

})
.then(function() {
window.location = "<?php echo SVRURL ?>equip";
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