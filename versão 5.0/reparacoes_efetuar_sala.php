﻿<!DOCTYPE html>
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

if ($_GET["x"]==0)
{
    $sa=$_POST["sala"];
    $idescola=$_GET["escola"];
}
else{
    $sa=$_GET["sa"]; 
    $idescola=$_GET["escola"];
}

?>


<?php

$op=$_GET["op"];

//echo $op;

if ($op=='t')
{
$op2='Todas';
}
elseif ($op=='al')
{
$op2='Ano letivo';


}
else
{
?>
<script>
  window.setTimeout(function() {
      window.location.href = '<?php echo SVRURL ?>reparacoes_efetuar_sala.php?op=t';
  }, 10);
  </script>
<?php

}
?>
      <?php

$sql3 = "select max(ano_lectivo) from periodos";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);
?>
     <?php
//session_start();



include("sessao_timeout.php");



$sql10 = "select nome from salas where id=$sa";
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
                     <h2>Reparações a efetuar <br> (<?php echo $ns ?> | <?php echo $ne ?> ) <br> <?php echo $op2?> <br>
                    <h4>
                     <a style="color:black;" class="underlineHover" title="Reparações a efetuar (todas)" 
                       href="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=1&&op=t&&sa=<?php echo($sa);?>&&escola=<?php echo $idescola ?>">
                       Todas </a>  &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;   
                       <a style="color:black;" class="underlineHover" title="Reparações a efetuar (ano letivo)" 
                       href="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=1&&op=al&&sa=<?php echo($sa);?>&&escola=<?php echo $idescola ?>">
                        Ano letivo: <?php echo $rows3[0]; ?></a>
                          </h4>
                    </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                  <h4>         

                  <?php
           
           include("verifica_sessao.php");
             
             ?>
           

<script> 

            

            // Function to increase image size 

            function enlargeImg(img) { 

                       img.style.transform = "scale(2.5)"; 

                       img.style.transition = 

                         "transform 0.25s ease"; 

                   } 

       

       

                   function resetImg(img) { 

                       img.style.transform = "scale(1)"; 

                       //img.style.width = "40%"; 

                       //img.style.height = "auto"; 

                       //img.style.transition = "width 0.5s ease"; 

                   } 

               </script> 

       







<br>

<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        No fim da reparação será enviado um email ao autor da avaria. 



<?php 

  if(isset($_POST['records-limit'])){

      $_SESSION['records-limit'] = $_POST['records-limit'];

  }

  

  $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;

  $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;

  $paginationStart = ($page - 1) * $limit;

  

//echo($paginationStart);

//echo($limit);

$em=$_SESSION['email'];

//$sa=$_POST['sala'];

if ($op=='t')
{

  
    $sql = "select * from avarias_reparacoes where datareparacao is null and id_sala='".$sa."' order by dataavaria desc LIMIT $paginationStart, $limit";

  $result = mysqli_query($db,$sql);





  // Get total records

   $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null and id_sala='".$sa."'";

  $result1 = mysqli_query($db,$sql1); 

  $rows =mysqli_fetch_row($result1);

}



if ($op=='al')
{

  $sql = "select * from avarias_reparacoes where datareparacao is null and id_sala='".$sa."' 
  and ano_letivo='".$rows3[0]."' 
  order by dataavaria desc LIMIT $paginationStart, $limit";

  $result = mysqli_query($db,$sql);





  // Get total records

   $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null 
   and ano_letivo='".$rows3[0]."' and id_sala='".$sa."'";

  $result1 = mysqli_query($db,$sql1); 

  $rows =mysqli_fetch_row($result1);

}










  $totallinhas = $rows[0];

//echo($totallinhas );





  

  // Calculate total pages

  $totoalPages = ceil($totallinhas / $limit);



  // Prev + Next

  $prev = $page - 1;

  $next = $page + 1;

?>






<script language="JavaScript" >

function enviardados2(){
alert("aaaa");
    
//document.getElementById('reparacao').submit();

var z=document.getElementById('datarep').value;
alert(z);


var dre= document.getElementsByName("datarep")[0].value;

var dav= document.getElementsByName("dav")[0].value;


alert(dre);

alert(dav);


if(dre<dav)
{
      alert( "A data de reparação deve ser igual ou superior à data da avaria." );

      return false;
}

else {
      return true;

  }




}



