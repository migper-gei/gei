<?php
require_once("config_serverbd_settings.php");

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Sessão segura — deve usar as mesmas definições de enviar_email_pass.php
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

// ── Validar token do URL (?t=...) contra a BD de settings ──
$token_raw = $_GET['t'] ?? '';

if (empty($token_raw)) {
    header("Location: " . (defined("SVRURL") ? SVRURL : "/gei/") . "recuperapass/" . base64_encode(0));
    exit;
}

$token_hash = hash('sha256', $token_raw);

$dbTok = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($dbTok->connect_errno) {
    header("Location: " . (defined("SVRURL") ? SVRURL : "/gei/") . "recuperapass/" . base64_encode(0));
    exit;
}

$stmtTok = $dbTok->prepare(
    "SELECT email FROM password_reset_tokens
     WHERE token_hash = ? AND expires_at > UTC_TIMESTAMP() AND used = 0
     LIMIT 1"
);
$stmtTok->bind_param("s", $token_hash);
$stmtTok->execute();
$rowTok = $stmtTok->get_result()->fetch_assoc();
$stmtTok->close();
$dbTok->close();

if (!$rowTok) {
    // Token inválido, expirado ou já utilizado
    session_unset();
    session_destroy();
    header("Location: " . (defined("SVRURL") ? SVRURL : "/gei/") . "login.php?link_expirado=1");
    exit;
}

// Token válido — guardar email e nome na sessão
$em_token = $rowTok['email'];

// Buscar nome do utilizador na BD da escola
$_SESSION['reset_email'] = $em_token;

// Obter nome na BD da escola (lookup via settingsbd)
$nobd_tmp     = $_SESSION['nobd']     ?? '';
$serverbd_tmp = $_SESSION['serverbd'] ?? '';

if (empty($nobd_tmp) || empty($serverbd_tmp)) {
    $dbS2 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if (!$dbS2->connect_errno) {
        $httpHost2 = $_SERVER['HTTP_HOST'] ?? '';
        $stmtS2 = $dbS2->prepare("SELECT serverbd, nobd FROM settingsbd WHERE url LIKE ? LIMIT 1");
        $likeH2 = '%' . $httpHost2 . '%';
        $stmtS2->bind_param("s", $likeH2);
        $stmtS2->execute();
        $rowS2 = $stmtS2->get_result()->fetch_assoc();
        $stmtS2->close();
        $dbS2->close();
        $nobd_tmp     = $rowS2['nobd']     ?? '';
        $serverbd_tmp = $rowS2['serverbd'] ?? '';
    }
}

if (!empty($nobd_tmp) && !empty($serverbd_tmp)) {
    $dbNome = new mysqli($serverbd_tmp, DB_USERNAME, DB_PASSWORD, $nobd_tmp);
    if (!$dbNome->connect_errno) {
        $stmtNome = $dbNome->prepare("SELECT nome FROM utilizadores WHERE email = ? LIMIT 1");
        $stmtNome->bind_param("s", $em_token);
        $stmtNome->execute();
        $rowNome = $stmtNome->get_result()->fetch_assoc();
        $stmtNome->close();
        $dbNome->close();
        $_SESSION['reset_nome'] = $rowNome['nome'] ?? '';
    }
}

// Verifica se a sessão tem os dados do utilizador
if (empty($_SESSION['reset_nome']) || empty($_SESSION['reset_email'])) {
    header("Location: " . (defined("SVRURL") ? SVRURL : "/gei/") . "recuperapass/" . base64_encode(0));
    exit;
}

$nome_utilizador = $_SESSION['reset_nome'];
$em              = $_SESSION['reset_email'];

