<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php include ("head.php");?>



   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>-->
      <!-- end loader -->


     <?php //include ("header.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
             
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">




  <?php
if ( !isset($_POST['nif']) || !isset($_POST['email']) 
|| empty($_POST['nif']) || empty($_POST['email']) 
|| !isset($_POST['contato']) || empty($_POST['contato'])
|| !isset($_POST['nome_esc_inst']) || empty($_POST['nome_esc_inst'])


)
{




?>




<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>criarbd.php';
}, 10);
</script>


<?php
}

?>




<?php

include ("config_serverbd.php");

$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD);



if($_SERVER["REQUEST_METHOD"] == "POST") {
       
   


    $codigo=$_POST["codigo"];
    $em=$_POST["email"];
    $contato=$_POST["contato"];

    $nome_esc_inst=$_POST["nome_esc_inst"];
  
   
//echo $nif ;




    //define('DB_DATABASE', 'geidb');
    //$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
 
 

 
 /* check connection */
 if (mysqli_connect_errno()) {
     printf("Falha na ligação à base de dados!: %s\n", mysqli_connect_error());
     exit();
 }
 
    
  
 
$nomebd='gei_'.$codigo;


   ?>
   
   <?php
   $database=$nomebd;

   
$query="SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
$stmt = $db->prepare($query);
$stmt->bind_param('s',$database);
$stmt->execute();
$stmt->bind_result($data);
if($stmt->fetch())
{
   // echo "Database exists.";


?>

   <script>
         
         swal({
   title: 'ERRO',
   text: 'A base de dados já existe!',
 icon: 'error',
   //buttons: false,

})
.then(function() {
   window.location = "<?php echo SVRURL ?>criarbd.php";
})
;

         </script>

<?php
} 

else
{

$sql0 = "create database $nomebd";
$result = mysqli_query($db,$sql0);




//$seldb = mysqli_select_db( $db, $nomebd );
mysqli_select_db( $db, $nomebd );

//criar tabelas na BD
$sqlFile = 'geidb.sql';
$sql11 = file_get_contents($sqlFile);

// Execute multiple queries from SQL file
$db->multi_query($sql11);
   // echo "SQL file executed successfully";



   //mysqli_refresh($db, MYSQLI_REFRESH_TABLES );



   //mysqli_close($db);

   ?>





<?php





 /*

$sql2 = $db->prepare("select count(*) from criarbd where nif=?");
$sql2->bind_param("s", $nif);

$sql2->execute();


$rows2 = $sql2->get_result()->fetch_row();



$contanif = $rows2[0];
 

//echo ($contanif);



if ($contanif==1)
{

?>
  


 <script>
         
         swal({
   title: 'ERRO',
   text: 'O NIF já está registado!',
 icon: 'error',
   //buttons: false,

})
.then(function() {
   window.location = "<?php echo SVRURL ?>criarbd.php";
})
;

         </script>





         <?php
}
         ?>




<?php

}
elseif ($contanif==0)

{
*/









//echo $nomebd;

//$seldb = mysqli_select_db( $db, $nomebd );


/*

$sql2 = $db->prepare("insert into utilizadores (nome,email,tipo,pass)
values (?,?,?,AES_ENCRYPT('$pa', 'secret'))");

$t=1;

$sql2 -> bind_param('ssi', 'Admin',$em,$t);


$sql2->execute();

*/




//echo($conta);
//echo($no);
//echo($em);
//echo($pa);



function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
{
$lmin = 'abcdefghijklmnopqrstuvwxyz';
$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$num = '1234567890';
$simb = '!@#$%*-';
$retorno = '';
$caracteres = '';

$caracteres .= $lmin;
if ($maiusculas) $caracteres .= $lmai;
if ($numeros) $caracteres .= $num;
if ($simbolos) $caracteres .= $simb;

$len = strlen($caracteres);
for ($n = 1; $n <= $tamanho; $n++) {
$rand = mt_rand(1, $len);
$retorno .= $caracteres[$rand-1];
}
return $retorno;


}

$pa = geraSenha(8, true, true, true);






// Return name of current default database
/*
if ($result = $db1 -> query("SELECT DATABASE()")) {
   $row = $result -> fetch_row();
   echo "Default database is " . $row[0];
   $result -> close();
 }
 */
 // Change db to "test" db
// $db-> select_db($row[0]);


//mysqli_select_db( $db, $nomebd );

//echo $nomebd;
//echo '<br>';
//echo $db->host_info;



/*


$query = mysqli_query($db1, "SHOW TABLES IN $nomebd");
$numrows = mysqli_num_rows($query);
echo "<b>Amount of tables: ".$numrows." and their names:</b>";
while ($row = mysqli_fetch_array($query)) {
    echo $row[0]." ";
}


echo '<br>';


*/






//define('DB_DATABASE', 'gei_escolas_instituicoes');
//include ("gei_esc_inst.php");

$db1 = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

$sql00 = "insert into settingsbd (codigo,nome_esc_inst,email,contato,nomebd) 
values (".$codigo.",'".$nome_esc_inst."','".$em."','".$contato."','".$nomebd."'

  )";
$result = mysqli_query($db1,$sql00);




?>


<!--

 <script>
         
         swal({
title: 'Os dados foram guardados!',
//text: 'Email enviado.',
icon: 'success',

})
.then(function() {
  // window.location = "<?php echo SVRURL ?>l";
})
;

         </script>


-->








<!--
<script>
window.setTimeout(function() {
   // window.location.href = 'l';
}, 15000);
</script>
-->











<?php



mysqli_select_db( $db1, $nomebd );

$sql3 = "insert into utilizadores (nome,email,tipo,pass)
values ('Admin','$em',1,AES_ENCRYPT('123+-abc', 'secret') )";

$result3 = mysqli_query($db1,$sql3);


mysqli_close($db);
mysqli_close($db1);


?>





<script>
         
         swal({
      title: 'A base de dados foi criado com sucesso!',
      //text: 'A base de dados foi criado com sucesso!',
      icon: 'success',
      //buttons: false,
      
      })
      .then(function() {
      window.location = "<?php echo SVRURL ?>criarbd.php";
      })
      ;
      
         </script>

<?php
}

}
?>



  </div>
</div>


</div>
         </div>
      </div>
      <!-- end about -->
    
<br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br>
      <?php include ("footer.php");?>



</body>
</html>