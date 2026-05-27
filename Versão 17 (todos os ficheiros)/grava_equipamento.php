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
                        <span style="color:#4b6cb7;">Equipamento</span>
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
$escdig = base64_decode($_GET["ed"]); // string: "Sim" ou "Não"

if ( !isset($_POST['tipoeq']) || !isset($_POST['sala']) || !isset($_POST['nomeq']) || !isset($_GET['ies'])  
   || empty($_POST['tipoeq']) || empty($_POST['sala']) || empty($_POST['nomeq']) || empty($idescola)   
   || !is_numeric($idescola)  || base64_decode($_GET["ies"])>$maxesc
   || !isset($escdig)
   )
{

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(0) ?>&ies=<?php echo base64_encode($idescola);?>';
}, 10);
</script>


<?php
} 

?>



<?php

// --- Validação CSRF ---
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    ?>
    <script>
    swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(0); ?>&&ies=<?php echo base64_encode($idescola); ?>"; });
    </script>
    <?php
    exit;
}
// Invalidar token após uso (one-time use)
unset($_SESSION['csrf_token']);

$noe=$_POST["nomeq"];
$ids=$_POST["sala"];

   $sql2 = $db->prepare("select count(*) from equipamento 
   where nomeequi=? and id_sala=? ");
   
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
window.location = "<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?>";
});

</script>

<?php

}


if ($contaeq==0) 
{

 $dt=$_POST["datacompra"];

// Recolher campos comuns
$_eq_nome  = $_POST["nomeq"]       ?? '';
$_eq_sala  = (int)($_POST["sala"]  ?? 0);
$_eq_tipo  = $_POST["tipoeq"]      ?? '';
$_eq_marca = $_POST["marcamod"]    ?? '';
$_eq_serie = $_POST["nserie"]      ?? '';
$_eq_obs   = $_POST["obs"]         ?? '';
// $escdig já vem validado do GET (descodificado) — usar directamente como string
$_eq_escdig = ($escdig === 'Sim') ? 'Sim' : 'Não';

// Campos exclusivos de Escola Digital (vazios se não aplicável)
$_eq_inv   = ($_eq_escdig === 'Sim') ? ($_POST["numinv"]          ?? '') : '';
$_eq_forn  = ($_eq_escdig === 'Sim') ? ($_POST["fornecedor"]       ?? '') : '';
$_eq_email = ($_eq_escdig === 'Sim') ? ($_POST["emailfornecedor"]  ?? '') : '';
$_eq_nif   = ($_eq_escdig === 'Sim') ? ($_POST["nifpessoa"]        ?? '') : '';

// Data de compra: NULL se vazia
$_eq_data = (!empty($dt)) ? $dt : null;

if ($_eq_data !== null) {
    $stmt_eq = $db->prepare(
        "INSERT INTO equipamento
            (nomeequi, id_sala, tipo, marca_modelo, numserie, data_compra,
             observacoes, escola_digital, num_inv_dgest, fornecedor, email_fornecedor, nif_pessoa)
         VALUES (?, ?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, ?, ?, ?, ?, ?)"
    );
    $stmt_eq->bind_param(
        "sissssssssss",
        $_eq_nome, $_eq_sala, $_eq_tipo, $_eq_marca, $_eq_serie, $_eq_data,
        $_eq_obs, $_eq_escdig, $_eq_inv, $_eq_forn, $_eq_email, $_eq_nif
    );
} else {
    $stmt_eq = $db->prepare(
        "INSERT INTO equipamento
            (nomeequi, id_sala, tipo, marca_modelo, numserie, data_compra,
             observacoes, escola_digital, num_inv_dgest, fornecedor, email_fornecedor, nif_pessoa)
         VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?)"
    );
    $stmt_eq->bind_param(
        "sisssssssss",
        $_eq_nome, $_eq_sala, $_eq_tipo, $_eq_marca, $_eq_serie,
        $_eq_obs, $_eq_escdig, $_eq_inv, $_eq_forn, $_eq_email, $_eq_nif
    );
}
$stmt_eq->execute();
// insert_id capturado imediatamente após o INSERT, antes de qualquer close
$maxideq = $db->insert_id;
$stmt_eq->close();

mysqli_close($db);
?>

<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Os dados técnicos e de rede são opcionais. Caso deseje sair clicar na seta "Voltar", no final da página.',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>dados_tec_redes.php?z=1&&qi=<?php echo base64_encode($maxideq);?>&&ies=<?php echo base64_encode ($idescola) ?>";
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