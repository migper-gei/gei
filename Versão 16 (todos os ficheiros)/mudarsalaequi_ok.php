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
//session_start();



include("sessao_timeout.php");

$idescola=$_GET['id'];



$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);
 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Equipamentos >> Ver equipamentos da sala >> Mudar de sala </a>
               <div class="titlepage">
                
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                  <div class="welcome-section">
<?php
include("msg_bemvindo.php");
?>
     </div>
                   

<?php

//$salaant=$_GET["sala"];

//echo $salaant;


//echo $_GET["escola"];


$novasala=$_POST["sala"];

//echo $salaant;
//echo ('<br>');
//echo $_POST["sala"];

$sql = "update equipamento 
set id_sala=".$novasala."
where id=".$_GET['id']." ";

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
window.location = "<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode ($_GET['sala']) ?>&&ies=<?php echo base64_encode ($_GET['escola']) ?>";
});


</script>





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