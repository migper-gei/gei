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
      window.location.href = '<?php echo SVRURL ?>reparafaz?op=t';
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



      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Reparações a efetuar - <?php echo $op2?> </h2> 
                     <br> 
                      <h4>
                     <a style="color:black;" class="underlineHover" title="Reparações a efetuar (todas)" 
                       href="<?php echo SVRURL ?>reparafaz?op=t">
                       Todas </a>  &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;   
                       <a style="color:black;" class="underlineHover" title="Reparações a efetuar (ano letivo)" 
                       href="<?php echo SVRURL ?>reparafaz?op=al">
                        Ano letivo: <?php echo $rows3[0]; ?></a>
                          </h4>
                     
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
           

    

           <?php
           //include('config.php');
       
          


           if ($op=='t')
           {
           
       
           
             $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null";
             $result1 = mysqli_query($db,$sql1); 
             $rows =mysqli_fetch_row($result1);
             
             //echo($_SESSION['tipo']);
           
             $totallinhas = $rows[0];
            }
           
           
            
           if ($op=='al')
           {
           $sql0 = "select max(ano_lectivo) from periodos";
           $result0 = mysqli_query($db,$sql0); 
           $rows0 =mysqli_fetch_row($result0);
           
           
              
           
           $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null and ano_letivo='".$rows0[0]."'";
           $result1 = mysqli_query($db,$sql1); 
           $rows1 =mysqli_fetch_row($result1);
           
           //echo($_SESSION['tipo']);
           
           $totallinhas = $rows1[0];
           }
           


if ($totallinhas >0 && isset($_SESSION['login_user']) && $_SESSION['tipo']==1)
                 {
             ?>
             &nbsp;
           <br>
           
         
           <?php
                 //echo('<br>');
               }
                
           ?>
           
            </h4>   

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





if ($op=='t')
{

 $sql = "select ar.*, e.nomeequi, s.nome
 from avarias_reparacoes ar, equipamento e, salas s
 where ar.id_equi=e.id and ar.id_sala=s.id
  and ar.datareparacao is null 
 order by ar.dataavaria asc LIMIT $paginationStart, $limit";
 $result = mysqli_query($db,$sql);



 }


 
if ($op=='al')
{

//echo $rows0[0] ;

$sql2 = "select ar.*, e.nomeequi, s.nome
from avarias_reparacoes ar, equipamento e, salas s
where ar.id_equi=e.id and ar.id_sala=s.id
  and ar.datareparacao is null 
and ano_letivo='".$rows0[0]."' 
order by dataavaria asc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql2);


}



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
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


<script language="JavaScript" >
function enviardados2(){

//alert("aaaa");

var dre= document.getElementsByName("datarep")[0].value;


var dav= document.getElementsByName("dav")[0].value;



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




   
        <!-- Select dropdown 
        <div class="d-flex flex-row-reverse bd-highlight mb-3">
            <form action="reparafaz" method="post">
            <?php include("num_linhas.php");?>
            </form>
        </div>
-->
<div class="d-flex flex-row-reverse bd-highlight mb-3">
        <form action="<?php echo SVRURL ?>reparafaz?op=<?php echo $op?>" method="post"  name="form1">
 

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
                    <th scope="col">Sala / Equipamento</th>
                    <th scope="col">Avaria</th>
                    <th scope="col">Reparação</th>
                    <th scope="col">&nbsp;&nbsp; </th>
             
                     
                     
                    
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


                    ?>
                <tr>
                    <th width="20%"  scope="row"><?php echo $row['nome']; echo('<br>'.'/'.'<br>'); echo $row['nomeequi'];?>
                 
                    </th>
                    <td width="30%" >
                    <label>Autor: </label> 
                    <?php echo ($rows2[0]); //echo(' - '); echo $row['autoravaria']; 
                    
                    echo('<br>'.'Email: '); 
                    echo $em; 
                    echo('<br><br>'.'Data avaria: '); 
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
                    

                    
                    
                    if ($row["video"] == null) 
                    {
                    echo ("");
                    }

                    else {
                        echo '              
                        <video 
                        onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                        width="250" height="200" alt="test" controls >
                        <source src="data:video/mp4;base64,'.base64_encode($row['video']).' " >
                     
                    </video>
        
                    ';    
                    }
                   ?>
                  


                    </td>



                    <form name="reparacao" action="repara_avaria.php?id=<?php echo ($row['id']);?>&&em=<?php echo ($em) ?>" method="post"
                   >

                    <td width="30%" >
                    
               
                    <label>Data: </label>  
                     <input  required             
                     size="10" type = "date" name = "datarep" >
                    
                    <br /><br />
                  
                    <label>Descrição: </label>  <br>  
                    <textarea   required style="text-align: justify;"  rows="5" cols=35 name="reparacao"></textarea>   
                    <br /><br />
                    <label>Reparado por: </label>
                    <input style="background-color:white; text-align:left" required  type = "text" name = "repar_por" >  
                    
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



                    <td width="1%">
                

                
                    <input title="Reparar" value="<?php echo date("Y-m-d");?>"   required alt="Reparar" type="image" src="<?php echo SVRURL ?>images/reparacao.svg"/> 
                 
             

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
                    <a  style="color:black;" class="page-link"
                        href="<?php if($page <= 1){ echo '#'; } else { echo "?op=$op&&page=" . $prev; } ?>"><<</a>
                </li>

                <?php for($i = 1; $i <= $totoalPages; $i++ ): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a  style="color:black;" class="page-link" href="reparafaz?op=<?php echo $op?>&&page=<?= $i; ?>"> <?= $i; ?> </a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php if($page >= $totoalPages) { echo 'disabled'; } ?>">
                    <a  style="color:black;" class="page-link"
                        href="<?php if($page >= $totoalPages){ echo '#'; } else {echo "?op=$op&&page=". $next; } ?>">>></a>
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
    


      <?php 
      mysqli_close($db);
      include ("footer.php");?>


   </body>
</html>