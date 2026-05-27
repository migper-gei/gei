<?php
// =====================================================================
// retencao_dados.php — Processamento de retenção de dados
// Actua na BD da escola da sessão actual (instalação mono-escola)
// ─────────────────────────────────────────────────────────────────────
// Execução:
//   • Manual: aceder via browser (apenas administradores)
//   • Automática: cron → php /caminho/gei/retencao_dados.php
// =====================================================================

$isCli = (php_sapi_name() === 'cli');

// ── Sessão ────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// Apenas administradores em contexto web
if (!$isCli && (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1)) {
    http_response_code(403);
    exit('Acesso negado. Apenas administradores.');
}

// ── Includes base ─────────────────────────────────────────────────────
include("svrurl.php");   // define SVRURL
include("head.php") && false; // não queremos HTML agora — só $db via header.php
include("header.php");   // define $db ligado à BD da escola actual

// ── PHPMailer ─────────────────────────────────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$a = getcwd();
require_once $a . '/vendor/autoload.php';

set_time_limit(300);

// ── Log ───────────────────────────────────────────────────────────────
$log = [];
function addLog(string $level, string $msg): void {
    global $log;
    $log[] = ['level' => $level, 'msg' => $msg, 'ts' => date('H:i:s')];
    if (php_sapi_name() === 'cli') {
        echo "[{$level}] {$msg}\n";
    }
}

// ── Enviar email de aviso ─────────────────────────────────────────────
function enviarEmailAviso(
    mysqli $db, string $emailDest, string $nomeUtil,
    int $diasAviso, string $nomeApp, string $svrUrl
): bool {
    $_smtpKey = $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '';
    if (empty($_smtpKey)) return false;

    $stmt = $db->prepare("SELECT email_user, AES_DECRYPT(pass,?) as pass_dec, email_smtp, email_smtpport FROM settings LIMIT 1");
    $stmt->bind_param("s", $_smtpKey);
    $stmt->execute();
    $cfg = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$cfg || empty($cfg['pass_dec'])) return false;

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet  = 'UTF-8';
        $mail->isSMTP();
        $mail->Host        = $cfg['email_smtp'];
        $mail->Port        = (int)$cfg['email_smtpport'];
        $mail->SMTPAuth    = true;
        $mail->Username    = $cfg['email_user'];
        $mail->Password    = $cfg['pass_dec'];
        $mail->SMTPSecure  = 'tls';
        $mail->SMTPAutoTLS = false;
        $mail->Timeout     = 15;
        $mail->SMTPOptions = ['ssl' => [
            'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true
        ]];
        $mail->From     = $cfg['email_user'];
        $mail->FromName = $nomeApp;
        $mail->Sender   = $cfg['email_user'];
        $mail->addAddress($emailDest);
        $mail->isHTML(true);
        $mail->Subject = "[{$nomeApp}] Aviso: conta será eliminada por inatividade";
        $mail->Body    = "
        <div style='font-family:Arial,sans-serif;max-width:580px;margin:0 auto;border:1px solid #e3e8f4;border-radius:10px;overflow:hidden;'>
            <div style='background:linear-gradient(135deg,#e67e22,#f39c12);padding:20px 28px;'>
                <h2 style='color:#fff;margin:0;font-size:1.05rem;'>⚠️ Aviso de inatividade — {$nomeApp}</h2>
            </div>
            <div style='padding:24px 28px;background:#fff;'>
                <p>Olá <strong>" . htmlspecialchars($nomeUtil, ENT_QUOTES, 'UTF-8') . "</strong>,</p>
                <p>A sua conta no sistema <strong>{$nomeApp}</strong> não regista atividade há um período prolongado.</p>
                <p style='background:#fff3cd;border-left:4px solid #e67e22;padding:12px 16px;border-radius:6px;'>
                    Se não iniciar sessão nos próximos <strong>{$diasAviso} dias</strong>, a sua conta
                    poderá ser <strong>eliminada automaticamente</strong> de acordo com a política de
                    retenção de dados da sua instituição.
                </p>
                <p>Para manter a sua conta ativa, basta iniciar sessão em:</p>
                <p style='text-align:center;'>
                    <a href='{$svrUrl}' style='display:inline-block;padding:10px 24px;background:#4b6cb7;color:#fff;border-radius:7px;text-decoration:none;font-weight:700;'>{$svrUrl}</a>
                </p>
                <p>Se já não utiliza este sistema, pode ignorar este email.</p>
            </div>
            <div style='background:#f4f6fb;padding:10px 28px;font-size:.75rem;color:#7b88a0;'>
                Email automático — não responda. &nbsp;·&nbsp; {$nomeApp}
            </div>
        </div>";
        $mail->AltBody = "Olá {$nomeUtil},\n\nA sua conta em {$nomeApp} não regista atividade.\nSe não iniciar sessão nos próximos {$diasAviso} dias, a sua conta poderá ser eliminada.\n\nAceda a: {$svrUrl}\n\nEmail automático.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("[GEI retencao] Erro email para {$emailDest}: " . $mail->ErrorInfo);
        return false;
    }
}

