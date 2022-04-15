<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     







     

     <?php
//session_start();



include("sessao_timeout.php");


/*
     
if ($_GET["x"]==1)
{
$idescola=$_GET["escola"];
}
elseif ($_GET["x"]==0)
{
$idescola=$_POST["escola"];

}
  */ 


//echo $idescola;


?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Tarefas</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

<?php
include("msg_bemvindo.php");
?>
    <br>

      
 

    

    <script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>



<div style="  text-align: center;">   
      
<a  class="underlineHover" href="<?php echo SVRURL ?>tarefas_num_escola.php" title="Ver nº de tarefas a realizar por escola" style="color:blue;font-size:16px;">Ver nº de tarefas a realizar por escola </a>
</div >




<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">


<h3 style="color:black;">ESCOLA



      

<select  name="escola" onChange="showesc(this.value);"  style="background-color:#CEF6CE">


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by nome_escola";
$result2 = mysqli_query($db,$sql2);

//echo('<option value=""> Escolha a escola  </option>');  

while($row2=mysqli_fetch_array($result2))
{

   if ($row2['id']==$_REQUEST["escola"])
   {
     //'.$row2['nome_escola'].'
      echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');


   }
   else

  echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');


}


echo('</select>');

?>

</div>


</form>







<?php

$x=$_GET['x'];

$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];

//echo $x;
//echo ('<br>');


if (!empty($_POST["escola"])) {
              
              $esc=$_POST["escola"];
              
              }
              elseif ($x==0) 
              {
               $esc=$nes;   
              }
              elseif ($x==1) 
              {
               $esc=$_GET['escola'];  
              }
           
             // echo $esc;
           ?>
<br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível atualizar e eliminar tarefas não concluídas. 
<?php

$sql11 = "select nome_escola from escolas where id=$esc";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

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
          window.location.href = '<?php echo SVRURL ?>configura';
      }, 10);
      </script>
<?php
}
?>




        <?php 

if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;


$sql = "select * FROM tarefas
where id_escola=$esc
order by data_criacao desc, data_conclusao
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$sql1 = "select count(*) as cs from tarefas where id_escola=$esc";
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
function a(id,ns,esc,idesc) {

var id1;
var ns1;
var esc1;
var idesc1;

id1=id;
ns1=ns;
esc1=esc;
idesc1=idesc;

//alert (idesc1);
 //alert(id1);
 //alert(ns1);
 //alert(esc1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar?",
  text: "Tarefa da sala "+ns1+" da "+esc1,
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
    window.location.href = '<?php echo SVRURL ?>eliminatarefa/'+id1+'/'+idesc1;
}, 10);


          
  } else {
    swal("Cancelado.");

//    window.setTimeout(function() {
  //  window.location.href = '<?php echo SVRURL ?>salas?x=1&&escola='+es1;
//}, 10);
  

  }

});

}

</script>



       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>tarefas.php?x=1&&escola=<?php echo($esc);?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable class="table table-striped"-->
        <table   class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-success">
                   
                    <th  scope="col">Sala</th>
                    <th   scope="col">Descrição</th>
                    <th   scope="col">Urgência</th>
                    <th   scope="col">Criado por / data</th>
                    <th   scope="col">Concluído por / data</th>
                  
     
                     
                     <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th  colspan="3" style="text-align: center;">
                    <a class="underlineHover"  tilte="Inserir tarefa" href="<?php echo SVRURL ?>inserir_tarefa.php?id=<?php echo $esc ?>">
                    <p style="color:blue;"> Inserir </p> </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id_sala'];
                                  
                    //echo $n;

                    $sql2 = "select nome from salas 
                    where id=$n";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                    $nomesala = $rows2[0];
                  

                    ?>
                <tr>
                <td width="20%"  scope="row"><?php echo $nomesala; ?></td>
                      <td width="35%"  scope="row"><?php echo $row['descricao']; ?></td>
                    <td width="10%" ><?php echo $row['urgencia']; ?></td>
                    <td width="20%" ><?php echo $row['criado_por']; ?>
                    <br>
                    <?php echo $row['data_criacao']; ?>
                </td>
                <td width="20%" ><?php echo $row['concluido_por']; ?>
                    <br>
                    <?php echo $row['data_conclusao']; ?>
                </td>
                    


                    <?php
                      if ($_SESSION['tipo']==1 )
                      {
                     ?>
                      <?php 
                   if ($row['data_conclusao']=="") 
                   {
                   ?>
                    <td width="5%" >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualtarefa/<?php echo $row['id'] ?>/<?php echo $esc ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                   
                   
                  
                   
                    <td width="5%"  >
                    
                    
                    <?php
                      $sql2b = "select nome_escola
                      from escolas
                      where id=$esc";
                      $result2b = mysqli_query($db,$sql2b); 
                      $rows2b =mysqli_fetch_row($result2b);
                      
                      $noesc = $rows2b[0];
                     ?>
              
                        
                    <a onclick="a('<?php echo  $row['id'] ;?>','<?php echo  $nomesala ;?>','<?php echo  $noesc ;?>','<?php echo  $esc ;?>');" title="Eliminar" href="<?php echo SVRURL ?>eliminatarefa/<?php echo $row['id'] ?>/<?php echo $esc ?>">
                  
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a></td>
                    
                    <?php
                   }
                     ?>
                    
               
                    
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


        <!--
        <script src="js/jquery.js"></script>
    <script src="js/sort-table.js"></script>
         -->

         <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

       
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&escola=".$esc."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>tarefas.php?x=1&&escola=<?php echo($esc);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&escola=".$esc."&&page=". $next; } ?>">>></a>
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