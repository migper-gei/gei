<?php
// SEGURANÇA: display_errors desativado em produção
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

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
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// SEGURANÇA: rejeitar imediatamente se não for POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SVRURL . 'reset_pass.php');
    exit;
}

// SEGURANÇA: validar token CSRF
if (
    empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header('Location: ' . SVRURL . 'reset_pass.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include("head.php"); ?>
</head>

<body class="main-layout">
  <!-- loader
  <?php include("loader.php"); ?>-->

  <?php include("header.php"); ?>

  <div class="about">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="titlepage">
            <h2>Mudar password</h2>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row">
          <div class="wrapper fadeInDown">
            <div id="formContent">

<?php

$nobd     = $_SESSION['nobd']     ?? '';
$serverbd = $_SESSION['serverbd'] ?? '';

// SEGURANÇA: sessão deve ter nobd e serverbd (utilizador autenticado)
if (empty($nobd) || empty($serverbd)) {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>reset_pass.php';
    }, 10);
    </script>
    <?php
    include("footer.php");
    exit;
}

// SEGURANÇA: email vem da sessão — não do POST — para impedir alteração de password de outro utilizador
$em = $_SESSION['email'] ?? '';
if (empty($em)) {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>reset_pass.php';
    }, 10);
    </script>
    <?php
    include("footer.php");
    exit;
}

