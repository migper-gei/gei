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



 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir avaria</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-5 offset-md-4">
              
                        










  <br>   <br>



  <!--

            
require_once 'getid3/getid3.php';

//$filename = '1.mp4';
$filename = $_FILES["video"]["name"];

$getID3 = new getID3;
$file = $getID3->analyze($filename);

echo 'Duração: ', $file['playtime_string'], PHP_EOL,
'<br>',
     
     'Tamanho (Bytes): ', $file['filesize'];

 //'Resolução: ', $file['video']['resolution_x'], 'x', $file['video']['resolution_y'], PHP_EOL,



?>

 

-->

<br>


<script type="text/javascript">

Filevalidation = () => {
        const fi = document.getElementById('file');
        // Check if any file is selected.
        if (fi.files.length > 0) {
            for (const i = 0; i <= fi.files.length - 1; i++) {
  
                const fsize = fi.files.item(i).size;




                const file = Math.round((fsize / 1024));
                // The size of the file.
                if (file >= 3000) {
                    alert(
                      "O vídeo deve ter menos de 3Mb!");
                      return false;

                //} else if (file < 2048) {
                  //  alert(
                    //  "File too small, please select a file greater than 2mb");
                      //return false;
                } else {
                    document.getElementById('size').innerHTML = '<b>'
                    + file + '</b> KB';
                    return true;
                }
            }
        }
    }

</script>


<form name="avaria" action = "grava_video.php?a=abc"  method = "post" enctype="multipart/form-data"
              onSubmit="return Filevalidation();">
  
               
                   <br />
                   <br>
                   <label>video: </label>  <br>  
                   <input size=50 type="file" name="v" id="file" >
              
                   <!--onchange="Filevalidation()" 
                    onSubmit="Filevalidation();"
   -->
                  
                   <br /><br />
                                   
                                   
                   <div  style=" text-align:center;width:90%"> 
                   <input   type = "submit" value = "Inserir" />   
             
    </div>

                </form>

<br>




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