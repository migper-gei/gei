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
    $eqdat=$_POST["equipdata"];

    $eq = strtok($eqdat,  ' ');

     
    $dat = substr($eqdat, strpos($eqdat, " ") + 1);   

    
}
else
{

  
    $eq=$_GET["eq"];
    $dat=$_GET["dat"];
}

$idescola=$_GET["escola"];


//echo($eq);
//echo $dat;
?>




     <?php
include("sessao_timeout.php");



$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];



$sql10 = "select nomeequi from equipamento where id=$eq";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $neq = $rows10[0];


 $sql2 = "select s.nome 
 from equipamento e, salas s
where e.id_sala=s.id
 and e.id=$eq";
 $result2 = mysqli_query($db,$sql2); 
 $rows2 =mysqli_fetch_row($result2);

 $ns = $rows2[0];

  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>MANUTENÇÕES do equipamento 
                         <br> <?php echo($neq); ?>  - <?php echo($ns);?>  <?php echo($ne);?> 
                     <br> <?php echo($dat);?></h2>
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


$sql3 = "
SELECT * FROM manutencao m, equipamento e, salas s
  WHERE m.id_equi=e.id and e.id_sala=s.id
and m.id_equi=".$eq." and m.data_manutencao=STR_TO_DATE('$dat','%Y-%m-%d')
order by m.data_manutencao desc 
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql3);






// Get total records
$sql1 = "select count(*) 
FROM manutencao m, equipamento e, salas s
  WHERE m.id_equi=e.id and e.id_sala=s.id
and  m.id_equi=".$eq." and m.data_manutencao=STR_TO_DATE('$dat','%Y-%m-%d')";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$totallinhas= $rows[0];

//echo  $totallinhas;
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
            <form action="manutencoes_equip_data.php?x=1&&dat=<?php echo($dat);?>&&eq=<?php echo($eq);?>&&escola=<?php echo($idescola);?>" method="post">
                <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable js-sort-date -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success" >
      
           
                    <th scope="col">Descrição</th>
                    <th scope="col">Realizada por</th>
                    <th scope="col">Observações</th>              
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
             


                while($row2=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
               

                    ?>
                <tr>
                


                    <td width="30%" >
                    
                    <?php echo $row2['descricao']; echo('<br>'); 
                    
                    ?>
                     <td width="15%" >
                    
                    <?php echo $row2['pessoa']; echo('<br>'); 
                    
                    ?>
                    
                 
                    </td>

                    </td>
                    <td width="30%" >
                    
                    <?php echo $row2['observacoes']; 
                    
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


        <?php
include "realcelinhatabela.php";
?>
        <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

       

        
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a  style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&dat=".$dat."&&eq=".$eq."&&escola=$idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a  style="color:black;" class="page-link" href="manutencoes_equip_data.php?x=1&&dat=<?php echo($dat);?>&&eq=<?php echo($eq);?>&&escola=<?php echo($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a  style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&dat=".$dat."&&eq=".$eq."&&escola=$idescola&&page=". $next; } ?>">>></a>
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