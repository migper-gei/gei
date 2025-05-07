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
$idescola=base64_decode($_GET["ies"]);


if ($idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0 || !isset($x)    || !is_numeric($x) 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  

)
{



?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}



if ($x==0)
{

$idescola=base64_decode($_GET["ies"]);
}
elseif ($x==1)
{

$idescola=base64_decode($_GET["ies"]);
}


//echo $idescola;

$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
?>



<?php
     if ($num_ne==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>lista';
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
               <a href="#" class="btn btn-secondary disabled">Listagens >> Quantidade total de equipamento</a>
               <div class="titlepage">
                     <h2>
                    <?php echo $ne ?>
                    </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-9 offset-md-2">
              
                        

 <!-- Welcome Section -->
 <div class="welcome-section"> 
<?php
include("msg_bemvindo.php");
?>
        </div>
    
    

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
$em=$_SESSION['email'];


$sql = "select tipo,count(*) as qta 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
group by tipo 
order by tipo asc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$totallinhas=$result->num_rows;

//echo $totallinhas;


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



        <!-- Select dropdown -->
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="qta_equipamentos_total.php?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?>" method="post">
                     <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-primary">
                    <th scope="col">Tipo</th>
                    <th class="js-sort-number" scope="col">Quantidade</th>
              
                </tr>
            </thead>
            <tbody>

              

      

                <?php  
                
                $c=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                     // $c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td width="40%"  scope="row"><?php echo $row['tipo']; 
                    
                    ?>
                 
                    </td>
                    <td width="20%" >
                    
                    <?php echo $row['qta']; echo('<br>'); 
                    
                    ?>
                    
                 
                    </td>

                
                </tr>
                <?php }          
                   // echo($totallinhas);
                     
                    // Calculate total pages
                    $totoalPages = ceil($totallinhas / $limit);
                
                
                ?>

            </tbody>
        </table>     
                
        
        <img src="images/ordenar_tab.svg" alt="Ordenar coluna">
         Clicar na coluna para ordenar.

               
        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=".base64_encode(1)."&ies=".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="qta_equipamentos_total.php?x=<?php echo base64_encode(1)?>&&ies=<?php echo base64_encode($idescola)?>&&page= <?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "x=".base64_encode(1)."&&ies=". base64_encode($idescola)."&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ".$totallinhas);
        ?>
                </li>
            </ul>
        </nav>





        
 
        <br><br><br>

<?php

$sql2 = "SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
FROM avarias_reparacoes a,equipamento e 
WHERE  a.datareparacao is null and a.id_equi=e.id and a.id_escola=$idescola
group by e.tipo order by tipo asc"; 
      $result2 = mysqli_query($db,$sql2);

      if (mysqli_fetch_row($result2)<>null)
      {
?>


<table class="table table-sm" >
   <thead>
       <tr >
           <th scope="col">Tipo</th>
           <th  scope="col">Avariados</th>
         
                        
           
       </tr>
   </thead>
   <tbody>


<?php  

$sql2 = "SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
FROM avarias_reparacoes a,equipamento e 
WHERE  a.datareparacao is null 
and a.id_equi=e.id 
and a.id_escola=$idescola
group by e.tipo order by tipo asc"; 
      $result2 = mysqli_query($db,$sql2);

while($row2=mysqli_fetch_array($result2)) { 

?>
<tr>
<td width="40%"  scope="row"><?php echo $row2['ti']; echo('<br>');

?>

</td>
<td width="45%" >

<?php echo $row2['c2']; 

?>

<?php
$sql3 = "SELECT DISTINCT(e.nomeequi) as n 
FROM equipamento e, avarias_reparacoes a  
WHERE a.id_equi=e.id
and a.id_escola=$idescola
and a.datareparacao is null and e.tipo='".$row2['ti']."' 
and a.id_equi=e.id
 order by e.nomeequi asc;"; 
$result3 = mysqli_query($db,$sql3);

echo ('<br>');


while($row3=mysqli_fetch_array($result3)) { 
    echo $row3['n'];
    echo ('  |  ');
}



?>




</td>












</tr>
<?php }          

?>



</tbody>
</table>     


<?php
    }
    else
    {
        ?>
        <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Sem avarias. 

        <br>
   <?php
 
    }

  
?>

<br>



<?php


$sql3 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='USB'";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);

 $ratosusb = $rows3[0];

 $sql3a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='PS/2'";
 $result3a = mysqli_query($db,$sql3a); 
 $rows3a =mysqli_fetch_row($result3a);
 
  $ratosps2 = $rows3a[0];



  $sql4 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and  e.tecladointerface='USB'";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);

 $tecladosusb = $rows4[0];

 $sql4a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.tecladointerface='PS/2'";
 $result4a = mysqli_query($db,$sql4a); 
 $rows4a =mysqli_fetch_row($result4a);
 
  $tecladosps2 = $rows4a[0];




  $sql7 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and  e.tecladointerface='Sem fios'";
