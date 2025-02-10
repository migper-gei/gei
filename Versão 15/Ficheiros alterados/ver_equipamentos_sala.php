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

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

//echo  base64_decode ($_GET['ies']);
//echo $_GET['ies'];
$x=base64_decode($_GET["x"]);
$idescola=base64_decode($_GET["ies"]);

//echo $x;
//echo $idescola;

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if ($x==2 && (empty($_POST['sala']) || !isset($_POST['sala']) )  

)

{
  $said=base64_decode($_GET["si"]);
  $idescola=base64_decode($_GET["ies"]);
}
else
{  


if ($x>2 || $x<0 || !is_numeric($x)
|| $idescola>$maxesc || $idescola<0 
|| !isset($x) || !isset($idescola) || !is_numeric($idescola) 
 || empty($idescola)  
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}



//echo $_POST["sala"];


if ($x==0 && (!isset($_POST["sala"]) || empty($_POST["sala"])))
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}


}



//echo $_POST["sala"];
//echo $_GET["x"];
     
     if ($x==1)
     {
     $said=base64_decode($_GET["si"]);
     $idescola=base64_decode($_GET["ies"]);
     }
     elseif ($x==0)
     {
     $said=$_POST["sala"];
     $idescola=base64_decode($_GET["ies"]);
     }
   

     
     
   
      

  //echo $said ;

     $sql10 = "select nome from salas where id=$said";
     $result10 = mysqli_query($db,$sql10); 
     $rows10 =mysqli_fetch_row($result10);
     
      $ns = $rows10[0];
      $num_ns = mysqli_num_rows($result10);
      //echo $num_ns;

     $sql11 = "select nome_escola from escolas where id=$idescola";
    $result11 = mysqli_query($db,$sql11); 
    $rows11 =mysqli_fetch_row($result11);

 
    $ne = $rows11[0];
    $num_ne = mysqli_num_rows($result11);
   
    //echo $num_ne;
    //echo ($_GET["x"]);

     ?>
     

<?php
     if ($num_ns==0 || $num_ne==0 || $x>2)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>equip';
}, 10);
</script>


<?php

}

?>




      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS</a>
               <div class="titlepage">
                     <h2> Equipamento da sala  
                     <?php echo($ns);?> <br> <?php echo $ne ?>
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

  title: "Deseja eliminar? (Vai eliminar também as avarias caso seja eq. informático)",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    window.location.href = '<?php echo SVRURL ?>eliminaequip/'+n0+'/'+ides1+'/'+said1;
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






function a1a(n,no,ne,noeq,ides,said) {

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

  title: "Deseja eliminar? ",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    window.location.href = '<?php echo SVRURL ?>eliminaoutequip/'+n0+'/'+ides1+'/'+said1;
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




<script>
function a2(n,no,ne,noeq,ides,said) {

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

  title: "Deseja mudar o equipamento de sala?",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    window.location.href = '<?php echo SVRURL ?>mudarsalaequi/'+n0+'/'+ides1+'/'+said1;
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







function a2a(isa,sa,es) {

var isa1,sa1,ne1;

isa1=isa;
sa1=sa;
ne1=es;



//alert(es1);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja eliminar todos os equipamentos informáticos (todas as avarias serão eliminadas)?",
text: "Sala: "+sa1 + " | " + "Instituição: "+ne1+" ",
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
  window.location.href = '<?php echo SVRURL ?>eliminaequisala/'+isa1;
}, 10);


        
} else {
  swal("Cancelado.");



}

});

}









function a2b(isa,sa,es) {

var isa1,sa1,ne1;

isa1=isa;
sa1=sa;
ne1=es;



//alert(es1);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja eliminar todos os outros equipamentos?",
text: "Sala: "+sa1 + " | " + "Instituição: "+ne1+" ",
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
  window.location.href = '<?php echo SVRURL ?>eliminaoutequisala/'+isa1;
}, 10);


        
} else {
  swal("Cancelado.");



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
  


  $sql = "
  select e.*,s.nome from equipamento e, salas s
where e.id_sala=s.id
and s.id=".$said." and s.id_escola=".$idescola."
order by e.tipo desc
  LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


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
   <br>
<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Ao eliminar o equipamento serão eliminadas todas as avarias. 
        <?php
     }
     ?>    
 
 <br>
