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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir Períodos / Semestres</h2>
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
if ( !isset($_POST['anoletivo']) || !isset($_POST['periodo']) || !isset($_POST['datai']) 
|| !isset($_POST['dataf']) 
|| empty($_POST['anoletivo']) || empty($_POST['periodo']) || empty($_POST['datai'])
|| empty($_POST['dataf'])
) 
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>peri';
}, 10);
</script>


<?php
}
else
{
?>







<?php
$al=$_POST["anoletivo"];
$np=$_POST["periodo"];



$sql = "select count(*) from periodos where ano_lectivo='".$al."' and num_periodo=".$np."";
$result = mysqli_query($db,$sql);

$count = mysqli_fetch_array($result);

//echo $count[0];



if ($count[0]>0)

{
?>
    <script>
    
    swal({
title: 'O ano letivo e período já existem!',
text: '<?php echo $al; ?>' + '  -  ' + '<?php echo $np; ?>',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inserirper";
});


</script>


<?php
}


elseif  ( $_SESSION['tipo']==1  )
{


$sql = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('".$_POST["anoletivo"]."',".$_POST["periodo"].",STR_TO_DATE('".$_POST["datai"]."','%Y-%m-%d'),STR_TO_DATE('".$_POST["dataf"]."','%Y-%m-%d'))";

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
window.location = "<?php echo SVRURL ?>peri";
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
window.location = "<?php echo SVRURL ?>peri";
});


</script>



<?php
}

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