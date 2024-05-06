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
include("sessao_timeout.php");


 
  ?>






      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS</a>
               <div class="titlepage">
                     <h2>Minhas requisições  </h2> 
                     <br> 
                      
                     
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



$sql = "SELECT  r.id as rid,r.*,s.*
from requisicao r, salas s
where s.id=r.id_sala and
 email_util='$em' 
order by r.datautil,r.horainicio  LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
 $sql1 = "select count(*) 
 from requisicao
where email_util='$em' ";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$totallinhas = $rows[0];
//echo($totallinhas );





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
text: "Nº requisição: "+n1,
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
  window.location.href = '<?php echo SVRURL ?>eliminarequi/'+n1;
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



        <!-- Select dropdown -->
      <!-- Select dropdown -->
      <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="myrequi" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

      

     




        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                <th scope="col">Nº </th>
                    <th scope="col">Data da requisição </th>
                    <th scope="col">Data da utilização / Sala / Horas</th>
                    <th  scope="col">Equipamentos</th>
                    
                     
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row2=mysqli_fetch_array($result)) { 
                   
                   $n=$row2['rid'];
                   ?>
                   
<tr>
<td width="5%"  scope="row">
<?php echo $n; ?>
</td>
<td width="20%"  scope="row">
<?php echo date('d/m/Y',strtotime($row2['datarequi'])); ?>



</td>


<td width="25%" >
<?php echo date('d/m/Y',strtotime($row2['datautil']));  echo('<br>');?> 
<?php echo $row2['nome'];  echo('<br>');?> 


<?php echo $row2['horainicio'];   

?>


-
<?php echo $row2['horafim'];   

?>
</td>

<td width=40%>
<?php

$idr=$row2['rid'];

//echo $idr;

$sql3 = "
select e.nomeequi 
from equip_requisitado er, equipamento e
where er.id_equip=e.id
and er.id_req=".$idr."
"; 
$result3 = mysqli_query($db,$sql3);

while($row3=mysqli_fetch_array($result3)) { 
    echo $row3['nomeequi'];
    echo ('  |  ');
}

echo ('<br>');echo ('<br>');
if ($row2['dataentrega'] <> null)
{ echo ('Data de entrega: ');
   echo date('d/m/Y',strtotime($row2['dataentrega']));  
}
?>
</td>


                    <?php
                      if ($row2['dataentrega'] == null)
                      {
                     ?>
                    <td  >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualiza_requisicao.php?ri=<?php echo base64_encode($n);?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" > </a></td>
                    <td  >
                    <?php
                      
                     ?>

                    <a onclick="a(<?php echo $n;?>);" title="Eliminar" href="<?php echo SVRURL ?>eliminarequi">
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a></td>


                    
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
 <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

        

        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="myrequi?page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;"  class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
       

        <br>
         <a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br>

        <?php include ("jquery_bootstrap.php");?>







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