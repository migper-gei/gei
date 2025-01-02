       <?php 
   
//$mail->SMTPDebug = 1;

//Ask for HTML-friendly debug output
//$mail->Debugoutput = 'html';

$sql00 = "select count(*) as c from settings";
$result00 = mysqli_query($db,$sql00);
$row00=mysqli_fetch_array($result00);




$sql01 = "select email_user,pass,email_smtp,email_smtpport from settings";
$result01 = mysqli_query($db,$sql01);
$row01=mysqli_fetch_array($result01);



if ($row00['c']>0)
{

$sql02 = "select * from settings";
$result02 = mysqli_query($db,$sql02);
$row02=mysqli_fetch_array($result02);

$mail->Host=$row02['email_smtp'];
$mail->Port=$row02['email_smtpport'];




$mail->SMTPAuth = true;




//$mail->SMTPDebug=3;

$sql2 = $db->prepare("select AES_DECRYPT(`pass`, 'secret') from settings");
$sql2->execute();
$row2 = $sql2->get_result()->fetch_row();

$mail->Username =$row02['email_user'];
$mail->Password = $row2[0];



        
}
else
{
        ?>


<script>
    
    swal({
title: 'Ainda não foram definidas as configurações de email!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>emsess";
});


</script>

<?php
}
?>