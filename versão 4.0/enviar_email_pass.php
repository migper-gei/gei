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

  

   
$em= $_GET["em"];

//echo($em);

//$sql = "select AES_DECRYPT('pass', 'secret') as p from utilizadores where email='".$_GET["em"]."'";
$sql = "select AES_DECRYPT(`pass`, 'secret') from utilizadores where email='$em'";
$result = mysqli_query($db,$sql);
//$row=mysqli_fetch_array($result);
$row=mysqli_fetch_row($result);



//echo($em);
        $mail = new PHPMailer();
       // $mail->isSendmail();
        $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
        $mail->SMTPAuth = true; 


      
  include('email_settings.php');


   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);

        $mail->IsHTML(true);
        $mail->From="gsimasterdb@gmail.com";
        $mail->FromName="GEI";
        $mail->Sender="gsimasterdb@gmail.com";
        $mail->AddReplyTo('gsimasterdb@gmail.com','GEI');
        $mail->Subject = 'Recuperacao da password.';
        $mail->Body = "A sua password: "."<h4>".$row['0']."</h4>";
        $mail->AddAddress($em);

       
   //echo($row['0']);

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
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