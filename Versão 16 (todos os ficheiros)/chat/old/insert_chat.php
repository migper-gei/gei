
<?php

//insert_chat.php


include('database_connection.php');



/*

$data = array(
	':to_user_id'		=>	$_POST['to_user_id'],
	':from_user_id'		=>	$_SESSION['user_id'],
	':chat_message'		=>	$_POST['chat_message'],
	':status'			=>	'1'
);

$query = "
INSERT INTO chat_message 
(to_user_id, from_user_id, chat_message, status) 
VALUES (:to_user_id, :from_user_id, :chat_message, :status)
";
*/

/*
echo $_SESSION['user_id'];
echo ('<br>');
echo $_POST['to_user_id'];
echo ('<br>');
echo $_POST['chat_message'];
*/



$to_user_id=$_POST['to_user_id'];
$from_user_id=$_SESSION['user_id'];
$chat_message=$_POST['chat_message'];
$status=1;

/*
echo $to_user_id;
echo ('<br>');
echo $from_user_id;
echo ('<br>');
echo $chat_message;
*/

$query = "
INSERT INTO chat_message 
(to_user_id, from_user_id, chat_message, status) 
VALUES (".$to_user_id.", ".$from_user_id.", '".$chat_message."', ".$status.")
";

$statement = $connect->prepare($query);

$statement->execute();


$result = $statement->get_result();

if($result)
{
	echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $connect);


	}
?>