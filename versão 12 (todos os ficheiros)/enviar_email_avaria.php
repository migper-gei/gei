<?php
  session_start();
  session_regenerate_id();
  ?>
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
      
     

<!--
  require "PHPMailer/PHPMailer/PHPMailerAutoload.php";
-->
  

  <?php


$id=base64_decode($_GET["ia"]);




//echo($id);

$sql = "select ar.*,s.nome as sn,eq.*, u.nome as nu, e.* 
from avarias_reparacoes ar, salas s, equipamento eq, utilizadores u, escolas e
where ar.id_sala=s.id and eq.id=ar.id_equi and u.email=ar.autoravaria and e.id=ar.id_escola
and ar.id=".$id." ";
$result = mysqli_query($db,$sql);


$row=mysqli_fetch_array($result);


$mail = new PHPMailer();
       // $mail->isSendmail();

       $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
       //$mail->SMTPAuth = true; 

        
       include('email_settings.php');


       
   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);


        include('dados_enviar_email.php');
 
        //echo $row['escola_digital'];

        if ($row['escola_digital']=="Sim")

        {
         $mail->Subject = 'Dados da avaria.';
         $mail->Body = '
         <font color=navy size=3><b>Exmos Srs.
         <br>   <br> </font>
         Encontra-se para envio o seguinte equipamento: </b>
         <br>
         '.$row['numserie'].'
         <br>
         
        
         
         <br>
        <b>Avaria: </b>
         <br>   
        '.$row['avaria'].' 
 
         <br><br>
         '.$row['nome_escola'].' 
         <br>
         '.$row['morada'].' 
         <br>
         '.$row['codigopostal'].'  &nbsp;&nbsp; '.$row['localidade'].' 
         <br>
         '.$row['telefone'].' 


         <!--<br><br>
         <img height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).'">
         -->
     
         
         <br><br>
         <br>'.
         '</font>';   

        }

        else
        {
        $mail->Subject = 'Dados da avaria.';
        $mail->Body = '
        <font color=navy size=3><b>AUTOR DA AVARIA: </b>
        <br><br>
        NOME / EMAIL: '.$row['nu'].' / '.$row['autoravaria'].'
        <br>
        </font>
        
        <br><br>
         <font color=navy size=3><b>AVARIA: </b>
        <br><br>
       ESCOLA / SALA / EQUIPAMENTO: '.$row['nome_escola'].' / '.$row['sn'].' / '.$row['nomeequi'].'  
        <br><br>DATA: '.$row['dataavaria'].'
        <br><br>AVARIA: '.$row['avaria'].' 

       
       
    
        
        <br><br>
        <br>'.
        '</font>';   
        
      }
      
      
      if ($row['escola_digital']=="Sim")

      {
         //$sql2z="select email from utilizadores where ";
         //foreach ($db->query($sql2z) as $row2z) {
        
          //echo $row['email'];
  
         $mail->addAddress($row['email_fornecedor']);

     // }

      }


      else
      {


     if (base64_decode($_GET["r"])==1)
     {
      foreach($_POST['rep'] as $value)
      {

         $rep=$value;
         //echo $rep;

         $sql1="select email from utilizadores where id=$rep";
         $result1 = mysqli_query($db,$sql1); 
         $rows1 =mysqli_fetch_row($result1);
         
         //echo ($rows1[0]); 
         $mail->addAddress($rows1[0]);
      }
     }

     else{

      $sql2="select email from utilizadores where tipo=1 or tipo=3";
       foreach ($db->query($sql2) as $row2) {
      
        //echo $row['email'];

       $mail->addAddress($row2['email']);
       
       //$mail->addAddress('mig_per@hotmail.com');
      
       //$mail->AddAttachment($row['imgavaria']);
         
         
       //$mail->AddAttachment('img src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).'"');
      
      }

   } 
   
}
  

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else 
{
   

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

 <br><br>
        <img height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).'">
        
-->



      <!-- end about -->
    



</body>
</html>