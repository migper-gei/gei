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

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];



if ($_GET["escola"]>$maxesc || $_GET["x"]>1)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>


<?php

}





if ($_GET["x"]==0)
{
    $di=$_POST['datami'];
    $df=$_POST['datamf'];
    $sa=$_POST["sala"];
}
elseif ($_GET["x"]==1)
{

    $di=$_GET["dmi"];
    $df=$_GET["dmf"];
    $sa=$_GET["sa"];
}




if ( !isset($di) || !isset($df) || !isset($sa))
{

 ?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>manut';
          },40);
          </script>


<?php
}




$idescola=$_GET["escola"];

//echo($sa);
?>


     <?php
//session_start();



include("sessao_timeout.php");


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
     if ($num_ns==0 || $num_ne==0 || $_GET["x"]>1)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
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
                     <h2>Manutenções >> MANUTENÇÕES da sala 
                         <br><?php echo($ns); ?> | <?php echo($ne);?>
                     <br> entre <?php echo date('d/m/Y',strtotime($di));?> e <?php echo date('d/m/Y',strtotime($df));?></h2>
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
//$em=$_SESSION['email'];





  $sql = "
  SELECT * FROM manutencao m, equipamento e, salas s
  WHERE m.id_equi=e.id and e.id_sala=s.id
and e.id_sala=".$sa." and m.data_manutencao BETWEEN 
STR_TO_DATE('$di','%Y-%m-%d') AND
STR_TO_DATE('$df','%Y-%m-%d')
 order by m.data_manutencao desc, e.nomeequi asc
  LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  

  // Get total records
  $sql1 = "select count(*) FROM manutencao m, equipamento e, salas s
  WHERE m.id_equi=e.id and e.id_sala=s.id
 and  e.id_sala=".$sa." and m.data_manutencao BETWEEN 
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
            <form action="manutencoes_sala_entredatas.php?x=1&&dmi=<?php echo($di);?>&&dmf=<?php echo($df);?>&&sa=<?php echo($sa);?>&&escola=<?php echo($idescola);?>" method="post">
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
                    <th scope="col">Observações</th>      
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
             


                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                   
                   
               

                    ?>
                <tr>
                    <td width="20%"  scope="row">
                    
                    <?php echo $row['nomeequi'];?>
   
                 
                    </td>

                    <td width="15%"  scope="row">
                    
                    <?php echo $row['data_manutencao']; ?>
   
                 
                    </td>

                    <td width="30%" >
                    
                    <?php echo $row['descricao']; 
                    
                    ?>
                     <td width="15%" >
                    
                    <?php echo $row['pessoa']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="30%" >
                    
                    <?php echo $row['observacoes']; 
                    
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
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&dmi=".$di."&&dmf=".$df."&&sa=".$sa."&&escola=$idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="manutencoes_sala_entredatas.php?x=1&&dmi=<?php echo($di);?>&&dmf=<?php echo($df);?>&&sa=<?php echo($sa);?>&&escola=<?php echo($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&dmi=".$di."&&dmf=".$df."&&sa=".$sa."&&escola=$idescola&&page=". $next; } ?>">>></a>
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



        <a href="<?php echo SVRURL ?>manut">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>








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