// ──────────────────────────────────────────────────────────────
// Processar POST: gravar nova password
// ──────────────────────────────────────────────────────────────
$erroMsg = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar CSRF
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        header('Location: ' . (defined('SVRURL') ? SVRURL : '/gei/') . 'reset_pass_token.php?t=' . urlencode($token_raw));
        exit;
    }

    // Re-validar token no POST: garante que não foi usado entretanto (evita dupla submissão)
    $dbRecheck = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if ($dbRecheck->connect_errno) {
        $erroMsg = "Erro interno. Tente mais tarde.";
    } else {
        $stmtRecheck = $dbRecheck->prepare(
            "SELECT email FROM password_reset_tokens
             WHERE token_hash = ? AND expires_at > UTC_TIMESTAMP() AND used = 0
             LIMIT 1"
        );
        $stmtRecheck->bind_param("s", $token_hash);
        $stmtRecheck->execute();
        $rowRecheck = $stmtRecheck->get_result()->fetch_assoc();
        $stmtRecheck->close();
        $dbRecheck->close();

        if (!$rowRecheck) {
            // Token já foi usado ou expirou — destruir sessão e redirecionar
            session_unset();
            session_destroy();
            header("Location: " . (defined("SVRURL") ? SVRURL : "/gei/") . "login.php?link_expirado=1");
            exit;
        }
    }

    $pwd  = $_POST['password']        ?? '';
    $pwdc = $_POST['confirmapassword'] ?? '';

    if (empty($pwd) || empty($pwdc)) {
        $erroMsg = "Preencha todos os campos.";
    } elseif ($pwd !== $pwdc) {
        $erroMsg = "As passwords não são iguais!";
    } elseif (strlen($pwd) < 12) {
        $erroMsg = "A password deve ter no mínimo 12 carateres!";
    } elseif (!preg_match("#[0-9]+#", $pwd)) {
        $erroMsg = "A password deve ter pelo menos um número!";
    } elseif (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $erroMsg = "A password deve ter pelo menos uma letra!";
    } elseif (!preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!\-]/", $pwd)) {
        $erroMsg = "A password deve ter pelo menos um símbolo!";
    } else {

        // ── Have I Been Pwned – k-anonymity ──
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

            // Obter nobd e serverbd: primeiro tenta da sessão (utilizador já logado),
            // caso contrário vai buscar à BD de settings pelo domínio (reset por token).
            $nobd     = $_SESSION['nobd']     ?? '';
            $serverbd = $_SESSION['serverbd'] ?? '';

            if (empty($nobd) || empty($serverbd)) {
                // Lookup na settingsbd pelo domínio atual
                $dbSettings = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
                if (!$dbSettings->connect_errno) {
                    $httpHost   = $_SERVER['HTTP_HOST'] ?? '';
                    $stmtSrv = $dbSettings->prepare(
                        "SELECT serverbd, nobd FROM settingsbd WHERE url LIKE ? LIMIT 1"
                    );
                    $likeHost = '%' . $httpHost . '%';
                    $stmtSrv->bind_param("s", $likeHost);
                    $stmtSrv->execute();
                    $rowSrv = $stmtSrv->get_result()->fetch_assoc();
                    $stmtSrv->close();
                    $dbSettings->close();
                    $nobd     = $rowSrv['nobd']     ?? '';
                    $serverbd = $rowSrv['serverbd'] ?? '';
                }
            }

            if (empty($nobd) || empty($serverbd)) {
                $erroMsg = "Erro de sessão. Tente novamente.";
            } else {
                $db = new mysqli($serverbd, DB_USERNAME, DB_PASSWORD, $nobd);

                if ($db->connect_errno) {
                    error_log("reset_pass_token: erro ligação BD: " . $db->connect_error);
                    $erroMsg = "Erro interno. Tente mais tarde.";
                } else {
                    // Obter validade da password a partir das settings
                    $stmt_tmp = $db->prepare("SELECT tempoduracaopass FROM settings LIMIT 1");
                    $stmt_tmp->execute();
                    $rowTmp = $stmt_tmp->get_result()->fetch_row();
                    $stmt_tmp->close();

                    $diasValidade = (!empty($rowTmp[0]) && is_numeric($rowTmp[0])) ? (int)$rowTmp[0] : 365;

                    $dataalteradapass = new DateTime('now');
                    $dataalteradapass->modify('+' . $diasValidade . ' day');
                    $d = $dataalteradapass->format('Y-m-d');

                    $novaHash = password_hash($pwd, PASSWORD_ARGON2ID);

                    $sql1 = $db->prepare("UPDATE utilizadores SET pass=?, dataalteracaopass=STR_TO_DATE(?,'%Y-%m-%d') WHERE email=?");
                    $sql1->bind_param("sss", $novaHash, $d, $em);
                    $sql1->execute();
                    $sql1->close();

                    mysqli_close($db);

                    // Marcar token como utilizado (one-time use)
                    $dbUsed = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
                    if (!$dbUsed->connect_errno) {
                        $stmtUsed = $dbUsed->prepare(
                            "UPDATE password_reset_tokens SET used = 1 WHERE token_hash = ?"
                        );
                        $stmtUsed->bind_param("s", $token_hash);
                        $stmtUsed->execute();
                        $stmtUsed->close();
                        $dbUsed->close();
                    }

                    // Limpar dados de reset da sessão após sucesso
                    unset(
                        $_SESSION['reset_nome'],
                        $_SESSION['reset_email'],
                        $_SESSION['csrf_token']
                    );

                    $sucesso = true;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include("head.php"); ?>
    <style>
        .hint-item { color: #777; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .hint-item.valid { color: #28a745 !important; font-weight: 600; }
        .hint-item i { font-size: 11px; }
        .valid i { color: #28a745; }
        body, .main-layout, .wrapper-reset, .about, section { background: #fff !important; }
    </style>
</head>
<body class="main-layout">
    <?php include("header2.php"); ?>

    <div class="about">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="titlepage">
                        <h2>Nova Password</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper-reset fadeInDown">
        <div class="card-reset" style="max-width: 450px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.06);">

            <div class="user-badge" style="margin-bottom: 20px; padding: 15px; background: #f0f7ff; border-radius: 10px;">
                <div style="margin-bottom: 8px; font-size: 16px;">
                    <strong>👤 Nome:</strong> <?php echo htmlspecialchars($nome_utilizador); ?>
                </div>
                <div style="font-size: 16px;">
                    <strong>📧 Email:</strong> <?php echo htmlspecialchars($em); ?>
                </div>
            </div>

            <div class="pwd-hints" style="text-align: left; background: #f8fbff; padding: 15px; border: 1px solid #dbeafe; border-radius: 8px; margin-bottom: 20px;">
                <div id="len" class="hint-item"><i class="fa fa-circle"></i> Pelo menos 12 caracteres</div>
                <div id="char" class="hint-item"><i class="fa fa-circle"></i> Letras (A-z), Números (0-9) e Símbolos</div>
            </div>

            <form method="post" id="resetForm" action="" onsubmit="return false;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <input type="password" id="password" name="password"
                       class="input-prof" placeholder="Nova Password"
                       style="width: 100%; padding: 14px; margin-bottom: 12px; border-radius: 8px; border: 1px solid #ddd;" required>

                <input type="password" id="confirm_password" name="confirmapassword"
                       class="input-prof" placeholder="Confirmar Password"
                       style="width: 100%; padding: 14px; margin-bottom: 12px; border-radius: 8px; border: 1px solid #ddd;" required>

                <button type="button" onclick="executarValidacaoFinal()" class="btn-submit-prof"
                        style="width: 100%; background: #5fbae9; color: white; padding: 15px; border: none; border-radius: 8px; font-weight: bold; text-transform: uppercase; cursor: pointer;">
                    Guardar Password
                </button>
            </form>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php if ($sucesso): ?>
    <script>
    swal({
        title: 'Os dados foram guardados!',
        icon: 'success',
    }).then(function() {
        window.location.href = '<?php echo SVRURL ?>login.php';
    });
    </script>
    <?php elseif (!empty($erroMsg)): ?>
    <script>
    swal({
        title: 'ERRO',
        text: '<?php echo htmlspecialchars($erroMsg, ENT_QUOTES, 'UTF-8'); ?>',
        icon: 'error',
    }).then(function() {
        window.location.href = window.location.href;
    });
    </script>
    <?php endif; ?>

    <script>
    const pwdInput = document.getElementById('password');

    function checkRequirements(val) {
        return {
            length:     val.length >= 12,
            complexity: /[a-zA-Z]/.test(val) && /[0-9]/.test(val) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val)
        };
    }

    pwdInput.addEventListener('input', function() {
        const results  = checkRequirements(this.value);
        const lenHint  = document.getElementById('len');
        const charHint = document.getElementById('char');

        lenHint.classList.toggle('valid', results.length);
        lenHint.querySelector('i').className = results.length ? 'fa fa-check-circle' : 'fa fa-circle';

        charHint.classList.toggle('valid', results.complexity);
        charHint.querySelector('i').className = results.complexity ? 'fa fa-check-circle' : 'fa fa-circle';
    });

    function executarValidacaoFinal() {
        const pass    = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        const results = checkRequirements(pass);

        if (pass === "") {
            swal("Aviso", "Por favor, preencha a password.", "warning");
            return;
        }
        if (pass !== confirm) {
            swal("Erro", "As passwords introduzidas não coincidem.", "error");
            return;
        }
        if (!results.length || !results.complexity) {
            swal({
                title:  "Segurança Insuficiente",
                text:   "A sua password deve ter no mínimo 12 caracteres e incluir letras, números e símbolos.",
                icon:   "warning",
                button: "Vou corrigir",
            });
            return;
        }

        swal("A processar...", "A verificar e guardar a password.", "info");
        setTimeout(() => {
            document.getElementById('resetForm').onsubmit = null;
            document.getElementById('resetForm').submit();
        }, 1000);
    }
    </script>
</body>
</html>
