<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();





//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>





      
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
                  <div class="col-md-11 offset-md-1">
              
                        

    

<?php 






$sql = "select * from video";
$result = mysqli_query($db,$sql);



?>






        <!-- Datatable -->
        <table class="table table-striped">
            <thead>
                <tr class="table-success">
                    
                    <th scope="col">videos</th>
             
                     
                     
                    
                </tr>
            </thead>
            <tbody>

              



                <?php  while($row=mysqli_fetch_array($result)) { 
                


                    ?>
                <tr>
                    
                    <td width="35%" >
                    
                
                    <!--
                    
                    data:video/' . $type . ';base64,' . base64_encode(file_get_contents($file))
                    
 <object width="320" height="240">
               <embed width="320" height="240" src="data:video/mp4;base64,'.base64_encode($row['vid']).' ">
               </object>

                    <video width="320" height="240" alt="test" controls >
               <source src="data:video/mp4;base64,'.base64_encode($row['vid']).' " >
                -->


                <?php
                echo '
              
                <video width="320" height="240" alt="test" controls >
                <source src="data:video/mp4;base64,'.base64_encode($row['vid']).' " >
             
            </video>

            ';
                   

                }
                    
                    ?>
 
                  


                    </td>



                     </td>
                    
                
                </tr>
             
            </tbody>
        </table>     
                
        
        

    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <script>
        $(document).ready(function () {
            $('#records-limit').change(function () {
                $('form').submit();
            })
        });
    </script>




<br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>