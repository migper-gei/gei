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
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php


    $eq=base64_decode($_GET["ieq"]); 
    $said=base64_decode($_GET["sai"]);
    $idescola=base64_decode($_GET["ies"]);
/*
echo $eq;
echo ('<br>');
echo $said;
echo ('<br>');
echo $idescola;
*/
?>


<?php

if ( !is_numeric($eq) || !isset($eq) || empty($eq) 
|| !isset($idescola) || !is_numeric($idescola)  || empty($idescola)  
     || !is_numeric($said) || !isset($said) || empty($said) 
     
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
    ?>




     <?php
//session_start();



include("sessao_timeout.php");

$sql9 = "select nome from salas where id=$said";
$result9 = mysqli_query($db,$sql9); 
$rows9 =mysqli_fetch_row($result9);

 $ns = $rows9[0];
 $num_ns = mysqli_num_rows($result9);


$sql10 = "select nomeequi from equipamento where id=$eq";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $noeq = $rows10[0];
 $num_noeq = mysqli_num_rows($result10);


$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
 
  ?>


<?php
     if ($num_ns==0 || $num_ne==0 || $num_noeq==0)
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
               <div class="titlepage">
                     <h2>Reparações a efetuar equipamento
                      <br>    <?php echo $noeq ?> |
                     <?php echo $ns ?> | <?php echo $ne ?>  
              
                   
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
         
include("msg_bemvindo.php");

 
             
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

       







<br><br>

<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
Ao clicar em <img width="15px" height="15px" src="<?php echo SVRURL ?>images/checkbox.png">, 
o autor da avaria recebe um email com os dados da reparação. 


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



  
    $sql = "select * 
    from avarias_reparacoes 
    where datareparacao is null and id_equi=".$eq." and id_escola=".$idescola."
   LIMIT $paginationStart, $limit";

  $result = mysqli_query($db,$sql);



?>






<script language="JavaScript" >

function enviardados2(){

    
//document.getElementById('reparacao').submit();

var z=document.getElementById('datarep').value;



var dre= document.getElementsByName("datarep")[0].value;

var dav= document.getElementsByName("dav")[0].value;




if(dre<dav)
{
     // alert( "A data de reparação deve ser igual ou superior à data da avaria." );
     swal({
title: 'A data de reparação deve ser igual ou superior à data da avaria!',
icon: 'error',
//buttons: false,

})
;
      return false;
}

else {
      return true;

  }




}



</script>







        <!-- Select dropdown -->
 
     
    <div class="d-flex flex-row-reverse bd-highlight mb-3">
        
    
                </div>





        <!-- Datatable -->

        <table class="table table-striped">

            <thead>

                <tr class="table-success">

                

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




                    ?>


<form   onSubmit="return enviardados2();" name="reparacao" id="reparacao" 
action="<?php echo SVRURL ?>repara_avaria.php?ia=<?php echo base64_encode($row['id']);?>" method="post">
            <!--
onSubmit="return enviardados2();"
            -->
                <tr>

  


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

                     <input onchange="return enviardados2();" value="<?php echo date("Y-m-d");?>"  required id="datarep"   size="10" type = "date" name = "datarep" >

                    <!--value="<php echo date("Y-m-d");?>" -->

                    <br /><br />

                  

                    <label>Reparação: </label>  <br>  

                    <textarea  required  style="text-align: justify;" rows="3" cols="40"  name="reparacao"></textarea>   

                    <br /><br />

                    <label>Reparado por: </label>

                    <input required  size="36" type = "text" name = "repar_por" >  
                    
                    

                    <input hidden name = "dav" value="<?php echo $row['dataavaria'];  ?>" > 
                                  



                     </td>



                     <?php
                        if ( $_SESSION['tipo']==1 || $_SESSION['tipo']==3)
                    {
                    ?>

                    <td width="5%" >

                    <input title="Enviar email ao autor da avaria" type=checkbox name="my_check" value="yes">  
         

                    <input  title="Reparar" alt="Reparar" type="image" src="<?php echo SVRURL ?>images/reparacao.svg"/> 

                    </td>

                    <?php
                    }
                    ?>

                   
                   </form>


                    

                

                </tr>

                <?php } ?>

            </tbody>

        </table>     

              <br>


        
       
  
      

      

        <?php include ("jquery_bootstrap.php");?>




<div style="  text-align: left;">   
      

<form action = "<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($said) ?>&&ies=<?php echo base64_encode($idescola) ?>" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>

</div >




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