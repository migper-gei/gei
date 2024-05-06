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
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Atualizar escola/instituição</h2>
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
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>
<?php
}



    





 $sql = "select * from escolas where id=".base64_decode ($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
?>
           <!-- <a href="<?php echo SVRURL ?>sair">Sair</a>-->
              </h3>   
<br>

<?php
if (mysqli_num_rows($result)==0 )
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>

<?php
}
else
{ 
?>


<form action = "<?php echo SVRURL ?>atualizaescok/<?php echo base64_encode ($row['id']); ?> " method = "post" >
                    <label>Nome da escola/instituição: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name="nome"  required value="<?php echo $row['nome_escola']; ?>"/><br /><br />
                 
                   
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
                 </form>


                 <form action = "<?php echo SVRURL ?>dadosescola" method="post" >
<input type = "hidden" name = "" value = "">
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