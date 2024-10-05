<?php



  // Initialize the session
session_start();
//echo $_SESSION['iduser'];

$id=$_SESSION['user_id'];


//echo $id;

include ("config.php");

$sql = "update utilizadores set sessao_ativa='0' where id=".$id."";
$result = mysqli_query($db,$sql);





$_SESSION = array();

  // Initialize the session
//session_start();
 
// Unset all of the session variables

 
// Destroy the session.
//session_destroy();
 



// Limpa a sessão 
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
//session_regenerate_id(true);  





header("location: i");
exit;

?>