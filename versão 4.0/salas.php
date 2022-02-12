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
                     <h2>Salas</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
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


$sql = "select * from salas order by nome LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
 $sql1 = "select count(*) as cs from salas";
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
function a(n,s) {

var n1,s;
n1=n;
s1=s;

 //alert(s1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar?",
 text: "Sala: "+s1,
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
    window.location.href = '<?php echo SVRURL ?>eliminasala/'+n1;
}, 10);


          
  } else {
    //swal("Cancelled", "Your imaginary file is safe :)", "error");
    window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>salas';
}, 10);
  

  }

});

}

</script>



       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="salas" method="post">
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
                    <a class="underlineHover"  tilte="Inserir sala" href="<?php echo SVRURL ?>inserirsala">
                    <p style="color:blue;"> Inserir </p> </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                    $sa=$row['nome'];
                    //echo($sa);


                    $sql2 = "select count(*) from equipamento,salas where salas.nome=equipamento.sala
                    and salas.nome='$sa'";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                    $contasala = $rows2[0];
                    //echo($contasala);

                    ?>
                <tr>
                    <td width="25%"  scope="row"><?php echo $row['nome']; ?></td>
                    <td width="30%" ><?php echo $row['localizacao']; ?></td>
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
              
                        
                    <a onclick="a(<?php echo $n;?>,'<?php echo $sa;?>');" title="Eliminar" href="<?php echo SVRURL ?>eliminasala/<?php echo $n ?>">
                  
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>salas?page=<?= $i; ?>"> <?= $i; ?> </a>
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