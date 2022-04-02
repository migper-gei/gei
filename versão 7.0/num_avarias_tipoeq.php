<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>-->
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

 
  ?>

<?php
if ($_GET["x"]==0)
{
$teq=$_POST["tipoeq"];
}
elseif ($_GET["x"]==1)
{
$teq=$_GET["tipoeq"];
}



$idescola=$_GET["escola"];

//echo $sa;
//echo $idescola;


$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);



?>



<?php
     if ( $num_ne==0 || $_GET["x"]>1)
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
                     <h2>Nº de avarias por tipo equipamento<br> <?php echo($teq);?> 
                     | <?php echo($ne);?>
                    </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

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




$sql = "select ar.ano_letivo,ar.periodo, count(*) as c 
FROM avarias_reparacoes ar, equipamento eq
where eq.id=ar.id_equi and eq.tipo='".$teq."' and ar.id_escola=$idescola
GROUP by ar.ano_letivo,ar.periodo order by ar.ano_letivo desc, periodo LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<br>
<!--
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar no nº para ver as avarias/reparações.
-->

       <!-- Select dropdown -->
   <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="num_avarias_tipoeq.php?x=1&&tipoeq=<?php echo($teq);?>&&escola=<?php echo($idescola);?>" method="post">
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
                    <th scope="col" scope="col">Salas</th>
                  
                                 
                    
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
                    <td width="10%" >
                    
                    <?php echo $row['periodo']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="5%" >
                    
                  
                  
                    <?php echo $row['c'];   ?>
                  
                   
                   
                    
                 
                    </td>

                    <td width="30%"  scope="row">
                        
               <?php
                   $sql3 = "select  DISTINCT(s.nome), s.id
                   FROM avarias_reparacoes ar, equipamento eq, salas s
                   where eq.id=ar.id_equi and eq.tipo='".$teq."' and ar.id_sala=s.id
                   and ar.ano_letivo='".$row['ano_letivo']."' and ar.periodo=".$row['periodo']."
                   and ar.id_escola=$idescola
                   ";
                   $result3 = mysqli_query($db,$sql3);

                   while($row3=mysqli_fetch_array($result3))
                    { 
                        echo $row3['nome'];   
                        
                  

                   $sql4 = "select  count(*) as cc
                   FROM avarias_reparacoes ar, equipamento eq, salas s
                   where eq.id=ar.id_equi and eq.tipo='".$teq."' and ar.id_sala=s.id
                   and ar.ano_letivo='".$row['ano_letivo']."' and ar.periodo=".$row['periodo']."
                   and ar.id_sala=".$row3['id']." and ar.id_escola=$idescola";
                   $result4 = mysqli_query($db,$sql4);
                   $rows4 =mysqli_fetch_row($result4);
                   
                   echo '('.$rows4[0].')';  

                   echo (' | ');
                
                }

                   
               ?>                    
                    
                 
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
                    <a style="color:black;"  class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&tipoeq=".$teq."&&escola=$idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="num_avarias_tipoeq.php?x=1&&tipoeq=<?php echo($teq);?>&&escola=<?php echo($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&tipoeq=".$teq."&&escola=$idescola&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
        </form>


 

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