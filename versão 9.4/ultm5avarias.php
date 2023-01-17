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


if (base64_decode($_GET["aves"])>$maxesc)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>avaria';
          },10);
          </script>


<?php
}


   $idescola=base64_decode($_GET["aves"]);
   


   if ( !isset($idescola)    || empty($idescola)     || !is_numeric($idescola) 
   )
   
   {
      //echo "aaaaaa";
   ?>
   
   
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>avaria';
   }, 10);
   </script>
   
   
   <?php
   }

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
                     <h2>Avarias >> Últimas 5 avarias registadas

                     <br><?php echo $ne?>
                     </h2> 
                     
                     
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                  <h4>         

                  <?php
           
          // include("verifica_sessao.php");
             
             ?>
           

           <?php
include("msg_bemvindo.php");
?>
  
    










           <?php
           //include('config.php');
       
          


       
           
        
           
             $totallinhas = 5;
           
           
         
           


if ($totallinhas >0 && isset($_SESSION['login_user']) && ($_SESSION['tipo']==1 || $_SESSION['tipo']==3))
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




 $sql = "select ar.*, e.nomeequi, s.nome, esc.nome_escola
 from avarias_reparacoes ar, equipamento e, salas s, escolas esc
 where ar.id_equi=e.id and ar.id_sala=s.id and ar.id_escola=esc.id
  and ar.datareparacao is null and esc.id=$idescola
 order by ar.dataavaria desc 
 LIMIT 5";
 $result = mysqli_query($db,$sql);





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




<div class="d-flex flex-row-reverse bd-highlight mb-3">
        <form action="<?php echo SVRURL ?>reparafaz?op=<?php echo $op?>" method="post"  name="form1">
 




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
                    <th width="20%"  scope="row"><?php  
                    echo $row['nome']; echo('<br>'.'/'.'<br>'); echo $row['nomeequi'];?>
                 
                    </th>
                    <td width="30%" >
                    <label>Autor: </label> 
                    <?php echo ($rows2[0]); //echo(' - '); echo $row['autoravaria']; 
                    
                    echo('<br>'.'Email: '); 
                    echo $em; 
                    echo('<br><br>'.'Data avaria: '); 
                     echo  date('d/m/Y',strtotime($row['dataavaria'])); 
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



                    <form name="reparacao" action="repara_avaria.php?ia=<?php echo base64_encode($row['id']);?>" method="post">

                    <td width="30%" >
                    
           
                    <label>Data: </label>  
                     <input  required     value="<?php echo date("Y-m-d") ?>"        
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
                   
                    
  
                    <?php if ($_SESSION['tipo']==1)
                {
                ?>

                    <td width="5%">
                         
                 <a href="enviar_email_reparador.php?ia=<?php echo base64_encode($row['id']);?>&&em=<?php echo base64_encode($em)?>">
                 <img alt="Enviar email a reparador" src="<?php echo SVRURL ?>images/envelope.svg"> 
                </a>
                </td>
                <?php } ?>



                   </form>

                    
                
                </tr>
                <?php } ?>
            </tbody>
        </table>     
                






        <?php include ("jquery_bootstrap.php");?>




        <a href="<?php echo SVRURL ?>avaria">
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
    


      <?php 
      mysqli_close($db);
      include ("footer.php");?>


   </body>
</html>