// Se os campos obrigatórios não vierem no POST, redireciona de imediato
if (
    !isset($_POST['passworda'], $_POST['password'], $_POST['confirmapassword']) ||
    empty($_POST['passworda']) || empty($_POST['password']) || empty($_POST['confirmapassword'])
) {
    ?>
    <script>
    window.setTimeout(function() {
        window.location.href = '<?php echo SVRURL ?>reset_pass.php';
    }, 10);
    </script>
    <?php
} else {

    $pwdant = $_POST['passworda'];
    $pwd    = $_POST['password'];
    $pwdc   = $_POST['confirmapassword'];
    // $em já definido a partir da sessão acima

    $erroMsg  = "";   // mensagem de erro (vazia = sem erro)
    $sucesso  = false;

    $db = new mysqli($serverbd, DB_USERNAME, DB_PASSWORD, $nobd);

    if ($db->connect_errno) {
        error_log("reset_pass_OK: erro ligação BD: " . $db->connect_error);
        $erroMsg = "Erro interno. Tente mais tarde.";
    } else {

    // Verificar se o email existe
    $sql0 = $db->prepare("SELECT COUNT(*) FROM utilizadores WHERE email=?");
    $sql0->bind_param("s", $em);
    $sql0->execute();
    $count = (int) $sql0->get_result()->fetch_row()[0];
    $sql0->close();

    // Buscar hash da password atual
    $sql2a = $db->prepare("SELECT pass FROM utilizadores WHERE email=?");
    $sql2a->bind_param("s", $em);
    $sql2a->execute();
    $rows2a    = $sql2a->get_result()->fetch_row();
    $passAtual = $rows2a[0] ?? null;
    $sql2a->close();

    // Verificar password antiga: Argon2ID moderno ou AES legado
    if ($passAtual && password_get_info($passAtual)['algo'] !== 0) {
        $passCorreta = password_verify($pwdant, $passAtual);
    } else {
        // SEGURANÇA: chave AES vem de constante de configuração, não hardcoded
        // Definir AES_SECRET em config_serverbd.php ou config_serverbd_settings.php
        $aesKey  = defined('AES_SECRET') ? AES_SECRET : '';
        $sqlAes  = $db->prepare("SELECT AES_DECRYPT(pass, ?) FROM utilizadores WHERE email=?");
        $sqlAes->bind_param("ss", $aesKey, $em);
        $sqlAes->execute();
        $rowAes      = $sqlAes->get_result()->fetch_row();
        $sqlAes->close();
        $passCorreta = ($rowAes[0] === $pwdant);
    }

    if ($count == 0 || !$passCorreta) {
        $erroMsg = "O Email não está registado ou password antiga errada!";

    } else {
        // Validações da nova password
        if (strlen($pwd) < 12) {
            $erroMsg = "A password deve ter no mínimo 12 carateres!";
        } elseif (!preg_match("#[0-9]+#", $pwd)) {
            $erroMsg = "A password deve ter pelo menos um número!";
        } elseif (!preg_match("#[a-z]+#", $pwd)) {
            $erroMsg = "A password deve ter pelo menos uma letra!";
        } elseif (!preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $pwd)) {
            $erroMsg = "A password deve ter pelo menos um símbolo!";
        } elseif ($pwd !== $pwdc) {
            $erroMsg = "As passwords não são iguais!";
        } else {
            // ── Have I Been Pwned – verificação server-side (k-anonymity) ──
            $sha1pwd    = strtoupper(sha1($pwd));
            $hibpPrefix = substr($sha1pwd, 0, 5);
            $hibpSuffix = substr($sha1pwd, 5);
            $hibpCtx = stream_context_create(['http' => [
                'method'  => 'GET',
                'header'  => "User-Agent: GEI-PasswordReset/1.0\r\n",
                'timeout' => 5,
            ]]);
            $hibpResp = @file_get_contents(
                'https://api.pwnedpasswords.com/range/' . $hibpPrefix,
                false, $hibpCtx
            );
            $pwnado = false;
            if ($hibpResp !== false) {
                foreach (explode("\n", $hibpResp) as $line) {
                    if (strpos(trim($line), ':') !== false) {
                        [$hs,] = explode(':', trim($line), 2);
                        if (strtoupper($hs) === $hibpSuffix) {
                            $pwnado = true;
                            break;
                        }
                    }
                }
            }

            if ($pwnado) {
                $erroMsg = "Esta password foi exposta em fugas de dados conhecidas (Have I Been Pwned). Por favor escolha uma password diferente.";
            } else {
                // Tudo válido — gravar nova password
                $stmt_tmp = $db->prepare("SELECT tempoduracaopass FROM settings LIMIT 1");
                $stmt_tmp->execute();
                $rowTmp = $stmt_tmp->get_result()->fetch_row();
                $stmt_tmp->close();

                // SEGURANÇA: guard contra tempoduracaopass NULL ou zero
                $diasValidade = (!empty($rowTmp[0]) && is_numeric($rowTmp[0])) ? (int)$rowTmp[0] : 365;

                $dataalteradapass = new DateTime('now');
                $dataalteradapass->modify('+' . $diasValidade . ' day');
                $d = $dataalteradapass->format('Y-m-d');

                $novaHash = password_hash($pwd, PASSWORD_ARGON2ID);
                $sql1 = $db->prepare("UPDATE utilizadores SET pass=?, dataalteracaopass=STR_TO_DATE(?,'%Y-%m-%d') WHERE email=?");
                $sql1->bind_param("sss", $novaHash, $d, $em);
                $sql1->execute();
                $sql1->close();

                $sucesso = true;
            }
        }
    }

    } // fim if connect_errno

    mysqli_close($db);

    // ── Emitir UM único swal no final ──
    if ($sucesso) {
        ?>
        <script>
        swal({
            title: 'Os dados foram guardados!',
            icon: 'success',
        })
        .then(function() {
            window.location.href = '<?php echo SVRURL ?>acessorap';
        });
        </script>
        <?php
    } else {
        $erroMsgJs = htmlspecialchars($erroMsg, ENT_QUOTES, 'UTF-8');
        ?>
        <script>
        swal({
            title: 'ERRO',
            text: '<?php echo $erroMsgJs; ?>',
            icon: 'error',
        })
        .then(function() {
            window.location.href = '<?php echo SVRURL ?>reset_pass.php';
        });
        </script>
        <?php
    }
}
?>

<br><br><br><br><br><br><br><br><br>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end about -->

  <?php include("footer.php"); ?>

</body>
</html>
