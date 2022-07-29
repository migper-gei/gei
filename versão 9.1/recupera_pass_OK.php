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
if ( !isset($_POST['email']) || empty($_POST['email']) )
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
       
   
     // Validar email
     $emaila = $_POST["email"];
    // echo($emaila);
 
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
             text: 'O email não está registado!',
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