<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php 

include ("head.php");?>


   </head>


<?php



//echo($_SESSION['nif']);

if (!isset($_POST['email']) ||  empty($_POST['email'])  
|| !isset($_POST['password']) ||  empty($_POST['password']) 
|| !isset($_POST['codigo']) ||  empty($_POST['codigo']) 
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>l';
          },10);
          </script>


<?php
}


?>




   <?php
   
if (isset($_GET['url']) && is_numeric (base64_decode($_GET['url'])))
{
$url = explode('/',base64_decode($_GET['url']));

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>
<?php
}
?>







   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php 
     


//session_start();



     include ("header2.php");
     
     ?>
     

      

           <div  class="about">
         <div class="container">
            
               <div class="row">
               <div class="col-md-10 offset-md-2">

              <div class="titlepage">
                     <h2></h2>
                  </div>
        

<?php
    


  //$_SESSION = array();
  //session_destroy();

   //session_start();

// Set the last request variable
$_SESSION['lastaccess'] = time();
//echo(time());
   
   //include("sessao_timeout.php");


//$z1=$_GET["z"];
//echo($z1);

$z1=$url[0];

//echo $z1;
if ($z1!=0)
{
   ?>
   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>

<?php
}



if($_SERVER["REQUEST_METHOD"] == "POST") {

  
        
 $codigo = $_POST['codigo']; 


 include ("config_serverbd_settings.php");




$db0 = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);


$sql0 = $db0->prepare("select count(*) as ccod from settingsbd  where codigo=?");
$sql0->bind_param("s", $codigo);

$sql0->execute();

$rows0 = $sql0->get_result()->fetch_row();
$ccod = $rows0[0];


/*

 $sqla = "select count(*) as ccod from settingsbd
 where codigo=".$_POST["codigo"]." ";
 
 $result = mysqli_query($db0,$sqla);
 $rows0 =mysqli_fetch_row($result);
 $ccod = $rows0[0];
 */


//echo $ccod;




 if ($ccod==0)
 {
?>




<script>
    
swal({
   title: 'ERRO',
   text: 'Código incorreto!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
   window.location = "<?php echo SVRURL ?>l";
});


</script>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<?php
 }

 if ($ccod==1)
 {
 

 //$db0 = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   

 $sql0 = "SELECT nomebd,serverbd from settingsbd WHERE codigo = '$codigo' ";
   $result0 = mysqli_query($db0,$sql0);
   $row0 = mysqli_fetch_array($result0);

   $nomebd=$row0['nomebd'];;
   $serverbd=$row0['serverbd'];

   //echo $serverbd;
  

mysqli_close($db0);

//echo $nomebd;
$_SESSION['nobd']=$nomebd;
$_SESSION['serverbd']=$serverbd;






$db = new mysqli($serverbd,DB_USERNAME,DB_PASSWORD,$nomebd);


mysqli_select_db($db, $nomebd );
//include("config.php");
  

      $myemail = mysqli_real_escape_string($db,$_POST['email']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
      
 // echo($mypassword);
  //    echo($myemail);




      $sql = "SELECT id,nome,tipo,email FROM utilizadores WHERE email = '$myemail' and AES_DECRYPT(pass, 'secret') = '$mypassword'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result);
      //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      //$active = $row['active'];
      
      $count = mysqli_num_rows($result);
       //echo($count);
      // se encontrar o user: $myusername and $mypassword
        
  
      mysqli_close($db);



      if($count == 1) 
      {

         //echo "conta = 1";
       // echo($row['nome']); 
   
   
        $_SESSION['login_user'] = $row['nome'];
        $_SESSION['tipo'] = $row['tipo'];
        $_SESSION['email'] = $row['email'];

      
       // $_SESSION['nobd']=$nomebd;




       //atualizar o valor do campo "sessao_ativa"
       $_SESSION['user_id'] = $row['id'];
       //$sql = "update utilizadores set sessao_ativa='1' where id=".$row["id"]."";
       //$result = mysqli_query($db,$sql);
      


       
?>


  <?php

/*

      include("msg_bemvindo.php");
      echo('<br>');
      include("texto_gei.php");
        */
     
     

   ?>  


<script lang="javascript">
 //window.onload = function() {
	//if(!window.location.hash) {
		//window.location = window.location + '#loaded';
		//window.location.reload();
     
     
      window.location = "<?php echo SVRURL ?>acessorap";
	
      //}
//}
  </script>


<?php
        
      }
      elseif($count == 0) 
      {
         //$error = "Email ou Password incorreta.";
         //echo($error);
         echo "<br><br>";
     

        ?>


        <script>
             
                  swal({
            title: 'ERRO',
            text: 'Verifique os dados (Email, password e código)!',
          icon: 'error',
            //buttons: false,
        
        })
        .then(function() {
            window.location = "<?php echo SVRURL ?>l";
        })
        ;
      
                  </script>
        
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
     
        <?php

      }


}




elseif ($_SERVER["REQUEST_METHOD"] != "POST")
{
?>

<script>
      window.setTimeout(function() {
          window.location.href = '<?php echo SVRURL ?>l';
      }, 10);
      </script>

<?php
   }



}
  
//mysqli_close($db);

?>




  



             
            </div>
         </div>
      </div>
      <!-- end about -->
    
<br>
      <?php include ("footer.php");?>

</body>
</html>
