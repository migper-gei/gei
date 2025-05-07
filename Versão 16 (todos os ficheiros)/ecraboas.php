<?php
  session_start();
  session_regenerate_id();
  ?>

<!DOCTYPE html>
<html lang="pt">
   <head>




<?php 

include ("head.php");?>


   </head>







   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     

      

           <div  class="about">
         <div class="container">
      
                   <!--
         <h3 class="quick-access-title">
         Links de acesso rápido </h3>-->
         <a href="#" class="btn btn-secondary disabled"> Links de acesso rápido </a>

               <div class="row">

            


               <div class="col-md-10 offset-md-2">
              <!--
               <a href="#" class="btn btn-secondary disabled">LINKS DE ACESSO RÁPIDO</a>
      -->
           
              <div class="titlepage">
                    
                  </div>
   

<?php



include("sessao_timeout.php");


 //include("msg_bemvindo.php");
      //echo('<br>');
?>

     
    <!-- Welcome Section -->
    <div class="welcome-section">
               
                        <div>
                      
                            <?php include("msg_bemvindo.php"); ?>
                        </div>
               
                </div>
  
         
                <div class="action-section">
  <?php
      include("texto_gei.php");
               
   ?>  
   </div>


<?php
$hoje = date('d/m/Y');
//echo $hoje;

$da=date("Y");
//echo $da;


$sql3 = "
SELECT count(*) FROM periodos WHERE 
YEAR(data_fim) = $da; ";

$result3 = mysqli_query($db,$sql3);
$rows3 =mysqli_fetch_row($result3);
$contap = $rows3[0];

//echo $contap;
$datai='01-01-'."$da";
$dataf='31-12-'."$da";
//echo $datai;
//echo '<br>';
//echo $dataf;

//$datai=;

if ($contap==0)
{
   $sql = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
   values ('".$da."',1,STR_TO_DATE('".$datai."','%d-%m-%Y'),STR_TO_DATE('".$dataf."','%d-%m-%Y'))";
   
   $result = mysqli_query($db,$sql);


}

?>



  



             
            </div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>


      
</body>
</html>
