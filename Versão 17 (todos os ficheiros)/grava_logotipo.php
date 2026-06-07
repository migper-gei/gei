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
                  <div class="col-md-10 offset-md-2">
              
                        


                  <div class="welcome-section">
<?php
include("msg_bemvindo.php");
?>
     </div>
    

    <?php
//if (empty($_POST['nomeescola']) || empty($_POST['site']))


if (!isset($_POST['nomeescola'])  || !isset($_POST['site'])  
|| empty($_POST['nomeescola']) || empty($_POST['site'])  )
{

?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 140);
</script>

<?php
}

else
{

// --- Validação CSRF ---
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    ?>
    <script>
    swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>dadosesc"; });
    </script>
    <?php
    exit;
}
// Invalidar token após uso (one-time use)
unset($_SESSION['csrf_token']);

?>

<!--
<script>
    
swal({
title: 'Os dados não foram guardados!',
//text: 'Verificar data da utilização, horas e sala. Consulte a tabela das requisições para o dia.',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>equip";
});


</script>
-->








    

    <?php
    $x=0;

$filename = $_FILES["logo"]["name"];


if ($filename=="")
{
    // Sem ficheiro novo — é válido (o logotipo existente mantém-se)
    $tmp = "";
    $x   = 0;
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
       .then(function() { window.location = "<?php echo SVRURL ?>dadosesc"; });
       </script>
       <?php
   } else {

   $tmp = file_get_contents($tmpname); // BLOB — sem addslashes; bind_param("b") trata os dados binários

   // FIX: usar finfo para detetar MIME real (ignorar $_FILES["type"] controlado pelo browser)
   $filepath = $tmpname;
   $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
   $filetype = finfo_file($fileinfo, $filepath);
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

   if (!$mimeOk || !$extOk || @getimagesize($filepath) === false) 
{
$x=1;
?>
<script>
swal({
    title: 'ERRO',
    text: 'O ficheiro não é uma imagem válida (PNG, JPEG, GIF ou BMP)!',
    icon: 'error',
}).then(function() {
    window.location = "<?php echo SVRURL ?>dadosesc";
});
</script>
<?php
}

   } // fim is_uploaded_file
}



