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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">CONFIGURAÇÕES</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">EQUIPAMENTO >> ATUALIZAR</li>
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

    // Validação CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erro: token CSRF inválido.');
    }

    $id  = (int)base64_decode($_GET['ide']);
    $sa  = (int)base64_decode($_GET['sai']);
    $ies = (int)base64_decode($_GET['ies']);

    // Obter escola_digital com prepared statement
    $stmtEd = $db->prepare("SELECT escola_digital FROM equipamento WHERE id=?");
    $stmtEd->bind_param("i", $id);
    $stmtEd->execute();
    $stmtEd->bind_result($ed);
    $stmtEd->fetch();
    $stmtEd->close();

    $dt       = $_POST["datacompra"] ?? '';
    $nomeq    = $_POST["nomeq"]      ?? '';
    $sala     = (int)($_POST["sala"] ?? 0);
    $tipoeq   = $_POST["tipoeq"]     ?? '';
    $marcamod = $_POST["marcamod"]   ?? '';
    $nserie   = $_POST["nserie"]     ?? '';
    $obs      = $_POST["obs"]        ?? '';

    // Atualizar data_compra
    if ($dt === '') {
        $stmtDt = $db->prepare("UPDATE equipamento SET data_compra=NULL WHERE id=?");
        $stmtDt->bind_param("i", $id);
    } else {
        $stmtDt = $db->prepare("UPDATE equipamento SET data_compra=? WHERE id=?");
        $stmtDt->bind_param("si", $dt, $id);
    }
    $stmtDt->execute();
    $stmtDt->close();

    // Atualizar restantes campos
    if ($ed === "Sim") {
        $numinv    = $_POST["numinv"]         ?? '';
        $fornec    = $_POST["fornecedor"]      ?? '';
        $emailforn = $_POST["emailfornecedor"] ?? '';
        $nifpessoa = $_POST["nifpessoa"]       ?? '';

        $stmt = $db->prepare("UPDATE equipamento
            SET nomeequi=?, id_sala=?, tipo=?, marca_modelo=?, numserie=?, observacoes=?,
                num_inv_dgest=?, fornecedor=?, email_fornecedor=?, nif_pessoa=?, escola_digital=?
            WHERE id=?");
        $stmt->bind_param("sisssssssssi",
            $nomeq, $sala, $tipoeq, $marcamod, $nserie, $obs,
            $numinv, $fornec, $emailforn, $nifpessoa, $ed, $id);
    } else {
        $stmt = $db->prepare("UPDATE equipamento
            SET nomeequi=?, id_sala=?, tipo=?, marca_modelo=?, numserie=?, observacoes=?, escola_digital=?
            WHERE id=?");
        $stmt->bind_param("sisssssi",
            $nomeq, $sala, $tipoeq, $marcamod, $nserie, $obs, $ed, $id);
    }
    $stmt->execute();
    $stmt->close();

    mysqli_close($db);

//$id=$_GET['id'];
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atualiza_dadostecredes.php?ide=<?php echo base64_encode($id);?>&&sa=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($ies);?>";
});



</script>

<br><br><br><br><br><br><br><br><br><br>
<?php

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