$result7 = mysqli_query($db,$sql7); 
$rows7 =mysqli_fetch_row($result7);

 $tecladossemfios = $rows7[0];

 $sql7a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='Sem fios'";
 $result7a = mysqli_query($db,$sql7a); 
 $rows7a =mysqli_fetch_row($result7a);
 
  $ratossemfios = $rows7a[0];




?>

<?php
if (($ratosusb+$ratosps2+$ratossemfios<>0) and ($tecladosusb+$tecladosps2+$tecladossemfios<>0))
{

?>



        <!-- Datatable ratos e teclados-->
        <table class="table-light" >
            <thead >
                <tr >
                <th width="5%">Interface </th>
                    <th width="5%">Ratos  
                    <a class="underlineHover" title="Ver por sala"   
                    href="<?php echo SVRURL ?>qta_ratos_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($idescola) ?>">
               
                   <p style="color:DodgerBlue;">
                    (<i class="fa-solid fa-magnifying-glass fa-flip-horizontal"></i> Ver por sala)
</p> 
              
                 <!-- <img alt="Ver por sala" src="images/lupa2.svg"> -->
                    </a>
                    </th>
                 
                    <th width="5%">Teclados

                    <a class="underlineHover" title="Ver por sala"   
                    href="<?php echo SVRURL ?>qta_teclados_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($idescola) ?>">

                    <p style="color:DodgerBlue;">
                    (<i class="fa-solid fa-magnifying-glass fa-flip-horizontal"></i> Ver por sala)
</p> 
                    </a>

                    </th>
             

                     
                  
                </tr>
            </thead>
            <tbody>

                 

                
                <tr>
                    <td >
               USB
                                     
                     </td>


                    <td >
                    
              <?php echo($ratosusb);?>
                    
                 
                    </td>

                    

                    <td  >
                    
                    <?php echo($tecladosusb);?>     
           
                            </td>

                
                </tr>

   
                
            <tr>
            <td>
              PS/2
                                     
                     </td>
            <td>
            <?php echo($ratosps2);?>
             </td>
             <td>
             <?php echo($tecladosps2);?>  
             </td>
                </tr>


                <tr>
            <td>
            Sem fios
                                     
                     </td>
            <td>
            <?php echo($ratossemfios);?>
             </td>
             <td>
             <?php echo($tecladossemfios);?>  
             </td>
                </tr>

                <tr>
            <td >
         
                                     
                     </td>
            <td >
            <?php echo('(');echo($ratosusb+$ratosps2+$ratossemfios);echo(')')?>
          
             </td>
             <td>
             <?php echo('(');echo($tecladosusb+$tecladosps2+$tecladossemfios);echo(')')?>
          
             </td>
                </tr>








               

      </tbody>
        </table>     

        <br>  

    
<?php
}
?>


<br>





<?php

//echo '<br>';
//echo $idescola;

        $sql5 = "
  select oe.*, sum(oe.qta) as so from outro_equipamento oe, salas s
where oe.id_sala=s.id
and  s.id_escola=$idescola
group by oe.nomeoutro
order by oe.nomeoutro ";
 
  $result5 = mysqli_query($db,$sql5);

  $count = mysqli_num_rows($result5);
?>




        <!-- Datatable outro equi-->
        <table class="table" id="js-sort-table">
            <thead class="table-info">
                <tr >
                    <th scope="col">Nome</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Observações</th>

                 
             
                     
                  
                </tr>
            </thead>
            <tbody>

            <?php
if ($count>0) {

?>
                <?php 
                //$c=0;
                while($row5=mysqli_fetch_array($result5)) { 
                   // $n=$row['id'];
                    //$noeq=$row['nomeequi'];
               
             

                    ?>
                <tr>
                    <td width="30%"  scope="row"><?php  echo $row5['nomeoutro'];  ?>
               
                                     
                     </td>


                    <td width="35%" contenteditable="true" >
                    
                    <?php  echo $row5['so']; 
                   
                    ?>
                    
                 
                    </td>



                    

                    <td width="25%" >
                    
                                    
                    <?php echo $row5['observacoes'];  ?>
           
                   


                     </td>



            
                    
                
                </tr>
               



                <?php } 
                   }
              
                else
                {
                  ?>
                
            <tr>
            <td>
              Sem registos.</td>
                </tr>


                  <?php
               
              }
             
              
                ?>

      </tbody>
        </table>     




 <?php  
                mysqli_close($db);
                ?>





<?php include ("jquery_bootstrap.php");?>




<a href="<?php echo SVRURL ?>lista">
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