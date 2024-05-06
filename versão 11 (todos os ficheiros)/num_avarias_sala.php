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

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$x=base64_decode($_GET["x"]);
$idescola=base64_decode($_GET["ies"]);




if ($x==2 && ( empty($_POST['sala2']) || !isset($_POST['sala2']) 
)  
 )

{
  
    $sa=base64_decode($_GET["si"]);
}
else

if ($idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0  || !isset($x)  || !is_numeric($x) 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
|| !isset($_POST["sala2"]) || empty($_POST["sala2"])
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },40);
          </script>


<?php
}



if ($x==0)
{
$sa=$_POST["sala2"];
}
elseif ($x==1)
{
$sa=$_GET["si"];
}



$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);


$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];
 $num_ns = mysqli_num_rows($result10);
?>



<?php
     if ($num_ns==0 || $num_ne==0 )
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
               <a href="#" class="btn btn-secondary disabled">LISTAGENS >> Nº DE AVARIAS DA SALA</a>
               <div class="titlepage">
                     <h2> <?php echo($ns);?> 
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




$sql = "select ano_letivo,periodo, count(*) as c FROM avarias_reparacoes 
where id_sala=".$sa."
GROUP by ano_letivo,periodo order by ano_letivo desc, periodo LIMIT $paginationStart, $limit";
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
            <form action="num_avarias_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($idescola);?>" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Ano</th>
                    <th scope="col">Período</th>
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
                    <td width="10%" >
                    
                    <?php echo $row['periodo']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="5%" >
                    
                    <a style="color:black;" class="underlineHover" title="Ver avarias" 
                    href="avarias_sala.php?si=<?php echo base64_encode($sa);?>&&p=<?php echo base64_encode($row['periodo']);?>&&al=<?php echo base64_encode($row['ano_letivo']);?>&&ies=<?php echo base64_encode($idescola) ?> ">             
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
                    <a style="color:black;"  class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(2)."&&si=".base64_encode($sa)."&&ies=".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="num_avarias_sala.php?x=<?php echo base64_encode(2);?>&&si=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=".base64_encode(2)."&&si=". base64_encode($sa)."&&ies=". base64_encode($idescola)."&&page=". $next; } ?>">>></a>
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

        <a href="<?php echo SVRURL ?>lista">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
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