if($_SERVER["REQUEST_METHOD"] == "POST" && $x==0) 
{









/*
$sql2 = "delete from logotipo;";
$result = mysqli_query($db,$sql2);
*/


$sql2 = "select id,count(*) from logotipo group by id limit 1";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$id = $rows[0];
$totalesc = $rows[1];



if ($totalesc==0)
{

$_log_nome = $_POST["nomeescola"] ?? '';
$_log_site = $_POST["site"]       ?? '';
if ($tmp !== "") {
    // Com imagem nova
    $stmt_ins = $db->prepare("INSERT INTO logotipo (nomeescola, logotipo, site) VALUES (?, ?, ?)");
    $null = null;
    $stmt_ins->bind_param("sbs", $_log_nome, $null, $_log_site);
    $stmt_ins->send_long_data(1, $tmp);
} else {
    // Sem imagem
    $stmt_ins = $db->prepare("INSERT INTO logotipo (nomeescola, site) VALUES (?, ?)");
    $stmt_ins->bind_param("ss", $_log_nome, $_log_site);
}
$stmt_ins->execute();
$stmt_ins->close();


//$sql1b = "delete from escolas";
//$result1b = mysqli_query($db,$sql1b);



$_esc1_nome = $_POST["nomeescola"] ?? '';
$_esc1_morada = $_POST["morada"] ?? '';
$_esc1_cp = $_POST["codpostal"] ?? '';
$_esc1_loc = $_POST["localidade"] ?? '';
$_esc1_tel = (int)($_POST["telefone"] ?? 0);
$stmt_ins_esc = $db->prepare("INSERT INTO escolas (nome_escola,morada,codigopostal,localidade,telefone) VALUES (?,?,?,?,?)");
$stmt_ins_esc->bind_param("ssssi", $_esc1_nome, $_esc1_morada, $_esc1_cp, $_esc1_loc, $_esc1_tel);
$stmt_ins_esc->execute();
$stmt_ins_esc->close();






// Instituições adicionais 2..11 — INSERT/UPDATE se preenchido, DELETE se vazio
for ($i = 2; $i <= 11; $i++) {
    $_nome_add = trim($_POST["nomeescola{$i}"] ?? '');
    if ($_nome_add !== '') {
        $_stmt_esc_add = $db->prepare(
            "INSERT INTO escolas (id, nome_escola) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE nome_escola = VALUES(nome_escola)"
        );
        $_stmt_esc_add->bind_param("is", $i, $_nome_add);
        $_stmt_esc_add->execute();
        $_stmt_esc_add->close();
    } else {
        // Campo vazio — apagar o registo se existir
        $_stmt_esc_del = $db->prepare("DELETE FROM escolas WHERE id = ?");
        $_stmt_esc_del->bind_param("i", $i);
        $_stmt_esc_del->execute();
        $_stmt_esc_del->close();
    }
}



}
elseif ($totalesc>0)
{

   if ($filename=="")
   {


   $_log_nome = $_POST["nomeescola"] ?? '';
   $_log_site = $_POST["site"] ?? '';
   $stmt_log=$db->prepare("UPDATE logotipo SET nomeescola=?,site=? WHERE id=?");
   $stmt_log->bind_param("ssi", $_log_nome, $_log_site, $id);
   $stmt_log->execute(); $stmt_log->close();
   $sql3 = "-- replaced";

   }

   if ($filename<>"")
   {
      // Com ficheiro novo: actualizar também o BLOB logotipo
      $_log_nome = $_POST["nomeescola"] ?? '';
      $_log_site = $_POST["site"] ?? '';
      $stmt_log  = $db->prepare("UPDATE logotipo SET nomeescola=?, logotipo=?, site=? WHERE id=?");
      $null      = null;
      $stmt_log->bind_param("sbsi", $_log_nome, $null, $_log_site, $id);
      $stmt_log->send_long_data(1, $tmp); // envia o BLOB sem escaping
      $stmt_log->execute();
      $stmt_log->close();
   }

   // result3 handled by prepared stmt above



   $sql2a = "select min(id) from escolas";
   $result2a = mysqli_query($db,$sql2a);
   $rows2a =mysqli_fetch_row($result2a);
   $minid = $rows2a[0];

   
   $stmt_esc=$db->prepare("UPDATE escolas SET nome_escola=?,morada=?,codigopostal=?,localidade=?,telefone=? WHERE id=?");
   $_esc_nome     = $_POST["nomeescola"]  ?? '';
   $_esc_morada   = $_POST["morada"]      ?? '';
   $_esc_cp       = $_POST["codpostal"]   ?? '';
   $_esc_loc      = $_POST["localidade"]  ?? '';
   $_tel          = (int)($_POST["telefone"] ?? 0);
   $stmt_esc->bind_param("ssssii", $_esc_nome, $_esc_morada, $_esc_cp, $_esc_loc, $_tel, $minid);
   $stmt_esc->execute(); $stmt_esc->close();



// Instituições adicionais 2..11 — INSERT/UPDATE se preenchido, DELETE se vazio
for ($i = 2; $i <= 11; $i++) {
    $_nome_add = trim($_POST["nomeescola{$i}"] ?? '');
    if ($_nome_add !== '') {
        $_stmt_esc_add = $db->prepare(
            "INSERT INTO escolas (id, nome_escola) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE nome_escola = VALUES(nome_escola)"
        );
        $_stmt_esc_add->bind_param("is", $i, $_nome_add);
        $_stmt_esc_add->execute();
        $_stmt_esc_add->close();
    } else {
        // Campo vazio — apagar o registo se existir
        $_stmt_esc_del = $db->prepare("DELETE FROM escolas WHERE id = ?");
        $_stmt_esc_del->bind_param("i", $i);
        $_stmt_esc_del->execute();
        $_stmt_esc_del->close();
    }
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
    


      <?php include ("footer.php");?>


   </body>
</html>