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
if ( !isset($_POST['email']) || !isset($_POST['passworda']) || !isset($_POST['passworda']) 
|| !isset($_POST['confirmapassword']) 
|| empty($_POST['email']) || empty($_POST['passworda']) || empty($_POST['passworda']) 
|| empty($_POST['confirmapassword']) 
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(1)?>';
}, 10);
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
  
   $em=$_POST["email"];


  //echo $pwdant;
  
  
  $sql0 = $db->prepare("select count(*), AES_DECRYPT(pass, 'secret') 
  from utilizadores where email=? ");
 
  $sql0->bind_param("s", $em);
  $sql0->execute();


  $rows2 = $sql0->get_result()->fetch_row();
  
  $passant = $rows2[1];

    $count=$rows2[0];


    /*
echo $passant;
echo ('<br><br><br>');
echo $pwdant;


  
  echo ('<br>');
  echo $count;

*/

  /*
if ($passant==$pwdant)
{
    $sql = "select count(*) from utilizadores 
    where email=? and AES_DECRYPT(pass, 'secret') = ?";
    $sql->bind_param("ss", $em, $passant);
        //$result = mysqli_query($db,$sql);
    
        //$count = mysqli_num_rows($result);

        $count = $sql->get_result()->fetch_row();
    //    echo ('<br>');
  echo $count;
 } 
*/


    if($count == 0 ||  ($passant<>$pwdant)  ) 
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
      window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(1)?>";
  })
  ;
          
          
          </script>
  
          <?php
        }
  
  
        
    elseif ($count==1 &&  ($passant==$pwdant) )
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
  window.location = "recuperapass/<?php echo base64_encode(1)?>";
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
   window.location = "recuperapass/<?php echo base64_encode(1)?>";
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
  window.location = "recuperapass/<?php echo base64_encode(1)?>";
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
    window.location = "recuperapass/<?php echo base64_encode(1)?>";
    })
    ;
    
      </script>
    <?php
    }
  
  
  
  
  if ($v!=0)
  {
  
  
    $sql1 = $db-> prepare( "update utilizadores 
    set pass=AES_ENCRYPT('".$pwd."', 'secret') 
    where email=?");
    $sql1 -> bind_param('s', $em);
     $sql1 -> execute();

  
   ?>
  
  
  <script>
  
  swal({
  title: 'Os dados foram guardados!',

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