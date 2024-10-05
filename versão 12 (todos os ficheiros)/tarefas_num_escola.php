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



?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> TAREFAS >> Nº POR INSTITUIÇÃO</a>
               <div class="titlepage">
                  
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

<?php
include("msg_bemvindo.php");
?>
    <br>

      
 



        <?php 

if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;


$sql = "select count(*) as ct, e.nome_escola ,e.id
from tarefas t, escolas e 
where t.id_escola=e.id and t.data_conclusao is null 
group by t.id_escola order by e.nome_escola
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);




$totallinhas =$result->num_rows ;//$rows[0];
//echo($totallinhas );



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>


<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar no nº para ver as tarefas.



       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>tarefas_num_escola.php" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable class="table table-striped"-->
        <table   class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-success">
                   
                    <th  scope="col">Instituição</th>
                    <th   scope="col">Nº de tarefas a realizar</th>
                 
                  
     
                     
                   
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                
                  

                    ?>
                <tr>
                <td width="30%"  scope="row"><?php echo $row['nome_escola']; ?></td>
                
                <td width="10%"  scope="row">
                <a style="color:black;" class="underlineHover" title="Ver tarefas a realizar" 
                    href="tarefas.php?x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode(0) ?>&&esi=<?php echo base64_encode($row['id'])?> ">             
                    <h5>  
                        <?php echo $row['ct']; ?>
                </h5>  
                </a>
                </td>
                    
           
                </tr>
                <?php } ?>
            </tbody>
        </table>

       

<?php
include "realcelinhatabela.php";
?>


        <!--
        <script src="js/jquery.js"></script>
    <script src="js/sort-table.js"></script>
         -->

         <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

       
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>tarefas_num_escola.php?page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
       



        <form action = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&&z=<?php echo base64_encode(0) ?>" method="post" >
<input type = "hidden" name = "sala" value = "">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

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