</script>







        <!-- Select dropdown      <php include("num_linhas.php");?>
    action="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=1&&sa=<?php echo($sa);?>"         
    -->
     
    <div class="d-flex flex-row-reverse bd-highlight mb-3">
        <form action="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?op=<?php echo $op?>&&x=1&&sa=<?php echo($sa);?>&&escola=<?php echo($idescola);?>" method="post"  name="form1">
 

 <select name="records-limit"  onchange="this.form.submit()" class="custom-select">
                    <option disabled selected>Nº Linhas</option>
                    <?php foreach([10,20,30,40,50] as $limit) : ?>
                    <option
                        <?php if(isset($_SESSION['records-limit']) && $_SESSION['records-limit'] == $limit) echo 'selected'; ?>
                        value="<?= $limit; ?>">
                        <?= $limit; ?>
                    </option>
                    <?php endforeach; ?>
                </select>



                </form>
                </div>





        <!-- Datatable -->

        <table class="table table-striped">

            <thead>

                <tr class="table-success">

                    <th scope="col">Equipamento</th>

                    <th scope="col">Avaria</th>

                    <th scope="col">Reparação</th>


                </tr>

            </thead>

            <tbody>




                <?php  while($row=mysqli_fetch_array($result)) { 

                    $n=$row['id'];
                    
                 

                    $em= $row['autoravaria'];

                   



                    $sql2 = "select nome from utilizadores where email='$em' ";

                    $result2 = mysqli_query($db,$sql2); 

                    $rows2 =mysqli_fetch_row($result2);

                    

                   // echo ($rows2[0]); 



                   $sql10 = "select nomeequi from equipamento where id=".$row['id_equi']."";
                   $result10 = mysqli_query($db,$sql10); 
                   $rows10 =mysqli_fetch_row($result10);
                   
                    $noeq = $rows10[0];

                    ?>


<form  name="reparacao" id="reparacao" action="<?php echo SVRURL ?>repara_avaria.php?id=<?php echo ($row['id']);?>&&em=<?php echo ($em) ?>" method="post" 
 >
            <!--
onSubmit="return enviardados2();"
            -->
                <tr>

                <th width="20%"  scope="row"><?php  echo $noeq;?>


                    </th>

                    <td width="35%" >

                    
                    <label>Autor: </label> 
                    <?php echo ($rows2[0]); //echo(' - '); echo $row['autoravaria']; 
                    echo('<br>'.'Email: '); 
                     echo $em; 
                    echo('<br><br>'); 

                     echo $row['dataavaria']; 

                    echo('<br><br>'.'Descrição:'.'<br>'); 
                    echo $row['avaria']; echo('<br>');

                    ?>

              
                    

                    <?php

                     

                    if ($row["imgavaria"] == null) 

                    {

                    echo ("");

                    }

                    

                    else {?>

                   

                  

                   <?php 

                   echo '<img onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 

                   height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).' ">';

                    }

                    

                   ?>

                  

                    </td>



         



               
                    <td width="35%" >

                    <label>Data: </label>  

                     <input value="<?php echo date("Y-m-d");?>"  required id="datarep"   size="10" type = "date" name = "datarep" >

                    <!--value="<?php echo date("Y-m-d");?>" -->

                    <br /><br />

                  

                    <label>Reparação: </label>  <br>  

                    <textarea  required  style="text-align: justify;" rows="3" cols="35"  name="reparacao"></textarea>   

                    <br /><br />

                    <label>Reparado por: </label>

                    <input required  size="36" type = "text" name = "repar_por" >  
                    
                    

                    <input hidden name = "dav" value="<?php echo $row['dataavaria'];  ?>" > 
                                  

                    



                   <!-- 
                    <br /><br />

                    <label>Resolvido: </label>

                    <select required name="resolvido">

                          <option value=""></option>

                          <option value="Sim">Sim</option>

                          <option value="Não">Não</option>

                    </select>

                -->



                     </td>







                    <td width="1%" >

                    <!--

                        <a title="Reparação" >

                    href="repara_avaria.php?id=<php echo $n ?>"

                       

                    <img src="images/reparacao.png" alt="Reparação" > </a> 

                                  

                    <input alt="Reparação" type="image" src="images/reparacao.png"/>  -->

                    <input  title="Reparar" alt="Reparar" type="image" src="<?php echo SVRURL ?>images/reparacao.svg"/> 

                    </td>

                    

                   
                   </form>


                    

                

                </tr>

                <?php } ?>

            </tbody>

        </table>     

              

      
        

        <!-- Pagination -->

        <nav aria-label="Page navigation example mt-5">

            <ul class="pagination justify-content-center">

                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">

                    <a class="page-link"

                        href="<?php if($page <= 1){ echo '#'; } else { echo "?x=1&&sa=".$sa."&&op=$op&&escola= $idescola&&page=" . $prev; } ?>"><<</a>

                </li>



                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>

                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">

                    <a class="page-link" href="reparacoes_efetuar_sala?x=1&&sa=<?php echo($sa);?>&&op=<?php echo $op ?>&&escola=<?php echo $idescola ?>&&page=<?= $i; ?>"> <?= $i; ?> </a>

                </li>

                <?php endfor; ?>



                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">

                    <a class="page-link"

                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?x=1&&sa=".$sa."&&op=$op&&escola= $idescola&&page=". $next; } ?>">>></a>

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
    


      <?php 
      mysqli_close($db);
      include ("footer.php");?>


   </body>
</html>