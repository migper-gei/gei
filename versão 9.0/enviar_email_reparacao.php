<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>




   </head>


   <!-- body -->
   <body class="main-layout">
  

     <?php include ("header.php");?>
     

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
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Reparar avaria </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">

<!--
  require "PHPMailer/PHPMailer/PHPMailerAutoload.php";
-->
  

  <?php




//echo($em);

//$sql = "select AES_DECRYPT('pass', 'secret') as p from utilizadores where email='".$_GET["em"]."'";
$sql = "select ar.*,s.nome,eq.nomeequi from avarias_reparacoes ar, salas s, equipamento eq
where ar.id_sala=s.id and eq.id=ar.id_equi
and ar.id=".$_GET["id"]." ";
$result = mysqli_query($db,$sql);


$row=mysqli_fetch_array($result);

//echo($row['sala']);


  //echo("<br><br><br>");
  //echo " <font color=navy font face='courier' size='5pt'>O Email não está registado.</font>";



//echo($em);
$mail = new PHPMailer();
       // $mail->isSendmail();

       $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
       //$mail->SMTPAuth = true; 

        
       include('email_settings.php');


       
   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);


        include('dados_enviar_email.php');
 

        $mail->Subject = 'Reparação da avaria.';
        $mail->Body = '
         <font color=navy size=3><b>AVARIA: </b>
        <br><br>
       SALA / EQUIPAMENTO: '.$row['nome'].' / '.$row['nomeequi'].'  
        <br><br>DATA: '.$row['dataavaria'].'
        <br><br>AVARIA: '.$row['avaria'].' <br><br>
        ---------------------------------------------------------------------------------------------------------
        <b>REPARAÇÃO: </b>
        <br><br>
        <br>DATA: '.$row['datareparacao'].'
        <br><br>REPARAÇÃO: '.$row['reparacao'].'<br><br>'.
        'REPARADO POR: '.$row['rep_efectuada_por'].'<br><br>'.
            '</font>';   
        
     //echo($row['autoravaria']);

        
      //echo($ema);

      $mail->addAddress($row['autoravaria']);
      //echo($row['datareparacao']);

       // $mail->addAddress($row['autoravaria']);
      
  

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else 
{
    //echo "Email enviado.";
    //header("Refresh:0;url=reparacoes_efetuar.php");
  
  

?>


     
<h2>O email está a ser enviado.</h2>
 
 <br> <br>
<div class="bs-example">
 <div class="progress">
     <div class="progress-bar progress-bar-striped" style="min-width: 20px;"></div>
 </div>

 <!-- jQuery Script -->
 <script>
     var i = 0;
     function makeProgress(){
         if(i < 100){
             i = i + 1;
             $(".progress-bar").css("width", i + "%").text(i + " %");
         }
         // Wait for sometime before running this script again
         setTimeout("makeProgress()", 100);
     }
     makeProgress();
 </script>
</div>


<?php

}

  mysqli_close($db);

?>



<script>
window.setTimeout(function() {
window.location.href = 'avaria';
}, 15000);
</script>




   

  </div>
</div>



</div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

</body>
</html>