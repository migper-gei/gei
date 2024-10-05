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


     <?php include ("header.php"); ?>
     




     <?php

$dmi=base64_decode($_GET["da1"]);
$dmf=base64_decode($_GET["da2"]);
$sa=base64_decode($_GET["sa"]);
$ides=base64_decode($_GET["ides"]);



if ( !isset($_POST['pessoa']) || !isset($_POST['data']) || !isset($_POST['m']) 
|| empty($_POST['pessoa']) || empty($_POST['data']) || empty($_POST['m']) 

)
{


?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(1)?>>&&dmi=<?php echo base64_encode($da1)?>&&dmf=<?php echo base64_encode($da2)?>&&sai=<?php echo base64_encode($sa)?>&&esm=<?php echo base64_encode($idescola)?>';
}, 10);
</script>


<?php
}

?>





     <?php
//session_start();



include("sessao_timeout.php");


  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> MANUTENÇÃO >> ATUALIZAR</a>
               <div class="titlepage">
                     <h2>Atualiza manutenção 
                  
                     </h2>
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
$c= base64_decode($_GET["c"]);
$e= base64_decode($_GET["e"]);


$da1=base64_decode($_GET["da1"]);
$da2=base64_decode($_GET["da2"]);

$sa=base64_decode($_GET["sa"]);
$ides=base64_decode($_GET["ides"]);


//echo $e;

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if ($ides>$maxesc || $ides<0 
||  !isset($c)   || !is_numeric($c) ||  !isset($e)   || !is_numeric($e) 
|| !isset($ides)  || empty($ides)  || !is_numeric($ides)  
|| !isset($da1) || !isset($da2) || !isset($sa)
|| empty($da1) || empty($da2) || empty($sa)
)
{
?>

<script>
window.setTimeout(function() {
   window.location.href = '<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(1)?>&&dmi=<?php echo base64_encode($da1)?>&&dmf=<?php echo base64_encode($da2)?>&&sai=<?php echo base64_encode($sa)?>&&esm=<?php echo base64_encode($ides)?>';
}, 10);
</script>


<?php

}



//echo $_POST["obs"]; 


$sql1 = " delete from manutencao where codigo=$c";
$result1 = mysqli_query($db,$sql1);


          if(!empty($_POST['m'])) {
          
            foreach($_POST['m'] as $value){
    
               $m=$value;
              // echo($m.'<br/>');

            
            $sql2 = "insert into manutencao (id_equi,data_manutencao,pessoa,descricao,observacoes) 
          values ($e,STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),'".$_POST["pessoa"]."','$m','".$_POST["obs"]."') ";
            
          $result2 = mysqli_query($db,$sql2);
         
            }
    
        }

        

   
mysqli_close($db);
?>


<script>    
        swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',

})
.then(function() {
  window.location = "<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(1)?>&&dmi=<?php echo base64_encode($dmi)?>&&dmf=<?php echo base64_encode($dmf)?>&&sai=<?php echo base64_encode($sa)?>&&esm=<?php echo base64_encode($ides)?>";
});
</script>


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