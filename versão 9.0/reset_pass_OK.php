<!DOCTYPE html>
<html lang="pt">
   <head>



<?php include ("head.php");

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
                  


         <h2> Mudar password </h2>





                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ( !isset($_POST['Email']) || !isset($_POST['passworda']) || !isset($_POST['passworda']) 
|| !isset($_POST['confirmapassword'])
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 140);
</script>


<?php
}
?>



  <?php
    

    // include("config.php");
  
  
  if($_SERVER["REQUEST_METHOD"] == "POST") {
        
    $v=1;
    $error="";
  
    $pwdant = $_POST['passworda'];
    $pwd = $_POST['password'];
    $pwdc = $_POST['confirmapassword'];
  
  //echo $pwdant;
  
  
  $sql0 = "select AES_DECRYPT(pass, 'secret') from utilizadores where email='".$_POST["email"]."' ";
  $result0 = mysqli_query($db,$sql0);
   $rows2 =mysqli_fetch_row($result0);

  //echo $rows2[0];

  $passant = $rows2[0];

  $count =0;
if ($passant==$pwdant)
{
    $sql = "select count(*) from utilizadores where email='".$_POST["email"]."' and AES_DECRYPT(pass, 'secret') = '$passant'";
        $result = mysqli_query($db,$sql);
       
        $count = mysqli_num_rows($result);

    //    echo ('<br>');
  //echo $count;
 } 



    if($count == 0) 
        {
       
          ?>
          <script>
  
                 swal({
      title: 'ERRO',
      text: 'O Email não está registado ou password antiga errada!',
    icon: 'error',
      //buttons: false,
  
  })
  .then(function() {
      window.location = "<?php echo SVRURL ?>recuperapass/1";
  })
  ;
          
          
          </script>
  
          <?php
        }
  
  
        
    elseif ($count==1)
     {
         
  
  
    if( strlen($pwd) < 8 ) {
    //$error .= "Password too short!";
    $v=0;
  ?>
    <script>
  
    swal({
  title: 'ERRO',
  text: 'A password deve ter no mínimo 8 carateres!',
  icon: 'error',
  //buttons: false,
  
  })
  .then(function() {
  window.location = "recuperapass/1";
  })
  ;
  
    </script>
  <?php
  }
  
  
  
    
    if( !preg_match("#[0-9]+#", $pwd) ) {
      $v=0;
     // $error .= "Password must include at least one number!";
     ?>
     <script>
   
     swal({
   title: 'ERRO',
   text: 'A password deve ter pelo menos um número!',
   icon: 'error',
   //buttons: false,
   
   })
   .then(function() {
   window.location = "recuperapass/1";
   })
   ;
   
     </script>
   <?php
   }



       
    if( !preg_match("#[a-z]+#", $pwd) ) {
      $v=0;
    //$error .= "Password must include at least one letter!";

    ?>
    <script>
  
    swal({
  title: 'ERRO',
  text: 'A password deve ter pelo menos uma letra!',
  icon: 'error',
  //buttons: false,
  
  })
  .then(function() {
  window.location = "recuperapass/1";
  })
  ;
  
    </script>
  <?php
   
    }
    
  

  
    if($pwd != $pwdc) {
     $v=0;
    ?>
      <script>
    
      swal({
    title: 'ERRO',
    text: 'As passwords não são iguais!',
    icon: 'error',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "recuperapass/1";
    })
    ;
    
      </script>
    <?php
    }
  
  
  
  
  if ($v!=0)
  {
  
  
    //AES_ENCRYPT('".$_POST["password"]."', 'secret')
    $sql1 = "update utilizadores set pass=AES_ENCRYPT('".$_POST["password"]."', 'secret') where email='".$_POST["email"]."'";
    $result = mysqli_query($db,$sql1);
    
  
   ?>
  
  
  <script>
  
  swal({
  title: 'Os dados foram guardados!',
  //text: 'Email enviado com a password.',
  icon: 'success',
  //buttons: false,
  
  })
  .then(function() {
  window.location = "<?php echo SVRURL ?>l";
  })
  ;
  
  </script>
  
  
   <?php
  }
  
  
  }
  
   
  
  
  
  mysqli_close($db);
  
  }
  
  
  
  /*
    
    if($error){
    echo "Password validation failure(your choise is weak): $error";
    } else {
    echo "Your password is strong.";
    }
    */
    
  ?>
  


<br><br><br><br><br><br><br><br><br>



  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

</body>
</html>