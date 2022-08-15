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



<script>
function a2(n,d,da,es) {

var n1,d1,d2;
n1=n;
d1=d;
d2=da;
es1=es;

//alert(n1);
//alert(d1);
//alert(d2);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja entregar os equipamentos?",
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
  

      window.setTimeout(function() {
  window.location.href = '<?php echo SVRURL ?>entregar_requisicao_datas.php?ir='+n1+'&d1='+d1+'&d2='+d2+'&ies='+es1;
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





<?php

$x=base64_decode($_GET["x"]);

$esc=base64_decode($_GET["ies"]);



$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if (base64_decode($_GET["ies"])>$maxesc)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}



$sql11 = "select nome_escola  from escolas where id=$esc";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];




if ($x==2 && !empty($_GET["d1"]) &&  isset($_GET["d1"]) &&  
 !empty($_GET["d2"]) &&  isset($_GET["d2"]) &&   
( empty($_POST['datai1']) || !isset($_POST['dataf1']) 
)  
 )

{
  
    $d1=base64_decode($_GET['d1']);
    $d2=base64_decode($_GET['d2']);
}
else



if (!isset($x)  || !is_numeric($x) || !isset($esc)  || !is_numeric($esc) 
||  $x<0 ||  $x>1  
|| !isset($_POST["datai1"]) || empty($_POST["datai1"])
|| !isset($_POST["dataf1"]) || empty($_POST["dataf1"])
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




if ($x==0)
{
    $d1=$_POST['datai1'];
    $d2=$_POST['dataf1'];
}
elseif ($x==1 )
{
       $d1=$_GET['d1'];
       $d2=$_GET['d2'];
}  
?>

<?php
//echo $d1;
//echo $d2;
?>

<?php
if ( ( !isset($d1) && !isset($d2))  )
{

    

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
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
                     <h2>Listagens >> Requisições a terminar entre datas   
                     <br> 
                   Entre <?php echo date('d/m/Y',strtotime($d1)) ?>  e  <?php echo date('d/m/Y',strtotime($d2)) ?> 
                   <br><?php echo $ne ?></h2>
                     
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




$sql = "SELECT  r.id as rid, r.*,s.*, u.nome as nu
from requisicao r, salas s, utilizadores u
where s.id=r.id_sala   and u.email=r.email_util
and datautil BETWEEN 
STR_TO_DATE('$d1','%Y-%m-%d') AND
STR_TO_DATE('$d2','%Y-%m-%d')
and dataentrega is null
and s.id_escola=$esc
order by datautil LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
 $sql1 = "select count(*) 
 from requisicao r, salas s
where s.id=r.id_sala 
and datautil BETWEEN 
STR_TO_DATE('$d1','%Y-%m-%d') AND
STR_TO_DATE('$d2','%Y-%m-%d')
and dataentrega is null
and s.id_escola=$esc
order by datautil ";
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







        <!-- Select dropdown -->
      <!-- Select dropdown -->
      <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(2)?>&&d1=<?php echo base64_encode($d1);?>&&d2=<?php echo base64_encode($d2);?>&&ies=<?php echo base64_encode($esc) ?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

      

     




        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                <th scope="col">Nº </th>
                    <th scope="col">Utilizador / Data da requisição </th>
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
<?php echo $row2['nu']; ?>
<br> 
<?php echo $row2['email_util']; ?>
<br> 
<?php echo date('d/m/Y',strtotime($row2['datarequi'])); ?>



</td>


<td width="30%" >
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
?>
</td>


                
                    <td  >
                    <a 
                    onclick="a2(<?php echo $idr;?>,'<?php echo $d1;?>','<?php echo $d2;?>',<?php echo $esc;?>);"
                    title="Entregar" href="<?php echo SVRURL ?>>entregar_requisicao_datas.php">
                    <img src="<?php echo SVRURL;?>images/entregar.svg" alt="Entregar" > </a>
                   </td  >
                                   
            

                  
                
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(2)."&&d1=".base64_encode($d1)."&&d2=".base64_encode($d2)."&&ies=".base64_encode($esc)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(2);?>&&d1=<?php echo base64_encode($d1);?>&&d2=<?php echo base64_encode($d2);?>&&ies=<?php echo base64_encode($esc);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;"  class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=".base64_encode(2)."&&d1=".base64_encode($d1)."&&d2=".base64_encode($d2)."&&ies=".base64_encode($esc)."&&page=". $next; } ?>">>></a>
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
    
   


      <?php 
      
      mysqli_close($db);
      include ("footer.php");?>


   </body>
</html>