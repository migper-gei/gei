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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> SALAS</a>
               <div class="titlepage">
         
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



$sql = "select id,nome from salas 
where id_escola=$id and id not in (
select  s.id
from equipamento e, salas s «, outro_equipamento oe
where s.id=e.id_sala or oe.id_sala=s.id
and s.id_escola=$id )";
$result = mysqli_query($db,$sql);




if ( $_SESSION['tipo']==1  )

{


while($row=mysqli_fetch_array($result)) 
{
    $sid=$row['id'];
//echo $sid;

                    $sql2 = "delete from salas where id=$sid ";
                    $result2 = mysqli_query($db,$sql2); 
              


}
?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($id)?>';
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