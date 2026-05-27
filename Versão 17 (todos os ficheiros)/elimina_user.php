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
               <div class="titlepage">
                     <h2>Eliminar utilizador</h2>
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

// ── 1. Obter ID da URL ────────────────────────────────────────────────────────
if (isset($_GET['url'])) {
    $url = explode('/', $_GET['url']);
} else {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = 'i';
    }, 10); 
    </script>
    <?php
    exit;
}

$id = (int)$url[0];

// ── 2. Validação CSRF (token por ID, guardado em array na sessão) ─────────────
$token_recebido  = $_GET['token'] ?? '';
$token_esperado  = $_SESSION['csrf_delete_tokens'][$id] ?? '';

if (
    empty($token_recebido) ||
    empty($token_esperado) ||
    !hash_equals($token_esperado, $token_recebido)
) {
    ?>
    <script>
    swal({
        title: 'Erro de segurança!',
        text: 'Token inválido ou expirado. Por favor recarregue a página.',
        icon: 'error',
    })
    .then(function() {
        window.location = "<?php echo SVRURL ?>utiliz";
    });
    </script>
    <?php
    exit;
}

// Token de uso único — invalidar apenas este ID
unset($_SESSION['csrf_delete_tokens'][$id]);
// ─────────────────────────────────────────────────────────────────────────────


// ── 3. Controlo de acesso ─────────────────────────────────────────────────────
if ($_SESSION['tipo'] == 1)
{

require_once('gei_audit.php');

// ── 4. Capturar snapshot ANTES de apagar ─────────────────────────────────────
$stmt_snap = $db->prepare("
    SELECT id, nome, email, tipo
    FROM utilizadores
    WHERE id = ?
    LIMIT 1
");
$stmt_snap->bind_param('i', $id);
$stmt_snap->execute();
$snap = $stmt_snap->get_result()->fetch_assoc();
$stmt_snap->close();

// Formatar detalhe
$detalhe = '(registo não encontrado)';
if ($snap) {
    $partes = [
        'id='    . $snap['id'],
        'nome='  . ($snap['nome']  ?? ''),
        'email=' . ($snap['email'] ?? ''),
        'tipo='  . ($snap['tipo']  ?? ''),
    ];
    $detalhe = implode(' | ', array_filter($partes, fn($p) => !str_ends_with($p, '=')));
}

// ── 5. Apagar ────────────────────────────────────────────────────────────────
$sql = $db->prepare("DELETE FROM utilizadores WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

// ── 6. Auditar com snapshot ───────────────────────────────────────────────────
gei_audit($db, 'eliminar', 'utilizador', $id, $detalhe);
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>utiliz';
}, 10);
</script>

<?php
}
else
{
?>
    <script>
    swal({
        title: 'Não pode eliminar!',
        text: 'Não tem permissão.',
        icon: 'error',
    })
    .then(function() {
        window.location = "<?php echo SVRURL ?>utiliz";
    });
    </script>
<?php
}
?>

          <?php
          mysqli_close($db);
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
