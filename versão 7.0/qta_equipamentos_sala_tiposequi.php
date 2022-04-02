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
$tipoeq=$_POST["tiposequi"];

}
elseif  ($_GET["x"]==1)
{
$tipoeq=$_GET["tiposequi"];

}

$idescola=$_GET["escola"];

$sql11 = "select nome_escola  from escolas where id=$idescola";
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
                     <h2>Quantidade de equipamento <br> Tipo <?php echo($tipoeq);?> | <?php echo($ne);?> </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        

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
//$em=$_SESSION['email'];


//echo $tipoeq;
//echo ('<br>');
//echo $idescola;

  $sql = "select tipo,count(*) as qta, s.id,s.nome
  from equipamento eq, salas s
    where eq.id_sala=s.id and eq.tipo='".$tipoeq."' and s.id_escola=$idescola
    group by s.id 
    order by qta desc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  $totallinhas=$result->num_rows;

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?>


<br>

      <!-- Select dropdown -->
      <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="qta_equipamentos_sala_tiposequi.php?x=1&tiposequi=<?php echo $tipoeq?>&&escola=<?php echo $idescola ?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Sala</th>
                    <th class="js-sort-number" scope="col">Quantidade</th>
                  
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  



                
                $c=0; $soma=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      //$c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td width="40%"  scope="row"><?php echo $row['nome']; 
                    
                    ?>
                 
                    </td>
                    <td width="45%" >
                    
                    <?php echo $row['qta']; echo('<br>'); 
                    
                    $soma=$soma+$row['qta'];

                    ?>
                 
                    </td>




               

                    
                
                </tr>
                <?php }          
                   // echo($totallinhas);
                     
                    // Calculate total pages
                    $totoalPages = ceil($totallinhas / $limit);
                
                  // echo $soma;
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&tiposequi=".$tipoeq."&&escola= $idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="qta_equipamentos_sala_tiposequi.php?x=1&&tiposequi=<?php echo($tipoeq);?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&tiposequi=".$tipoeq."&&escola= $idescola&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);

      
        ?>
                </li>
            </ul>
        </nav>
       


        
        <?php

  echo("QUANTIDADE TOTAL: ".$soma);
        ?>


 
        <br>


<br>


   

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