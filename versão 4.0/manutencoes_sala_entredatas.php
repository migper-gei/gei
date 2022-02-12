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

if ($_GET["x"]==0)
{
    $di=$_POST['datami'];
    $df=$_POST['datamf'];
    $sa=$_POST["sala"];
}
else
{

    $di=$_GET["dmi"];
    $df=$_GET["dmf"];
    $sa=$_GET["sa"];
}



//echo($sa);
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
               <div class="titlepage">
                     <h2>MANUTENÇÕES da sala <?php echo($sa); ?> <br> entre <?php echo($di);?> e <?php echo($df);?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>





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


  $sql = "
  SELECT * FROM manutencao 
WHERE sala='".$sa."' and data_manutencao BETWEEN 
STR_TO_DATE('$di','%Y-%m-%d') AND
STR_TO_DATE('$df','%Y-%m-%d')
 order by data_manutencao desc, nomeequi asc
  LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);



  

  // Get total records
  $sql1 = "select count(*) FROM manutencao 
  WHERE sala='".$sa."' and data_manutencao BETWEEN 
  STR_TO_DATE('$di','%Y-%m-%d') AND
  STR_TO_DATE('$df','%Y-%m-%d')";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  

  $totallinhas= $rows[0];


 // $totallinhas = mysqli_num_rows($result);
//echo($totallinhas);


  
  // Calculate total pages
  $totoalPages = ceil($totallinhas / $limit);


  
  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?>


        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="manutencoes_sala_entredatas.php?x=1&&dmi=<?php echo($di);?>&&dmf=<?php echo($df);?>&&sa=<?php echo($sa);?>" method="post">
                <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable js-sort-date -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success" >
                    <th scope="col">Equipamento</th>
                    <th  scope="col">Data</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Realizada por</th>          
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
             


                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                   
                   
               

                    ?>
                <tr>
                    <td width="30%"  scope="row">
                    
                    <?php echo $row['nomeequi'];?>
   
                 
                    </td>

                    <td width="15%"  scope="row">
                    
                    <?php echo $row['data_manutencao']; ?>
   
                 
                    </td>

                    <td width="35%" >
                    
                    <?php echo $row['descricao']; 
                    
                    ?>
                     <td width="20%" >
                    
                    <?php echo $row['pessoa']; 
                    
                    ?>
                    
                 
                    </td>




               

                    
                
                </tr>
                <?php }          
                   
                    // Calculate total pages
                    //$totoalPages = ceil($totallinhas / $limit);
                
                  // echo($totoalPages);

                ?>



            </tbody>
        </table>     


                
        <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

       

        
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&dmi=".$di."&&dmf=".$df."&&sa=".$sa."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="manutencoes_sala_entredatas.php?x=1&&dmi=<?php echo($di);?>&&dmf=<?php echo($df);?>&&sa=<?php echo($sa);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&dmi=".$di."&&dmf=".$df."&&sa=".$sa."&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
       


                
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