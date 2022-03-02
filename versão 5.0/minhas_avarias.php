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

    $op=$_GET["op"];

//echo $op;

if ($op=='t')
{
    $op2='Todas';
}
elseif ($op=='al')
{
    $op2='Ano letivo';

    
}
else
{
?>
<script>
      window.setTimeout(function() {
          window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
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
               <div class="titlepage">
                     <h2>Minhas avarias - <?php echo $op2?> </h2> 
                     <br> 
                      <h4>
                     <a style="color:black;" class="underlineHover" title="Minhas avarias (todas)" 
                       href="<?php echo SVRURL ?>myavarias?op=t">
                       Todas </a>  &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;   
                       <a style="color:black;" class="underlineHover" title="Minhas avarias (ano letivo)" 
                       href="<?php echo SVRURL ?>myavarias?op=al">
                        Ano letivo: <?php echo $rows3[0]; ?></a>
                          </h4>
                     
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
$sql = "select * from avarias_reparacoes 
where autoravaria='$em' 
order by dataavaria desc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
 $sql1 = "select count(*) from avarias_reparacoes where autoravaria='$em'";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$totallinhas = $rows[0];
//echo($totallinhas );
}



if ($op=='al')
{
    $sql0 = "select max(ano_lectivo) from periodos";
    $result0 = mysqli_query($db,$sql0); 
    $rows0 =mysqli_fetch_row($result0);
    


$sql = "select * from avarias_reparacoes where autoravaria='$em' and ano_letivo='".$rows0[0]."' 
order by dataavaria desc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$totallinhas=mysqli_num_rows($result);
//echo($totallinhas );
}



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>




<script>
function a(n) {

var n1;
n1=n;


//alert(n1);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja eliminar?",
//text: "Sala: "+s1,
type: "warning",
showCancelButton: true,
//confirmButtonColor: "#DD6B55",


confirmButtonText: "Sim",
cancelButtonText: "Não",
closeOnConfirm: false,
closeOnCancel: false

},
function(isConfirm){
if (isConfirm) {
  
      //alert(n1);
      window.setTimeout(function() {
  window.location.href = '<?php echo SVRURL ?>eliminaavaria/'+n1;
}, 10);


        
} else {
    swal("Cancelado.");
  //window.setTimeout(function() {
  //window.location.href = '<?php echo SVRURL ?>minhas_avarias.php';
//}, 10);


}

});

}

</script>




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



        <!-- Select dropdown -->
      <!-- Select dropdown -->
      <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="myavarias?op=<?php echo $op?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

      

     




        <!-- Datatable -->
        <table class="table table-striped">
            <thead>
                <tr class="table-success">
                    <th scope="col">Escola / Sala / Equipamento</th>
                    <th scope="col">Avaria</th>
                    <th scope="col">Reparação</th>
                    
                     
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                   
                    $ideq=$row['id_equi'];
                    $idsa=$row['id_sala'];
                    $idesc=$row['id_escola'];

                    $sql11 = "select nomeequi from equipamento where id=$ideq";
                    $result11 = mysqli_query($db,$sql11); 
                    $rows11 =mysqli_fetch_row($result11);
                                      
                    $neq = $rows11[0];


                    $sql12 = "select nome from salas where id=$idsa";
                    $result12 = mysqli_query($db,$sql12); 
                    $rows12 =mysqli_fetch_row($result12);
                                      
                    $nsa = $rows12[0];

                    

                    $sql13 = "select nome_escola from escolas where id=$idesc";
                    $result13 = mysqli_query($db,$sql13); 
                    $rows13 =mysqli_fetch_row($result13);
                                      
                    $noesc = $rows13[0];


                    ?>
                <tr>
                    <th width="20%"  scope="row">
                    
                        <?php echo $noesc;  echo('<br>'.'/'.'<br>'); echo $nsa;  echo('<br>'.'/'.'<br>'); echo $neq;?>
                 
                    </th>
                    <td width="35%" >
                        
                    <?php 
                     echo('Data avaria: '); 
                    echo $row['dataavaria'];
                    echo('<br><br>'.'Descrição: '); 
                    echo('<br>'); echo $row['avaria']; echo('<br>');
                    ?>
                    
                                  
                    
                    <?php
                     
                    if ($row["imgavaria"] == null) 
                    {
                    echo ("");
                    }
                    
                    else {
                   echo '<img onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
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

                     <?php
                     if ($row['datareparacao'] <> null)
                    {
                      ?>
                        <label>Data: </label>  
                        
                        <?php echo $row['datareparacao'];
                        ?>    
                     <br>  <br><label>Descrição: </label> 
                    <?php
                    echo('<br>'); echo $row['reparacao']; echo('<br>');
                   
                   
                    echo('<br><br>Reparado por: '); echo $row['rep_efectuada_por'];
                    //echo('<br>');
                    //echo('<br>Resolvido: '); echo $row['problema_resolvido']; 
                    }
                   ?>
                     </td>


                    <?php
                      if ($row['datareparacao']==null)
                      {
                     ?>
                    <td  >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualavaria/<?php echo $n ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" > </a></td>
                    <td  >
                    <?php
                      
                     ?>

                    <a onclick="a(<?php echo $n;?>);" title="Eliminar" href="<?php echo SVRURL ?>eliminaavaria/<?php echo $n ?>">
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a></td>


                    
                    <?php
                      }
                     
                    ?>

                    <?php                        
                    if ($_SESSION['tipo']==1 and $row['datareparacao']==null)
                    {
                    ?>

                    <!--
                    <td width="3%" align="center">
                    <a title="Reparar" href="repara_avaria.php?id=<?php echo $n ?>">
                    <img src="images/reparacao.png" alt="Repararr" > </a>          
                   
                    </td>   
                    -->
                    <?php
                      }
                     
                    ?>
                
                </tr>
                <?php } ?>
            </tbody>
        </table>


        <?php
include "realcelinhatabela.php";
?>



        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?op=$op&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="myavarias?op=<?php echo $op?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;"  class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?op=$op&&page=". $next; } ?>">>></a>
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
    
   


      <?php 
      
      mysqli_close($db);
      include ("footer.php");?>


   </body>
</html>