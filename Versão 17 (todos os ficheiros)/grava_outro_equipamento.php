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

// ── CSRF: verificar token antes de qualquer processamento POST ────
if (
    empty($_POST['token']) ||
    empty($_SESSION['token']) ||
    !hash_equals($_SESSION['token'], $_POST['token'])
) {
    http_response_code(403);
    die('Pedido inválido (token CSRF em falta ou inválido).');
}
// Invalidar token após uso (one-time use — previne replay attacks)
unset($_SESSION['token']);
// ─────────────────────────────────────────────────────────────────
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Outro equipamento</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
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
$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$idescola = (int)base64_decode($_GET["ies"]);

if (  !isset($_POST['sala']) || !isset($_POST['nomeq']) || !isset($_GET['ies'])  
   ||  empty($_POST['sala']) || empty($_POST['nomeq']) || empty($idescola)   
   || !is_numeric($idescola)  || base64_decode($_GET["ies"])>$maxesc
   || !isset($_POST['qta']) || empty($_POST['qta']) 
   )
{

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(0) ?>&ies=<?php echo base64_encode($idescola);?>';
}, 10);
</script>


<?php
} 

?>



<?php

$noe=$_POST["nomeq"];
$ids=$_POST["sala"];

   $sql2 = $db->prepare("select count(*) from outro_equipamento 
   where nomeoutro=? and id_sala=? ");
   
   $sql2->bind_param("si", $noe, $ids);
   $sql2->execute();
 
 
   $rows2 = $sql2->get_result()->fetch_row();


   $contaeq = $rows2[0];

 if ($contaeq==1) 
{
?>

   
<script>
    
swal({
title: 'O nome do equipamento já existe!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?>";
});

</script>

<?php

}


if ($contaeq==0) 
{


 
$stmt_oe=$db->prepare("INSERT INTO outro_equipamento (nomeoutro,id_sala,qta,observacoes) VALUES (?,?,?,?)");
$_oe_nome=$_POST["nomeq"]??'';$_oe_sala=(int)($_POST["sala"]??0);$_oe_qta=$_POST["qta"]??'';$_oe_obs=$_POST["obs"]??'';
$stmt_oe->bind_param("siss",$_oe_nome,$_oe_sala,$_oe_qta,$_oe_obs);
$stmt_oe->execute();$stmt_oe->close();


$result = mysqli_query($db,$sql);




?>

<script>
    
    swal({
title: 'Os dados foram guardados!',
//text: 'Os dados técnicos e de rede são opcionais. Caso deseje sair clicar na seta "Voltar", no final da página.',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?> ";
});



</script>

<br><br><br><br><br><br><br><br><br><br>

<?php

}


?>



<br><br><br><br><br><br>


                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>
