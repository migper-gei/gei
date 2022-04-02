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

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}

$sql11 = "select e.nome_escola from escolas e, salas s
where s.id_escola=e.id and
s.id=$url[0]";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);




?>


      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Atualizar sala <br> <?php echo $rows11[0] ?></h2>
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

 $sql = "select * from salas where id=".$url[0]."";
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
    window.location.href = '<?php echo SVRURL ?>salas?x=1&&escola=<?php echo $row['id_escola']?>';
}, 10);
</script>

<?php
}
else
{ 
?>


<form action = "<?php echo SVRURL ?>atualiza_ok_sala.php?id=<?php echo $row['id']; ?> " method = "post" >
                    <label>Nome da sala: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name = "nome"  required value="<?php echo $row['nome']; ?>"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name = "localizacao"  required value="<?php echo $row['localizacao']; ?>"/><br /><br />
                
                    <label>Departamento: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name = "departamento"   value="<?php echo $row['departamento']; ?>"/><br /><br />
                    
                                 
                         <!--           (Ao atualizar, também será atualizado a sala nos respetivos equipamentos)  
                          -->

                                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
                 </form>


                 <form action = "<?php echo SVRURL ?>salas?x=1&&escola=<?php echo $row['id_escola']?>" method="post" >
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