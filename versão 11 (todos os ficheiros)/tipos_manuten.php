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
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> TIPOS DE MANUTENÇÃO</a>
               <div class="titlepage">
            
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-2">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>
<script>
function a(n,no) {

var n1,no1;
n1=n;
no1=no;
 //alert(no1);
  event.preventDefault(); // prevent form submit

   swal({
  title: "Deseja eliminar?",
  text: "Tipo manutenção: "+no1,
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
    window.location.href = '<?php echo SVRURL ?>eliminatmanuten/'+n1;
}, 10);


          
  } else {
    swal("Cancelado.");
    //swal("Cancelled", "Your imaginary file is safe :)", "error");
   // window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>tiposequip';
//}, 10);
  

  }


});



}


</script>


<?php
     if ($_SESSION['tipo']==1 )
     {
   ?>

<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível eliminar tipos sem manutenção associada.

        
        <div style="text-align: right;">
   <a href="<?php echo SVRURL ?>tiposmanuten_csv.php" target="_blank">
   <button title="Exportar para CSV" type="submit" class="btn btn-outline-primary" >CSV</button>
   </a>
   <br><br>

</div>
<?php 
     }


if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

//echo($paginationStart);
//echo($limit);


$sql = "select * from tipos_manutencao order by nome LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$sql1 = "select count(*) as cs from tipos_manutencao";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$totallinhas = $rows[0];
//echo($totallinhas );



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

//echo  $totoalPages;

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>tiposmanuten" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Nome</th>
                    
                     
                     <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th colspan=2 style="text-align: center;">
<!--
    <form action="<?php echo SVRURL ?>inserirtequip" method="post">

<button title="Inserir tipo equipamento" type="submit" class="btn btn-outline-primary" >Inserir</button>

</form>  -->
                    <a class="btn btn-outline-primary" title="Inserir tipo manutenção" 
                     href="<?php echo SVRURL ?>inserirtmanuten">
                       <p style="color:blue;"> Inserir </p></a>
                     
                 </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                   
                    $tp2=($row['nome']);
                   
 


                    $sql2 = "select count(*) from manutencao where descricao='$tp2'";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    $n=$row['id'];
                   

          

                    $conta = $rows2[0];

                    //echo $conta;

                    ?>
                <tr>
                    <td width="50%"  scope="row"><?php echo $row['nome']; ?></td>
                    

                    <?php
                      if ($_SESSION['tipo']==1 )
                      {
                     ?>
                  
                    <td width="5%" style="text-align: center;">
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualtmanuten/<?php echo base64_encode($n) ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                   
                    <td width="5%" style="text-align: center;" >
                   
                    <?php
//echo $conta;
//echo ('<br>');
//echo $tp2;

                      if ($conta==0)
                      {
                     ?>
                   

                    <a onclick="a(<?php echo $n;?>,'<?php echo $tp2;?>');" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminatmanuten">

                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" >  </a></td>
 
                    <?php 
                      }
                    } ?>               
                  
                
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
include "realcelinhatabela.php";
?>


<img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.


         <br><br>

        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>tiposmanuten?page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
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
       


        <?php include ("jquery_bootstrap.php");?>


        <a href="<?php echo SVRURL ?>configura">
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