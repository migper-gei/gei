<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'miguelar_geiuser');
   define('DB_PASSWORD', 'geiuser+123');
   define('DB_DATABASE', 'miguelar_geidb');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   
      mysqli_set_charset($db, "utf8");
      
?>