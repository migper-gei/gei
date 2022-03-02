<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>--> 
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

//$sql2 = "delete from logotipo;";
//$result = mysqli_query($db,$sql2);

$sql2 = "select id,count(*) from logotipo";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$id = $rows[0];
$totalesc = $rows[1];

//echo $totalesc;


if ($totalesc==0)
{

$sql = "insert into logotipo (nomeescola,logotipo,site) 
values ('".$_POST["nomeescola"]."','$tmp','".$_POST["site"]."')";

$result = mysqli_query($db,$sql);


//$sql1b = "delete from escolas";
//$result1b = mysqli_query($db,$sql1b);



$sql1a = "insert into escolas (nome_escola) 
values ('".$_POST["nomeescola"]."')";
$result1a = mysqli_query($db,$sql1a);






if ($_POST["nomeescola2"]<>"")
{
   $sql2a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola2"]."')";
   $result2a = mysqli_query($db,$sql2a);
   
}


if ($_POST["nomeescola3"]<>"")
{
   $sql3a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola3"]."')";
   $result3a = mysqli_query($db,$sql3a);
   
}

if ($_POST["nomeescola4"]<>"")
{
   $sql4a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola4"]."')";
   $result4a = mysqli_query($db,$sql4a);
   
}

if ($_POST["nomeescola5"]<>"")
{
   $sql5a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola5"]."')";
   $result5a = mysqli_query($db,$sql5a);
   
}



}
elseif ($totalesc>0)
{

   if ($filename=="")
   {


   $sql3 = "update logotipo set nomeescola='".$_POST["nomeescola"]."',
    
   site='".$_POST["site"]."'
   where id=$id";

   }

   if ($filename<>"")
   {
      $sql3 = "update logotipo set nomeescola='".$_POST["nomeescola"]."',
      logotipo='$tmp', 
      site='".$_POST["site"]."'
      where id=$id";
   }

   $result3 = mysqli_query($db,$sql3);



   $sql2a = "select min(id) from escolas";
   $result2a = mysqli_query($db,$sql2a);
   $rows2a =mysqli_fetch_row($result2a);
   $minid = $rows2a[0];

   
   $sql3a = "update escolas set nome_escola='".$_POST["nomeescola"]."'
   where id=$minid";
   $result3a = mysqli_query($db,$sql3a);



   if ($_POST["nomeescola2"]<>"")
{
   $sql2a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola2"]."')";
   $result2a = mysqli_query($db,$sql2a);
   
}


if ($_POST["nomeescola3"]<>"")
{
   $sql3a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola3"]."')";
   $result3a = mysqli_query($db,$sql3a);
   
}

if ($_POST["nomeescola4"]<>"")
{
   $sql4a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola4"]."')";
   $result4a = mysqli_query($db,$sql4a);
   
}

if ($_POST["nomeescola5"]<>"")
{
   $sql5a = "insert into escolas (nome_escola) 
   values ('".$_POST["nomeescola5"]."')";
   $result5a = mysqli_query($db,$sql5a);
   
}

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