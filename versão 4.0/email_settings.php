       <?php 
   
//$mail->SMTPDebug = 1;

//Ask for HTML-friendly debug output
//$mail->Debugoutput = 'html';

$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
//$mail->SMTPSecure = 'ssl'; 
//$mail->Port = 465; 
$mail->SMTPAuth = true;

//$mail->SMTPDebug=3;

        $mail->Username = "gsimasterdb@gmail.com";
        $mail->Password = "admingsi+123abc";  
        ?>