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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      

  
      <?php

     
     if ($_GET["x"]==1)
     {
     $said=$_GET["sala"];
     $idescola=$_GET["escola"];
     }
     elseif ($_GET["x"]==0)
     {
     $said=$_POST["sala"];
     $idescola=$_GET["escola"];
     
     }
   
      

  //echo $said ;

     $sql10 = "select nome from salas where id=$said";
     $result10 = mysqli_query($db,$sql10); 
     $rows10 =mysqli_fetch_row($result10);
     
      $ns = $rows10[0];



     $sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
     ?>
     

      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Equipamento da sala  (<?php echo($ns);?>)
                    <br><?php echo $ne ?>
                    </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">
              
                        

<?php
include("msg_bemvindo.php");
?>
 


    <script>
function a1(n,no,ne,noeq,ides,said) {

var n0,n1,ne1,noeq1,ides,said;
n0=n; //id_equi
n1=no;  //sala
ne1=ne;  //escola
noeq1=noeq;  //nome equi
ides1=ides;  //id_escola
said1=said;  //id_sala

//alert(ides1);

 //alert(n0);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar? (Vai eliminar também as avarias)",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Escola: "+ne1+")",
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
    window.location.href = '<?php echo SVRURL ?>eliminaequi/'+n0+'/'+ides1+'/'+said1;
}, 10);


          
  } else {
    swal("Cancelado.");
   // swal("Cancelled", "Your imaginary file is safe :)", "error");
   // window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php';
//}, 10);
  

  }

});

}

</script>



<?php 
  if(isset($_POST['records-limit'])){
      $_SESSION['records-limit'] = $_POST['records-limit'];
  }
  
  $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
  $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
  $paginationStart = ($page - 1) * $limit;
  
//and id_escola=$idescola

  $sql = "
  select e.* from equipamento e, salas s
where e.id_sala=s.id
and s.id=".$said." and s.id_escola=".$idescola."
order by e.tipo desc
  LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  // Get total records
  $sql1 = "select count(*) from equipamento e, salas s
  where e.id_sala=s.id
  and s.id=".$said." 
  and s.id_escola=".$idescola." ";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  

  $totallinhas = $rows[0];
//echo   $totallinhas;


  // Calculate total pages
  $totoalPages = ceil($totallinhas / $limit);

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?> 

