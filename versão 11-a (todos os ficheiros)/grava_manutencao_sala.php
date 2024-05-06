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

$sa=base64_decode($_GET["si"]);
$idescola=base64_decode($_GET["esm"]);

if ( !isset($_POST['pessoa']) || !isset($_POST['data']) || !isset($_POST['eq']) || !isset($_POST['m']) 
|| empty($_POST['pessoa']) || empty($_POST['data']) || empty($_POST['eq']) || empty($_POST['m']) 
|| !isset($sa)  || !isset($idescola)   
|| empty($sa)  || empty($idescola)  
)
{


//inserirmanut?esm=<?php echo base64_encode($idescola);?>
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>


<?php
}

?>





     <?php
//session_start();



include("sessao_timeout.php");





$sql2 = "select nome from salas where id=".$sa." ";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$ns=$rows[0];

$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);
$ne = $rows11[0];
 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">MANUTENÇÕES >> INSERIR</a>
               <div class="titlepage">
                     <h2>
                     (<?php echo $ns ?>)
                  <br><?php echo $ne ?>
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
/*
if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
$id =$url[0];
//echo $id;


$sa=$_GET["sa"];


$sql2 = "select nome from salas where id=".$id." ";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$sa=$rows[0];

*/

/*
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
 */





    if(!empty($_POST['eq'])) {




        //echo('aaa');
        foreach($_POST['eq'] as $value){

           $ne=$value;
          //  echo "value : ".$value.'<br/>';

          //echo($ne.'<br/>');



          if(!empty($_POST['m'])) {
          
            foreach($_POST['m'] as $value){
    
               $m=$value;
              // echo($m.'<br/>');

            
            $sql2 = "insert into manutencao (id_equi,data_manutencao,pessoa,descricao,observacoes) 
          values ('$ne',STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),'".$_POST["pessoa"]."','$m','".$_POST["obs"]."') ";
            
          $result2 = mysqli_query($db,$sql2);
         
            }
    
        }

        }

    }

   

//header("Refresh:0;url=manutencao.php");
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
window.location = "<?php echo SVRURL ?>manut";
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