// ═════════════════════════════════════════════════════════════════════
//  PROCESSAMENTO
// ═════════════════════════════════════════════════════════════════════
addLog('info', 'Início — ' . date('Y-m-d H:i:s'));

// Ler configuração de retenção
$stmt_cfg = $db->prepare("SELECT retencao_ativa, retencao_anos, retencao_dias_aviso, nome_app FROM settings LIMIT 1");
$stmt_cfg->execute();
$cfg = $stmt_cfg->get_result()->fetch_assoc();
$stmt_cfg->close();

$total_notificados = 0;
$total_eliminados  = 0;
$total_bloqueados  = 0;

if (!$cfg || !(int)$cfg['retencao_ativa']) {
    addLog('warn', 'Política de retenção inativa. Ative-a em Configurações → Email/Sessão.');
    goto output;
}

$ret_anos  = (int)($cfg['retencao_anos']       ?? 3);
$ret_dias  = (int)($cfg['retencao_dias_aviso'] ?? 30);
$nomeApp   = $cfg['nome_app'] ?? 'GEI';
$svrUrl    = defined('SVRURL') ? SVRURL : ($_ENV['APP_URL'] ?? 'http://localhost/gei/');

addLog('info', "Configuração: {$ret_anos} ano(s) de inatividade, aviso {$ret_dias} dia(s).");

// ─────────────────────────────────────────────────────────────────────
// FASE 1 — Inativos NÃO notificados → enviar aviso
// ─────────────────────────────────────────────────────────────────────
addLog('info', 'FASE 1 — Identificar e notificar inativos...');

