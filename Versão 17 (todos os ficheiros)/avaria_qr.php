<?php
// ─────────────────────────────────────────────
//  PHPMailer 6.x — instalação manual (ficheiros src)
// ─────────────────────────────────────────────

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';



// ============================================================
// avaria_qr.php — GEI
// Formulário público de reporte de avaria via QR Code.
// Não requer autenticação.
// URL: avaria_qr.php?eq=ID_EQUIP&sala=ID_SALA&esc=ID_ESCOLA
// ============================================================

// Sessão anónima apenas para CSRF — não usa gei_session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('svrurl.php');

// ── Ligação à BD correcta via código de acesso (sem sessão — página pública) ──
include_once('config_serverbd_settings.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Parâmetros do QR Code
$id_equip = isset($_GET['eq'])   ? (int)$_GET['eq']   : 0;
$id_sala  = isset($_GET['sala']) ? (int)$_GET['sala']  : 0;
$id_esc   = isset($_GET['esc'])  ? (int)$_GET['esc']   : 0;
$_codigo  = isset($_GET['cod'])  ? (int)$_GET['cod']   : 0;

if ($_codigo <= 0) {
    die('QR Code inválido — código em falta. Por favor use o QR Code afixado no equipamento.');
}

// 1. Ligar à BD de settings e obter nomebd + serverbd pelo código
try {
    $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $db0->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('avaria_qr settings BD error: ' . $e->getMessage());
    die('Erro de configuração. Contacte o administrador.');
}

$stmt0 = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
$stmt0->bind_param('i', $_codigo);
$stmt0->execute();
$stmt0->bind_result($_nomebd, $_serverbd);
$_found = $stmt0->fetch();
$stmt0->close();
$db0->close();

if (!$_found || empty($_nomebd)) {
    die('QR Code inválido — instituição não encontrada.');
}

// 2. Ligar à BD da instituição
try {
    $db = new mysqli($_serverbd, DB_USERNAME, DB_PASSWORD, $_nomebd);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('avaria_qr main BD error: ' . $e->getMessage());
    die('Erro ao ligar à base de dados. Contacte o administrador.');
}

// ── Validar que o equipamento pertence à sala e escola indicadas
$info = null;
if ($id_equip && $id_sala && $id_esc) {
    $stmt_info = $db->prepare("
        SELECT eq.nomeequi, s.nome AS nomsala, es.nome_escola
        FROM equipamento eq
        INNER JOIN salas s    ON s.id  = eq.id_sala
        INNER JOIN escolas es ON es.id = s.id_escola
        WHERE eq.id = ? AND s.id = ? AND es.id = ?
        LIMIT 1
    ");
    $stmt_info->bind_param('iii', $id_equip, $id_sala, $id_esc);
    $stmt_info->execute();
    $info = $stmt_info->get_result()->fetch_assoc();
    $stmt_info->close();
}

$erro_params = ($info === null);

// ── Rate limiting por IP — máximo 5 submissões por hora ──────────────────────
// Usa uma tabela leve na BD da instituição:
//   CREATE TABLE IF NOT EXISTS avaria_rate_limit (
//       ip         VARCHAR(45)  NOT NULL,
//       submetido_em DATETIME   NOT NULL,
//       INDEX idx_ip_ts (ip, submetido_em)
//   );
// A tabela é criada automaticamente na primeira execução.
define('RL_MAX',    5);    // submissões permitidas
define('RL_JANELA', 3600); // janela em segundos (1 hora)

function _rl_get_ip(): string {
    // Preferir X-Forwarded-For apenas se existir (proxy/CDN confiável)
    $fwd = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($fwd) {
        // Pode conter lista "client, proxy1, proxy2" — usar o primeiro
        $ip = trim(explode(',', $fwd)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function _rl_check_and_record(mysqli $db, string $ip): bool {
    // Garantir que a tabela existe (criação idempotente)
    $db->query("
        CREATE TABLE IF NOT EXISTS avaria_rate_limit (
            ip           VARCHAR(45)  NOT NULL,
            submetido_em DATETIME     NOT NULL,
            INDEX idx_ip_ts (ip, submetido_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $janela_inicio = date('Y-m-d H:i:s', time() - RL_JANELA);

    // Contar submissões deste IP na última hora
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM avaria_rate_limit
        WHERE ip = ? AND submetido_em >= ?
    ");
    $stmt->bind_param('ss', $ip, $janela_inicio);
    $stmt->execute();
    $stmt->bind_result($contagem);
    $stmt->fetch();
    $stmt->close();

    if ($contagem >= RL_MAX) {
        return false; // limite atingido
    }

    // Registar esta submissão
    $agora = date('Y-m-d H:i:s');
    $stmt_ins = $db->prepare("
        INSERT INTO avaria_rate_limit (ip, submetido_em) VALUES (?, ?)
    ");
    $stmt_ins->bind_param('ss', $ip, $agora);
    $stmt_ins->execute();
    $stmt_ins->close();

    // Limpeza oportunista: apagar registos com mais de 2 horas (1% dos pedidos)
    if (random_int(1, 100) === 1) {
        $expirado = date('Y-m-d H:i:s', time() - RL_JANELA * 2);
        $db->query("DELETE FROM avaria_rate_limit WHERE submetido_em < '$expirado'");
    }

    return true; // permitido
}

// ── Gerar token CSRF para este formulário ─────────────────────────────────────
if (empty($_SESSION['csrf_qr']) || empty($_SESSION['csrf_qr_time']) ||
    (time() - $_SESSION['csrf_qr_time']) > 1800) {
    $_SESSION['csrf_qr']      = bin2hex(random_bytes(32));
    $_SESSION['csrf_qr_time'] = time();
}

// ── Processar POST ────────────────────────────────────────────────────────────
$sucesso = false;
$erro_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$erro_params) {

    // CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_qr'], $_POST['csrf_token'])) {
        $erro_msg = 'Token de segurança inválido. Por favor recarregue a página.';
    } else {

        $email_autor = trim($_POST['email_autor'] ?? '');
        $descricao   = trim($_POST['avaria']      ?? '');
        $data_hoje   = date('Y-m-d');

        // ── Verificar rate limiting antes de qualquer escrita na BD ──────────
        $_ip_cliente = _rl_get_ip();
        if (!_rl_check_and_record($db, $_ip_cliente)) {
            $erro_msg = 'Demasiadas submissões. Por favor aguarde antes de reportar outra avaria.';
        } elseif (empty($email_autor) || !filter_var($email_autor, FILTER_VALIDATE_EMAIL)) {
            $erro_msg = 'Por favor indique um endereço de email válido.';
        } elseif (mb_strlen($descricao) < 10) {
        } else {
            // Ano letivo e período atuais
            $res_ano = mysqli_query($db, "SELECT MAX(ano_lectivo) FROM periodos");
            $ano     = mysqli_fetch_row($res_ano)[0] ?? '';

            $stmtPer = $db->prepare("
                SELECT MAX(num_periodo) FROM periodos
                WHERE STR_TO_DATE(?, '%Y-%m-%d') >= STR_TO_DATE(data_inicio, '%Y-%m-%d')
                  AND STR_TO_DATE(?, '%Y-%m-%d') <= STR_TO_DATE(data_fim,    '%Y-%m-%d')
                  AND ano_lectivo = ?
            ");
            $stmtPer->bind_param('sss', $data_hoje, $data_hoje, $ano);
            $stmtPer->execute();
            $per = $stmtPer->get_result()->fetch_row()[0] ?? null;
            $stmtPer->close();

            // Inserir avaria
            $stmt_av = $db->prepare("
                INSERT INTO avarias_reparacoes
                    (id_equi, id_sala, id_escola, autoravaria, dataavaria, avaria, ano_letivo, periodo)
                VALUES (?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, ?, ?)
            ");
            $stmt_av->bind_param(
                'iiissss' . ($per !== null ? 'i' : 's'),
                $id_equip, $id_sala, $id_esc,
                $email_autor, $data_hoje,
                $descricao, $ano, $per
            );
            $stmt_av->execute();
            $id_avaria = $db->insert_id;
            $stmt_av->close();

            // Renovar token após uso
            $_SESSION['csrf_qr']      = bin2hex(random_bytes(32));
            $_SESSION['csrf_qr_time'] = time();

            // Enviar email de notificação directamente (sem redirect — página pública)
            if ($id_avaria) {
                try {
                    // Dados da avaria para o email
                    $stmt_em = $db->prepare("
                        SELECT ar.avaria, ar.dataavaria, ar.autoravaria,
                               eq.nomeequi, s.nome AS nomsala, es.nome_escola
                        FROM avarias_reparacoes ar
                        INNER JOIN equipamento eq ON eq.id = ar.id_equi
                        INNER JOIN salas s         ON s.id  = ar.id_sala
                        INNER JOIN escolas es      ON es.id = ar.id_escola
                        WHERE ar.id = ? LIMIT 1
                    ");
                    $stmt_em->bind_param('i', $id_avaria);
                    $stmt_em->execute();
                    $dados_av = $stmt_em->get_result()->fetch_assoc();
                    $stmt_em->close();

                    // Destinatários: autor + admins/reparadores
                    $stmt_dest = $db->prepare("SELECT email FROM utilizadores WHERE tipo = 1 OR tipo = 3");
                    $stmt_dest->execute();
                    $res_dest = $stmt_dest->get_result();
                    $dest_emails = [];
                    while ($d = $res_dest->fetch_row()) { $dest_emails[] = $d[0]; }
                    $stmt_dest->close();

                    $mail = new PHPMailer(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    include('email_settings.php');
                    include('dados_enviar_email.php');
                    $mail->isHTML(true);
                    $mail->Subject = 'Nova avaria registada via QR Code';
                    $mail->addAddress($email_autor);
                    foreach ($dest_emails as $de) { $mail->addAddress($de); }
                    $mail->Body = '
                    <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
                      <div style="background:#182848;padding:18px 24px;">
                        <h2 style="color:#fff;margin:0;font-size:16px;">Avaria registada via QR Code</h2>
                      </div>
                      <div style="padding:20px 24px;background:#f7f9fe;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                          <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;width:35%;">Equipamento</td><td style="padding:8px;">' . htmlspecialchars($dados_av['nomeequi'] ?? '') . '</td></tr>
                          <tr><td style="padding:8px;font-weight:bold;color:#555;">Sala</td><td style="padding:8px;">' . htmlspecialchars($dados_av['nomsala'] ?? '') . '</td></tr>
                          <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;">Escola</td><td style="padding:8px;">' . htmlspecialchars($dados_av['nome_escola'] ?? '') . '</td></tr>
                          <tr><td style="padding:8px;font-weight:bold;color:#555;">Autor</td><td style="padding:8px;">' . htmlspecialchars($email_autor) . '</td></tr>
                          <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;">Data</td><td style="padding:8px;">' . date('d/m/Y') . '</td></tr>
                          <tr><td style="padding:8px;font-weight:bold;color:#555;">Descrição</td><td style="padding:8px;">' . nl2br(htmlspecialchars($dados_av['avaria'] ?? '')) . '</td></tr>
                        </table>
                      </div>
                      <div style="background:#eee;padding:10px 24px;font-size:11px;color:#888;text-align:center;">Este email foi gerado automaticamente pelo SGEI.</div>
                    </div>';
                    $mail->send();
                } catch (\Exception $e) {
                    error_log('avaria_qr email error: ' . $e->getMessage());
                    // Não bloquear o sucesso se o email falhar
                }
            }

            mysqli_close($db);
            $sucesso = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Reportar Avaria — SGEI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Fontes carregadas globalmente em head.php via tokens.css -->
    <style>
        :root {
            --primary:    #4b6cb7;
            --primary-dk: #182848;
            --accent:     #507feb;
            --success:    #1cc88a;
            --danger:     #e74a3b;
            --bg:         #f0f4fb;
            --surface:    #ffffff;
            --border:     #e3e8f4;
            --text:       #1e2a45;
            --muted:      #7b88a0;
            --radius:     12px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px 16px;
        }
        .card-qr {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: 0 4px 24px rgba(75,108,183,.14);
            max-width: 480px; width: 100%;
            overflow: hidden;
        }
        .card-header-qr {
            background: var(--primary-dk);
            padding: 22px 28px 18px;
            color: #fff;
        }
        .card-header-qr .logo-gei {
            font-size: .75rem; font-weight: 700;
            letter-spacing: 1px; opacity: .7;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .card-header-qr h1 {
            font-size: 1.25rem; font-weight: 700;
            margin: 0 0 4px; color: #fff;
        }
        .card-header-qr p {
            font-size: .82rem; margin: 0;
            color: rgba(255,255,255,.65);
        }
        .equip-badge {
            display: flex; align-items: center; gap: 12px;
            background: #f0f4fb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .equip-badge .icon-wrap {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .equip-badge .icon-wrap i { color: #fff; font-size: .9rem; }
        .equip-badge .eq-nome { font-weight: 700; font-size: .92rem; color: var(--text); }
        .equip-badge .eq-sala { font-size: .78rem; color: var(--muted); margin-top: 2px; }
        .card-body-qr { padding: 24px 28px; }
        .form-label-qr {
            font-size: .78rem; font-weight: 700;
            color: var(--muted); text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: 6px; display: block;
        }
        .form-control-qr {
            width: 100%; border: 1.5px solid var(--border);
            border-radius: 8px; padding: 10px 14px;
            font-family: inherit; font-size: .9rem;
            color: var(--text); background: #f7f9fe;
            transition: border .2s; margin-bottom: 16px;
        }
        .form-control-qr:focus {
            outline: none; border-color: var(--accent);
            background: #fff;
        }
        textarea.form-control-qr { resize: vertical; min-height: 100px; }
        .btn-submit-qr {
            width: 100%; background: var(--primary); color: #fff;
            border: none; border-radius: 8px; padding: 12px;
            font-family: inherit; font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: background .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit-qr:hover { background: var(--accent); transform: translateY(-1px); }
        .alert-qr {
            padding: 12px 16px; border-radius: 8px;
            font-size: .85rem; font-weight: 600;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }
        .alert-danger-qr  { background: #fde8e6; color: #c0392b; border: 1px solid #f5c6c6; }
        .alert-success-qr { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
        .error-page { text-align: center; padding: 40px 28px; }
        .error-page i { font-size: 2.5rem; color: var(--muted); margin-bottom: 12px; display: block; }
        .char-count { font-size: .72rem; color: var(--muted); text-align: right; margin-top: -12px; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="card-qr">
    <div class="card-header-qr">
        <div class="logo-gei">SGEI — Sistema de Gestão de Equipamento Informático</div>
        <h1><i class="fas fa-exclamation-triangle" style="font-size:1rem;margin-right:8px;opacity:.85;"></i>Reportar Avaria</h1>
        <p>Preencha o formulário para registar a avaria deste equipamento</p>
    </div>

    <div class="card-body-qr">

    <?php if ($erro_params): ?>
        <div class="error-page">
            <i class="fas fa-qrcode"></i>
            <p style="font-weight:700;color:var(--text);">QR Code inválido ou expirado</p>
            <p style="font-size:.85rem;color:var(--muted);">Por favor utilize o QR Code afixado no equipamento.</p>
        </div>

    <?php elseif ($sucesso): ?>
        <div class="alert-qr alert-success-qr">
            <i class="fas fa-check-circle"></i>
            Avaria registada com sucesso! Receberá um email de confirmação.
        </div>
        <p style="font-size:.85rem;color:var(--muted);text-align:center;">
            Pode fechar esta página.
        </p>

    <?php else: ?>

        <?php if ($erro_msg): ?>
        <div class="alert-qr alert-danger-qr">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($erro_msg); ?>
        </div>
        <?php endif; ?>

        <!-- Identificação do equipamento (só leitura) -->
        <div class="equip-badge">
            <div class="icon-wrap"><i class="fas fa-desktop"></i></div>
            <div>
                <div class="eq-nome"><?php echo htmlspecialchars($info['nomeequi']); ?></div>
                <div class="eq-sala">
                    <i class="fas fa-door-open" style="font-size:.7rem;"></i>
                    <?php echo htmlspecialchars($info['nomsala']); ?>
                    &nbsp;·&nbsp;
                    <?php echo htmlspecialchars($info['nome_escola']); ?>
                </div>
            </div>
        </div>

        <?php $action_avaria = '?eq=' . $id_equip . '&sala=' . $id_sala . '&esc=' . $id_esc . '&cod=' . $_codigo; ?>
        <form method="POST" action="<?php echo htmlspecialchars($action_avaria); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_qr']); ?>">

            <label class="form-label-qr" for="email_autor">O seu email</label>
            <input type="email" id="email_autor" name="email_autor" class="form-control-qr"
                   placeholder="nome@exemplo.pt" required
                   value="<?php echo htmlspecialchars($_POST['email_autor'] ?? ''); ?>">

            <label class="form-label-qr" for="avaria">Descrição da avaria</label>
            <textarea id="avaria" name="avaria" class="form-control-qr"
                      placeholder="Descreva o problema que observou..."
                      required maxlength="1000"
                      oninput="document.getElementById('cc').textContent=this.value.length+'/1000'"
                      ><?php echo htmlspecialchars($_POST['avaria'] ?? ''); ?></textarea>
            <div class="char-count"><span id="cc">0/1000</span></div>

            <button type="submit" class="btn-submit-qr">
                <i class="fas fa-paper-plane"></i> Submeter avaria
            </button>
        </form>

        <p style="font-size:.72rem;color:var(--muted);text-align:center;margin-top:16px;">
            O seu email será utilizado apenas para confirmação desta avaria.
        </p>

    <?php endif; ?>

    <!-- Botão voltar ao QR acesso -->
    <?php
    $url_voltar = SVRURL . 'qr_acesso.php'
        . '?eq='   . $id_equip
        . '&sala='  . $id_sala
        . '&esc='   . $id_esc
        . '&cod='   . $_codigo;
    ?>
    <div style="margin-top:20px;text-align:center;">
        <a href="<?php echo htmlspecialchars($url_voltar); ?>"
           style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--primary);text-decoration:none;padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:#f7f9fe;transition:background .2s;"
           onmouseover="this.style.background='#e8ecf4'" onmouseout="this.style.background='#f7f9fe'">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    </div>
</div>
</body>
</html>
