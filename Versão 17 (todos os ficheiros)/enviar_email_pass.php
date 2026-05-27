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
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/vendor/phpmailer/src/Exception.php';
require __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/src/SMTP.php';

include("head.php"); // disponibiliza SVRURL e ligação $db

// Carregar .env para acesso à BD central
$env_path = __DIR__ . '/.env';
if (file_exists($env_path)) {
    foreach (file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
    }
}

$db_host          = $_ENV['DB_HOST'] ?? 'localhost';
$db_user          = $_ENV['DB_USER'] ?? 'root';
$db_pass          = $_ENV['DB_PASS'] ?? '';
$db_settings_name = $_ENV['DB_SETTINGS_NAME'] ?? 'gei_escolas_instituicoes';

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include("head.php"); ?>
</head>
<body class="main-layout">
<?php include("header.php"); ?>

<div class="about">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="titlepage">
          <h2>Recuperação de password</h2>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="wrapper fadeInDown">
          <div id="formContent">

<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $em = base64_decode($_GET["em"] ?? '');

    // Resposta genérica (evita enumeração)
    $mail_ok   = true;
    $mail_erro = '';

    if (!empty($em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {

        // Confirmar se o utilizador existe e obter o nome
        $nome_utilizador = '';
        $user_exists     = false;

        if (isset($db) && $db instanceof mysqli) {
            $stmt_chk = $db->prepare(
                "SELECT id, nome FROM utilizadores WHERE email = ? LIMIT 1"
            );
            if ($stmt_chk) {
                $stmt_chk->bind_param("s", $em);
                $stmt_chk->execute();
                $row_user = $stmt_chk->get_result()->fetch_assoc();
                $stmt_chk->close();

                if ($row_user) {
                    $user_exists     = true;
                    $nome_utilizador = $row_user['nome'];
                }
            }
        }

        if ($user_exists) {
            // Guardar na sessão para usar em reset_pass_token.php
            $_SESSION['reset_nome']  = $nome_utilizador;
            $_SESSION['reset_email'] = $em;

            // Ligar à BD central para guardar token
            $db_central = new mysqli($db_host, $db_user, $db_pass, $db_settings_name);

            if (!$db_central->connect_errno) {

                // Remover tokens anteriores deste email
                $stmt_del = $db_central->prepare(
                    "DELETE FROM password_reset_tokens WHERE email = ?"
                );
                $stmt_del->bind_param("s", $em);
                $stmt_del->execute();
                $stmt_del->close();

                // Gerar token seguro
                $token_raw  = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token_raw);

                // Expira em 1 hora
                $stmt_ins = $db_central->prepare(
                    "INSERT INTO password_reset_tokens (email, token_hash, expires_at)
                     VALUES (?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR))"
                );

                if ($stmt_ins) {
                    $stmt_ins->bind_param("ss", $em, $token_hash);
                    $stmt_ins->execute();
                    $stmt_ins->close();

                    // Construir link
                    $base_url = defined('SVRURL')
                        ? rtrim(SVRURL, '/') . '/'
                        : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                            . '://' . $_SERVER['HTTP_HOST'] . '/');

                    $reset_link = $base_url . 'reset_pass_token.php?t=' . urlencode($token_raw);

                    // Enviar email
                    $mail = new PHPMailer(true);

                    try {
                        $mail->CharSet = 'UTF-8';
                        $mail->isSMTP();

                        include('email_settings.php');
                        include('dados_enviar_email.php');

                        $mail->isHTML(true);
                        $mail->addAddress($em);

                        $mail->Subject = 'Recuperação de password - SGEI';

                        $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
  <div style="padding:24px;background-color:#f9f9f9;">
    <h3 style="color:#003366;margin-top:0;">🔐 Recuperação de password</h3>

    <p>Olá, <strong>' . htmlspecialchars($nome_utilizador) . '</strong>.</p>

    <p>Foi solicitada a recuperação da password da sua conta.</p>

    <p>Clique no botão abaixo para definir uma nova password:</p>

    <div style="text-align:center;margin:30px 0;">
      <a href="' . $reset_link . '"
         style="display:inline-block;padding:12px 24px;background:#003366;color:#ffffff;
                text-decoration:none;border-radius:6px;font-weight:bold;">
        Definir nova password
      </a>
    </div>

    <p>Se o botão não funcionar, copie e cole este link no navegador:</p>

    <p style="word-break:break-all;">
      <a href="' . $reset_link . '">' . $reset_link . '</a>
    </p>

    <p style="font-size:13px;color:#666;">
      Este link é válido durante 1 hora e só pode ser utilizado uma vez.
    </p>

    <p style="font-size:13px;color:#666;">
      Se não solicitou esta recuperação, ignore este email.
    </p>
  </div>
</div>';

                        $mail->AltBody =
                            "Olá, " . $nome_utilizador . ".\n\n"
                            . "Foi solicitada a recuperação da sua password.\n\n"
                            . "Abra o seguinte link para definir uma nova password:\n"
                            . $reset_link . "\n\n"
                            . "O link é válido durante 1 hora.\n\n"
                            . "Se não solicitou esta recuperação, ignore este email.";

                        $mail->send();
                    } catch (\Exception $e) {
                        $mail_ok   = false;
                        $mail_erro = $mail->ErrorInfo;
                        error_log('Erro ao enviar email de recuperação para ' . $em . ': ' . $mail->ErrorInfo);
                    }
                }
                $db_central->close();
            }
        }
    }
}
?>

<?php if ($mail_ok): ?>
    <h2>Email enviado.</h2>
    <p>Consulte a sua caixa de correio para definir uma nova password.</p>
    <p>Se não encontrar o email, verifique a pasta de spam.</p>
<?php else: ?>
    <div style="color:red; padding:10px; border:1px solid red; margin:10px;">
        <strong>O email não foi enviado.</strong><br>
        <small><?php echo htmlspecialchars($mail_erro); ?></small>
    </div>
<?php endif; ?>

<form action="<?php echo SVRURL; ?>l" method="post" style="margin-top:20px;">
    <input type="submit" value="OK">
</form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
