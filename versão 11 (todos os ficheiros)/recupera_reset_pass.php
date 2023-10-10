<!DOCTYPE html>
<html lang="pt">
   <head>



<?php include ("head.php");

?>




<?php
//echo base64_decode($_GET['url']);

if (isset($_GET['url']) && is_numeric(base64_decode($_GET['url'])))
{
$url = explode('/',$_GET['url']);
$rsenha = base64_decode($url[0]);
//echo $rsenha;
}
else
{
   ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>
<?php
   
}


if (!isset($url[0])   || base64_decode($url[0])>1 || base64_decode($url[0])<0

)
{
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>

<?php
}
?>



<?php

    


//var_dump($url);

//echo('<br>');
//echo($url[0]);
//echo('<br>');

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
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                  
                  <div style="  text-align: right;">              
                  <a  class="underlineHover" href="l" title="Login/Registo" style="color:blue;">Login/Registo</a>
                    </div>

<?php
if ($rsenha==0)

{

?>
         <h2> Recuperar password </h2>

 <?php
}
elseif ($rsenha==1) 
{
?>
    <h2> Mudar password </h2>
   
<?php

}
?>



                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ($rsenha==0)

{

?>
    <!-- Login Form -->
    <form action = "<?php echo SVRURL ?>recupera_pass_OK.php" method = "post">

      <input  title="Email"  required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
					type = "text" name = "email"  class="fadeIn second"  placeholder="Email">
 
      <input title="Recuperar (enviar email)"  type="submit" class="fadeIn fourth" value="Recuperar">
  
      <br>

     
      <h7>Após clicar no botão, será enviado uma email com a password.</h7>
       </form>


       <?php
}
elseif ($rsenha==1) 
{
?>


<form action = "<?php echo SVRURL ?>reset_pass_OK.php" method = "post">
           
             <input title="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
					type = "text" name = "email"  class="fadeIn second"  placeholder="Email">
                    
             
                   <br>
                  
                   <input title="Password antiga" 
                         class="fadeIn second" placeholder="Password antiga" type = "password" name = "passworda" pattern=".{8,}" minlength="8" required/>
                   
                   <br>                 <br>
                   <input title="Password (>= 8 digitos, letras e números)" 
                       class="fadeIn second" placeholder="Password (>= 8 digitos, letras e números)" type = "password" name = "password" pattern=".{8,}" minlength="8" required/>
                      
                   <br>
              <input title="Confirmar password" class="fadeIn second" placeholder="Confirmar password"  pattern=".{8,}" type = "password" minlength="8" name = "confirmapassword"  required/>
              <br>
									<input  class="fadeIn fourth" title="Mudar password" type = "submit" value = "Mudar"/>  
                                           
                                              <br />
                                  
                                            
                 </form>


<?php

}
?>






  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

</body>
</html>