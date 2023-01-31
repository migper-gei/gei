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




$sql2a = "select max(id) as me from escolas";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$idescola=base64_decode($_GET['ti']);


if ($idescola>$maxesc )
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tarefas.php?x=0';
          },40);
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
                     <h2>Inserir tarefa <br> <?php echo $ne ?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
                        

   <form action = "<?php echo SVRURL ?>grava_tarefa.php?ti=<?php echo base64_encode($idescola)?>" method = "post" >

     
<br>  
<?php


$sqla = "SELECT  DISTINCT(nome),id 
FROM salas 
where id_escola=$idescola
order by nome";

$resulta = mysqli_query($db,$sqla);
?>

<label>Sala: </label>  



<select  style="background-color:#CEF6CE" name="salatar" required>


<?php
echo('<option value="">Escolha a sala</option>');
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');

        }




?>     
</select>

<br><br>
<label>Descrição: </label>  <br>  
                   <textarea  required style="text-align: justify;background-color:#CEF6CE" rows="5" cols="70"  name="descricao"></textarea>
                   <br><br>
                    <label>Urgência: </label>  
                   

<select  style="background-color:#CEF6CE" name="urgencia" required>


<?php
echo('<option value="">Escolha a urgência</option>');

      echo('<option value="Alta">Alta</option>');
      echo('<option value="Média">Média</option>');
      echo('<option value="Baixa">Baixa</option>');
        

?>     
</select>
<br><br>

                     <label>Criado por: </label>  <br>  
                    <input size=50 type = "text" name = "criado_por"  required style="background-color:#CEF6CE"/><br /><br />
                   
                    <label>Data: </label>  
                    <input style="background-color:#CEF6CE" required  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "data_criacao" >
                    
<br><br>

                    
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                 </form>

<form action = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode (1)?>&&esi=<?php echo base64_encode ($idescola) ?>" method="post" >
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