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

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$x=base64_decode($_GET["x"]);
$idescola=base64_decode($_GET["esm"]);

//echo $x;


if ($x==1  )
{
  
    $di=base64_decode($_GET["dmi"]);
    $df=base64_decode($_GET["dmf"]);
    $sa=base64_decode($_GET["sai"]);
  }


elseif ($x==2 && (empty($_POST['sala']) || !isset($_POST['sala']) 
|| empty($_POST['datami']) || !isset($_POST['datami']) 
|| empty($_POST['datamf']) || !isset($_POST['datamf']) 
)  
 )

{
  
  $di=base64_decode($_GET["dmi"]);
  $df=base64_decode($_GET["dmf"]);
  $sa=base64_decode($_GET["sai"]);
}
else
{  

if ($idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0  || !isset($x)   || !is_numeric($x) 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
|| !isset($_POST['datami']) || !isset($_POST['datamf']) || !isset($_POST['sala'])
|| empty($_POST['datami']) || empty($_POST['datamf']) || empty($_POST['sala'])
)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>


<?php

}

}



if ($x==0)
{
    $di=$_POST['datami'];
    $df=$_POST['datamf'];
    $sa=$_POST["sala"];
}
elseif ($x==1)
{

    $di=base64_decode($_GET["dmi"]);
    $df=base64_decode($_GET["dmf"]);
    $sa=base64_decode($_GET["sai"]);
}


if ( !isset($di) ||!isset($df) || !isset($sa) )
{

 ?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>manut';
          },10);
          </script>



<?php
}



//echo($sa);
?>


     <?php


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
     if ($num_ns==0 || $num_ne==0 )
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



<script>
function a(n) {

var n1;
n1=n;

 //alert(n1);
  event.preventDefault(); // prevent form submit

   swal({
  title: "Deseja eliminar?",
  //text: al1+" - "+np1+" Per/Sem",
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
    window.location.href = '<?php echo SVRURL ?>eliminaman/'+n1;
}, 10);


          
  } else {
   
    swal("Cancelado.");
 
  

  }

});

}

</script>

      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Manutenções >> Sala entre datas</a>
               <div class="titlepage">
                     <h2>
                        <?php echo date('d/m/Y',strtotime($di));?> a <?php echo date('d/m/Y',strtotime($df));?>
                     <br>  <?php echo($ns); ?> | <?php echo($ne);?> </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
              
     
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
//$em=$_SESSION['email'];





  $sql = "
  SELECT m.*,e.*,s.*, m.observacoes as mobs
  FROM manutencao m, equipamento e, salas s
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


<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível atualizar/eliminar manutenções do ano corrente. 


        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="manutencoes_sala_entredatas.php?x=<?php echo base64_encode(2) ?>&&dmi=<?php echo base64_encode($di);?>&&dmf=<?php echo base64_encode($df);?>&&sai=<?php echo base64_encode($sa);?>&&esm=<?php echo base64_encode($idescola);?>" method="post">
                <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable js-sort-date -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-primary" >
                    <th scope="col">Equipamento</th>
                    <th scope="col">Data</th>
                    <th scope="col">Descrição / Realizada por </th>
                 <!--   <th scope="col"></th>   -->   
                    <th scope="col">Observações</th>      
                    <th scope="col"></th>  
                    
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

                    <td width="35%" >
                    
                    <?php echo $row['descricao']; 
                    
                    ?><br><br>
                      <?php echo $row['pessoa']; ?>
                     
                    <td width="15%" >                    
                    <?php echo $row['mobs'];    ?>
                    
                 
                    
                 
                    </td>


                    <td width="10%" >
                    <?php
$sql2 = "select max(ano_lectivo),min(num_periodo) from periodos";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$mal = $rows2[0];
$mnp=$rows2[1];
//echo $mnp;

$sql2b = "select data_inicio from periodos where 
ano_lectivo='".$mal."' and num_periodo=".$mnp." ";
$result2b = mysqli_query($db,$sql2b); 
$rows2b =mysqli_fetch_row($result2b);

$df2b=$rows2b[0];



$dm=$row['data_manutencao'];


//echo $row['id_equi'];


$sql3 = "
SELECT count(*) FROM manutencao WHERE 
id_equi=".$row['id_equi']."  and
data_manutencao=STR_TO_DATE('$dm','%Y-%m-%d') AND 
STR_TO_DATE(data_manutencao,'%Y-%m-%d') > STR_TO_DATE('$df2b','%Y-%m-%d')";

$result3 = mysqli_query($db,$sql3);
$rows3 =mysqli_fetch_row($result3);
$contama = $rows3[0];

//echo $contama;


                      if ( ($_SESSION['tipo']==1 || $_SESSION['tipo']==3 ) && $contama>0)
                      {
                     ?>
                     
                     <a title="Atualizar" href="atualiza_manutencao.php?c=<?php echo base64_encode($row['codigo'])?>&&da1=<?php echo base64_encode($di)?>&&da2=<?php echo base64_encode($df)?>&&sa=<?php echo base64_encode($sa)?>&&ides=<?php echo base64_encode($idescola)?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a>


                    &nbsp;   &nbsp;&nbsp;            
                    <a onclick="a(<?php echo $row['codigo'];?>);" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminaman">
                  

                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
                      <?php
      
                      }
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(2)."&&dmi=".base64_encode($di)."&&dmf=".base64_encode($df)."&&sai=".base64_encode($sa)."&&esm=".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="manutencoes_sala_entredatas.php?x=<?php echo base64_encode(2) ?>&&dmi=<?php echo base64_encode($di);?>&&dmf=<?php echo base64_encode($df);?>&&sai=<?php echo base64_encode($sa);?>&&esm=<?php echo base64_encode($idescola);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=".base64_encode(2)."&&dmi=".base64_encode($di)."&&dmf=".base64_encode($df)."&&sai=".base64_encode($sa)."&&esm=".base64_encode($idescola)."&&page=". $next; } ?>">>></a>
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