$stmt_f1 = $db->prepare("
    SELECT id, nome, email
    FROM utilizadores
    WHERE tipo != 1
      AND notificado_retencao = 0
      AND (
          (ultimo_login IS NOT NULL AND ultimo_login < DATE_SUB(NOW(), INTERVAL ? YEAR))
          OR
          (ultimo_login IS NULL AND (created_at IS NULL OR created_at < DATE_SUB(NOW(), INTERVAL ? YEAR)))
      )
");
$stmt_f1->bind_param("ii", $ret_anos, $ret_anos);
$stmt_f1->execute();
$inativos_f1 = $stmt_f1->get_result();
$stmt_f1->close();

if ($inativos_f1->num_rows === 0) {
    addLog('info', 'Fase 1: nenhum utilizador inativo por notificar.');
} else {
    $stmt_notif = $db->prepare("UPDATE utilizadores SET notificado_retencao=1 WHERE id=?");
    while ($u = $inativos_f1->fetch_assoc()) {
        $enviado = enviarEmailAviso($db, $u['email'], $u['nome'], $ret_dias, $nomeApp, $svrUrl);
        if ($enviado) {
            $stmt_notif->bind_param("i", $u['id']);
            $stmt_notif->execute();
            addLog('ok',   "Notificado: {$u['nome']} <{$u['email']}>");
            $total_notificados++;
        } else {
            addLog('warn', "Falha no envio do email: {$u['nome']} <{$u['email']}>");
        }
    }
    $stmt_notif->close();
}

// ─────────────────────────────────────────────────────────────────────
// FASE 2 — Inativos JÁ notificados há mais de X anos + Y dias → eliminar
// ─────────────────────────────────────────────────────────────────────
addLog('info', 'FASE 2 — Eliminar inativos já notificados...');

$dias_total = $ret_anos * 365 + $ret_dias;

$stmt_f2 = $db->prepare("
    SELECT id, nome, email
    FROM utilizadores
    WHERE tipo != 1
      AND notificado_retencao = 1
      AND (
          (ultimo_login IS NOT NULL AND ultimo_login < DATE_SUB(NOW(), INTERVAL ? DAY))
          OR
          (ultimo_login IS NULL AND (created_at IS NULL OR created_at < DATE_SUB(NOW(), INTERVAL ? DAY)))
      )
");
$stmt_f2->bind_param("i", $dias_total);
$stmt_f2->execute();
$candidatos_f2 = $stmt_f2->get_result();
$stmt_f2->close();

if ($candidatos_f2->num_rows === 0) {
    addLog('info', 'Fase 2: nenhum utilizador a eliminar.');
} else {
    $stmt_chk_av = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes WHERE autoravaria=? AND datareparacao IS NULL");
    $stmt_del    = $db->prepare("DELETE FROM utilizadores WHERE id=? AND tipo!=1");

    while ($u = $candidatos_f2->fetch_assoc()) {
        // Salvaguarda: não eliminar se tiver avarias activas
        $stmt_chk_av->bind_param("s", $u['email']);
        $stmt_chk_av->execute();
        $n_av = $stmt_chk_av->get_result()->fetch_row()[0];

        if ($n_av > 0) {
            addLog('warn', "Bloqueado (avarias activas): {$u['nome']} <{$u['email']}>");
            $total_bloqueados++;
            continue;
        }

        $stmt_del->bind_param("i", $u['id']);
        $stmt_del->execute();
        addLog('ok', "Eliminado: {$u['nome']} <{$u['email']}>");
        error_log("[GEI retencao] Eliminado: {$u['email']} — " . date('Y-m-d H:i:s'));
        $total_eliminados++;
    }
    $stmt_chk_av->close();
    $stmt_del->close();
}

addLog('info', '─────────────────────────────────────────');
addLog('info', "Resumo: notificados={$total_notificados} | eliminados={$total_eliminados} | bloqueados={$total_bloqueados}");
addLog('info', 'Fim — ' . date('Y-m-d H:i:s'));

// Em CLI terminar aqui
if ($isCli) exit(0);

output:
?>
<!DOCTYPE html>
<html lang="pt">
<head><?php include("head.php"); ?></head>
<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php include("sessao_timeout.php"); ?>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav style="margin-bottom:10px;">
                    <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Retenção de Dados — Processamento</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-md-1">

                    <div class="welcome-section"><?php include("msg_bemvindo.php"); ?></div>
                    <br>

                    <!-- Resumo -->
                    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
                        <?php foreach ([
                            ['Notificados', $total_notificados, '#fff3cd', '#7d4e00', '#e67e22'],
                            ['Eliminados',  $total_eliminados,  '#fdecea', '#c0392b', '#e74c3c'],
                            ['Bloqueados',  $total_bloqueados,  '#f4f6fb', '#7b88a0', '#c5cde0'],
                        ] as [$lbl, $val, $bg, $tc, $bc]): ?>
                        <div style="flex:1;min-width:130px;text-align:center;background:<?php echo $bg; ?>;border:1.5px solid <?php echo $bc; ?>;border-radius:10px;padding:16px;">
                            <div style="font-size:1.8rem;font-weight:800;color:<?php echo $tc; ?>;"><?php echo $val; ?></div>
                            <div style="font-size:.75rem;font-weight:700;color:<?php echo $tc; ?>;text-transform:uppercase;letter-spacing:.4px;"><?php echo $lbl; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Log -->
                    <div style="background:#1e1e1e;border-radius:8px;padding:1.2em;font-family:'Courier New',monospace;font-size:.85em;line-height:1.9;color:#d4d4d4;margin-bottom:20px;">
                        <?php
                        $colors = ['ok'=>'#4ec9b0','info'=>'#9cdcfe','warn'=>'#ce9178','error'=>'#f44747'];
                        foreach ($log as $entry):
                            $c = $colors[$entry['level']] ?? '#d4d4d4';
                        ?>
                        <span style="color:#6a9955;"><?php echo $entry['ts']; ?></span>
                        <span style="color:<?php echo $c; ?>;"> [<?php echo strtoupper($entry['level']); ?>] <?php echo htmlspecialchars($entry['msg'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                        <?php endforeach; ?>
                    </div>

                    <!-- Instruções cron -->
                    <div style="background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;padding:14px 18px;margin-bottom:20px;">
                        <div style="font-size:.8rem;font-weight:700;color:#182848;margin-bottom:8px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2" stroke-linecap="round" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Automatizar via Cron Job
                        </div>
                        <code style="font-size:.78rem;color:#1e2a45;background:#fff;padding:8px 12px;border-radius:6px;display:block;border:1px solid #e3e8f4;">
                            # Linux — todo o domingo às 02:00<br>
                            0 2 * * 0 php <?php echo rtrim($_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html', '/'); ?>/gei/retencao_dados.php >> /var/log/gei_retencao.log 2>&1
                        </code>
                        <div style="font-size:.75rem;color:#7b88a0;margin-top:8px;">
                            Windows/XAMPP — Agendador de Tarefas:<br>
                            <code>php C:\xampp\htdocs\gei\retencao_dados.php</code>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a class="btn btn-secondary" href="<?php echo SVRURL ?>utiliz_inativos">
                            <i class="bi bi-people"></i> Ver utilizadores inativos
                        </a>
                        &nbsp;
                        <a class="btn btn-secondary" href="<?php echo SVRURL ?>configura">
                            <i class="bi bi-arrow-left"></i> Configurações
                        </a>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
