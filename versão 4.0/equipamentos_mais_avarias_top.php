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

<?php

$op=$_GET["op"];

//echo $op;

if ($op=='t')
{
$op2='Todos os anos letivos';
}
elseif ($op=='al')
{
$op2='Ano letivo';


}
else
{
?>
<script>
  window.setTimeout(function() {
      window.location.href = '<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t';
  }, 10);
  </script>
<?php

}
?>


<?php

$sql3 = "select max(ano_lectivo) from periodos";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);
?>




      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Equipamentos com mais avarias <br>top 10 - <?php echo $op2?> </h2>

                     <br> 
                      <h4>
                     <a style="color:black;" class="underlineHover" title="Equipamentos com mais avarias - top 10 (todos os anos letivos)" 
                       href="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t">
                       Todos os anos letivos </a>  &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;   
                       <a style="color:black;" class="underlineHover" title="Equipamentos com mais avarias - top 10 (ano letivo)" 
                       href="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=al">
                        Ano letivo: <?php echo $rows3[0]; ?></a>
                          </h4>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
     

<?php 





// Database
//include('config.php');

// Set session
// session_start();
if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

//echo($paginationStart);
//echo($limit);
$em=$_SESSION['email'];


if ($op=='t')
{
$sql = "select nomeequi,ano_letivo, count(*) as c FROM avaria_reparacao
GROUP by nomeequi order by c desc
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);
}


if ($op=='al')
{
    $sql = "select nomeequi,ano_letivo, count(*) as c FROM avaria_reparacao
    where ano_letivo='".$rows3[0]."' 
    GROUP by nomeequi order by c desc
    LIMIT $paginationStart, $limit";
    $result = mysqli_query($db,$sql);
}





// Get total records


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar no nº para ver as avarias/reparações do equipamento.



        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Equipamento</th>
                    <th scope="col">Ano letivo</th>
                    <th class="js-sort-number" scope="col">Nº avarias</th>
                  
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
                $c=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      $c=$c+1;
                     // echo $c;
                      $totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td width="20%"  scope="row"><?php echo $row['nomeequi']; 
                    
                    ?>
                 
                    </td>
                    <td width="10%" >
                    
                    <?php echo $row['ano_letivo']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="5%" >
                    
                    <a style="color:black;" class="underlineHover" 
                    title="Ver avarias" href="num_avarias_equipamento.php?x=1&&eq=<?php echo $row['nomeequi'];?>   ">             
                    <h5>  
                    <?php echo $row['c'];   ?>
                  
                     </h5>
                    </a>
                    
                 
                    </td>




               

                    
                
                </tr>
                <?php }          
                   // echo($totallinhas);
                     
                    // Calculate total pages
                    $totoalPages = ceil($totallinhas / $limit);
                
                
                ?>



            </tbody>
        </table>     
                
        

        <?php
include "realcelinhatabela.php";
?>





        <img src="images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

       


        
      
                <?php
      //echo str_repeat("&nbsp;", 10);
        //echo("TOTAL: ".$totallinhas);
        ?>
        
        </form>


    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <script>
        $(document).ready(function () {
            $('#records-limit').change(function () {
                $('form').submit();
            })
        });
    </script>




<br><br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>