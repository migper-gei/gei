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



if (  !empty($_GET["op"]) &&  isset($_GET["op"])   &&  !empty($_GET["ies"]) &&  isset($_GET["ies"])   )
{


    $op=$_GET["op"];
    $idescola=base64_decode($_GET["ies"]);
    

}

else
{
?>



<script>

window.setTimeout(function() {
             window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}






if ($idescola>$maxesc || $idescola<0 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
|| !isset($op)  || empty($op)  
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}



$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];



if ($op=='t')
{
$op2='Todos os anos';
}
elseif ($op=='al')
{
$op2='Ano';


}
else
{
?>
<script>
  window.setTimeout(function() {
      window.location.href = '<?php echo SVRURL ?>salas_mais_avarias_top.php?op=t&&ies=<?php echo base64_encode($idescola) ?>';
  }, 10);
  </script>
<?php

}
?>


<?php

$sql3 = "select max(ano_lectivo) from periodos";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);
?>




      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Listagens >> Salas com mais avarias (Top 10)</a>
            
               <div class="titlepage">
                     <h2> <?php echo $op2?> 
                     <br><?php echo $ne;?>
                </h2>

                     <br> 
                      <h4>
                     <a style="color:black;" class="underlineHover" title="Salas com mais avarias - top 10 (todos os anos letivos)" 
                       href="<?php echo SVRURL ?>salas_mais_avarias_top.php?op=t&&ies=<?php echo  base64_encode($idescola); ?>">
                       Todos os anos</a>  &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;   
                       <a style="color:black;" class="underlineHover" title="Salas com mais avarias - top 10 (ano letivo)" 
                       href="<?php echo SVRURL ?>salas_mais_avarias_top.php?op=al&&ies=<?php echo  base64_encode($idescola); ?>">
                        Ano: <?php echo $rows3[0]; ?></a>
                          </h4>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>
    
     

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


if ($op=='t')
{
$sql = "select  ar.id_sala,s.nome,ar.ano_letivo, count(*) as c 
FROM avarias_reparacoes ar, salas s
where ar.id_sala=s.id 
and ar.id_escola=$idescola
GROUP by ar.id_sala, ar.ano_letivo
order by c desc,ar.id_sala, ar.ano_letivo desc
LIMIT 10";
$result = mysqli_query($db,$sql);
}


if ($op=='al')
{
    $sql = "select ar.id_sala,s.nome,ar.ano_letivo, count(*) as c 
    FROM avarias_reparacoes ar, salas s
    where s.id=ar.id_sala and
    ar.ano_letivo='".$rows3[0]."' and ar.id_escola=$idescola
    GROUP by ar.id_sala, ar.ano_letivo
    order by c desc,ar.id_sala, ar.ano_letivo desc
    LIMIT 10";
    $result = mysqli_query($db,$sql);
}





// Get total records


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Clicar no nº para ver as avarias/reparações da sala.


        <br><br>



        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-primary">
                    <th scope="col">Sala</th>
                    <th scope="col">Ano</th>
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
                    <td width="20%"  scope="row"><?php echo $row['nome']; 
                    
                    ?>
                 
                    </td>
                    <td width="10%" >
                    
                    <?php echo $row['ano_letivo']; 
                    
                    ?>
                    
                 
                    </td>
                    <td width="5%" >
                    
                    <a style="color:black;" class="underlineHover" 
                    title="Ver avarias" href="num_avarias_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($row['id_sala']);?>&&ies=<?php echo base64_encode($idescola);?>  ">             
                    <h5>  
                    <?php echo $row['c'];   ?>
                    &nbsp; &nbsp;
                    <i class="fa-solid fa-magnifying-glass fa-flip-horizontal"></i>
                  
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

       


        
        </form>

        <?php include ("jquery_bootstrap.php");?>

        <br>


        <?php include ("jquery_bootstrap.php");?>
        
        <br>
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