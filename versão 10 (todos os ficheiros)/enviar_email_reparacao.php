<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>




   </head>


   <!-- body -->
   <body class="main-layout">
  

     <?php include ("header.php");
     //include ("config.php")
     ?>
     

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
      
     
  

  <?php


$id=base64_decode($_GET["ia"]);




//echo($em);

//$sql = "select AES_DECRYPT('pass', 'secret') as p from utilizadores where email='".$_GET["em"]."'";
$sql = "select ar.*,s.nome,eq.nomeequi from avarias_reparacoes ar, salas s, equipamento eq
where ar.id_sala=s.id and eq.id=ar.id_equi
and ar.id=".$id." ";
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
        ------------------------------------------------------------------------------------------
        <br>
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



<?php

}

  mysqli_close($db);

?>

<script>
    
    swal({
title: 'O email foi enviado!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>avaria";
});


</script>

<!--
<script>
window.setTimeout(function() {
window.location.href = 'avaria';
}, 0);
</script>

-->

<!--
   

  </div>
</div>



</div>
         </div>
      </div>
-->


      <!-- end about -->
    

      <?php //include ("footer.php");?>

</body>
</html>