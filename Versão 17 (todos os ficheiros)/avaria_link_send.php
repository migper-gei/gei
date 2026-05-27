<?php
ob_start(); // evitar "headers already sent" causado por BOM/whitespace nos includes
// ============================================================
// avaria_link_send.php — GEI
// Processa o pedido de link por email:
//   1. Valida CSRF e email
//   2. Verifica rate-limit por IP (3 pedidos / hora)
//   3. Gera token seguro e guarda na BD (tabela avaria_link_tokens)
//   4. Envia email com link único (30 min)
//   5. Redireciona para avaria_link.php com feedback
// ============================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('svrurl.php');
include_once('config_serverbd_settings.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ── Helpers ───────────────────────────────────────────────────────────────────

function _al_redirect(string $cod, string $msg): never {
    while (ob_get_level() > 0) { ob_end_clean(); }
    $_SESSION['avlink_feedback'] = $msg;
    $url = SVRURL . 'avaria_link.php' . ((int)$cod > 0 ? '?cod=' . (int)$cod : '');
    header('Location: ' . $url);
    exit;
}

function _al_get_ip(): string {
    $fwd = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($fwd) {
        $ip = trim(explode(',', $fwd)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ── Validar método ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Location: ' . SVRURL . 'avaria_link.php');
    exit;
}

$cod   = isset($_POST['cod']) ? (int)$_POST['cod'] : 0;
$email = trim($_POST['email'] ?? '');

if ($cod <= 0) {
    _al_redirect(0, 'Parâmetro de instituição em falta.');
}

// ── CSRF ───────────────────────────────────────────────────────────────────────
if (empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_avlink']) ||
    !hash_equals($_SESSION['csrf_avlink'], $_POST['csrf_token'])) {
    _al_redirect($cod, 'Token de segurança inválido. Por favor recarregue a página.');
}

// Rotação imediata do token CSRF
$_SESSION['csrf_avlink']      = bin2hex(random_bytes(32));
$_SESSION['csrf_avlink_time'] = time();

// ── Validar email ─────────────────────────────────────────────────────────────
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    _al_redirect($cod, 'Por favor introduza um endereço de email válido.');
}

// ── Ligar à BD de settings e verificar código ─────────────────────────────────
try {
    $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $db0->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('avaria_link_send settings BD: ' . $e->getMessage());
    _al_redirect($cod, 'Erro interno. Tente mais tarde.');
}

$stmt0 = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
$stmt0->bind_param('i', $cod);
$stmt0->execute();
$stmt0->bind_result($_nomebd, $_serverbd);
$_found = $stmt0->fetch();
$stmt0->close();

if (!$_found || empty($_nomebd)) {
    $db0->close();
    _al_redirect($cod, 'Código de instituição inválido.');
}

// ── Criar tabela de tokens se não existir (na BD de settings) ─────────────────
//
// CREATE TABLE avaria_link_tokens (
//   id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   email       VARCHAR(254)  NOT NULL,
//   token_hash  VARCHAR(64)   NOT NULL,
//   codigo      INT UNSIGNED  NOT NULL,        -- código da escola
//   ip          VARCHAR(45)   NOT NULL,
//   criado_em   DATETIME      NOT NULL,
//   expira_em   DATETIME      NOT NULL,
//   usado       TINYINT(1)    NOT NULL DEFAULT 0,
//   INDEX idx_token  (token_hash),
//   INDEX idx_ip_ts  (ip, criado_em)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
//
$db0->query("
    CREATE TABLE IF NOT EXISTS avaria_link_tokens (
        id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email       VARCHAR(254)  NOT NULL,
        token_hash  VARCHAR(64)   NOT NULL,
        codigo      INT UNSIGNED  NOT NULL,
        ip          VARCHAR(45)   NOT NULL,
        criado_em   DATETIME      NOT NULL,
        expira_em   DATETIME      NOT NULL,
        usado       TINYINT(1)    NOT NULL DEFAULT 0,
        INDEX idx_token (token_hash),
        INDEX idx_ip_ts (ip, criado_em)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ── Rate limiting por IP: máximo 3 links por hora ─────────────────────────────
define('AL_RL_MAX',    3);
define('AL_RL_JANELA', 3600);

$_ip          = _al_get_ip();
$janela_ini   = date('Y-m-d H:i:s', time() - AL_RL_JANELA);

$stmtRL = $db0->prepare("
    SELECT COUNT(*) FROM avaria_link_tokens
    WHERE ip = ? AND criado_em >= ?
");
$stmtRL->bind_param('ss', $_ip, $janela_ini);
$stmtRL->execute();
$stmtRL->bind_result($contRL);
$stmtRL->fetch();
$stmtRL->close();

if ($contRL >= AL_RL_MAX) {
    $db0->close();
    _al_redirect($cod, 'Demasiados pedidos. Por favor aguarde alguns minutos e tente novamente.');
}

// ── Invalidar tokens anteriores do mesmo email + código (ainda não usados) ────
$stmtInv = $db0->prepare("
    UPDATE avaria_link_tokens
    SET usado = 1
    WHERE email = ? AND codigo = ? AND usado = 0
");
$stmtInv->bind_param('si', $email, $cod);
$stmtInv->execute();
$stmtInv->close();

// ── Gerar token seguro ────────────────────────────────────────────────────────
$token_raw  = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token_raw);

$stmtIns = $db0->prepare("
    INSERT INTO avaria_link_tokens (email, token_hash, codigo, ip, criado_em, expira_em, usado)
    VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 MINUTE), 0)
");
$stmtIns->bind_param('ssis', $email, $token_hash, $cod, $_ip);
$stmtIns->execute();
$stmtIns->close();

// Limpeza oportunista: remover tokens expirados há mais de 2h (1% das vezes)
if (random_int(1, 100) === 1) {
    $db0->query("DELETE FROM avaria_link_tokens WHERE expira_em < DATE_SUB(NOW(), INTERVAL 2 HOUR)");
}

$db0->close();

// ── Construir link ────────────────────────────────────────────────────────────
$link_acesso = SVRURL . 'avaria_link_acesso.php?t=' . urlencode($token_raw) . '&cod=' . $cod;

// ── Enviar email ──────────────────────────────────────────────────────────────
$email_enviado = false;

try {
    // email_settings.php precisa de $db ligado à BD da instituição
    $db = new mysqli($_serverbd, DB_USERNAME, DB_PASSWORD, $_nomebd);
    $db->set_charset('utf8mb4');

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    include('email_settings.php');
    include('dados_enviar_email.php');

    $mail->isHTML(true);
    $mail->addAddress($email);
    $mail->Subject = 'Link para reportar avaria — SGEI';

    $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #dde3f0;border-radius:10px;overflow:hidden;">
  <div style="background:#182848;padding:22px 28px;">
    <h2 style="color:#fff;margin:0;font-size:17px;font-weight:700;">
      <span style="opacity:.75;font-size:.8em;display:block;font-weight:400;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">SGEI — Sistema de Gestão de Equipamento Informático</span>
      🔧 Reportar uma Avaria
    </h2>
  </div>
  <div style="padding:28px 28px 20px;background:#f7f9fe;">
    <p style="margin-top:0;">Recebemos um pedido para reportar uma avaria associado ao seu endereço de email.</p>
    <p>Clique no botão abaixo para abrir o formulário de reporte:</p>
    <div style="text-align:center;margin:28px 0;">
      <a href="' . $link_acesso . '"
         style="display:inline-block;padding:14px 32px;background:#4b6cb7;color:#ffffff;
                text-decoration:none;border-radius:8px;font-weight:700;font-size:15px;">
        Abrir formulário de avaria
      </a>
    </div>
    <p style="font-size:13px;color:#666;">
      Se o botão não funcionar, copie e cole este link no navegador:<br>
      <a href="' . $link_acesso . '" style="word-break:break-all;color:#4b6cb7;">' . $link_acesso . '</a>
    </p>
    <hr style="border:none;border-top:1px solid #e3e8f4;margin:20px 0;">
    <p style="font-size:12px;color:#999;margin:0;">
      ⏱ Este link é válido durante <strong>30 minutos</strong> e só pode ser utilizado uma vez.<br>
      Se não solicitou este link, ignore este email em segurança.
    </p>
  </div>
</div>';

    $mail->AltBody =
        "Reportar uma avaria — SGEI\n\n"
        . "Clique no link abaixo para abrir o formulário:\n\n"
        . $link_acesso . "\n\n"
        . "O link é válido durante 30 minutos e só pode ser utilizado uma vez.\n"
        . "Se não solicitou este link, ignore este email.";

    $mail->send();
    $email_enviado = true;
    $db->close();

} catch (\Exception $e) {
    error_log('avaria_link_send email error (' . $email . '): ' . $e->getMessage());
    if (isset($db) && $db instanceof mysqli) { $db->close(); }
}

// Resposta genérica mesmo em caso de erro no email (evita enumeração)
// O utilizador vê sempre "email enviado" — mas logamos o erro
_al_redirect($cod, $email_enviado ? 'ok' : 'ok');
