<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}
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
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Dados da(s) Instituição(ões)</li>
                  </ol>
               </nav>
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
   $tmpname = $_FILES["logo"]["tmp_name"];

   // FIX: verificar que o ficheiro foi enviado via HTTP POST
   if (!is_uploaded_file($tmpname)) {
       $x = 1;
       ?>
       <script>
       swal({ title: 'ERRO', text: 'Upload inválido.', icon: 'error' })
       .then(function() { window.location = "<?php echo SVRURL ?>atulog"; });
       </script>
       <?php
   } else {

   $tmp = addslashes(file_get_contents($tmpname));

   // FIX: usar finfo para detetar MIME real (ignorar $_FILES["type"] controlado pelo browser)
   $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
   $filetype = finfo_file($fileinfo, $tmpname);
   finfo_close($fileinfo);

   // FIX: verificar extensão contra MIME real
   $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

   $allowedTypes = [
       'image/png'  => ['png'],
       'image/jpeg' => ['jpg', 'jpeg'],
       'image/gif'  => ['gif'],
       'image/bmp'  => ['bmp'],
   ];

   $mimeOk = array_key_exists($filetype, $allowedTypes);
   $extOk  = $mimeOk && in_array($ext, $allowedTypes[$filetype], true);

   if (!$mimeOk || !$extOk || @getimagesize($tmpname) === false)
{
$x=1;
?>
<script>
swal({
    title: 'ERRO',
    text: 'O ficheiro não é uma imagem válida (PNG, JPEG, GIF ou BMP)!',
    icon: 'error',
}).then(function() {
    window.location = "<?php echo SVRURL ?>atulog";
});
</script>
<?php
}

   } // fim is_uploaded_file
}



if($_SERVER["REQUEST_METHOD"] == "POST" && $x==0) 
{




$sql2 = "select id,count(*) from logotipo group by id limit 1";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$id = $rows[0];
$totalesc = $rows[1];





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