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
               

               if (isset($_GET['url']))
               {
               $url = explode('/',$_GET['url']);
               $url2 = explode('/',$_GET['url2']);
               $url3 = explode('/',$_GET['url3']);
               
               
               //echo $url[0];
               //echo $url2[0];
               //echo $url3[0];
               }
               else
               {
                //echo ("aaa");
                   ?>
                   
               <script>
               window.setTimeout(function() {
                   window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
            
               <?php
               }
               
?>



<?php


   $sql = "SELECT e.nomeequi, s.nome ,es.nome_escola
   FROM equipamento e, salas s, escolas es
   WHERE e.id_sala=s.id and s.id_escola=es.id
   and e.id=".$url[0]." ";
   $result = mysqli_query($db,$sql);
   $rows1 =mysqli_fetch_row($result);
  

?>






      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Mudar equipamento de sala

                     <br>
                     <?php echo($rows1[0]);?> | <?php echo $rows1[1]; ?> | <?php echo $rows1[2]; ?>
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
    


<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>



<?php 


$sql = "SELECT * FROM escolas ORDER by nome_escola";
$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);
 ?>

<br><br>
  





<form name="frme" id="frme" action = "" method = "post" >

   <div style="text-align: left;">


   <h3 style="color:black;">ESCOLA:
   
 
   
   
   <select title="Escolha a escola" 
   class="btn btn-info dropdown-toggle"
    name="escola" onChange="showescola(this.value);"  style="width:310px;">
   
   
   <?php
   
   
   //echo $idescola;
   
   
   
   $sql2 = "SELECT * FROM escolas ORDER by nome_escola";
   $result2 = mysqli_query($db,$sql2);
   
   //echo('<option value=""> Escolha a escola  </option>');  
   
   while($row2=mysqli_fetch_array($result2))
   {
   
      if ($row2['id']==$_REQUEST["escola"])
      {
        //'.$row2['nome_escola'].'
         echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
      }
      else
   
     echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
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



if (!empty($_POST["escola"])) {
              
   $esc=$_POST["escola"];
   
   }
   else{
    $esc=$nes;  //1;
   }

   //echo $esc;


?>

<br>


  




    <?php
    
//echo($_SESSION['tipo']);

    if ($_SESSION['tipo']==1 )
    {

       
   
   ?>
  




<br>



<?php
//echo $url3[0];

$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s
where e.id=s.id_escola and s.id<>$url3[0]
and e.id=$esc 
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>



<form action="<?php echo SVRURL ?>mudarsalaequi_ok.php?id=<?php echo($url[0]);?>&&sala=<?php echo($url3[0]);?>&&escola=<?php echo $esc?>" method="post" >


<h3 style="color:black;">
SALA:
    
<select style="width:310px;" class="btn btn-outline-secondary dropdown-toggle" name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }
?>     
</select>
</h3>

<?php } ?>







<br /><br />
                                   
                   <div  style=" text-align:center;width:90%"> 
                   <input  type = "submit" value = "Mudar de sala"/>   
    </div>
   </form>
       

<a href="<?php echo SVRURL ?>verequipsala?x=1&&sala=<?php echo($url3[0]); ?>&&escola=<?php echo($url2[0]);  ?>">
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