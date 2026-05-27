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

// ── Proteção CSRF ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Pedido inválido: token CSRF em falta ou incorreto.');
    }
}
// ──────────────────────────────────────────────────────────────────────────────
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

$sa = (int)base64_decode($_GET["si"]);
$idescola = (int)base64_decode($_GET["esm"]);

if ( !isset($_POST['pessoa']) || !isset($_POST['data']) || !isset($_POST['eq']) || !isset($_POST['m']) 
|| empty($_POST['pessoa']) || empty($_POST['data']) || empty($_POST['eq']) || empty($_POST['m']) 
|| !isset($sa)  || !isset($idescola)   
|| empty($sa)  || empty($idescola)  
)
{

//inserirmanut?esm=<?php echo base64_encode($idescola);?>
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>

<?php
}

?>

     <?php
//session_start();

include("sessao_timeout.php");

$sql2 = "select nome from salas where id=".$sa." ";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$ns=$rows[0];

$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);
$ne = $rows11[0];
 
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">Manutenções</span>
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
                  <div class="col-md-10 offset-md-2">
              
     
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>
                   



               <!-- ========================================================
                    CABEÇALHO: sala + escola na mesma linha, por baixo do utilizador
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.1rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                     <?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                  <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
<?php
/*
if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$id =$url[0];

$sa=$_GET["sa"];

$sql2 = "select nome from salas where id=".$id." ";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$sa=$rows[0];

*/

/*
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>

<?php
}
 */

    if(!empty($_POST['eq'])) {
        foreach($_POST['eq'] as $value){

           $ne=$value;

          if(!empty($_POST['m'])) {
          
            foreach($_POST['m'] as $value){
    
               $m=$value;

            
            $_mn_data = $_POST["data"]    ?? '';
            $_mn_pess = $_POST["pessoa"]  ?? '';
            $_mn_obs  = $_POST["obs"]     ?? '';
            $stmt_mn = $db->prepare("INSERT INTO manutencao (id_equi, data_manutencao, pessoa, descricao, observacoes) VALUES (?, STR_TO_DATE(?,'%Y-%m-%d'), ?, ?, ?)");
            $stmt_mn->bind_param("issss", $ne, $_mn_data, $_mn_pess, $m, $_mn_obs);
            $stmt_mn->execute();
            $stmt_mn->close();
         
            }
    
        }

        }

    }

   

//header("Refresh:0;url=manutencao.php");
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
window.location = "<?php echo SVRURL ?>manut";
});

</script>

<br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

   </body>
</html>