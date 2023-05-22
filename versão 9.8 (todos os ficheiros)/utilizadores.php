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

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Configurações >> Utilizadores</h2>
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
<!--
                  <img src="images/informacao.svg" alt="Informação">
  Tipo: 1 - Administrador     &nbsp;&nbsp;&nbsp; &nbsp;    2 - Utilizador
&nbsp; &nbsp; &nbsp; &nbsp; 3 - Reparador
&nbsp; &nbsp; &nbsp; &nbsp; 
-->

<div class="row">
  <div >
 
  <!--
    <p><form action="<?php echo SVRURL ?>userspdf" method="post" target="_blank">

<button title="Exportar para PDF" type="submit" class="btn btn-outline-primary" >PDF</button>

</form>

</p>
-->


<a href="<?php echo SVRURL ?>userspdf" target="_blank">
<button title="Exportar para PDF" type="submit" class="btn btn-outline-primary" >PDF</button>
</a>
  </div>


&nbsp;&nbsp;&nbsp;

  <div  >
  <a href="<?php echo SVRURL ?>userscsv" target="_blank">
<button title="Exportar para CSV" type="submit" class="btn btn-outline-primary" >CSV</button>
</a>
<!--
    <p>
<form action="<?php echo SVRURL ?>userscsv" method="post" target="_blank">

<button title="Exportar para CSV" type="submit" class="btn btn-outline-primary" >CSV</button>

</form></p>
-->
  </div>
</div>


<?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>
<br>Tipo:
(1 - Administrador &nbsp; &nbsp;      2 - Utilizador   &nbsp; &nbsp;       3 - Reparador &nbsp; &nbsp;       4 - Funcionário)


  <?php }

if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

//echo($paginationStart);
//echo($limit);


$sql = "select * from utilizadores order by nome LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records

$sql1 = "select count(*) as cs from utilizadores";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$totallinhas = $rows[0];




// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<script>
function a(n,no,em) {

var n1;
n1=n;no1=no;em1=em;

 //alert(n1);
  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar?",
  text: no1+" - "+em1,
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
    window.location.href = '<?php echo SVRURL ?>eliminauser/'+n1;
}, 10);


          
  } else {
 
    swal("Cancelado.");

  

  }

});

}

</script>



        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>utiliz" method="post">
                 <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table" >
            <thead>
                <tr class="table-success">
         
                <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>
            <th scope="col">Tipo </th>   
            <?php } ?>
            

                <th scope="col">Nome</th>
                
                <th scope="col">Email</th>
               
              <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th  colspan="3" style="text-align: center;">


                    <a class="btn btn-outline-primary"  title="Inserir utilizador" href="<?php echo SVRURL ?>inserirutil">
                 
                    <p style="color:blue;"> Inserir </p> </th></a>
                              <?php
                      }
                     ?>
                    
                </tr>  
                   
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                   
                    
                    ?>
                <tr>


                   

                <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>
                   <td width="10%">
                     <?php echo $row['tipo']; ?></td>
                     <?php
                     }
                     ?>

                    <td width="30%"  scope="row"><?php echo $row['nome']; ?></td>
                    
                 <td width="35%">
                <?php echo $row['email']; ?></td>
                   
                 
                

                <td width="20%" style="text-align:center" >  
                         
             

                         <?php
                               if ($_SESSION['tipo']==1 && $row['nome']<>$_SESSION['login_user'] )
                               {
                              ?>
                         <a title="Atualizar" href="<?php echo SVRURL ?>atualizautili/<?php echo base64_encode($row['id']) ?>">
                             <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a>
         
                             &nbsp;  &nbsp;  
         
                           
                             <a onclick="a(<?php echo $row['id'];?>,'<?php echo $row['nome'];?>','<?php echo $row['email'];?>');" title="Eliminar" 
                             href="<?php echo SVRURL ?>eliminauser">
                           
                             <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a> 
                          
                             <?php 
                               }
                               ?> 
         
                           </td> 

                  
                
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
                    <a  style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a  style="color:black;" class="page-link" href="utiliz?page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a  style="color:black;" class="page-link"
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



<br>



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