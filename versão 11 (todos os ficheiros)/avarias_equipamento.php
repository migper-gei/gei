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



if (  !empty($_GET["ei"]) &&  isset($_GET["ei"])   &&  !empty($_GET["ies"]) &&  isset($_GET["ies"])   
&&  !empty($_GET["al"]) &&  isset($_GET["al"]) &&  !empty($_GET["p"]) &&  isset($_GET["p"]) 

)
{

    $eq=base64_decode($_GET["ei"]);
    $al=base64_decode($_GET["al"]);
    $per=base64_decode($_GET["p"]);
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
    
    




if ($idescola>$maxesc 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
|| !isset($per)  || empty($per)  || !is_numeric($per)  
|| !isset($al)  || empty($al)  
|| !isset($eq)  || empty($eq)  || !is_numeric($eq)  
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista'
          </script>


<?php
}






$sql10 = "select nomeequi from equipamento where id=$eq";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $noeq = $rows10[0];
 $num_noeq = mysqli_num_rows($result10);


 $sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);


$sql12 = "select s.nome from salas s,equipamento e
where s.id=e.id_sala
and e.id=$eq";
$result12 = mysqli_query($db,$sql12); 
$rows12 =mysqli_fetch_row($result12);

$ns = $rows12[0];
$num_ns = mysqli_num_rows($result12);
?>



<?php
     if ($num_ns==0 || $num_ne==0 ||  $num_noeq==0 || $per>3)
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
                     <h2>Avarias do equipamento<br> <?php echo($noeq);?>
                     <br>  <?php echo($ns);?>
                     |
                     <?php echo($ne);?> </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
   

    <script> 
          
          // Function to increase image size 
          function enlargeImg(img) { 
                     img.style.transform = "scale(2.5)"; 
                     img.style.transition = 
                       "transform 0.25s ease"; 
                 } 
     
     
                 function resetImg(img) { 
                     img.style.transform = "scale(1)"; 
                     //img.style.width = "40%"; 
                     //img.style.height = "auto"; 
                     //img.style.transition = "width 0.5s ease"; 
                 } 
             </script> 





<?php 





  // Database
 // include('config.php');
  
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




  $sql = "select ar.*, s.nome 
  from avarias_reparacoes ar, salas s 
  where ar.id_sala=s.id
  and ar.id_equi=$eq and ar.ano_letivo='$al' and ar.periodo=$per and ar.id_escola=$idescola
   order by ar.dataavaria desc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  /*
   $sql1 = "select count(*) from avarias_reparacoes 
   where id_equi='$eq' and ano_letivo='$al' and periodo=$per and ar.id_escola=$idescola" ;
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  
*/
  //$totallinhas = $rows[0];
//echo($totallinhas );
$totallinhas=mysqli_num_rows($result);

  
  // Calculate total pages
  $totoalPages = ceil($totallinhas / $limit);

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?>



        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="avarias_equipamento.php?ei=<?php echo base64_encode($eq); ?>&&al=<?php echo base64_encode($al);?>&&p=<?php echo base64_encode($per);?>&&ies=<?php echo base64_encode($idescola);?>" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped">
            <thead>
                <tr class="table-success">
                 
                    <th scope="col">Ano letivo(civil) / Período (Semestre)</th>
                    <th scope="col">Avaria</th>
                    <th scope="col">Reparação</th>
             
                     
                     
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                    //$sa=$row['nome'];
                    //echo($sa);

                    

                    ?>
                <tr>
                    

                    <td width="20%"  scope="row"><?php echo $row['ano_letivo']; 
                    echo('<br>'); echo('/'); echo('<br>'); echo $row['periodo'];?>
                 
                 </td>


                    <td width="35%" >
                    <label>Data avaria: </label>
                        <?php echo $row['dataavaria'];
                    echo('<br>');
                    echo('Descrição:'.'<br>'); 
                    echo $row['avaria']; echo('<br>');
                    ?>
                    
                                  
                    
                    <?php
                     
                    if ($row["imgavaria"] == null) 
                    {
                    echo ("");
                    }
                    
                    else {?>
                   
                  
                   <?php 
                   echo '<img 
                   onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                   height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).' ">';
                    }
                    


                    


                    if ($row["video"] == null) 
                    {
                    echo ("");
                    }

                    else {
                        echo '              
                        <video 
                        onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                        width="250" height="200" alt="test" controls >
                        <source src="data:video/mp4;base64,'.base64_encode($row['video']).' " >
                     
                    </video>
        
                    ';    
                    }
                   ?>
                  


                    </td>


                    <td width="35%" >
                    <label>Data: </label> 
                        <?php echo $row['datareparacao'];
                    echo('<br>'); 
                    echo ('Descrição: ');
                    echo $row['reparacao']; echo('<br>');
                   
                    if ($row['datareparacao'] <> null)
                    {
                    echo('<br>Reparado por: '); echo $row['rep_efectuada_por'];
                   // echo('<br>Resolvido: '); echo $row['problema_resolvido']; 
                    }
                   ?>
                     </td>


                
                </tr>
                <?php } ?>
            </tbody>
        </table>




        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else {  echo "?ei=".base64_encode($eq)."&&al=".base64_encode($al)."&&p=".base64_encode($per)."&&ies=".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="avarias_equipamento.php?ei=<?php echo base64_encode($eq);?>&&al=<?php echo base64_encode($al);?>&&p=<?php echo base64_encode($per);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else { echo "?ei=".base64_encode($eq)."&&al=".base64_encode($al)."&&p=".base64_encode($per)."&&ies=".base64_encode($idescola)."&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>









<a href="<?php echo SVRURL ?>num_avarias_equipamento.php?x=<?php echo base64_encode(2);?>&&eq=<?php echo base64_encode($eq);?>&&ies=<?php echo base64_encode($idescola) ?>">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
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