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

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$url2 = explode('/',$_GET['url2']);

//echo $url[0];

$idescola=$url2[0];

//echo (isset($_GET['url']));


$sql11 = "select nome_escola from escolas 
where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$num_rows = mysqli_num_rows($result11);

//echo ($num_rows);

}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
}


if ($num_rows==0)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
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
                     <h2>Atualizar tarefa <br> 
                     <?php echo $rows11[0] ?></h2>
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

 $sql = "select * from tarefas where id=".$url[0]."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
 //echo $url[0];
?>
            <a href="<?php echo SVRURL ?>sair">Sair</a>
              </h3>   
<br>

<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas?x=1&&escola=<?php echo $idescola?>';
}, 10);
</script>

<?php
}
else
{ 
?>


<form action = "<?php echo SVRURL ?>atualiza_ok_tarefa.php?id=<?php echo $row['id']; ?>&&escola=<?php echo $idescola?>" method = "post" >
                    
                
 
<?php


$sqla = "SELECT  DISTINCT(nome),id 
FROM salas 
where id_escola=$idescola
order by nome";

$resulta = mysqli_query($db,$sqla);
?>

<label>Sala: </label>  


<?php 
//echo ("a:") ;
//echo $row['id_sala'];
?>

<select  style="background-color:#CEF6CE" name="salatar" required>


<?php


echo('<option value="">Escolha a sala</option>');
while($rowa=mysqli_fetch_array($resulta))
{
   

    if ($rowa['id']==$row['id_sala'])
    {
        echo('<option selected value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');
    }
    else
      echo('<option value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');

        }




?>     
</select>

<br><br>
<label>Descrição: </label>  <br>  




                <textarea required style="text-align: justify;background-color:#CEF6CE" rows="5" cols="80"  name="descricao"><?php echo $row['descricao']?>
                </textarea>
                   <br><br>


                    <label>Urgência: </label>  
                   

<select  style="background-color:#CEF6CE" name="urgencia" required>


<?php

echo('<option value="">Escolha a urgência</option>');

if ($row['urgencia']=='Alta')
    
      echo('<option selected value="Alta">Alta</option>');

elseif ($row['urgencia']=='Média')
      echo('<option selected value="Média">Média</option>');
    
else
      echo('<option selected value="Baixa">Baixa</option>');
        

?>     
</select>
<br><br>

                    <label>Criado por: </label>  <br>  
                    <input size=50 type = "text" name = "criado_por"  
                    value="<?php echo $row['criado_por']; ?>"
                    required style="background-color:#CEF6CE"/><br /><br />
                   
                    <label>Data criação: </label>  
                    <input style="background-color:#CEF6CE" required  
                    value="<?php echo $row['data_criacao']; ?>"            
                    size="10" type = "date" name = "data_criacao" >
                    <br /><br />
                    <label>Concluido por: </label>  <br>  
                    <input size=50 type = "text" name = "concluido_por"  
                    value="<?php echo $row['concluido_por']; ?>"
                     /><br /><br />
                    
                    <label>Data conclusão: </label>  
                    <label><Datal></Datal>: </label>  
                    <input value="<?php echo $row['data_conclusao']; ?>"            
                    size="10" type = "date" name = "data_conclusao" >




                  <br> <br>


                                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
                 </form>


                 <form action = "<?php echo SVRURL ?>tarefas.php?x=1&&escola=<?php echo $row['id_escola']?>" method="post" >
<input type = "hidden" name = "sala" value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>
<?php
}
?>


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