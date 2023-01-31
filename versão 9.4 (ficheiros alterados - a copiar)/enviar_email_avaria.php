<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php //include ("head.php");?>




   </head>


   <!-- body -->
   <body class="main-layout">
  

     <?php //include ("header.php");
     include ("config.php")
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
      
      <!-- about -->

       <!--  
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
-->



<!--
  require "PHPMailer/PHPMailer/PHPMailerAutoload.php";
-->
  

  <?php


$id=base64_decode($_GET["ia"]);




//echo($id);

$sql = "select ar.*,s.nome as sn,eq.nomeequi, u.nome as nu, e.nome_escola as ne
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
       ESCOLA / SALA / EQUIPAMENTO: '.$row['ne'].' / '.$row['sn'].' / '.$row['nomeequi'].'  
        <br><br>DATA: '.$row['dataavaria'].'
        <br><br>AVARIA: '.$row['avaria'].' 

       
        <br><br>
        <img height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row['imgavaria']).'">
        
    
        
        <br><br>
        <br>'.
        '</font>';   
       
        
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
window.setTimeout(function() {
window.location.href = 'avaria';
}, 0);
</script>




      <!-- end about -->
    



</body>
</html>