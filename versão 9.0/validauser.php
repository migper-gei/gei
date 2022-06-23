<!DOCTYPE html>
<html lang="pt">
   <head>

<?php 

include ("head.php");?>


   </head>



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
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
}
?>





   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     

      

           <div  class="about">
         <div class="container">
            
               <div class="row">
               <div class="col-md-10 offset-md-2">

              <div class="titlepage">
                     <h2></h2>
                  </div>
        

<?php
    


  //$_SESSION = array();
  //session_destroy();

   //session_start();

// Set the last request variable
$_SESSION['lastaccess'] = time();
//echo(time());
   
   include("sessao_timeout.php");


//$z1=$_GET["z"];
//echo($z1);

$z1=$url[0];

//echo $z1;
if ($z1!=0)
{
   ?>
   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>

<?php
}



if($_SERVER["REQUEST_METHOD"] == "POST") {
        


      $myemail = mysqli_real_escape_string($db,$_POST['email']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
      
 // echo($mypassword);
  //    echo($myemail);

      $sql = "SELECT id,nome,tipo,email FROM utilizadores WHERE email = '$myemail' and AES_DECRYPT(pass, 'secret') = '$mypassword'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result);
      //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      //$active = $row['active'];
      
      $count = mysqli_num_rows($result);
       //echo($count);
      // se encontrar o user: $myusername and $mypassword
        
      

      if($count == 1) 
      {

         //echo "conta = 1";
       // echo($row['nome']); 
   
   
        $_SESSION['login_user'] = $row['nome'];
        $_SESSION['tipo'] = $row['tipo'];
        $_SESSION['email'] = $row['email'];

       //atualizar o valor do campo "sessao_ativa"
       $_SESSION['user_id'] = $row['id'];
       //$sql = "update utilizadores set sessao_ativa='1' where id=".$row["id"]."";
       //$result = mysqli_query($db,$sql);
      
       
?>

  <?php
      include("msg_bemvindo.php");
      echo('<br>');
      include("texto_gei.php");
        
     


   ?>  


<script lang="javascript">
 window.onload = function() {
	if(!window.location.hash) {
		window.location = window.location + '#loaded';
		window.location.reload();
	}
}
  </script>


<?php
        
      }
      elseif($count == 0) 
      {
         //$error = "Email ou Password incorreta.";
         //echo($error);
         echo "<br><br>";
     

        ?>


        <script>
             
                  swal({
            title: 'ERRO',
            text: 'Email ou Password incorreta!',
          icon: 'error',
            //buttons: false,
        
        })
        .then(function() {
            window.location = "<?php echo SVRURL ?>l";
        })
        ;
      
                  </script>
        
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
     
        <?php

      }


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

   
   mysqli_close($db);
?>




  



             
            </div>
         </div>
      </div>
      <!-- end about -->
    
<br>
      <?php include ("footer.php");?>

</body>
</html>
