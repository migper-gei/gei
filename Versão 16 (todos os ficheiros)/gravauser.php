<?php
  session_start();
  session_regenerate_id();
  ?>
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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> UTILIZADORES >> INSERIR</a>
                  <div class="titlepage">
             
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ( !isset($_POST['nome']) || !isset($_POST['email']) 
|| empty($_POST['nome']) || empty($_POST['email']) 
)
{



   if (base64_decode($_GET['x'])==1)
   {

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>inserirutil';
}, 10);
</script>


<?php
   }
   else
   {
?>




<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>registauser';
}, 10);
</script>


<?php
}
}
?>




<?php



if($_SERVER["REQUEST_METHOD"] == "POST") {
       
   

     //$no=mysqli_real_escape_string($db,$_POST["nome"]);
    // $em=mysqli_real_escape_string($db,$_POST["email"]);

    $no=$_POST["nome"];
    $em=$_POST["email"];

    //echo $em;

    
//Ver se email já existe
/*
$sql2 = "select count(*) from utilizadores where email='$em'";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);
*/


$sql2 = $db->prepare("select count(*) from utilizadores where email=?");
$sql2->bind_param("s", $em);

$sql2->execute();


$rows2 = $sql2->get_result()->fetch_row();



$contaem = $rows2[0];
 

//echo ($contaem);



if ($contaem==1)
{

?>
  






<?php
if (base64_decode($_GET['x'])==1)
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
   window.location = "<?php echo SVRURL ?>inserirutil";
})
;

         </script>

<?php
}
else
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
         ?>




<?php

}
elseif ($contaem==0)  //não existe o email

{

   $sql01 = "select count(*) as c,email_user,pass,email_smtp,email_smtpport from settings";
   $result01 = mysqli_query($db,$sql01);
   $row01=mysqli_fetch_array($result01);
   
   
   
   if ($row01['c']==0)
   {
?>

<script>
         
         swal({
            title: 'Ainda não foram definidas as configurações de email!',
            icon: 'error',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>emsess";
})
;

         </script>

<?php

   }


elseif(($row01['c']>0))
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



//$sql = "insert into utilizadores (nome,email,tipo,pass)
// values ('$no','$em',2,AES_ENCRYPT('$pa', 'secret') )";

//$result = mysqli_query($db,$sql);




$sql2 = $db->prepare("insert into utilizadores (nome,email,tipo,pass)
values (?,?,?,AES_ENCRYPT('$pa', 'secret'))");

$t=2;

$sql2 -> bind_param('ssi', $no, $em,$t);


$sql2->execute();






//echo($conta);
//echo($no);
//echo($em);
//echo($pa);



?>


<?php
if (base64_decode($_GET['x'])==1)
{
?>


 <script>
         
         swal({
title: 'Os dados foram guardados!',
text: 'Email enviado com a password.(Verifique a caixa de entrada e a caixa de spam)',
icon: 'success',

})
.then(function() {
   window.location = "<?php echo SVRURL ?>utiliz";
})
;

         </script>

<?php
}
else
{
?>


<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Email enviado com a password.(Verifique a caixa de entrada e a caixa de spam)',
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

       $mail = new PHPMailer();


       // $mail->isSendmail();
       $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
        $mail->SMTPAuth = true; 


      
  include('email_settings.php');

  include('dados_enviar_email.php');
  

        $mail->Subject = 'Registo.';
      
        $mail->Body = "A sua password: "."<h4>".$pa."</h4>"."<br><br>NOTA: Por favor não responda a este email."."<br><br><br>A equipa PTE"."<br>"."http://10.17.240.10/pte";
        

        $mail->Body   .= '<br>'.'<img width="20%" height="20%" src="cid:imagem_embutida" alt="Imagem Exemplo" />';  // Imagem no corpo do e-mail

         // Adiciona a imagem ao e-mail como um anexo incorporado
    $mail->addEmbeddedImage('images/logo_aemgn.png', 'imagem_embutida', 'imagem.jpg'); 


        $mail->AddAddress($em);
      
   //echo($row['0']);

//send the message, check for errors
if (!$mail->send()) {
    //echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    //echo "Email enviado.";
}
?>




<?php

 }

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