<?php
// ================================================================
// SEGURANÇA: Iniciar sessão segura (padrão validauser.php)
// ================================================================
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
      <script>
         function myFunction() {
           var x = document.getElementById("mypass");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         }
      </script>

      <?php include ("head.php"); ?>
   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

      <?php include ("header2.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2>Recuperar password</h2>
                  </div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="wrapper fadeInDown">
                     <div id="formContent">

<?php

// ================================================================
// VALIDAÇÃO INICIAL: campos POST obrigatórios
// ================================================================
if (
    !isset($_POST['email'])  || empty(trim($_POST['email']))  ||
    !isset($_POST['codigo']) || empty(trim($_POST['codigo']))
) {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>';
    }, 10);
    </script>
    <?php
    // Incluir footer e terminar de forma limpa
    include ("footer.php");
    exit;
}

// ================================================================
// PROCESSAR — apenas POST
// ================================================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>l';
    }, 10);
    </script>
    <?php
    include ("footer.php");
    exit;
}

// ------------------------------------------------------------------
// 1. Sanitizar e validar inputs
// ------------------------------------------------------------------
$rawCodigo = trim($_POST['codigo']);
$emaila    = trim($_POST['email']);

// Código: deve ser numérico, entre 1 e 9 dígitos (padrão validauser.php)
if (!ctype_digit($rawCodigo) || strlen($rawCodigo) < 1 || strlen($rawCodigo) > 9) {
    ?>
    <script>
    swal({ title: 'ERRO', text: 'Código inválido.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>"; });
    </script>
    <?php
    include ("footer.php");
    exit;
}

// Email: validação server-side
if (!filter_var($emaila, FILTER_VALIDATE_EMAIL)) {
    ?>
    <script>
    swal({ title: 'ERRO', text: 'Email inválido.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>"; });
    </script>
    <?php
    include ("footer.php");
    exit;
}

$codigo = $rawCodigo;

// ------------------------------------------------------------------
// 2. Ligar à BD principal
// ------------------------------------------------------------------
include ("config_serverbd_settings.php");
$db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($db0->connect_errno) {
    error_log("Erro ligação BD principal: " . $db0->connect_error);
    ?>
    <script>
    swal({ title: 'ERRO', text: 'Erro interno. Tente mais tarde.', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>l"; });
    </script>
    <?php
    include ("footer.php");
    exit;
}

// ------------------------------------------------------------------
// 3. Verificar se o código existe — prepared statement
//    CORRIGIDO: era "WHERE codigo = '$codigo'" — SQL injection
// ------------------------------------------------------------------
$stmtCount = $db0->prepare("SELECT COUNT(*) AS ccod FROM settingsbd WHERE codigo = ?");
$stmtCount->bind_param("s", $codigo);
$stmtCount->execute();
$ccod = (int) $stmtCount->get_result()->fetch_row()[0];
$stmtCount->close();

if ($ccod === 0) {
    mysqli_close($db0);
    ?>
    <script>
    swal({ title: 'ERRO', text: 'Código incorreto!', icon: 'error' })
    .then(function() { window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>"; });
    </script>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <?php
    include ("footer.php");
    exit;
}

if ($ccod === 1) {

    // ------------------------------------------------------------------
    // 4. Obter nomebd e serverbd — prepared statement
    //    CORRIGIDO: era "WHERE codigo = '$codigo'" — SQL injection
    // ------------------------------------------------------------------
    $stmtBd = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
    $stmtBd->bind_param("s", $codigo);
    $stmtBd->execute();
    $row0 = $stmtBd->get_result()->fetch_assoc();
    $stmtBd->close();

    $nomebd   = $row0['nomebd'];
    $serverbd = $row0['serverbd'];
    mysqli_close($db0);

    $_SESSION['nobd']     = $nomebd;
    $_SESSION['serverbd'] = $serverbd;

    // ------------------------------------------------------------------
    // 5. Ligar à BD do utilizador
    // ------------------------------------------------------------------
    include ("config_serverbd.php");
    $db = new mysqli($serverbd, DB_USERNAME, DB_PASSWORD, $nomebd);

    if ($db->connect_errno) {
        error_log("Erro ligação BD utilizador: " . $db->connect_error);
        ?>
        <script>
        swal({ title: 'ERRO', text: 'Erro interno. Tente mais tarde.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php
        include ("footer.php");
        exit;
    }

    mysqli_select_db($db, $nomebd);

    // ------------------------------------------------------------------
    // 6. Verificar se o email existe — prepared statement
    //    (já era correcto no original; mantido e reforçado com LIMIT 1)
    // ------------------------------------------------------------------
    $stmtEmail = $db->prepare("SELECT email FROM utilizadores WHERE email = ? LIMIT 1");
    $stmtEmail->bind_param("s", $emaila);
    $stmtEmail->execute();
    $stmtEmail->store_result();
    $count = $stmtEmail->num_rows;
    $stmtEmail->close();
    mysqli_close($db);

    if ($count === 0) {
        echo "<br><br><br>";
        ?>
        <script>
        swal({ title: 'ERRO', text: 'Verifique os dados (Email e código)!', icon: 'error' })
        .then(function() {
            window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>";
        });
        </script>
        <?php
        include ("footer.php");
        exit;
    }

    if ($count === 1) {
        // Email encontrado — redirecionar para envio
        ?>
        <script>
        window.setTimeout(function() {
            window.location.href = '<?php echo SVRURL ?>enviar_email_pass.php?em=<?php echo base64_encode($emaila) ?>';
        }, 10);
        </script>
        <?php
        include ("footer.php");
        exit;
    }

} else {
    // ccod > 1 — situação inesperada (códigos duplicados na BD)
    mysqli_close($db0);
    echo "<br><br><br>";
    ?>
    <script>
    swal({ title: 'ERRO', text: 'Email incorreto!', icon: 'error' })
    .then(function() {
        window.location = "<?php echo SVRURL ?>recuperapass/<?php echo base64_encode(0) ?>";
    });
    </script>
    <?php
}

?>

                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <br><br><br><br><br><br><br><br><br><br><br><br><br>
      <?php include ("footer.php"); ?>

   </body>
</html>
