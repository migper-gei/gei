<?php

// ================================================================
// TRATAMENTO DE ERROS — mostra erro via SweetAlert em vez de página em branco
// ================================================================

// Garantir que TODOS os erros são capturados pelo nosso handler (nada fica
// dependente da configuração do php.ini do servidor) e que o PHP não chega
// a imprimir o erro "nativo" antes do nosso ecrã SweetAlert aparecer.
//error_reporting(E_ALL);
// display_errors a '1' como rede de segurança: se por algum motivo o nosso
// handler não for acionado, o PHP mostra o erro nativo em vez de ficar em branco.
// (Em produção normal, recomenda-se voltar a '0' depois de resolvido o problema.)
//ini_set('display_errors', '1');

// Forçar o registo de erros num ficheiro local, dentro da mesma pasta deste
// script — útil quando não há acesso ao error_log do servidor/Apache.
// Depois de testares, abre "gei_error.log" (está na mesma pasta do validauser.php).
//ini_set('log_errors', '1');
//ini_set('error_log', __DIR__ . '/gei_error.log');

function gei_render_erro(string $titulo, string $linha): void
{
    // Limpar qualquer output anterior para garantir HTML limpo
    if (ob_get_level()) {
        ob_clean();
    }

    $svrurl = defined('SVRURL') ? SVRURL : '/';

    $tituloHtml = htmlspecialchars($titulo, ENT_QUOTES);
    $linhaHtml  = nl2br(htmlspecialchars($linha, ENT_QUOTES));

    echo '<!DOCTYPE html><html lang="pt"><head><meta charset="UTF-8">
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <style>
            body{font-family:Arial,sans-serif;background:#f4f6fb;margin:0;padding:40px 20px}
            .gei-erro-box{max-width:560px;margin:40px auto;background:#fff;border:1px solid #f5c0bb;
                border-left:6px solid #c0392b;border-radius:8px;padding:22px 26px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
            .gei-erro-box h2{color:#c0392b;margin:0 0 10px;font-size:1.15rem}
            .gei-erro-box p{color:#3d4e6b;font-size:.92rem;line-height:1.5;white-space:pre-line}
            .gei-erro-box a{display:inline-block;margin-top:16px;color:#fff;background:#4b6cb7;
                padding:8px 16px;border-radius:6px;text-decoration:none;font-size:.85rem;font-weight:600}
        </style>
    </head><body>
    <!-- Fallback visível em HTML puro: aparece mesmo que o SweetAlert (CDN) não carregue -->
    <div class="gei-erro-box" id="gei-fallback">
        <h2>' . $tituloHtml . '</h2>
        <p>' . $linhaHtml . '</p>
        <a href="' . htmlspecialchars($svrurl . 'l', ENT_QUOTES) . '">&larr; Voltar ao login</a>
    </div>
    <script>
    // Se o SweetAlert carregar, substitui o fallback por um modal mais bonito.
    window.addEventListener("load", function() {
        if (typeof swal === "function") {
            var fb = document.getElementById("gei-fallback");
            if (fb) fb.style.display = "none";
            swal({
                title: "' . addslashes($titulo) . '",
                text: "' . addslashes($linha) . '",
                icon: "error"
            }).then(function() {
                window.location = "' . $svrurl . 'l";
            });
        }
    });
    </script></body></html>';

    exit(1);
}

function gei_error_handler(int $errno, string $errstr, string $errfile, int $errline): bool
{
    // Ignorar erros suprimidos com @
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $tipos = [
        E_ERROR             => 'Erro Fatal',
        E_WARNING           => 'Aviso',
        E_PARSE             => 'Erro de Sintaxe',
        E_NOTICE            => 'Aviso (Notice)',
        E_USER_ERROR        => 'Erro de Utilizador',
        E_USER_WARNING      => 'Aviso de Utilizador',
        E_RECOVERABLE_ERROR => 'Erro Recuperável',
    ];
    $tipo = $tipos[$errno] ?? "Erro ($errno)";

    // Registar no log do servidor (sempre)
    error_log("[GEI] $tipo em $errfile:$errline — $errstr");

    $errstrSafe  = htmlspecialchars($errstr,  ENT_QUOTES);
    $errfileSafe = htmlspecialchars(basename($errfile), ENT_QUOTES);

    gei_render_erro(
        'Erro interno',
        "$tipo (linha $errline de $errfileSafe):\n$errstrSafe"
    );
    return true; // nunca chega aqui (gei_render_erro termina com exit), mas mantém o tipo bool
}

/**
 * Captura exceções/erros não apanhados por try/catch (Throwable),
 * que set_error_handler() NÃO intercepta (ex: TypeError, Exception lançada
 * por mysqli em modo de exceções, DivisionByZeroError, etc.)
 */
function gei_exception_handler(\Throwable $e): void
{
    $errfile = basename($e->getFile());
    $errline = $e->getLine();
    $errmsg  = $e->getMessage();
    $classe  = get_class($e);

    error_log("[GEI] Excepção não apanhada ($classe) em {$e->getFile()}:$errline — $errmsg");

    gei_render_erro(
        'Erro interno',
        htmlspecialchars($classe, ENT_QUOTES) . " (linha $errline de " . htmlspecialchars($errfile, ENT_QUOTES) . "):\n" . htmlspecialchars($errmsg, ENT_QUOTES)
    );
}

function gei_shutdown_handler(): void
{
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        gei_error_handler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

set_error_handler('gei_error_handler', E_ALL);
set_exception_handler('gei_exception_handler');
register_shutdown_function('gei_shutdown_handler');

ob_start(); // Buffer output — necessário para session_regenerate_id() após includes com HTML

// ================================================================
// SEGURANÇA: Iniciar sessão segura
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
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// ================================================================
// CONFIGURAÇÕES DE RATE LIMITING
// ================================================================
define('RL_MAX_ATTEMPTS',       5);   // tentativas máximas por IP por janela
define('RL_MAX_ATTEMPTS_EMAIL', 10);  // tentativas máximas por conta (email) por janela
define('RL_WINDOW_MIN',         15);  // janela em minutos
define('RL_LOCKOUT_MIN',        15);  // tempo de bloqueio em minutos

// ================================================================
// FUNÇÕES DE RATE LIMITING
// ================================================================

/**
 * Verifica se o IP excedeu o número máximo de tentativas.
 * Devolve o número de tentativas já registadas na janela atual.
 * Termina a execução com erro 429 se bloqueado.
 */
function checkRateLimit(mysqli $db, string $ip): int
{
    // Criar evento de limpeza automática (requer Event Scheduler activo no MySQL)
    // Elimina registos com mais de 24h, uma vez por hora
    $db->query("
        CREATE EVENT IF NOT EXISTS cleanup_login_attempts
            ON SCHEDULE EVERY 1 HOUR
            DO
                DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL 24 HOUR
    ");

    // Limpar registos expirados da janela atual (fallback para quando o Event Scheduler está desligado)
    $db->query("DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL " . RL_WINDOW_MIN . " MINUTE");

    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip = ? AND attempt_time > NOW() - INTERVAL " . RL_WINDOW_MIN . " MINUTE"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $attempts = (int) $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    if ($attempts >= RL_MAX_ATTEMPTS) {





        http_response_code(429);
        $_SESSION['login_attempts']  = RL_MAX_ATTEMPTS;
        $_SESSION['login_restantes'] = 0;
        $svrurl = defined('SVRURL') ? SVRURL : '/';
        echo '<!DOCTYPE html><html><head></head><body>';
        echo '<script>
            swal({
                title: "ACESSO BLOQUEADO",
                text: "Demasiadas tentativas falhadas. Aguarde ' . RL_LOCKOUT_MIN . ' minutos e tente novamente.",
                icon: "error"
            }).then(function() {
                window.location = "' . $svrurl . 'l";
            });
        </script></body></html>';





        exit;
    }

    return $attempts;
}

/**
 * Verifica se a conta (email) excedeu o número máximo de tentativas.
 * Bloqueia independentemente do IP — protege contra atacantes com IP dinâmico.
 */
function checkRateLimitByEmail(mysqli $db, string $email): void
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE email = ? AND attempt_time > NOW() - INTERVAL " . RL_WINDOW_MIN . " MINUTE"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $attempts = (int) $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    if ($attempts >= RL_MAX_ATTEMPTS_EMAIL) {




        http_response_code(429);
        $_SESSION['login_attempts']  = RL_MAX_ATTEMPTS_EMAIL;
        $_SESSION['login_restantes'] = 0;
        $svrurl = defined('SVRURL') ? SVRURL : '/';
        echo '<!DOCTYPE html><html><head></head><body>';
        echo '<script>
            swal({
                title: "CONTA BLOQUEADA",
                text: "Demasiadas tentativas falhadas nesta conta. Aguarde ' . RL_LOCKOUT_MIN . ' minutos e tente novamente.",
                icon: "error"
            }).then(function() {
                window.location = "' . $svrurl . 'l";
            });
        </script></body></html>';






        exit;
    }
}

/**
 * Regista uma tentativa falhada apenas por IP (email ainda desconhecido — ex: código errado).
 */
function registerFailedAttempt(mysqli $db, string $ip): void
{
    $stmt = $db->prepare("INSERT INTO login_attempts (ip, email, attempt_time) VALUES (?, NULL, NOW())");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}

/**
 * Regista uma tentativa falhada com IP + email (password errada — conta já conhecida).
 * Alimenta ambos os eixos de rate limiting: por IP e por conta.
 */
function registerFailedAttemptWithEmail(mysqli $db, string $ip, string $email): void
{
    $stmt = $db->prepare("INSERT INTO login_attempts (ip, email, attempt_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $ip, $email);
    $stmt->execute();
    $stmt->close();
}

// ================================================================
// FUNÇÕES DE VALIDAÇÃO
// ================================================================

/**
 * Valida token CSRF. Redireciona para login se inválido.
 * Não usa exit() direto para permitir que o HTML feche correctamente.
 */
function validateCsrfToken(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    // Rotacionar token após uso
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}

/**
 * Valida e sanitiza o código de acesso.
 * Deve ser numérico e ter entre 1 e 9 dígitos.
 */
function validateCodigo(string $value): bool
{
    return ctype_digit($value) && strlen($value) >= 1 && strlen($value) <= 9;
}

/**
 * Valida formato básico de email server-side.
 */
function validateEmail(string $value): bool
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php include ("head.php"); ?>

   </head>

<?php

// ================================================================
// VALIDAÇÃO INICIAL: campos POST obrigatórios
// ================================================================
if (
    !isset($_POST['email'])    || empty(trim($_POST['email']))    ||
    !isset($_POST['password']) || empty($_POST['password'])       ||
    !isset($_POST['codigo'])   || empty(trim($_POST['codigo']))
) {
?>
<script>
swal({ title: 'Sessão inválida', text: 'Acesso direto não permitido. Faça login novamente.', icon: 'error' })
.then(function() { window.location.href = '<?php echo SVRURL ?>l'; });
</script>
<?php
    exit; // Parar execução — campos em falta
}

// ================================================================
// VALIDAÇÃO DO PARÂMETRO URL (GET)
// ================================================================
if (isset($_GET['url']) && is_numeric(base64_decode($_GET['url']))) {
    $url = explode('/', base64_decode($_GET['url']));
} else {
?>
<script>
swal({ title: 'Erro', text: 'Parâmetro de acesso inválido.', icon: 'error' })
.then(function() { window.location.href = '<?php echo SVRURL ?>l'; });
</script>
<?php
    exit;
}

?>

   <!-- body -->
   <body class="main-layout">

     <?php include ("header2.php"); ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-10 offset-md-2">
                  <div class="titlepage"><h2></h2></div>

<?php

$_SESSION['lastaccess'] = time();

// Verificar parâmetro z1
$z1 = $url[0];
if ($z1 != 0) {
?>
<script>
swal({ title: 'Erro', text: 'Parâmetro de acesso inválido (z1).', icon: 'error' })
.then(function() { window.location.href = '<?php echo SVRURL ?>l'; });
</script>
<?php
    exit;
}

// ================================================================
// PROCESSAR LOGIN — apenas POST
// ================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ----------------------------------------------------------
    // 1. Validar token CSRF
    // ----------------------------------------------------------
    if (!validateCsrfToken()) {
        ?>
        <script>
        swal({ title: 'Sessão expirada', text: 'Token de segurança inválido ou expirado. Faça login novamente.', icon: 'error' })
        .then(function() { window.location.href = '<?php echo SVRURL ?>l'; });
        </script>
        <?php
        exit;
    }

    // ----------------------------------------------------------
    // 2. Validar e sanitizar inputs server-side
    // ----------------------------------------------------------
    $rawCodigo   = trim($_POST['codigo']   ?? '');
    $rawEmail    = trim($_POST['email']    ?? '');
    $rawPassword =      $_POST['password'] ?? '';

    if (!validateCodigo($rawCodigo)) {

        ?>
        <script>
        swal({ title: 'ERRO', text: 'Código inválido.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php

   



        exit;
    }

    if (!validateEmail($rawEmail)) {

          ?>
        <script>
        swal({ title: 'ERRO', text: 'Email inválido.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php

      
        exit;
    }

    $codigo = $rawCodigo;

    // ----------------------------------------------------------
    // 3. Ligar à BD principal e verificar código
    // ----------------------------------------------------------
    include ("config_serverbd_settings.php");
    $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    if ($db0->connect_errno) {
        error_log("Erro ligação BD principal: " . $db0->connect_error);
        $msgErroBd0 = addslashes('Erro ligação BD principal (' . $db0->connect_errno . '): ' . $db0->connect_error);
        ?>
        <script>
        swal({ title: 'ERRO', text: '<?php echo $msgErroBd0; ?>', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php
        exit;
    }

    // ----------------------------------------------------------
    // 4. Rate limiting — verificar ANTES de qualquer consulta de auth
    //    Eixo 1: por IP    — bloqueia ataques em massa
    //    Eixo 2: por email — bloqueia por conta independentemente do IP
    // ----------------------------------------------------------
    $clientIp         = $_SERVER['REMOTE_ADDR'];
    $tentativasFeitas = checkRateLimit($db0, $clientIp);
    $restantes        = RL_MAX_ATTEMPTS - $tentativasFeitas;

    checkRateLimitByEmail($db0, $rawEmail);

    // ----------------------------------------------------------
    // 5. Verificar se o código existe (prepared statement)
    // ----------------------------------------------------------
    $stmtCount = $db0->prepare("SELECT COUNT(*) AS ccod FROM settingsbd WHERE codigo = ?");
    $stmtCount->bind_param("s", $codigo);
    $stmtCount->execute();
    $ccod = (int) $stmtCount->get_result()->fetch_row()[0];
    $stmtCount->close();

    if ($ccod === 0) {
        // Registar falha (código errado também conta como tentativa)
        registerFailedAttempt($db0, $clientIp);
        mysqli_close($db0);
        ?>
        <script>
        swal({ title: 'ERRO', text: 'Código incorreto!', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <?php
        
 
        
        exit;
    }

    // ----------------------------------------------------------
    // 6. Obter nomebd e serverbd 
    //   
    // ----------------------------------------------------------
    $stmtBd = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
    $stmtBd->bind_param("s", $codigo);
    $stmtBd->execute();
    $row0    = $stmtBd->get_result()->fetch_assoc();
    $stmtBd->close();

    $nomebd   = $row0['nomebd'];
    $serverbd = $row0['serverbd'];

    mysqli_close($db0);

    // NOTA: $_SESSION['nobd'] e ['serverbd'] são definidos DEPOIS do session_regenerate_id
    // para garantir que não se perdem com a regeneração do ID de sessão.

    // ----------------------------------------------------------
    // 7. Ligar à BD do utilizador
    // ----------------------------------------------------------
    // NOTA: $serverbd pode vir no formato "host:porta" (ex: "localhost:3306").
    // O mysqli espera o host e a porta como parâmetros SEPARADOS — passar a
    // string completa como host faz com que o MySQL tente resolver
    // "localhost:3306" como nome de máquina, falhando a ligação.
    $hostbd = $serverbd;
    $portbd = null;
    if (strpos($serverbd, ':') !== false) {
        [$hostbd, $portStr] = explode(':', $serverbd, 2);
        $portbd = (int) $portStr;
    }

    $db = ($portbd !== null)
        ? new mysqli($hostbd, DB_USERNAME, DB_PASSWORD, $nomebd, $portbd)
        : new mysqli($hostbd, DB_USERNAME, DB_PASSWORD, $nomebd);

    if ($db->connect_errno) {
        error_log("Erro ligação BD utilizador: " . $db->connect_error);
        $msgErroBd = addslashes('Erro ligação BD utilizador (' . $db->connect_errno . '): ' . $db->connect_error);
        ?>
        <script>
        swal({ title: 'ERRO', text: '<?php echo $msgErroBd; ?>', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php
        exit;
    }

    mysqli_select_db($db, $nomebd);

    $myemail    = $rawEmail;
    $mypassword = $rawPassword;

    // ----------------------------------------------------------
    // 8. Autenticação — prepared statement + password_verify
    // ----------------------------------------------------------
    $stmtLogin = $db->prepare(
        "SELECT id, nome, tipo, email, pass, COALESCE(ativo,1) as ativo FROM utilizadores WHERE email = ? LIMIT 1"
    );
    $stmtLogin->bind_param("s", $myemail);
    $stmtLogin->execute();
    $rowLogin = $stmtLogin->get_result()->fetch_assoc();
    $stmtLogin->close();

    // Autenticação com Argon2id
    $loginOk = false;

    if ($rowLogin) {
        // Verificar se a conta está ativa antes de validar a password
        if ((int)($rowLogin['ativo'] ?? 1) === 0) { ?>
        <script>
        swal({ title: 'Conta desativada!', text: 'A sua conta foi desativada. Contacte o administrador.', icon: 'error' })
        .then(function(){ window.location = "<?php echo SVRURL ?>l"; });
        </script>
        <?php exit; }

        $passHash = $rowLogin['pass'] ?? '';
        $loginOk  = password_verify($mypassword, $passHash);
    }

    $count = $loginOk ? 1 : 0;
    $row   = $loginOk ? $rowLogin : null;

    // ── Política de retenção de dados ────────────────────────────────────
    // Registar data/hora do último login ANTES de fechar a ligação à BD
    if ($count === 1) {
        $uid_login = $row['id'];
        $stmt_ul = $db->prepare("UPDATE utilizadores SET ultimo_login=NOW(), notificado_retencao=0 WHERE id=?");
        if ($stmt_ul) {
            $stmt_ul->bind_param("i", $uid_login);
            $stmt_ul->execute();
            $stmt_ul->close();
        }

        //Auditoria-logs
require_once('gei_audit.php');
gei_audit($db, 'login_ok', 'sessao', null, 'Login', $row['id'], $row['nome'], $row['email']);

    }
    // ── Fim política de retenção ─────────────────────────────────────────






    mysqli_close($db);

    // ----------------------------------------------------------
    // 9. Resultado da autenticação
    // ----------------------------------------------------------
    if ($count === 1) {

        // FIX: regenerar ID de sessão imediatamente após autenticação bem-sucedida
        // Previne session fixation — o ID antigo (pré-login) é invalidado agora
        session_regenerate_id(true);
        $_SESSION['_created'] = time();

        // FIX: definir nobd e serverbd APÓS session_regenerate_id(true)
        // Em alguns ambientes PHP, variáveis de sessão definidas antes do regenerate
        // podem perder-se. Definir tudo aqui garante consistência.
        $_SESSION['nobd']       = $nomebd;
        $_SESSION['serverbd']   = $serverbd;

        // 2FA desativado — todos os utilizadores (incluindo administradores)
        // entram diretamente com utilizador e password, sem passo adicional.

        // Criar sessão completa para todos os tipos de utilizador
        $_SESSION['login_user'] = $row['nome'];
        $_SESSION['tipo']       = $row['tipo'];
        $_SESSION['email']      = $row['email'];
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ip_addr']    = $_SERVER['REMOTE_ADDR'];

        // Token de sessão secundário — verificado em verifica_sessao.php via cookie HttpOnly
        $sec_token = bin2hex(random_bytes(32));
        $_SESSION['sec_token'] = $sec_token;
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('gei_sec', $sec_token, [
            'expires'  => 0,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);




        
        ?>
        <script>
        window.location = "<?php echo SVRURL ?>acessorap";
        </script>
        <?php

    } else {

        // Login falhado: registar tentativa para ambos os eixos de rate limiting
        // $db0 foi fechado acima — abrir nova ligação pontual
        $dbRl = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if (!$dbRl->connect_errno) {
            registerFailedAttemptWithEmail($dbRl, $clientIp, $rawEmail);
            mysqli_close($dbRl);
        }

        // Atualizar tentativas restantes (já registámos mais uma)
        $restantes = max(0, $restantes - 1);
        $_SESSION['login_restantes'] = $restantes;

        $avisoRestantes = ($restantes > 0)
            ? "Tem mais {$restantes} tentativa(s) antes do bloqueio."
            : "Esta foi a sua última tentativa. O acesso está bloqueado por " . RL_LOCKOUT_MIN . " minutos.";

        echo "<br><br>";
        ?>
        <script>
        swal({
            title: 'ERRO',
            text: 'Verifique os dados (Email, password e código)!\n<?php echo addslashes($avisoRestantes); ?>',
            icon: 'error'
        }).then(function() {
            window.location = "<?php echo SVRURL ?>l";
        });
        </script>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <?php


    }
    
   



} else {
    // Não é POST — redirecionar
    ?>
    <script>
    swal({ title: 'Acesso inválido', text: 'Esta página só pode ser acedida através do formulário de login.', icon: 'warning' })
    .then(function() { window.location.href = '<?php echo SVRURL ?>l'; });
    </script>
    <?php
}

?>

               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

<br>
      <?php include ("footer.php"); ?>

</body>
</html>
