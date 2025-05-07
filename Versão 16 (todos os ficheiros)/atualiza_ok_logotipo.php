<?php
  session_start();
  session_regenerate_id();
  ?>

<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> GERAIS </a>
               <div class="titlepage">
                  
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-6 offset-md-3">
              
                        
         
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    
</div>





    

    <?php
    $x=0;

$filename = $_FILES["logo"]["name"];



if ($filename=="")
{
    //echo "aaaaa";
$tmp="";
$x=1;
?>

<script>

swal({
title: 'Não foi escolhido nenhuma imagem!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atulog";
});


</script>



<?php

}

elseif  ($filename<>"")
{
   $tmpname=$_FILES["logo"]["tmp_name"];
   
   $filetype= $_FILES["logo"]["type"];
   
   $tmp=addslashes(file_get_contents($tmpname));
   

//echo($filename);



$filepath = $_FILES["logo"]["tmp_name"];
$fileSize = filesize($filepath);
$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
$filetype = finfo_file($fileinfo, $filepath);

//$upload_permitted_types= array('image/jpeg:jpg','image/pjpeg:jpg','image/gif:gif','image/png:png');




$allowedTypes = [
'image/png' => 'png',
];


if (!in_array($filetype, array_keys($allowedTypes))) 
{
//echo("File not allowed.");

$x=1;

?>



<script>
          
          swal({
    title: 'ERRO',
    text: 'O ficheiro PNG não tem conteúdo de imagem!',
  icon: 'error',
    //buttons: false,

})
.then(function() {
    window.location = "<?php echo SVRURL ?>atulog";
})
;

          </script>


<?php
//}  }


}


}



if($_SERVER["REQUEST_METHOD"] == "POST" && $x==0) 
{




$sql2 = "select id,count(*) from logotipo";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$id = $rows[0];
$totalesc = $rows[1];

//echo $totalesc;





if ($totalesc==0)
{

   

   $sql3 = "update logotipo set logotipo='$tmp'
   where id=$rows[0]";

  

   $result3 = mysqli_query($db,$sql3);



 


}


if ($totalesc>0)
{

   

   $sql3 = "update logotipo set logotipo='$tmp'
   where id=$rows[0]";

  

   $result3 = mysqli_query($db,$sql3);



 


}




//header("Refresh:0;url=configuracao.php");
mysqli_close($db);

?>




<script>

swal({
title: 'Os dados foram guardados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
  window.location = "<?php echo SVRURL ?>configura";
});


</script>

<?php
}







?>


<?php include ("jquery_bootstrap.php");?>



<br><br><br><br><br><br><br><br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
      <br><br><br><br>

      <?php include ("footer.php");?>


   </body>
</html>