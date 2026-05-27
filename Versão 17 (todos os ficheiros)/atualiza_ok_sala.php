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
                     <li style="color:#1e2a45;">Sala >> Atuaizar</li>
                  </ol>
               </nav>
               <div class="titlepage">
                   
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>

<?php
$said = (int)base64_decode($_GET['sai']);


$sql11 = "select e.id from escolas e, salas s
where s.id_escola=e.id and
s.id=".$said."";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);




if ( !isset($_POST['eqreq']) || !isset($_POST['nome']) || !isset($_POST['localizacao']) 
|| empty($_POST['eqreq'])  || empty($_POST['nome'])  || empty($_POST['localizacao']) 
|| empty($said) || !isset($said) 
|| !is_numeric($said) 
)

{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=1&&esi=<?php echo base64_encode($rows11[0])?>';
}, 10);
</script>


<?php
}
else
{
?>



<?php

/*
if ( $_SESSION['tipo']==1 && $_SERVER["REQUEST_METHOD"] == "POST" )
{
*/

    // Validação CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erro: token CSRF inválido.');
    }
$_nome  = $_POST["nome"]        ?? '';
$_loc   = $_POST["localizacao"] ?? '';
$_dep   = $_POST["departamento"]?? '';
$_eqreq = $_POST["eqreq"]       ?? '';
$stmt = $db->prepare("UPDATE salas SET nome=?, localizacao=?, departamento=?, equip_requisitavel=? WHERE id=?");
$stmt->bind_param("ssssi", $_nome, $_loc, $_dep, $_eqreq, $said);
$stmt->execute();
$stmt->close();

$result = mysqli_query($db,$sql);






//$sql2 = "update equipamento set id_sala='".$_POST["nome"]."' where sala='".$_GET["nom"]."'";
//$result2 = mysqli_query($db,$sql2);


//header("Refresh:0;url=salas.php");
mysqli_close($db);
?>


<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,    
//position: 'top-rigth',

})
.then(function() {
window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($rows11[0])?>";
})
;

</script>
<?php
}

      
   
        ?>





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