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



<?php
if ($_GET["x"]==0)
{
    $di=$_POST['datai'];
    $df=$_POST['dataf'];
    $idescola=$_GET["escola"];
}
else
{

    $di=$_GET["di"];
    $df=$_GET["df"];
    $idescola=$_GET["escola"];
}

$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];

?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Nº de avarias <br> entre <?php echo($di);?> e <?php echo($df);?>
                    <br><?php echo($ne);?>
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


$sql = "
SELECT id_sala,COUNT(*) as qta FROM avarias_reparacoes 
WHERE dataavaria BETWEEN 
STR_TO_DATE('$di','%Y-%m-%d') AND
STR_TO_DATE('$df','%Y-%m-%d')
group by id_sala 
order by id_sala
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>

<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar na sala para ver as avarias/reparações.

       

        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="num_avarias_entredatas.php?x=1&&di=<?php echo($di);?>&&df=<?php echo($df);?>&&escola=<?php echo($idescola);?>" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success" >
                    <th scope="col">Sala</th>
                    <th class="js-sort-number" scope="col">Nº avarias</th>
                    <th class="js-sort-number" scope="col">Nº reparações</th>
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
             

                $c=0;
                $totallinhas = $c;

                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      $c=$c+1;
                      $totallinhas = $c;
                   

                    $sa=$row['id_sala'];
                   
                      $sql3 = "select count(*) FROM avarias_reparacoes 
                      WHERE datareparacao BETWEEN 
                      STR_TO_DATE('$di','%Y-%m-%d') AND
                      STR_TO_DATE('$df','%Y-%m-%d') and
                      id_sala=".$sa."
                      ";

                      $result3 = mysqli_query($db,$sql3);
                      $rows =mysqli_fetch_row($result3);
                      
                    
                      $contarep = $rows[0];
                   
                     //echo($contarep);

                   
               

                    ?>
                <tr>
                    <td width="40%"  scope="row">
                
                    <a style="color:black;" class="underlineHover"
                     href="ver_reparacoes_sala.php?di=<?php echo ($di);?>&&df=<?php echo ($df);?>&&x=1&&sa=<?php echo $sa?>&&escola=<?php echo $idescola?>" title="Ver reparações da sala">
             
<?php
                     $sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];

?>

                 <h5>       <?php echo $ns; echo('<br>');?>
               </h5>
                   </a> 

                   
                    </td>
                    <td width="25%" >
                    <?php echo $row['qta']; echo('<br>'); 
                     
                    ?>
                   
                 
                    </td>

                    <td width="25%" >
                  
                    <?php echo $contarep; echo('<br>'); 
                    
                    ?>
                 

                    <!--
                          <a href="ver_reparacoes_sala.php?qta=<?php echo ($contarep);?>&&di=<?php echo ($di);?>&&df=<?php echo ($df);?>&&x=1&&sa=<?php echo $row['sala']?>" title="Ver reparações da sala">
                   
                     </a>
                    -->
                    </td>



               

                    
                
                </tr>
                <?php }          
                    //echo($totallinhas);
                     
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&sa=".$sa."&&escola= $idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="num_avarias_entredatas.php?x=1&&sa=<?php echo($sa);?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&sa=".$sa."&&escola= $idescola&&page=". $next; } ?>">>></a>
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