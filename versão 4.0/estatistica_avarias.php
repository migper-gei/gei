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
                     <h2>Estatística de avarias <br> últimos 5 anos letivos</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
              
                        

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


$sql = "select  ano_letivo,periodo, count(*) as cp FROM avaria_reparacao
GROUP by ano_letivo,periodo
order by ano_letivo desc, periodo
LIMIT 5";
//LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);





// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>




       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="estatistica_avarias.php" method="post">
                <?php include("num_linhas.php");?>
            </form>
        </div>



        <!-- Datatable style="width:60%"-->
        <table class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-success">
                    <th scope="col">Ano Letivo</th>
                    <th scope="col">Período/Semestre</th>
                    <th scope="col">Nº avarias</th>
                 
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
              
  // Get total records
   //$sql1 = "select count(*) as c FROM avaria_reparacao GROUP by ano_letivo";
   //$result1 = mysqli_query($db,$sql1); 
   //$rows =mysqli_fetch_row($result1);
   
 
   //$totallinhas = $rows[0];
                
   $conta=0;

   while($row=mysqli_fetch_array($result)) { 
                          
                   
                   
               $conta=$conta+1;

                    ?>
                <tr>
                    <th width="20%"  scope="row">
                   
                    <?php echo $row['ano_letivo']; echo('<br><br>');?>
                    </a>
                 
                    </th>
                    <td width="10%" >
                    
                    <?php echo $row['periodo']; echo('<br>'); 
                    
                    ?>
                    
                 
                    </td>
                    <td width="10%" >
                    
                    <?php echo $row['cp']; echo('<br>'); 
                    
                    ?>
                    
                 
                    </td>

                </tr>
                <?php }          
               
                    $totallinhas = $conta;

                    // Calculate total pages
                    $totoalPages = ceil($totallinhas / $limit);
                
                
                ?>



            </tbody>
        </table>     
                
        

        <img src="images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.


        
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="estatistica_avarias.php?page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>

    




<br>





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




<br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>