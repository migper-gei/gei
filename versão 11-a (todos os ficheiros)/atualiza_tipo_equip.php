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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> TIPOS DE EQUIPAMENTO >> ATUALIZAR</a>
               <div class="titlepage">
                    
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>





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
    window.location.href = 'configura';
}, 10);
</script>
<?php
}


    



 $sql = "select * from tipos_equipamento where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
$row=mysqli_fetch_array($result);


?>
 


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tiposequip';
}, 10);
</script>

<?php
}
else
{ 
?>

    


<form action = "<?php echo SVRURL ?>atualiza_ok_tiposequip.php?tpi=<?php echo base64_encode($row['id']); ?>&nom=<?php echo $row['nome']; ?>" method = "post" >
                    <label>Tipo de equipamento:     </label>  <br>  
                    <input class="underlineHover" style="width:100%" type = "text" name="nomeeq"  
                    required value="<?php echo urldecode($row['nome']); ?>"/><br>
                    (Ao atualizar, também será atualizado o tipo nos respetivos equipamentos)
                    <div  style=" text-align:center;width:100%"> 
                    <br>
                    <input  type = "submit" value = "Atualizar"/>     </div>
                                 
                                        
                 </form>

 <form action = "<?php echo SVRURL ?>tiposequip" method="post" >
<input type = "hidden" name = "sala" value = "">
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