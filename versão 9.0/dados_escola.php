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
                     <h2>Escolas</h2>
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

   
    <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível eliminar escolas sem salas. 

        <br>  <br>
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


$sql = "select * from escolas order by id LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
/* 
$sql1 = "select count(*) as cs from escolas";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);

$totallinhas = $rows[0];
*/
$totallinhas=$result->num_rows;;
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
 text: "Escola: "+s1,
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
    window.location.href = '<?php echo SVRURL ?>eliminaesc/'+n1;
}, 10);


          
  } else {
    //swal("Cancelled", "Your imaginary file is safe :)", "error");
    swal("Cancelado.");
    //window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>dados_escola.php';
//}, 10);
  

  }

});

}

</script>




        <!-- Datatable class="table table-striped"-->
        <table   class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-success">
               
                    <th  scope="col">Nome da escola</th>
                   
                        
                     
                    
                </tr>
           </thead> 
            <tbody>
                <?php  
               $i=0;
                while($row=mysqli_fetch_array($result)) { 
                  
                    $i=$i+1;
                    $id=$row['id'];
                    $no=$row['nome_escola'];


                    $sql2 = "select count(*) from escolas,salas 
                    where salas.id_escola=escolas.id
                    and escolas.id='$id'";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                    $contaid = $rows2[0];



                    ?>
                <tr>
                    
                    <td width="40%"  scope="row">
                        <?php echo $row['nome_escola']; ?>
                    
                    </td>
                   

                    <?php
                      
                      if ( ($_SESSION['tipo']==1)    &&   $i<>1)
                      {
                     ?>

                 <td width="5%" >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualizaesc/<?php echo  $row['id']?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                              

                    <td width="5%"  >


                    <?php
                      if ($contaid==0 )
                      {
                     ?>


                    <a onclick="a(<?php echo $id;?>,'<?php echo $no;?>');" title="Eliminar" href="<?php echo SVRURL ?>eliminaesc/<?php echo $row['id'] ?>">
                  
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

       
<form action = "<?php echo SVRURL ?>dadosesc" method="post" >
<input type = "hidden" name = "" >
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>

<?php
include "realcelinhatabela.php";
?>


        <!--
        <script src="js/jquery.js"></script>
    <script src="js/sort-table.js"></script>
       

         <img src="<php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.
  -->
       
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>dadosescola?page=<?= $i; ?>"> <?= $i; ?> </a>
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