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

// Regenerar ID de sessão imediatamente após login bem-sucedido (previne session fixation)
if (!empty($_SESSION['_regen_after_login'])) {
    unset($_SESSION['_regen_after_login']);
    session_regenerate_id(true);
    $_SESSION['_created'] = time();
}
?>

<!DOCTYPE html>
<html lang="pt">
   <head>




<?php 

include ("head.php");?>


   </head>







   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     

      

           <div  class="about">
         <div class="container">
      
                   <!--
         <h3 class="quick-access-title">
         Links de acesso rápido </h3>-->
         <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Links de acesso rápido</li>
                  </ol>
               </nav>

               <div class="row">

            


               <div class="col-md-10 offset-md-2">
      
          

<?php



include("sessao_timeout.php");




 //include("msg_bemvindo.php");
?>

     
    <!-- Welcome Section -->
    <div class="welcome-section">
               
                        <div>
                      
                            <?php include("msg_bemvindo.php"); ?>
                        </div>
               
                </div>
  
         
                <div class="action-section">
  <?php
     
     
     include("texto_gei.php");
               
   ?>  
   </div>


<?php
$hoje = date('d/m/Y');

$da=date("Y");


$sql3 = "
SELECT count(*) FROM periodos WHERE 
YEAR(data_fim) = $da; ";

$result3 = mysqli_query($db,$sql3);
$rows3 =mysqli_fetch_row($result3);
$contap = $rows3[0];
$datai='01-01-'."$da";
$dataf='31-12-'."$da";

//$datai=;

if ($contap==0)
{
   $sql = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
   values ('".$da."',1,STR_TO_DATE('".$datai."','%d-%m-%Y'),STR_TO_DATE('".$dataf."','%d-%m-%Y'))";
   
   $result = mysqli_query($db,$sql);


}

?>



  



             
            </div>
         </div>
      </div>
      <!-- end about -->
    

      <?php include ("footer.php");?>

      <!-- ═══ TEMA ESCURO — JS ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ════════════════════════ -->
      <!-- fix stopPropagation -->
      <script>
      document.addEventListener('DOMContentLoaded', function() {
          document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
              btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
          });
      });
      </script>
</body>
</html>
