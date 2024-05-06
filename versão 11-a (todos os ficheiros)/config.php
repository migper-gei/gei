<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'geidb');
   //$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);


   $db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Falha na ligação à base de dados!: %s\n", mysqli_connect_error());
    exit();
}

   
 
   //mysqli_set_charset($db, "utf-8");
?>