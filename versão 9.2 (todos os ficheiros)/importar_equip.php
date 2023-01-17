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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Importar equipamentos</h2>
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

function showesc(escola) {

    document.frm.submit();

}

</script>



<br>
<br>



<form name="frm" id="frm" action = "" method = "post" >





<label>ESCOLA: </label>  





<select  style="width:310px;" class="btn btn-info dropdown-toggle" name="escola" onChange="showesc(this.value);">


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by nome_escola";
$result2 = mysqli_query($db,$sql2);






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



<?php


$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s
where e.id=s.id_escola
and e.id=$esc
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>






<form enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importar_equip_ok.php">
               
             


<br>
<label>Sala:</label>  






<select name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }




?>     
</select>

  
(Se lista vazia, inserir salas)





<br><br>




<div class="form-group">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista o equipamento, não é importado)</label>
                    <input name="file" type="file" class="form-control">
                </div>
                <div class="form-group">
                    <?php //echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:90%"> <input name="submit" type = "submit" value = "Importar"/>   
    </div>
                         
                 
                                       
                 </form>

                 <form action = "<?php echo SVRURL ?>configura" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>





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