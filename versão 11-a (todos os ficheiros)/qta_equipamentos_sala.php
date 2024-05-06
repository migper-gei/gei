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



<?php

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$idescola=base64_decode($_GET["ies"]);


$x=base64_decode($_GET["x"]);

//echo $x;





if ($x>1 || $x<0 || base64_decode ($_GET['ies'])>$maxesc || $idescola<0 
|| !isset($x) || !is_numeric($x)  
|| !isset($_GET['ies']) || !is_numeric(base64_decode ($_GET['ies'])) 
 || empty($_GET['ies'])  
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
$sa=$_POST["sala"];
$z1=base64_decode($_GET["z"]);
}
elseif  ($x==1)
{
$sa=base64_decode ($_GET["si"]);
$z1=base64_decode($_GET["z"]);
}

//echo $z1;

if ( !isset($sa) || empty($sa)  || !is_numeric($sa)   || !isset($z1) || empty($z1))
{

    

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}










$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];
 $num_ns = mysqli_num_rows($result10);


$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
?>



<?php
     if ($num_ns==0 || $num_ne==0 )
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
               <a href="#" class="btn btn-secondary disabled">LISTAGENS >> QUANTIDADE DE EQUIPAMENTO POR SALA</a>
               <div class="titlepage">
                     <h2>  <?php echo($ns);?> <BR> <?php echo($ne);?> </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        

<?php
include("msg_bemvindo.php");
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
$em=$_SESSION['email'];


  $sql = "select tipo,count(*) as qta from equipamento 
  where id_sala=".$sa." 
  group by tipo 
  order by tipo asc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  $totallinhas=$result->num_rows;

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?>


<br>

      <!-- Select dropdown -->
      <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="qta_equipamentos_sala.php?z=<?php echo base64_encode($z1) ?>&&x=<?php echo base64_encode(1) ?>&si=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola) ?>" method="post">
                      <?php include("num_linhas.php");?>
            </form>
        </div>

        <!-- Datatable -->
        <table class="table table-striped" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">Tipo</th>
                    <th class="js-sort-number" scope="col">Quantidade</th>
                    <th class="js-sort-number" scope="col">Equipamento</th>
                                 
                    
                </tr>
            </thead>
            <tbody>

              

      

                <?php  



                
                $c=0;
                $somaqta=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      //$c=$c+1;
                      //$totallinhas = $c;
                   
                   
               
                  
                          
               

                    ?>
                <tr>
                    <td width="40%"  scope="row"><?php echo $row['tipo']; 
     
                 
                    ?>
                 
                    </td>






                    <td width="10%" >
                    
                    <?php echo $row['qta']; echo('<br>'); 
                    
                    $somaqta=$somaqta+$row['qta'];
                    
                    ?>
                    
                 
                    </td>


                    <td>
                         <?php

                    $sql4 = "
                      SELECT nomeequi
                      FROM equipamento 
                      WHERE id_sala=".$sa." and tipo='".$row['tipo']."'
                      order by nomeequi;";
                       
                    $result4 = mysqli_query($db,$sql4);
                    while($row4=mysqli_fetch_array($result4)) { 
               

                        echo $row4['nomeequi'];
                        echo ('  |  ');
                                }
            
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
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?z=".base64_encode($z1)."&&x=".base64_encode(1)."&&si=".base64_encode($sa)."&&ies= ".base64_encode($idescola)."&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a style="color:black;" class="page-link" href="qta_equipamentos_sala.php?z=<?php echo base64_encode($z1) ?>&&x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($idescola) ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?z=".base64_encode($z1)."&&x=".base64_encode(1)."&&si=".base64_encode($sa)."&&ies=".base64_encode($idescola)."&&page=". $next; } ?>">>></a>
                </li>

                <li class="page-item ">
                <?php
      echo str_repeat("&nbsp;", 5);
        echo("TOTAL: ". $somaqta);
        ?>
                </li>
            </ul>
        </nav>
       



 
        <br><br><br>


<?php
  $sql2 = "
  SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
  FROM avarias_reparacoes a,equipamento e, salas s
  WHERE a.id_equi=e.id and s.id=e.id_sala and a.id_escola=$idescola
  and a.id_sala=".$sa." 
  and a.datareparacao is null  group by e.tipo order by tipo asc";
  
  
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
  $sql2 = "
  SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
  FROM avarias_reparacoes a,equipamento e 
  WHERE a.id_sala=".$sa." and a.id_escola=$idescola
  and a.datareparacao is null and a.id_equi=e.id group by e.tipo order by tipo asc";
   
$result2 = mysqli_query($db,$sql2);


      

while($row2=mysqli_fetch_array($result2)) { 

$tipo=$row2['ti'];
?>
<tr>
<td width="30%"  scope="row"><?php echo $row2['ti']; echo('<br>');

?>

</td>
<td width="10%" >

<?php echo $row2['c2'];  
//echo ('          ');


?>




</td>

<td width=50%>
<?php
$sql3 = "SELECT DISTINCT(e.nomeequi) as n FROM equipamento e, avarias_reparacoes a  
WHERE e.id_sala=".$sa." and a.datareparacao is null and e.tipo='".$tipo."' 
and a.id_equi=e.id
 order by e.nomeequi asc;"; 
$result3 = mysqli_query($db,$sql3);

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
   <?php
 
    }


   
?>

<br>


   

<?php include ("jquery_bootstrap.php");


//echo $x;
?>




<?php
//echo $sa;
//echo '<br>';
//echo $idescola;

        $sql5 = "
  select oe.* from outro_equipamento oe, salas s
where oe.id_sala=s.id
and s.id=$sa and s.id_escola=$idescola
order by oe.nomeoutro ";
 
  $result5 = mysqli_query($db,$sql5);

  $count = mysqli_num_rows($result5);
?>


<br>








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
                    <th width="30%"  scope="row"><?php  echo $row5['nomeoutro'];  ?>
               
                                     
                     </th>


                    <td width="35%" contenteditable="true" >
                    
                    <?php  echo $row5['qta']; 
                   
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

        <br>  




                <?php 
                mysqli_close($db);
                ?>











<?php 
$z1=base64_decode($_GET["z"]);

//echo $z1;

if ($z1=='eq')
{
$sa=base64_decode($_GET["si"]);
    ?>

<a href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(2)?>&&ies=<?php echo base64_encode($idescola)?>&&si=<?php echo base64_encode($sa)?>">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<?php 
}
elseif ($z1=='li')
{


    ?>
<a href="<?php echo SVRURL ?>lista">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
    <?php
}
    ?>



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