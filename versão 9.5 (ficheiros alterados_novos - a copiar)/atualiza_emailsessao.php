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
                     <h2>Atualizar email/sessao</h2>
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


<script>
         function myFunction() {
           var x = document.getElementById("mypass1");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
         </script>


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


    



 $sql = "select * from settings where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
$row=mysqli_fetch_array($result);

//echo $row['id'];
//echo $row['nome'];
?>
 


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>emsess';
}, 10);
</script>

<?php
}
else
{ 
  
$sql = $db->prepare("select AES_DECRYPT(`pass`, 'secret') from settings");
$sql->execute();
$row0 = $sql->get_result()->fetch_row();

 
?>

    


<form action = "<?php echo SVRURL ?>atualiza_ok_emailsessao.php?i=<?php echo base64_encode($row['id']);?>" method = "post" >
<label>Email:    </label>  <br>                    
<input  style="background-color:#CEF6CE" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
value="<?php echo ($row['email_user']); ?>" type = "text" name = "email" class="fadeIn second" placeholder="Email">
    <br>  
    <br>  
    <label>Password:  </label>  <br>  
                    <input value="<?php echo ($row0[0]); ?>"  id="mypass1" type = "password" placeholder="Password do email" size=50 type = "text" name = "pass"  required style="background-color:#CEF6CE"/>
                    <br> 
                    <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br>  <br>
         <label>Smtp: </label>  <br>
         <input    value="<?php echo ($row['email_smtp']); ?>" placeholder="Smtp" size=50 type = "text" name = "smtp"  required style="background-color:#CEF6CE"/>
                    <br><br> 
                    <label>Smtp porta: </label>  
                    <input  placeholder="Smtp porta"  
                    value="<?php echo ($row['email_smtpport']); ?>"
                    type = "number" name = "smtpport"  required style="background-color:#CEF6CE"/>
                    <br><br> 
                     
                     <label>Nome (sigla):  </label>  <br>  
                    <input value="<?php echo ($row['nome_app']); ?>" placeholder="Nome (sigla)" size=50 type = "text" name = "nome"  required style="background-color:#CEF6CE"/>
                    <br>  <br> 
                    <label>Tempo duração da sessão (em segundos): </label>   
                    <input value="<?php echo ($row['sessao_timeout']); ?>" type = "number" name = "sessao"  required style="background-color:#CEF6CE"/>
                    <br>  <br>
                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
                          

                 </form>





 <form action = "<?php echo SVRURL ?>emsess" method="post" >
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