<?php

//logout.php

session_start();

//session_destroy();

//header('location:../gei/i');
//exit;

include ("../config.php");


//echo $_SESSION['user_id'];

$sql = "update utilizadores set sessao_ativa='0' where id=".$_SESSION['user_id']."";
$result = mysqli_query($db,$sql);

?>

<script>
    window.close();
     
	</script>

