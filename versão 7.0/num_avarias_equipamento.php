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
if ($_GET["x"]==0)
{
$eq=$_POST["equi"];
}
elseif ($_GET["x"]==1)
{
$eq=$_GET["eq"];
}

$idescola=$_GET["escola"];
//echo $eq;


$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);

$sql10 = "select nomeequi from equipamento where id=$eq";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $noeq = $rows10[0];
 $num_noeq= mysqli_num_rows($result10);
?>




<?php
     if ($num_ne==0 || $num_noeq==0 || $_GET["x"]>1)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>lista';
}, 10);
</script>


<?php

}

?>






      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Nº de avarias do equipamento<br> 
                     <?php echo($noeq);?> 
                    | <?php echo($ne);?>
                </h2>
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


$sql = "select ar.ano_letivo,ar.periodo, count(*) as c 
FROM avarias_reparacoes ar, equipamento e
where e.id=ar.id_equi 
and ar.id_equi=".$eq."
GROUP by ar.ano_letivo,ar.periodo 
order by ar.ano_letivo desc, ar.periodo LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>

<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar no nº para ver as avarias/reparações.


   <!-- Select dropdown -->
   <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="num_avarias_equipamento.php?x=1&&eq=<?php echo($eq);?>&&escola=<?php echo $idescola ?>" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div>

       
   <!-- Datatable -->
   <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Ano Letivo</th>
                    <th scope="col">Período/Semestre</th>
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
                    <td width="20%"  scope="row"><?php echo $row['ano_letivo']; 
                    
                    ?>
                 
                    </td>
                    <td width="20%" >
                    
                    <?php echo $row['periodo']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="5%" >
                    
                    <a tyle="color:black;" 
                    class="underlineHover"
                    title="Ver avarias" href="avarias_equipamento.php?eq=<?php echo($eq);?>&&per=<?php echo $row['periodo'];?>&&al=<?php echo $row['ano_letivo'];?>&&escola=<?php echo $idescola;?>   ">             
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

       


        
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&eq=".$eq."&&escola=$idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="num_avarias_equipamento.php?x=1&&eq=<?php echo($eq);?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&eq=".$eq."&&escola=$idescola&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>





        <?php include ("jquery_bootstrap.php");?>







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