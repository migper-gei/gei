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





?>
   

   <?php

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


$x=base64_decode($_GET["x"]);
//echo ('x:');
//echo $x;

if ( $x>1 || $x<0 || !isset($x)  || !is_numeric($x)   )  
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



      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Salas</a>
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

    

<script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>




<div style="  text-align: center;">   
      
<form action="<?php echo SVRURL ?>salasnum" method="post">

<button title="Ver nº de salas por escola" type="submit" class="btn btn-outline-primary" > Ver nº de salas por instituição</button>

</form>

<!--
<a  class="underlineHover" href="<?php echo SVRURL ?>salas_num_escola.php" title="Ver nº de salas por escola" style="color:blue;font-size:16px;">Ver nº de salas por escola </a>
-->
</div >

<br>


<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>





<br>


<form name="frm" id="frm" action = "" method = "post" >



<div style="text-align: left;">

<!--
<h3 style="color:black;">ESCOLA/INSTITUIÇÃO:
-->

<?php
//echo base64_decode($_GET["esi"]);


if (!isset($_GET['esi']) && $x==1 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>


<?php
}

/*
echo '<br>';

echo '<br>';
echo 'x: '.$_GET['x'];
echo '<br>';
echo 'get: '.($_GET['esi']);
echo '<br>';
echo 'req: '.($_REQUEST["escola"]);
*/
?>

<select style="width:100%;" class="custom-select" name="escola" onChange="showesc(this.value);" >


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by id";
$result2 = mysqli_query($db,$sql2);

//echo('<option value=""> Escolha a escola  </option>');  

while($row2=mysqli_fetch_array($result2))
{


if ($x==0  )
  {
    if ($row2['id']==($_REQUEST["escola"]) )
    {
      //'.$row2['nome_escola'].'
       echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
  
  
    }
    else
  {
   echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
  }
  
  
  }



  
if ($x==1 && !is_numeric($_GET["esi"]) && !is_numeric($_REQUEST["escola"]))
  {
   
  
    if ($row2['id']==base64_decode($_GET["esi"]) )
    {
      //'.$row2['nome_escola'].'
       echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
  
  
    }

   
    else
  {
   echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
  }
  
  }



    
if ($x==1 && !is_numeric($_GET["esi"]) && is_numeric($_REQUEST["escola"]))
{
 

  if ($row2['id']==($_REQUEST["escola"]) )
  {
    //'.$row2['nome_escola'].'
     echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');


  }

 
  else
{
 echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
}

}







}
echo('</select>');

?>

</div>


</form>






<?php





$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];

//echo $x;
//echo ('<br>');
  



if (!empty($_POST["escola"]) && is_numeric($x) )
{
              
              $esc=$_POST["escola"];
              
}     
              elseif ($x==0) 
              {
               $esc=$nes;   
              }
              elseif ($x==1) 
              {
               $esc=base64_decode($_GET['esi']);  
              }
           
             //echo 'EE'.$esc;
            // echo 'x'.$_GET['esi'];
           
        ?>
        
    <?php
    
    if ( !is_numeric($esc) )
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
 

$sql1 = "select nome_escola
from escolas 
where id=$esc";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$ne = $rows[0];
           
           ?>
    
    <div class="text-center mt-3">
<span class="badge badge-primary p-2" style="font-size: 1rem;">
    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
