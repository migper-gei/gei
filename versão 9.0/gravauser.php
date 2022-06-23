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
             
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ( !isset($_POST['nome']) || !isset($_POST['email'])  )
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



if($_SERVER["REQUEST_METHOD"] == "POST") {
       
   

     $no=mysqli_real_escape_string($db,$_POST["nome"]);
     $em=mysqli_real_escape_string($db,$_POST["email"]);






//Ver se email já existe

$sql2 = "select count(*) from utilizadores where email='$em'";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$contaem = $rows2[0];
 




//echo ($conta);

if ($contaem==1)
{

?>
  




  <script>
         
         swal({
   title: 'ERRO',
   text: 'O email já está registado!',
 icon: 'error',
   //buttons: false,

})
.then(function() {
   window.location = "<?php echo SVRURL ?>registauser";
})
;

         </script>



<?php

}
elseif ($contaem==0)

{



   function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
   {
   $lmin = 'abcdefghijklmnopqrstuvwxyz';
   $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $num = '1234567890';
   $simb = '!@#$%*-';
   $retorno = '';
   $caracteres = '';
   
   $caracteres .= $lmin;
   if ($maiusculas) $caracteres .= $lmai;
   if ($numeros) $caracteres .= $num;
   if ($simbolos) $caracteres .= $simb;
   
   $len = strlen($caracteres);
   for ($n = 1; $n <= $tamanho; $n++) {
   $rand = mt_rand(1, $len);
   $retorno .= $caracteres[$rand-1];
   }
   return $retorno;
   
   
   }

   $pa = geraSenha(8, true, true, true);





$sql = "insert into utilizadores (nome,email,tipo,pass) values ('$no','$em',2,AES_ENCRYPT('$pa', 'secret'))";
$result = mysqli_query($db,$sql);


//echo($conta);
//echo($no);
//echo($em);
//echo($pa);



?>




<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Email enviado com a password.',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>l";
})
;


</script>




<?php


       $mail = new PHPMailer();


       // $mail->isSendmail();
       $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
        $mail->SMTPAuth = true; 


      
  include('email_settings.php');

  include('dados_enviar_email.php');
  

        $mail->Subject = 'Registo.';
        $mail->Body = "A sua password: "."<h4>".$pa."</h4>";
        $mail->AddAddress($em);
      
   //echo($row['0']);

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    //echo "Email enviado.";
}
?>




<?php

 }

?>


<!--
<script>
window.setTimeout(function() {
   // window.location.href = 'l';
}, 15000);
</script>
-->














<?php
}
mysqli_close($db);
?>









  </div>
</div>


</div>
         </div>
      </div>
      <!-- end about -->
    
<br><br><br><br><br><br><br><br><br><br><br><br><br>
      <?php include ("footer.php");?>



</body>
</html>