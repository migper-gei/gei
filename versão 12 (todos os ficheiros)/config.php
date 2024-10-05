<?php
//echo("aaa");
//echo ( $_SESSION['email']);

if (isset($_SESSION['nobd']) && isset($_SESSION['serverbd']))
{
$nobd=$_SESSION['nobd'];
$serverbd=$_SESSION['serverbd'];

/*
echo $nobd;
echo '<br>';
echo $serverbd;
*/

}
else
{
?>
<script>
      window.setTimeout(function() {
          window.location.href = '<?php echo SVRURL ?>i';
      }, 10);
      </script>

<?php
}
/*
if ($nobd == null)
{
    ?>

<script>
      window.setTimeout(function() {
        //  window.location.href = '<?php echo SVRURL ?>i';
      }, 10);
      </script>

<?php
}
*/


//echo $nifbd;

include ("config_serverbd.php");

/*
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
  */
   //define('DB_DATABASE', 'geidb');
   
   define('DB_SERVER', $serverbd);
  define('DB_DATABASE', $nobd);



   $db = new mysqli($serverbd,DB_USERNAME,DB_PASSWORD,$nobd);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Falha na ligação à base de dados!: %s\n", mysqli_connect_error());
    exit();
}

   
    //$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   //mysqli_set_charset($db, "utf-8");
?>