</span>
</div>
</div>

    
    
    <br>

    <?php
     if ($_SESSION['tipo']==1 )
     {



      $sql3 = "select count(*) as conta,id,nome from salas 
      where id_escola=$esc and id not in (
      select  s.id
      from equipamento e, salas s 
      where s.id=e.id_sala 
      and s.id_escola=$esc )";
      $result3 = mysqli_query($db,$sql3);

      $rows3 =mysqli_fetch_row($result3);
  

      $contasalas = $rows3[0];

      //echo $contasalas;


   ?>
    <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Só é possível eliminar salas sem equipamento associado. 
<br>
<div style="text-align: right;">
<a href="<?php echo SVRURL ?>salas_csv.php?id=<?php echo base64_encode($esc);?>" target="_blank">
<button class="btn btn-outline-secondary" title="Exportar para CSV" type="submit"  >
<i class="fas fa-file-csv btn-icon"></i>  
Exportar CSV</button>
</a>
&nbsp;&nbsp;&nbsp;


<?php
if ($contasalas>0) {
?>

<a onclick="a1('<?php echo $esc;?>','<?php echo $ne;?>')"
 href="<?php echo SVRURL ?>elimina_salas_semequi.php?id=<?php echo base64_encode($esc);?>" target="_blank">
<button title="Eliminar todas as salas sem equipamento" type="submit" class="btn btn-danger-action" >
Eliminar todas
</button>
<!--    
<img title="Eliminar salas sem equipamento" src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > -->
     </a>

<?php
}
?>

     </div>
     <br>
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


$sqlz = "select s.id as idsala,s.* 
from salas s, escolas e
where s.id_escola=e.id and e.id=$esc
order by s.nome
LIMIT $paginationStart, $limit";

$resultz = mysqli_query($db,$sqlz);


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


function a1(ne,es) {

  var es1,ne1;

es1=es;
ne1=ne;



 //alert(es1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar todas as salas sem equipamento?",
 text: "Instituição: "+es1+" ",
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
    window.location.href = '<?php echo SVRURL ?>eliminasalasemequi/'+ne1;
}, 10);


          
  } else {
    swal("Cancelado.");

 

  }

});

}





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
 text: "Sala: "+s1+" (Instituição: "+ne1+")",
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
  //  window.location.href = '<?php echo SVRURL ?>salas?x=1&&esi='+es1;
//}, 10);
  

  }

});

}

</script>



       <!-- Select dropdown -->
       <div class="d-flex flex-row-reverse bd-highlight mb-3">
        
            <form action="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($esc);?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable class="table table-striped"-->
        <table   class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-primary" >
                 
                    <th  scope="col">Nome</th>
                    <th   scope="col">Localização</th>
                    <th  scope="col">Departamento / Grupo / Serviço</th>
                    <th  scope="col">Com equipamento requisitável?</th>
                     
                     <?php
                      if ($_SESSION['tipo']==1)
                      {
                     ?>

                    <th  colspan="3" style="text-align: center;">


                    <a class="btn btn-primary"  title="Inserir sala" href="<?php echo SVRURL ?>inserirsala?ie=<?php echo base64_encode($esc) ?>">
                 
                    <p style="color:white;"> 
                 
                       <i class="fa-solid fa-plus"></i> </p></th></a>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($resultz)) { 
                    $n=$row['idsala'];
                    $sa=$row['nome'];
                    //echo($sa);
                    //echo $n;

                    $sql2 = "select count(*) from equipamento,salas 
                    where salas.id=equipamento.id_sala
                    and salas.id='$n' 
                    and salas.id_escola=$esc";
                    $result2 = mysqli_query($db,$sql2); 
                    $rows2 =mysqli_fetch_row($result2);
                    
                    $contasala = $rows2[0];
                    //echo($contasala);



                    $sql2a = "select count(*) from outro_equipamento,salas 
                    where salas.id=outro_equipamento.id_sala
                    and salas.id='$n' ";
                    $result2a = mysqli_query($db,$sql2a); 
                    $rows2a =mysqli_fetch_row($result2a);
                    
                    $contasalaoe = $rows2a[0];
                    ?>
                <tr>
             
                      <td width="20%"  scope="row"><?php echo $row['nome']; ?></td>
                    <td width="25%" ><?php echo $row['localizacao']; ?></td>
                    <td width="25%" ><?php echo $row['departamento']; ?></td>
                    <td width="10%" ><?php echo $row['equip_requisitavel']; ?></td>
                    
                    <?php
                      if ($_SESSION['tipo']==1 )
                      {


                     ?>
                    <td width="5%">
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualizasala/<?php echo base64_encode($n) ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a></td>
                    <td width="5%">

                    <?php
                    //&& $row['equip_requisitavel']<>'Sim'
                      if ($contasala==0 && $contasalaoe==0)
                      {
                     ?>
              
                        
                    <a onclick="a(<?php echo $n;?>,'<?php echo $sa;?>','<?php echo $esc;?>','<?php echo $ne;?>');" 
                    title="Eliminar" href="<?php echo SVRURL ?>eliminasala">
                  
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(1)."&&esi=".base64_encode($esc)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo( base64_encode($esc) );?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=".base64_encode(1)."&&esi=".base64_encode($esc)."&&page=". $next; } ?>">>></a>
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