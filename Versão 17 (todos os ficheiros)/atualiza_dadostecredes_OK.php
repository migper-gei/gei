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
      <!-- loader 
      <?php include("loader.php"); ?> -->
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php



include("sessao_timeout.php");

 
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
                     <li style="color:#1e2a45;">Equipamento > Atualizar</li>
                  </ol>
               </nav>
               <div class="titlepage">
              
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                    
<br>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        
$id = (int)base64_decode($_GET["id"]);
$parte = $_GET["parte"] ?? 'tec';

if ( $_SESSION['tipo']==1)
{

if ($parte === 'tec') {
    $_cpu=$_POST["cpu"]??'';$_ram=$_POST["ram"]??'';$_disco=$_POST["disco"]??'';
    $_gfx=$_POST["grafica"]??'';$_rede=$_POST["rede"]??'';$_som=$_POST["som"]??'';
    $_mon=$_POST["monitor"]??'';$_tec=$_POST["teclado"]??'';$_rato=$_POST["rato"]??'';
    $_col=$_POST["colunas"]??'';$_cddvd=$_POST["cddvd"]??'';$_ri=$_POST["ratointerface"]??'';$_ti=$_POST["tecladointerface"]??'';
    $stmt_tec=$db->prepare("UPDATE equipamento SET processador=?,memoria=?,disco=?,placagrafica=?,placarede=?,placasom=?,monitor=?,teclado=?,rato=?,colunas=?,cd_dvd=?,ratointerface=?,tecladointerface=? WHERE id=?");
    $stmt_tec->bind_param("sssssssssssssi",$_cpu,$_ram,$_disco,$_gfx,$_rede,$_som,$_mon,$_tec,$_rato,$_col,$_cddvd,$_ri,$_ti,$id);
    $stmt_tec->execute();$stmt_tec->close();
} else {
    $stmt_rede = $db->prepare("UPDATE equipamento SET dominio=?,ip=?,mascara_rede=?,gateway=?,dns_principal=?,dns_alternativo=? WHERE id=?");
    $_dom=$_POST["dominio"]??'';$_ip=$_POST["ip"]??'';$_mas=$_POST["mascara"]??'';
    $_gw=$_POST["gateway"]??'';$_dnsp=$_POST["dnsp"]??'';$_dnsa=$_POST["dnsa"]??'';
    $stmt_rede->bind_param("ssssssi",$_dom,$_ip,$_mas,$_gw,$_dnsp,$_dnsa,$id);
    $stmt_rede->execute();$stmt_rede->close();
}

mysqli_close($db);
?>

<script>
    swal({
title: 'Os dados foram atualizados!',
icon: 'success',
})
.then(function() {
window.location = "<?php echo SVRURL ?>equip";
});
</script>

<br><br><br><br><br><br><br><br><br><br>

<?php
}
else
{
?>
    <script>
window.setTimeout(function() {
    window.location.href = 'equip';
}, 10);
</script>

<?php
}
}

?>
<br><br>


                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>