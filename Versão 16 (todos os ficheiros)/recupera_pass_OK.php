<?php session_start();?>


<!DOCTYPE html>
<html lang="pt">
   <head>
      <script>
         function myFunction() {
           var x = document.getElementById("mypass");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
         </script>



<?php include ("head.php");

?>


   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->
      

     <?php include ("header2.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Recuperar password </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">





  <?php
if ( !isset($_POST['email']) || empty($_POST['email']) 
||
!isset($_POST['codigo']) || empty($_POST['codigo']) 

)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>';
}, 10);
</script>


<?php
}
?>

    

<?php
    

    //include("config.php");
 
 
 if($_SERVER["REQUEST_METHOD"] == "POST") {
       
 $codigo = $_POST['codigo']; 
 $emaila = $_POST["email"];

 include ("config_serverbd_settings.php");


 $db0 = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);


 $sql0 = $db0->prepare("select count(*) as ccod from settingsbd  where codigo=?");
 $sql0->bind_param("s", $codigo);
 
 $sql0->execute();
 
 $rows0 = $sql0->get_result()->fetch_row();
 $ccod = $rows0[0];
 

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
   
    elseif ($ccod==1)
    {
    

   
   

    
    $db0 = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
      
    $sql0 = "SELECT nomebd,serverbd from settingsbd WHERE codigo = '$codigo' ";
    $result0 = mysqli_query($db0,$sql0);
    $row0 = mysqli_fetch_array($result0);
 
    $nomebd=$row0['nomebd'];
    $serverbd=$row0['serverbd'];;
    mysqli_close($db0);

//echo $nomebd;
$_SESSION['nobd']=$nomebd;
$_SESSION['serverbd']=$serverbd;
  

    $db = new mysqli($serverbd,DB_USERNAME,DB_PASSWORD,$nomebd);
    mysqli_select_db($db, $nomebd );


 
 if (filter_var($emaila, FILTER_VALIDATE_EMAIL)){
    // echo("");
 
 

       $sql =$db->prepare( "select email from utilizadores where email=?");
           $sql->bind_param("s", $emaila);
       $sql -> execute();
       $sql -> store_result();
       
       //echo $sql -> num_rows;

       $count = $sql -> num_rows;



       //echo($count);
 
       if($count == 0) 
       {
         echo("<br><br><br>");
    
         ?>
 
 
         <script>
              
                   swal({
             title: 'ERRO',
             text: 'Verifique os dados (Email e código)!',
           icon: 'error',
             //buttons: false,
         
         })
         .then(function() {
             window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>";
         })
         ;
       
                   </script>
         
         
         <?php
 
 
 
 
       }
     
     elseif ($count==1)
     {
   
 ?>
 
 <script>
 window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>enviar_email_pass.php?em=<?php echo base64_encode($emaila);?>';
 }, 10);
 </script>
 
 <?php
 
 
}
 ?>
 
 
 
 
 
 <?php
 
     }
 
        
    mysqli_close($db);
 
 
   
 ?>
      
 

    
 <?php
 }
 
 
 else{
     echo("<br><br><br>");

 
     ?>
 
 
     <script>
          
               swal({
         title: 'ERRO',
         text: 'Email incorreto!',
       icon: 'error',
         //buttons: false,
     
     })
     .then(function() {
         window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0)?>";
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
      <?php include ("footer.php");?>

</body>
</html>