<div style="  text-align: center;">              
      
<form action="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode('eq') ?>&&x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode ($idescola)?>&&si=<?php echo  base64_encode ($said)?>" method="post">
<button title="Resumo do equipamento" type="submit" class="btn btn-outline-primary" >Resumo do equipamento</button>
    </form>
<!--
<a  class="underlineHover" href="<?php echo SVRURL ?>qta_equipamentos_sala.php?x=1&&escola=<?php echo $idescola?>&&sa=<?php echo $said?>" 
title="Resumo do equipamento" style="color:blue;font-size:16px;">Resumo do equipamento</a>-->
</div>






        <!-- Select dropdown-->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode ($said);?>&&ies=<?php echo base64_encode ($idescola) ?>" method="post">
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
if ( ($totallinhas>0) && ($_SESSION['tipo']==1) ) {
  ?>
                    <th scope="col">

<?php
                 
  //echo $ns;
  //echo $ne;
  //echo $said;
?>
   &nbsp;&nbsp;&nbsp;


   <a onclick="a2a(<?php echo $said;?>,'<?php echo $ns;?>','<?php echo $ne;?>')"
 href="<?php echo SVRURL ?>elimina_equi_sala.php?id=<?php echo base64_encode($said);?>" target="_blank">
<button title="Eliminar todos os equipamentos informáticos da sala" type="submit" class="btn btn-outline-primary" >
X
</button>
   </a>

   <?php
}
?>

                    </th>
                 
             
                     
                  
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
                    <br>  <br><br>
                    Escola Digital: 
                    <?php
                    echo ($row['escola_digital']);
                  
                    ?>
                    <br>
                     
                    <?php
                    if ($row['escola_digital']=="Sim")
                {
                    ?>

                    Nº Dgest: 
                    <?php
                    echo ($row['num_inv_dgest']);
                    ?>
                    <br>
                    Fornecedor / Email: <br>
                    <?php
                    echo ($row['fornecedor']); echo('<br>'); echo ($row['email_fornecedor']);
                    ?>
                    
                <?php
                }
                ?>



                    <br> <br> <br>
                    Estado:
                    <?php
                    if ($rows[0]==0)
                    {
                      echo '<em style="color:green;font-size:14px;">
      Operacional </em>';
                    ?>
                   <!--
                   <h5 style="color:green;">Operacional</h5>
                    -->
                    <?php
                    }
                    else
                    {
                      echo '<em style="color:red;font-size:14px;">
                     Avariado </em>';
                    ?>
                 
                     <!--
                      <h5 style="color:red;">Avariado</h5>
                      -->
                   <?php
                     
                      
                    if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3) 
                    {
                      ?>
                     <a   title="Ver avaria" href="<?php echo SVRURL ?>reparacoes_efetuar_equip.php?ieq=<?php echo base64_encode($row['id']);?>&&sai=<?php echo base64_encode($said);?>&&ies=<?php echo base64_encode($idescola) ?>">
                     <img  src="<?php echo SVRURL ?>images/reparacao.svg">
                    </a> 
                    
                    </h5>
                    <?php
                    }


                     }         //else echo ('Avariado');       //echo ('Operacional');
                    ?>
                 
                    


                    <br> 
                    <br>

                      <?php



                      if ( ($row['data_compra']<>null) && ( strcmp($row['data_compra'], "0000-00-00") !== 0) )
                      {
                      echo ('Data da compra: ');
                      echo ($row['data_compra']);
                      }
                      else
                      {
                        echo ('Data da compra: ---');
             
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
                    echo('Disco (GB): '); echo $row['disco']; 
                    echo('<br><br>'); 
                    echo('Gráfica: '); echo $row['placagrafica']; 
                    echo('<br>'); 
                    echo('Som: '); echo $row['placasom']; 
                    echo('<br>'); 
                    echo('Rede: '); echo $row['placarede']; 
                    echo('<br><br>'); 
                    echo('Monitor: ');echo $row['monitor'];  
                    echo('<br>');
                    echo('Teclado: ');echo $row['teclado'];  
                    echo(' - ');
                    echo $row['tecladointerface'];
                    echo('<br>');
                    echo('Rato: ');echo $row['rato']; 
                    echo(' - ');
                    echo $row['ratointerface'];
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
                    <br />
                    <label>Observações: </label><br />
                    <?php echo $row['observacoes']; echo('<br>'); ?>
                    <br /><br />
                   


                     </td>



                     <?php
                      if ($_SESSION['tipo']==1 )
                      {


                       

                     ?>
              
              <td >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualiequip?ide=<?php echo base64_encode($n) ?>&&sai=<?php echo base64_encode($said) ?>&&ies=<?php echo base64_encode($idescola) ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" > </a>
                 
                    &nbsp;   &nbsp;&nbsp;
                    <a onclick="a1('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminaequip">
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
               
<br><br>
<a onclick="a2('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');" 
title="Mudar de sala" href="<?php echo SVRURL ?>mudasalaequi">
                    <img src="<?php echo SVRURL ?>images/mudarsala.svg" alt="Mudar de sala" > </a>
               
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(1)."&&si=".base64_encode($said)."&&ies=".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($said);?>&&ies=<?php echo base64_encode($idescola) ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=".base64_encode(1)."&&si=".base64_encode($said)."&&ies=".base64_encode($idescola)."&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>
       

        <br>     




        <?php
        $sql2 = "
  select oe.* from outro_equipamento oe, salas s
where oe.id_sala=s.id
and s.id=".$said." and s.id_escola=".$idescola."
order by oe.nomeoutro ";
 
  $result2 = mysqli_query($db,$sql2);

  $count = mysqli_num_rows($result2);
  //echo($count);
?>




<br>


<?php

if ($count>0)
{


  ?>



        <!-- Datatable outro equi-->
        <table class="table" id="js-sort-table">
            <thead class="table-info">
                <tr >
                    <th scope="col">Nome</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Observações</th>
<?php
if ( ($count>0)  && ($_SESSION['tipo']==1) ) {
  ?>
                    <th scope="col">


<?php
//echo $ns;
//echo $ne;
//echo $said;
?>
&nbsp;&nbsp;&nbsp;


<a onclick="a2b(<?php echo $said;?>,'<?php echo $ns;?>','<?php echo $ne;?>')"
href="<?php echo SVRURL ?>elimina_out_equi_sala.php?id=<?php echo base64_encode($said);?>" target="_blank">
<button title="Eliminar todos os outros equipamentos da sala" type="submit" class="btn btn-outline-primary" >
X
</button>
</a>

<?php
}
?>

</th>
             
                     
                  
                </tr>
            </thead>
            <tbody>


                <?php 
                //$c=0;

                  if ($count>0)
                  {
              



                while($row=mysqli_fetch_array($result2)) { 

               

                    $n=$row['id'];
                    $noeq=$row['nomeoutro'];
               
             

                    ?>
                <tr>
                    <th width="30%"  scope="row"><?php  echo $row['nomeoutro'];  ?>
               
                                     
                     </th>


                    <td width="35%" contenteditable="true" >
                    
                    <?php  echo $row['qta']; 
                   
                    ?>
                    
                 
                    </td>



                    

                    <td width="25%" >
                    
                                    
                    <?php echo $row['observacoes'];  ?>
           
                   


                     </td>



                     <?php
                      if ($_SESSION['tipo']==1 )
                      {


                       

                     ?>
              
              <td >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualioutequip?ide=<?php echo base64_encode($n) ?>&&sai=<?php echo base64_encode($said) ?>&&ies=<?php echo base64_encode($idescola) ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" > </a>
                 

              
                    &nbsp;   &nbsp;&nbsp;
                    <a onclick="a1a('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');" title="Eliminar" 
                    href="<?php echo SVRURL ?>eliminaoutequip">
                    <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
               

               
                      </td>
                    
                    <?php

                      }
                     ?>
            
                    
                
                </tr>





                
                <?php } 
                }
              
                
                  ?>
                
           


                  <?php
               
              }
             
              
                ?>
      </tbody>
        </table>     

    





        <a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br>


        <?php include ("jquery_bootstrap.php");?>


        <?php
      // Clear the session
		unset($_SESSION['escola']);
?>

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