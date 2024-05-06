        <?php
       /* 
        $mail->IsHTML(true);
        $mail->From="geimasterdb@hotmail.com";
        $mail->FromName="GEI";
        $mail->Sender="geimasterdb@hotmail.com";
        $mail->AddReplyTo('geimasterdb@hotmail.com','GEI');
*/


$sql00 = "select count(*) as c,email_user,nome_app from settings";
$result00 = mysqli_query($db,$sql00);
$row00=mysqli_fetch_array($result00);



if ($row00['c']>0)
{


$mail->IsHTML(true);
$mail->From=$row00['email_user'];  
$mail->FromName=$row00['nome_app']; 
$mail->Sender=$row00['email_user']; 
$mail->AddReplyTo($row00['email_user'],$row00['nome_app']);

}
else
{


        
}
?>