<?php
     if ($_SESSION['tipo']==1 )
     {
   ?>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Ao eliminar o equipamento serão eliminadas todas as avarias. 

        <?php
     }
     ?>    
 

        <!-- Select dropdown-->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=1&&sala=<?php echo($said);?>&&escola=<?php echo $idescola ?>" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div> 
      

        
        <!-- Datatable -->
        <table class="table table-striped">
            <thead>
                <tr class="table-success">
                    <th scope="col">Tipo / Nome</th>
                    <th scope="col">Dados técnicos</th>
                    <th scope="col">Dados rede</th>

                 
             
                    <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th  >
                        <a tilte="Inserir equipamento" class="underlineHover"   href="<?php echo SVRURL ?>inserirequip">
                       <p style="color:blue;"> Inserir </p>
                            </a> </th>
                              <?php
                      }
                     ?>
                     
                  
                </tr>
            </thead>
            <tbody>


                <?php 
                //$c=0;
                while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                    $noeq=$row['nomeequi'];
                  //echo $n;

                    //$c=$c+1;
                    //$totallinhas = $c;
                    $sql1 = "select count(*) 
                    from avarias_reparacoes ar, equipamento eq, salas s
                    where ar.id_equi=eq.id and s.id=ar.id_sala
                    and s.id=".$said." and s.id_escola=".$idescola."
                    and eq.id=".$n." and datareparacao is null";

                 

                    $result1 = mysqli_query($db,$sql1); 
                    $rows =mysqli_fetch_row($result1);
             

                    ?>
                <tr>
                    <th width="30%"  scope="row"><?php echo $row['tipo']; echo('<br>/<br>'); echo $row['nomeequi'];  ?>
                    <br>  <br>
                    <br> <br> <br>
                    Estado:
                    <?php
                    if ($rows[0]==0)
                    {
                    ?>
                    <h5 style="color:green;">Operacional</h5>
                  
                    <?php
                    }
                    else
                    {
                    ?>
                      <h5 style="color:red;">Avariado</h5>
                   <?php
                     }         //else echo ('Avariado');       //echo ('Operacional');
                    ?>
                 
                    


                    <br> <br><br>
                    <br> <br>

                      <?php
                      if ($row['data_compra']<>null)
                      {
                      echo ('Data da compra: ');
                      echo ($row['data_compra']);
                      }
                      ?>
                     </th>


                    <td width="35%" >
                    
                    <?php echo('Nº série: '); echo $row['numserie']; echo('<br>'); 
                    echo('Marca / Modelo: '); echo $row['marca_modelo']; 
                    echo('<br><br>'); 
                    echo('CPU: ');echo $row['processador'];
                    echo('<br>'); 
                    echo('RAM (GB): ');echo $row['memoria']; 
                    echo('<br>'); 
                    echo('Disco: '); echo $row['disco']; 
                    echo('<br><br>'); 
                    echo('Gráfica: '); echo $row['placagrafica']; 
                    echo('<br>'); 
                    echo('Som: '); echo $row['placasom']; 
                    echo('<br>'); 
                    echo('Rede: '); echo $row['placarede']; 
                    echo('<br><br>'); 
                    echo('Monitor: ');echo $row['monitor'];  echo('<br>');
                    echo('Teclado: ');echo $row['teclado'];  echo('<br>');
                    echo('Rato: ');echo $row['rato']; 
                    echo('<br>');
                    ?>
                    
                 
                    </td>


                    <td width="25%" >
                    
                    <label>Dominio: </label>
                    <?php echo $row['dominio']; echo('<br>'); ?>
                    <label>IP: </label>
                    <?php echo $row['ip']; echo('<br>'); ?>
                    <label>Máscara: </label>
                    <?php echo $row['mascara_rede']; echo('<br>'); ?>
                    <label>Gateway: </label>
                    <?php echo $row['gateway']; echo('<br>'); ?>
                    <label>DNS principal: </label>
                    <?php echo $row['dns_principal']; echo('<br>'); ?>
                    <label>DNS alternativo: </label>
                    <?php echo $row['dns_alternativo']; echo('<br>'); ?>

                    <br /><br />
                   


                     </td>



                     <?php
                      if ($_SESSION['tipo']==1 )
                      {


                       

                     ?>
              
              <td >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualiequip?id=<?php echo $n ?>&&sa=<?php echo $said ?>&&idescola=<?php echo $idescola ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" > </a>
                 
                    &nbsp;   &nbsp;&nbsp;
                    <a onclick="a1('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');" title="Eliminar" href="<?php echo SVRURL ?>eliminaequi">
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
                   <!-- 
                   onclick="return confirm('Deseja eliminar o equipamento? (Todas as avarias serão eliminadas.)')" 
                    
                    &nbsp;   &nbsp;&nbsp;
                
                    <a title="Copiar" href="copia_equip.php?id=<?php echo $n ?>">
                    <img src="<?php echo SVRURL ?>images/copiar.svg" alt="Copiar" > </a> 
                   -->
                      </td>
                    
                    <?php
                      }
                     ?>
            
                    
                
                </tr>
                <?php } 
                //echo($c);
                 //$totoalPages = ceil($totallinhas / $limit);
                ?>
      </tbody>
        </table>     
                
        
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&sala=".$said."&&escola= $idescola&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=1&&sala=<?php echo($said);?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&sala=".$said."&&escola=$idescola&&page=". $next; } ?>">>></a>
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