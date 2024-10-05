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
               <a href="#" class="btn btn-secondary disabled">AVARIAS >> ATUALIZAR</a>
               <div class="titlepage">
     
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    


    <?php 




if ( !isset($_POST['data']) || !isset($_POST['avaria']) 
|| empty($_POST['data']) || empty($_POST['avaria']) 
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>


<?php

}
else
{

   
?>
 
 <?php
  


if ( $_SERVER["REQUEST_METHOD"] == "POST" )


{

 //echo ("zzz");



   $filenamev = $_FILES["v"]["name"];
   //echo($filename);
   
   $tmpnamev=$_FILES["v"]["tmp_name"];
   //$filetype= $_FILES["v"]["type"];
   //$tamanho = $_FILES["v"]["size"];
   
 


$x=0;
$filenamei=$_FILES["imgavaria"]["name"];
//echo($filenamei);



if ($filenamev=="")
{

$tmpv="";
$vid=0;


//include("config.php");


}
if ($filenamev<>"" )
{
   $tmpv=addslashes(file_get_contents($tmpnamev));
   $vid=1;
}


   if ($filenamei=="")
   {
   $tmp="";

   $img=0;
  
   
//include("config.php");


 }


if ($filenamei<>"" )
{

  

$tmpname=$_FILES["imgavaria"]["tmp_name"];
$filetype= $_FILES["imgavaria"]["type"];

$tmp=addslashes(file_get_contents($tmpname));
$img=1;


$filepath = $_FILES['imgavaria']['tmp_name'];
$fileSize = filesize($filepath);
$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
$filetype = finfo_file($fileinfo, $filepath);

//echo $filetype;

$allowedTypes = [
   'image/png' => 'png',
  'image/jpeg' => 'jpeg',
  'image/jpg' => 'jpg',
  'image/bpm' => 'bmp',
  'image/gif' => 'gif'
  ];
  
  if (!in_array($filetype, array_keys($allowedTypes))) 
{
//echo("File not allowed.");
//$x=1;
$x=1;


?>
 <script>
          
          swal({
      title: 'ERRO',
      text: 'O ficheiro não tem conteúdo de imagem!',
      icon: 'error',
      //buttons: false,
      
      })
      .then(function() {
      window.location = "<?php echo SVRURL ?>myavarias?op=t";
      })
      ;
      
      </script>



<?php
     
}

}



//echo ($x);

$idav=base64_decode($_GET['url']);

//echo $idav;


if ( isset($idav) && is_numeric($idav) )
{
$url = explode('/',$idav);

//echo $url[0];
}
else
{
    ?>
    
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>

<?php
}





if ($img==0)
{
$sql = "update avarias_reparacoes 
set dataavaria=STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),avaria='".$_POST["avaria"]."'
 where id=".$idav."";

$result = mysqli_query($db,$sql);

?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',

icon: 'success',

})
.then(function() {
window.location = "<?php echo SVRURL ?>myavarias?op=t";
})
;

</script>
<?php
}





if ($x==0 && $img==1 && $vid==0)
{
$sql = "update avarias_reparacoes 
set dataavaria=STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),avaria='".$_POST["avaria"]."',
imgavaria='$tmp' where id=".$idav."";

$result = mysqli_query($db,$sql);

?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',

icon: 'success',

})
.then(function() {
window.location = "<?php echo SVRURL ?>myavarias?op=t";
})
;

</script>
<?php

}





if ($vid==1 && ($x==1 || $img==0))
{
$sql = "update avarias_reparacoes 
set dataavaria=STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),avaria='".$_POST["avaria"]."',
video='$tmpv' where id=".$idav."";

$result = mysqli_query($db,$sql);

?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',

icon: 'success',

})
.then(function() {
window.location = "<?php echo SVRURL ?>myavarias?op=t";
})
;

</script>
<?php

}





if ($x==0 && $img==1 && $vid==1)
{
$sql = "update avarias_reparacoes 
set dataavaria=STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),avaria='".$_POST["avaria"]."',
imgavaria='$tmp',video='$tmpv' where id=".$idav."";

$result = mysqli_query($db,$sql);

?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',

icon: 'success',

})
.then(function() {
window.location = "<?php echo SVRURL ?>myavarias?op=t";
})
;

</script>
<?php
}


//header("Refresh:0;url=minhas_avarias.php");
mysqli_close($db);
?>








<?php
}

}
?>





<br><br><br><br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    

   

      <?php include ("footer.php");  ?>
      
      
      
    

   </body>
</html>