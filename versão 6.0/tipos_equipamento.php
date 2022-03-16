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
               <div class="titlepage">
                     <h2>Tipos de equipamento</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-6 offset-md-3">
              
                        

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
  text: "Tipo equipamento: "+no1,
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
    window.location.href = '<?php echo SVRURL ?>eliminatequip/'+n1;
}, 10);


          
  } else {
    //swal("Cancelled", "Your imaginary file is safe :)", "error");
    window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tiposequip';
}, 10);
  

  }


});



}


</script>


<?php
     if ($_SESSION['tipo']==1 )
     {
   ?>

<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível eliminar tipos sem equipamento associado.

        
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


$sql = "select * from tipos_equipamento order by nome LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$sql1 = "select count(*) as cs from tipos_equipamento";
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
            <form action="<?php echo SVRURL ?>tiposequip" method="post">
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
                    <a tilte="Inserir tipo equipamento" class="underlineHover" href="<?php echo SVRURL ?>inserirtequip">
                       <p style="color:blue;"> Inserir </p></a>
                 </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                   
                    $tp=$row['nome'];
                   
                    $sql2 = "select count(*) from equipamento where tipo='$tp'";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    $n=$row['id'];
                   

          

                    $conta = $rows2[0];

                    ?>
                <tr>
                    <td width="50%"  scope="row"><?php echo $row['nome']; ?></td>
                    

                    <?php
                      if ($_SESSION['tipo']==1 )
                      {
                     ?>
                  
                    <td width="5%" style="text-align: center;">
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualtequip/<?php echo $n ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                   
                    <td width="5%" style="text-align: center;" >
                   
                    <?php
                      if ($conta ==0 )
                      {
                     ?>
                     <!--/?php echo $tp ?>
                    <a onclick="return confirm('Deseja eliminar o tipo de equipamento?')" title="Eliminar" href="<?php echo SVRURL ?>eliminatequip/<?php echo $n ?>">
                    -->

                    <a onclick="a(<?php echo $n;?>,'<?php echo $tp;?>');" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminatequip/<?php echo $n ?>">
                                                          
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
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>tiposequip?page=<?= $i; ?>"> <?= $i; ?> </a>
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