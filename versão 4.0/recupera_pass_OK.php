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
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2> Recuperar ou mudar password </h2>
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">


<div class="wrapper fadeInDown">
  <div id="formContent">


    

<?php
    

    //include("config.php");
 
 
 if($_SERVER["REQUEST_METHOD"] == "POST") {
       
   
     // Validar email
     $emaila = $_POST["email"];
    // echo($emaila);
 
 if (filter_var($emaila, FILTER_VALIDATE_EMAIL)){
    // echo("");
 
 
 //$query = mysql_query("insert into students(student_name, student_email, student_contact, student_address) values ('$name', '$email', '$contact', '$address')");
 //AES_ENCRYPT('".$_POST["password"]."', 'secret')
       $sql = "select email from utilizadores where email='".$_POST["email"]."'";
       $result = mysqli_query($db,$sql);
      
       $count = mysqli_num_rows($result);
 
       //echo($count);
 
       if($count == 0) 
       {
         echo("<br><br><br>");
        // echo " <font color=navy font face='courier' size='5pt'>O Email não está registado.</font>";
         //header("Refresh:3; url=recupera_reset_pass.php?x=0");
 
 
         ?>
 
 
         <script>
              
                   swal({
             title: 'ERRO',
             text: 'O email não está registado!',
           icon: 'error',
             //buttons: false,
         
         })
         .then(function() {
             window.location = "<?php echo SVRURL ?>recuperapass/0";
         })
         ;
       
                   </script>
         
         
         <?php
 
 
 
 
       }
     
     elseif ($count==1)
     {
          //?em=$emaila   
        //header("Refresh:0; url=enviar_email_pass.php?em=$emaila");
 
 ?>
 
 <script>
 window.setTimeout(function() {
     window.location.href = '<?php echo SVRURL ?>enviar_email_pass.php?em=<?php echo($emaila);?>';
 }, 10);
 </script>
 
 <?php
 
 
 
 ?>
 
 
 
 
 
 <?php
 
     }
 
        
    mysqli_close($db);
 
 
   
 ?>
      
 
 <!--
      
       <div id="content">
       <div class="post">
 
      <h2 class="title"><b>GEI - Gestão do Equipamento Informático  </h2> 
     <br>
     -->
    
 <?php
 }
 
 
 else{
     echo("<br><br><br>");
     //echo ("<font color=navy font face='courier' size='5pt'>Email incorreto.</font>");
     //header("Refresh:3; url=recupera_reset_pass.php?x=0");
 
     ?>
 
 
     <script>
          
               swal({
         title: 'ERRO',
         text: 'Email incorreto!',
       icon: 'error',
         //buttons: false,
     
     })
     .then(function() {
         window.location = "<?php echo SVRURL ?>recuperapass/0";
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