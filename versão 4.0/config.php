<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'gsiuser');
   define('DB_PASSWORD', 'gsiuser+123');
   define('DB_DATABASE', 'geidb');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);


   //mysqli_set_charset($db, "utf-8");
?>