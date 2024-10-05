<?php

//fetch_user_chat_history.php
session_start();
include('database_connection.php');



echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $connect);

?>