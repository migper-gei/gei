<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//echo SVRURL;
//echo getcwd();
$a=getcwd();

//echo $a;

/* Exception class. */
require $a.'/PHPMailer/PHPMailer/src/Exception.php';

/* The main PHPMailer class. */
require $a.'/PHPMailer/PHPMailer/src/PHPMailer.php';

/* SMTP class, needed if you want to use SMTP. */
require $a.'/PHPMailer/PHPMailer/src/SMTP.php';

//$email = new PHPMailer();
?>




   </head>


   <!-- body -->
   <body class="main-layout">
  

     <?php include ("header.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Recuperar password </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


  <?php





if($_SERVER["REQUEST_METHOD"] == "GET") 

{

  

   
$em= base64_decode($_GET["em"]);

//echo($em);

$sql = $db->prepare("select AES_DECRYPT(`pass`, 'secret') from utilizadores where email=?");
//$result = mysqli_query($db,$sql);
//$row=mysqli_fetch_row($result);

$sql->bind_param("s", $em);
$sql->execute();

$row = $sql->get_result()->fetch_row();
//echo $row[0];


  

        $mail = new PHPMailer();
       // $mail->isSendmail();
        $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
        $mail->SMTPAuth = true; 


      include('email_settings.php');



   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);

    include('dados_enviar_email.php');


        $mail->Subject = 'Recuperacao da password.';
        $mail->Body = "A sua password: "."<h4>".$row[0]."</h4>";
        $mail->AddAddress($em);
    
   //echo($row['0']);

//send the message, check for errors
if (!$mail->send()) {
    //echo "Mailer Error: " . $mail->ErrorInfo;
?>
    
<script>
    
swal({
title: 'O email não foi enviado. Verifique as configurações de email!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>emsess";
});


</script>


  


    <?php  

} 

else {
    //echo "Email enviado.";
?>


<h2>O email foi enviado. Consulte a sua caixa de correio.</h2>
    <br>
    
 <h4>Caso não tenha recebido o email contacte um administrador.</h4>
 <br>



 <form action = "<?php echo SVRURL ?>l" method="post" >

<input type=submit title="Inicio" value="OK"   >

</form>




    
<?php
 
 }

 mysqli_close($db);


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