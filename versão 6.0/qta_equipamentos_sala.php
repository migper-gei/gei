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
$sa=$_POST["sala"];
$idescola=$_GET["escola"];
}
else
{
$sa=$_GET["sa"];
$idescola=$_GET["escola"];
}


$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];



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
                     <h2>Quantidade de equipamento <br> Sala <?php echo($ns);?> (<?php echo($ne);?>) </h2>
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
$em=$_SESSION['email'];


  $sql = "select tipo,count(*) as qta from equipamento 
  where id_sala=".$sa." 
  group by tipo 
  order by tipo asc LIMIT $paginationStart, $limit";
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
            <form action="qta_equipamentos_sala.php?x=1&sa=<?php echo $sa?>&&escola=<?php echo $idescola ?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Tipo</th>
                    <th class="js-sort-number" scope="col">Quantidade</th>
                  
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  



                
                $c=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      //$c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td width="40%"  scope="row"><?php echo $row['tipo']; 
                    
                    ?>
                 
                    </td>
                    <td width="45%" >
                    
                    <?php echo $row['qta']; echo('<br>'); 
                    
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
                    <a style="color:black;" class="page-link" href="qta_equipamentos_sala.php?x=1&&sa=<?php echo($sa);?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
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
       



 
        <br><br><br>


<?php
  $sql2 = "
  SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
  FROM avarias_reparacoes a,equipamento e, salas s
  WHERE a.id_equi=e.id and s.id=e.id_sala and a.id_escola=$idescola
  and a.id_sala=".$sa." 
  and a.datareparacao is null  group by e.tipo order by tipo asc";
  
  
  $result2 = mysqli_query($db,$sql2);
  
     if (mysqli_fetch_row($result2)<>null)
    {
?>




<table class="table table-sm" >
   <thead>
       <tr >
           <th scope="col">Tipo</th>
           <th  scope="col">Avariados</th>
         
                        
           
       </tr>
   </thead>
   <tbody>


<?php  
  $sql2 = "
  SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
  FROM avarias_reparacoes a,equipamento e 
  WHERE a.id_sala=".$sa." and a.id_escola=$idescola
  and a.datareparacao is null and a.id_equi=e.id group by e.tipo order by tipo asc";
   
$result2 = mysqli_query($db,$sql2);


      

while($row2=mysqli_fetch_array($result2)) { 

$tipo=$row2['ti'];
?>
<tr>
<td width="30%"  scope="row"><?php echo $row2['ti']; echo('<br>');

?>

</td>
<td width="10%" >

<?php echo $row2['c2'];  
//echo ('          ');


?>




</td>

<td width=50%>
<?php
$sql3 = "SELECT DISTINCT(e.nomeequi) as n FROM equipamento e, avarias_reparacoes a  
WHERE e.id_sala=".$sa." and a.datareparacao is null and e.tipo='".$tipo."' 
and a.id_equi=e.id
 order by e.nomeequi asc;"; 
$result3 = mysqli_query($db,$sql3);

while($row3=mysqli_fetch_array($result3)) { 
    echo $row3['n'];
    echo ('  |  ');
}
?>
</td>



</tr>
<?php }          

?>



</tbody>
</table>     

<?php
    }
    else
    {
        ?>
        <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Sem avarias. 
   <?php
 
    }


    mysqli_close($db);
?>

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