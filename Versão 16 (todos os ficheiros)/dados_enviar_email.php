        <?php
       /* 
        $mail->IsHTML(true);
        $mail->From="geimasterdb@hotmail.com";
        $mail->FromName="GEI";
        $mail->Sender="geimasterdb@hotmail.com";
        $mail->AddReplyTo('geimasterdb@hotmail.com','GEI');
*/

$sql000 = "select count(*) as c from settings";
$result000 = mysqli_query($db,$sql000);
$row000=mysqli_fetch_array($result000);



$sql00 = "select email_user,nome_app from settings";
$result00 = mysqli_query($db,$sql00);
$row00=mysqli_fetch_array($result00);



if ($row000['c']>0)
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