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
                     <h2>Grava avaria</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
     
<?php

?>
    
<br>
                   



<?php


//echo $_GET["a"];




    $filename = $_FILES["v"]["name"];
 //echo($filename);

    $tmpname=$_FILES["v"]["tmp_name"];
    $filetype= $_FILES["v"]["type"];
    


    $tamanho = $_FILES["v"]["size"];

    //ini_set('memory_limit','-1');

  //  var_dump(realpath_cache_size());
   

    //var_dump(memory_get_usage(true));

    
   
    $tmpv=addslashes(file_get_contents($tmpname));

 
    //echo($tmpv);


  

//var_dump(memory_get_usage(true));




    $filepath = $_FILES["v"]["tmp_name"];

    $fileSize = filesize($filepath);
    $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
    $filetype = finfo_file($fileinfo, $filepath);
    
    //echo($fileSize );
    //echo($filetype );


    $allowedTypes = [
     'video/mp4' => 'mp4'
    ];
    
    if (!in_array($filetype, array_keys($allowedTypes))) 
    {
    echo("File not allowed.");
   

    ?>
   


<?php
    }

else
{
    
$sql = "insert into video (vid) values ('$tmpv')";

//$result = mysqli_query($db,$sql);
mysqli_close($db);
   }
   
   ?>
   





<br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>