<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>--> 
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");


?>
   




      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Salas </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
    
    

<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>


<br>

<div style="  text-align: center;">   
      
<a  class="underlineHover" href="<?php echo SVRURL ?>salas_num_escola.php" title="Ver nº de salas por escola" style="color:blue;font-size:16px;">Ver nº de salas por escola </a>
</div >




<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">


<h3 style="color:black;">SALA




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
           
              //echo $esc;
           
           
           

$sql1 = "select nome_escola
from escolas 
where id=$esc";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$ne = $rows[0];
           
           ?>
    
    
    
    
    <br>

    <?php
     if ($_SESSION['tipo']==1 )
     {
   ?>
    <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível eliminir salas sem equipamento associado. 

     <?php
     }
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


$sql = "select s.id as idsala,s.* from salas s, escolas e
where s.id_escola=e.id and e.id=$esc
order by s.nome
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$sql1 = "select count(*) as cs from salas where id_escola=$esc";
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



function a(n,s,es,ne) {



var n1,s1,es1,ne1;
n1=n;
s1=s;
es1=es;
ne1=ne;

 //alert(es1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar?",
 text: "Sala: "+s1+" (Escola: "+ne1+")",
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
    window.location.href = '<?php echo SVRURL ?>eliminasala/'+n1+'/'+es1;
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
            <form action="<?php echo SVRURL ?>salas?x=1&&escola=<?php echo($esc);?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable class="table table-striped"-->
        <table   class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-success">
                 
                    <th  scope="col">Nome</th>
                    <th   scope="col">Localização</th>
                    <th  scope="col">Departamento / Grupo / Serviço</th>
     
                     
                     <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th  colspan="3" style="text-align: center;">
                    <a class="underlineHover"  tilte="Inserir sala" href="<?php echo SVRURL ?>inserirsala?id=<?php echo $esc ?>">
                    <p style="color:blue;"> Inserir </p> </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['idsala'];
                    $sa=$row['nome'];
                    //echo($sa);
                    //echo $n;

                    $sql2 = "select count(*) from equipamento,salas 
                    where salas.id=equipamento.id_sala
                    and salas.id='$n' and salas.id_escola=$esc";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                    $contasala = $rows2[0];
                    //echo($contasala);

                    ?>
                <tr>
             
                      <td width="25%"  scope="row"><?php echo $row['nome']; ?></td>
                    <td width="20%" ><?php echo $row['localizacao']; ?></td>
                    <td width="35%" ><?php echo $row['departamento']; ?></td>
                  
                    
                    <?php
                      if ($_SESSION['tipo']==1 )
                      {
                     ?>
                    <td width="5%" >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualizasala/<?php echo $n ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                    <td width="5%"  >
                    <?php
                      if ($contasala==0 )
                      {
                     ?>
              
                        
                    <a onclick="a(<?php echo $n;?>,'<?php echo $sa;?>','<?php echo $esc;?>','<?php echo $ne;?>');" title="Eliminar" href="<?php echo SVRURL ?>eliminasala/<?php echo $n ?>/<?php echo $esc ?>">
                  
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a></td>
                    
                    <?php
                      }
                     ?>
                    
                    <!--
                    <td width="5%" align="center" >
                    <a title="Copiar" href="<?php echo SVRURL ?>copiasala/<?php echo $n ?>"><img src="<?php echo SVRURL ?>images/copiar.png" alt="Copiar" > </a></td>
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
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>salas?x=1&&escola=<?php echo($esc);?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
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