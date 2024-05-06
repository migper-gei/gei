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
               <a href="#" class="btn btn-secondary disabled">LISTAGENS >> QUANTIDADE DE EQUIPAMENTO POR SALA</a>
               <div class="titlepage">
                     <h2>
                    <?php echo $ne ?>
                    </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

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


$sql = "select s.nome,tipo,count(*) as qta 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
group by s.nome,tipo 
order by s.nome,tipo asc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$totallinhas=$result->num_rows;

//echo $totallinhas;


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>


<br>

      


<?php
$sql0 = "select distinct(s.id), s.nome
from equipamento e, salas s, outro_equipamento oe
where e.id_sala=s.id and s.id_escola=$idescola or s.id=oe.id_sala
order by s.nome asc";
$result0 = mysqli_query($db,$sql0);
?>




    
<?php  
                
                $c=0;
                while($row0=mysqli_fetch_array($result0)) { 
                    $idsa=$row0['id'];
                    $nos=$row0['nome'];
                  //  echo $row0['nome']; 
                  //echo $idsa;
                    
                
?>

<?php

$sql001 = "select id_escola
from  salas s
where s.id=$idsa";
$result001 = mysqli_query($db,$sql001);
$rows001 =mysqli_fetch_row($result001);

/*
echo $rows001[0];
echo '<br>';
echo $idescola;
*/

if ($rows001[0]==$idescola)
{
?>

        <!-- Datatable -->
        <table class="table table-sm" id="js-sort-table">
            <thead>
                <tr class="table-success">
                    <th scope="col">SALA: <?php echo $nos?>  </th> 
                    <th scope="col">Tipo / Outro equipamento</th>
                    <th class="js-sort-number" scope="col">Quantidade</th>
              
                </tr>
            </thead>
            <tbody>

              

      

                <?php  







$sql01 = "select s.nome,tipo,count(*) as qta 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola and s.id=$idsa
group by tipo 
order by tipo asc";
$result01 = mysqli_query($db,$sql01);

                
              //  $c=0;
                while($row=mysqli_fetch_array($result01)) { 
                    //$n=$row['id'];
                   
              
                     // $c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <!--
                <td   width="20%"  scope="row"><?php echo $row['nome']; 
                    
                    ?>
                 
                    </td>
                -->
                <td   width="45%"  scope="row"> </td>
                 
                   

                    <td width="40%"  scope="row"><?php echo $row['tipo']; 
                    
                    ?>
                 
                    </td>
                    <td width="15%" >
                    
                    <?php echo $row['qta']; echo('<br>'); 
                    
                    ?>
                    
                 
                    </td>

                
                </tr>
                <?php }     
                
                
                
                
                ?>




<?php

//echo '<br>';
//echo $idescola;

        $sql5 = "
  select oe.*, sum(oe.qta) as so from outro_equipamento oe, salas s
where oe.id_sala=s.id
and  s.id_escola=$idescola and s.id=$idsa
group by oe.nomeoutro
order by oe.nomeoutro ";
 
  $result5 = mysqli_query($db,$sql5);

  $count = mysqli_num_rows($result5);
?>




            
      
            <?php 
                
                while($row5=mysqli_fetch_array($result5)) { 
                   // $n=$row['id'];
                    //$noeq=$row['nomeequi'];
               
             

                    ?>


<tr>
<td   width="45%"  scope="row"> </td>

                    <td width="40%"  scope="row"><?php  echo $row5['nomeoutro'];  ?>
               
                                     
                     </td>


                    <td width="15%" contenteditable="true" >
                    
                    <?php  echo $row5['so']; ?>
                   
                    
                    
                 
                    </td>

                    
                
                </tr>
               



                <?php } 
                  
             
            }   
        }
                ?>





            </tbody>
        </table>     
                
   
           



<br>








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