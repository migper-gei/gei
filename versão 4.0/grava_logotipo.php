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



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Dados da Escola/Agrupamento</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-6 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    



    <?php
    $x=0;

$filename = $_FILES["logo"]["name"];


if ($filename=="")
{

$tmp="";

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
    window.location = "<?php echo SVRURL ?>dadosesc";
})
;

          </script>


<?php
//}  }


}


}



if($_SERVER["REQUEST_METHOD"] == "POST" && $x==0) 
{

$sql2 = "delete from logotipo;";
$result = mysqli_query($db,$sql2);



$sql = "insert into logotipo (nomeescola,logotipo,site) 
values ('".$_POST["nomeescola"]."','$tmp','".$_POST["site"]."')";

$result = mysqli_query($db,$sql);




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