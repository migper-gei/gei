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

$di=$_GET["di"];
$df=$_GET["df"];

//echo($sa);

$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);



 $ns = $rows10[0];
$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];


$conta =mysqli_num_rows($result11);
//echo $conta;
if ($conta>0)
{
$ne = $rows11[0];
}
else
{
?>
<script>
      window.setTimeout(function() {
          window.location.href = '<?php echo SVRURL ?>equip';
      }, 10);
      </script>
<?php
}
?>
?>



      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>REPARAÇÕES <br> SALA <?php echo($ns);?> entre <?php echo($di);?> e <?php echo($df);?> 
                    <br><?php echo($ne);?> 
                    
                    </h2>
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


//echo($sa);



$sql = "select ar.*,s.nome, eq.nomeequi
from avarias_reparacoes ar, salas s, equipamento eq
where s.id=ar.id_sala and eq.id=ar.id_equi
and ar.id_sala=$sa 
order by ar.datareparacao desc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records

$sql3 = "select count(*) from avarias_reparacoes 
where id_sala=$sa";
$result3 = mysqli_query($db,$sql3); 
$rows = mysqli_fetch_row($result3);

$totallinhas = $rows[0];


//echo('<br>'.$totallinhas );

//$totallinhas = $_GET["qta"];

//echo('<br>'.$totallinhas );



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>





        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="ver_reparacoes_sala.php?x=1&&sa=<?php echo($sa);?>&&di=<?php echo ($di);?>&&df=<?php echo ($df);?>" method="post">
                  <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped">
            <thead>
                <tr class="table-success">
                    <th scope="col">Equipamento</th>
                    <th scope="col">Avaria</th>
                    <th scope="col">Reparação</th>
             
                     
                     
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                  
                    $em= $row['autoravaria'];
                   

                    $sql2 = "select nome from utilizadores where email='$em' ";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                   // echo ($rows2[0]); 



                    ?>
                <tr>
                    <th width="20%"  scope="row"><?php  echo $row['nomeequi'];?>
                 
                    </th>
                    <td width="35%" >
                    <label>Autor: </label> 
                    <?php echo ($rows2[0]); 
                       echo('<br>'.'Email: '); 
                    echo $row['autoravaria']; 
                       echo('<br><br>'.'Data avaria: '); 
                     echo $row['dataavaria']; 
                     echo('<br><br>'.'Descrição:'.'<br>'); 
                    echo $row['avaria']; echo('<br>');
                    ?>
                    
                                  
                    
                    <?php
                     
                    if ($row["imgavaria"] == null) 
                    {
                    echo ("");
                    }
                    
                    else {?>
                   
                  
                   <?php 
                
                   echo '<img onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                   height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).' ">';
                    
                }
                    
                   ?>
                  


                    </td>


                    <td width="35%" >
                    
               
                    <label>Data: </label>  
                    <?php
                    echo ($row["datareparacao"]);
                    ?>

                    <br /><br />
                  
                    <label>Reparação (descrição): </label>  <br>  
                        <?php
                    echo ($row["reparacao"]);
                    ?>
                    <br /><br />
                    <label>Reparado por: </label>
                    <?php
                    echo ($row["rep_efectuada_por"]);
                    ?>         
                     
                     <!--
                    <br /><br />
                    <label>Resolvido: </label>
                    <php
                    echo ($row["problema_resolvido"]);
                    ?>
                         -->

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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&sa=".$sa."&&di=$di&&df=$df&&escola= $idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="ver_reparacoes_sala.php?x=1&&di=<?php echo $di ?>&&df=<?php echo $df ?>&&sa=<?php echo($sa);?>&&escola=<?php echo($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&sa=".$sa."&&di=$di&&df=$df&&escola= $idescola&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
    

   <!--     
<form action = "<?php echo SVRURL ?>num_avarias_entredatas.php?x=1&di=<?php echo $di ?>&df=<?php echo $df ?>" method="post" >
<input title="Voltar" type=image src="<?php echo SVRURL ?>images/voltar.svg"  >
</form>
-->            


<a href="<?php echo SVRURL ?>num_avarias_entredatas.php?x=1&di=<?php echo $di ?>&df=<?php echo $df ?>&&sa=<?php echo($sa);?>&&escola=<?php echo($idescola);?>">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
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