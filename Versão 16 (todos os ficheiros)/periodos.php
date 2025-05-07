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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Períodos</a>
               <div class="titlepage">
          
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
            
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    
</div>




<?php
  
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


  $sql = "select * from periodos order by ano_lectivo desc, num_periodo asc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  $sql1 = "select count(*) as cs from periodos";
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
function a(n,al,np) {

var n1;
n1=n;al1=al;np1=np

 //alert(n1);
  event.preventDefault(); // prevent form submit

   swal({
  title: "Deseja eliminar?",
  text: al1+" - "+np1+" Per/Sem",
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
    window.location.href = '<?php echo SVRURL ?>eliminaper/'+n1;
}, 10);


          
  } else {
   
    swal("Cancelado.");
 
  

  }

});

}

</script>

<?php 


if ($_SESSION['tipo'] == 1) { ?>
<img src="images/informacao.svg"> Não é possível atualizar/eliminar períodos com avarias associadas.
<?php }?>

       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form  action="<?php echo SVRURL ?>peri" method="post">
                       <?php include("num_linhas.php");?>
            </form>
        </div>



   <!-- Datatable  class="table table-striped"
   <div class="table-responsive">-->


   <table class="table table-striped" id="js-sort-table" >
            <thead>
                <tr class="table-primary">
                    <th scope="col">Ano</th>
                    <th scope="col">Período de tempo</th>
                    <th scope="col">Data Início</th>
                    <th scope="col">Data Fim</th>
                     
                     <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th colspan="2"  style="text-align:center;">   
                     <!--
<form  action="<?php echo SVRURL ?>inserirper" method="post">
<button title="Inserir período/semestre" type="submit" class="btn btn-outline-primary" >Inserir</button>
</form>
            -->   
           
                    <a class="btn btn-primary"  title="Inserir período" class="underlineHover" href="<?php echo SVRURL ?>inserirper">
                       <p style="color:white;"> 
                 
                       <i class="fa-solid fa-plus"></i> </p>
                            </a>   
                       
                          
                          </th>  
                       
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
                    $id=$row['id'];
                    $np=$row['num_periodo'];
                    $al=$row['ano_lectivo'];
                   

                    ?>
                <tr>
                    <td width="15%"  scope="row"><?php echo $row['ano_lectivo']; ?></td>
                    <td width="20%" ><?php echo $row['num_periodo']; ?></td>
                    <td width="20%" ><?php echo date('d/m/Y',strtotime($row['data_inicio'])); ?></td>
                    <td width="20%" ><?php echo date('d/m/Y',strtotime($row['data_fim'])); ?></td>

                    <?php
                      if ($_SESSION['tipo']==1 )
                      {

                     ?>

                    <?php
                          $sql9 = "select count(*) as cav from avarias_reparacoes
                          where ano_letivo='".$al."' and periodo=".$np."  ";
                          $result9 = mysqli_query($db,$sql9); 
                          $rows9 =mysqli_fetch_row($result9);
                          
                          $ccav=$rows9[0];

                          if ($ccav==0)
                          {
                        ?>


                    <td  width="10%">
               
                    <a 
          
                    title="Atualizar" href="atualizaper/<?php echo base64_encode($id) ?>">
                   
                
                   
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  
                   
                    </a>
                          
                        
      
                     


                    &nbsp;   &nbsp;&nbsp;            
                    <a onclick="a(<?php echo $id;?>,'<?php echo $al;?>',<?php echo $np;?>);" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminaper">
                  

                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
                    </td>
                  
                  <?php
                          }
                  ?>



                    <!--
                    <td width="5%" align="center" >
                    <a title="Copiar" href="copia_periodo.php?id=<php echo $n ?>">
                    <img src="images/copiar.png" alt="Copiar" width="50%" height="50%"> </a></td>
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
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>peri?page=<?= $i; ?>"> <?= $i; ?> </a>
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
       

        <a href="<?php echo SVRURL ?>configura">
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
    


      <?php include ("footer.php");?>


   </body>
</html>