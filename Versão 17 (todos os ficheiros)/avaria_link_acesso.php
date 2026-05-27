<?php
// ============================================================
// avaria_link_acesso.php — GEI
// Página de destino do link enviado por email.
//   1. Valida token (?t=TOKEN&cod=CODIGO)
//   2. Se válido: mostra formulário completo de avaria
//   3. Se inválido/expirado: mostra mensagem de erro
//   4. Após submissão: regista avaria e envia email de confirmação
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

// ── Ler parâmetros do URL ─────────────────────────────────────────────────────
$token_raw = $_GET['t']   ?? '';
$cod       = isset($_GET['cod']) ? (int)$_GET['cod'] : 0;

$token_invalido  = false;
$token_expirado  = false;
$token_usado     = false;
$email_autor     = '';
$token_hash      = '';

if (empty($token_raw) || $cod <= 0) {
    $token_invalido = true;
} else {
    $token_hash = hash('sha256', $token_raw);

    try {
        $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $db0->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        error_log('avaria_link_acesso BD: ' . $e->getMessage());
        $token_invalido = true;
    }

    if (!$token_invalido) {
        $stmtTok = $db0->prepare("
            SELECT email, usado,
                   CASE WHEN expira_em < NOW() THEN 1 ELSE 0 END AS expirado
            FROM avaria_link_tokens
            WHERE token_hash = ? AND codigo = ?
            LIMIT 1
        ");
        $stmtTok->bind_param('si', $token_hash, $cod);
        $stmtTok->execute();
        $rowTok = $stmtTok->get_result()->fetch_assoc();
        $stmtTok->close();

        if (!$rowTok) {
            $token_invalido = true;
        } elseif ($rowTok['usado']) {
            $token_usado = true;
            $db0->close();
        } elseif ($rowTok['expirado']) {
            $token_expirado = true;
            $db0->close();
        } else {
            $email_autor = $rowTok['email'];
            // db0 permanece aberto — necessário para marcar o token como usado após submissão
        }
    }
}

$token_ok = !$token_invalido && !$token_expirado && !$token_usado;

// ── Se token válido: obter dados da BD da instituição ─────────────────────────
$db   = null;
$_nomebd   = '';
$_serverbd = '';

if ($token_ok) {
    $stmtBD = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
    $stmtBD->bind_param('i', $cod);
    $stmtBD->execute();
    $stmtBD->bind_result($_nomebd, $_serverbd);
    $stmtBD->fetch();
    $stmtBD->close();
    $db0->close();

    if (empty($_nomebd)) {
        $token_invalido = true;
        $token_ok = false;
    } else {
        try {
            $db = new mysqli($_serverbd, DB_USERNAME, DB_PASSWORD, $_nomebd);
            $db->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('avaria_link_acesso main BD: ' . $e->getMessage());
            $token_invalido = true;
            $token_ok = false;
        }
    }
}

// ── Carregar lista de equipamentos e salas (para o formulário) ─────────────────
$equipamentos = [];
$salas        = [];

if ($token_ok && $db) {
    $resEq = $db->query("
        SELECT eq.id, eq.nomeequi, s.nome AS nomsala, eq.id_sala, s.id_escola
        FROM equipamento eq
        INNER JOIN salas s ON s.id = eq.id_sala
        ORDER BY s.nome, eq.nomeequi
    ");
    while ($r = $resEq->fetch_assoc()) {
        $equipamentos[] = $r;
    }

    $resSl = $db->query("SELECT id, nome FROM salas ORDER BY nome");
    while ($r = $resSl->fetch_assoc()) {
        $salas[] = $r;
    }
}

// ── Token CSRF para o formulário ──────────────────────────────────────────────
if ($token_ok) {
    if (empty($_SESSION['csrf_alacesso']) || empty($_SESSION['csrf_alacesso_time']) ||
        (time() - $_SESSION['csrf_alacesso_time']) > 1800) {
        $_SESSION['csrf_alacesso']      = bin2hex(random_bytes(32));
        $_SESSION['csrf_alacesso_time'] = time();
    }
}

// ── Processar POST do formulário de avaria ────────────────────────────────────
$sucesso  = false;
$erro_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_ok && $db) {

    // CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_alacesso'], $_POST['csrf_token'])) {
        $erro_msg = 'Token de segurança inválido. Por favor recarregue a página.';
    } else {

        // Re-validar token (evitar dupla submissão)
        try {
            $db0re = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            $db0re->set_charset('utf8mb4');
            $stmtRe = $db0re->prepare("
                SELECT id FROM avaria_link_tokens
                WHERE token_hash = ? AND usado = 0 AND expira_em > NOW()
                LIMIT 1
            ");
            $stmtRe->bind_param('s', $token_hash);
            $stmtRe->execute();
            $rowRe = $stmtRe->get_result()->fetch_assoc();
            $stmtRe->close();
        } catch (mysqli_sql_exception $e) {
            $rowRe = null;
        }

        if (!$rowRe) {
            $erro_msg = 'Este link já foi utilizado ou expirou. Por favor solicite um novo.';
        } else {

            $id_equip  = isset($_POST['id_equip'])  ? (int)$_POST['id_equip']  : 0;
            $id_sala   = isset($_POST['id_sala'])    ? (int)$_POST['id_sala']   : 0;
            $id_escola = isset($_POST['id_escola'])  ? (int)$_POST['id_escola'] : 0;
            $descricao = trim($_POST['avaria'] ?? '');
            $data_hoje = date('Y-m-d');

            if ($id_equip <= 0) {
                $erro_msg = 'Por favor selecione o equipamento com avaria.';
            } elseif (mb_strlen($descricao) < 10) {
                $erro_msg = 'A descrição da avaria deve ter pelo menos 10 caracteres.';
            } else {
                // Verificar que equipamento pertence à sala/escola indicadas
                $stmtChk = $db->prepare("
                    SELECT eq.id FROM equipamento eq
                    INNER JOIN salas s    ON s.id  = eq.id_sala
                    INNER JOIN escolas es ON es.id = s.id_escola
                    WHERE eq.id = ? AND s.id = ? AND es.id = ?
                    LIMIT 1
                ");
                $stmtChk->bind_param('iii', $id_equip, $id_sala, $id_escola);
                $stmtChk->execute();
                $chkOk = $stmtChk->get_result()->fetch_assoc();
                $stmtChk->close();

                if (!$chkOk) {
                    $erro_msg = 'Equipamento não reconhecido. Por favor selecione novamente.';
                } else {
                    // Ano letivo e período
                    $resAno = $db->query("SELECT MAX(ano_lectivo) FROM periodos");
                    $ano    = $resAno->fetch_row()[0] ?? '';

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
                    $stmtAv = $db->prepare("
                        INSERT INTO avarias_reparacoes
                            (id_equi, id_sala, id_escola, autoravaria, dataavaria, avaria, ano_letivo, periodo)
                        VALUES (?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, ?, ?)
                    ");
                    $bindTypes = 'iiissss' . ($per !== null ? 'i' : 's');
                    $stmtAv->bind_param(
                        $bindTypes,
                        $id_equip, $id_sala, $id_escola,
                        $email_autor, $data_hoje,
                        $descricao, $ano, $per
                    );
                    $stmtAv->execute();
                    $id_avaria = $db->insert_id;
                    $stmtAv->close();

                    // Marcar token como usado (one-time use)
                    $stmtUsed = $db0re->prepare("
                        UPDATE avaria_link_tokens SET usado = 1 WHERE token_hash = ?
                    ");
                    $stmtUsed->bind_param('s', $token_hash);
                    $stmtUsed->execute();
                    $stmtUsed->close();
                    $db0re->close();

                    // Renovar CSRF
                    $_SESSION['csrf_alacesso']      = bin2hex(random_bytes(32));
                    $_SESSION['csrf_alacesso_time'] = time();

                    // Email de confirmação
                    if ($id_avaria) {
                        try {
                            $stmtEm = $db->prepare("
                                SELECT ar.avaria, ar.dataavaria, ar.autoravaria,
                                       eq.nomeequi, s.nome AS nomsala, es.nome_escola
                                FROM avarias_reparacoes ar
                                INNER JOIN equipamento eq ON eq.id = ar.id_equi
                                INNER JOIN salas s         ON s.id  = ar.id_sala
                                INNER JOIN escolas es      ON es.id = ar.id_escola
                                WHERE ar.id = ? LIMIT 1
                            ");
                            $stmtEm->bind_param('i', $id_avaria);
                            $stmtEm->execute();
                            $dadosAv = $stmtEm->get_result()->fetch_assoc();
                            $stmtEm->close();

                            $stmtDest = $db->prepare("SELECT email FROM utilizadores WHERE tipo = 1 OR tipo = 3");
                            $stmtDest->execute();
                            $destEmails = [];
                            $resDest = $stmtDest->get_result();
                            while ($d = $resDest->fetch_row()) { $destEmails[] = $d[0]; }
                            $stmtDest->close();

                            $mail = new PHPMailer(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();
                            include('email_settings.php');
                            include('dados_enviar_email.php');
                            $mail->isHTML(true);
                            $mail->Subject = 'Avaria registada — SGEI';
                            $mail->addAddress($email_autor);
                            foreach ($destEmails as $de) { $mail->addAddress($de); }
                            $mail->Body = '
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
  <div style="background:#182848;padding:18px 24px;">
    <h2 style="color:#fff;margin:0;font-size:16px;">Avaria registada</h2>
  </div>
  <div style="padding:20px 24px;background:#f7f9fe;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;width:35%;">Equipamento</td><td style="padding:8px;">' . htmlspecialchars($dadosAv['nomeequi'] ?? '') . '</td></tr>
      <tr><td style="padding:8px;font-weight:bold;color:#555;">Sala</td><td style="padding:8px;">' . htmlspecialchars($dadosAv['nomsala'] ?? '') . '</td></tr>
      <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;">Escola</td><td style="padding:8px;">' . htmlspecialchars($dadosAv['nome_escola'] ?? '') . '</td></tr>
      <tr><td style="padding:8px;font-weight:bold;color:#555;">Autor</td><td style="padding:8px;">' . htmlspecialchars($email_autor) . '</td></tr>
      <tr style="background:#eef2f7;"><td style="padding:8px;font-weight:bold;color:#555;">Data</td><td style="padding:8px;">' . date('d/m/Y') . '</td></tr>
      <tr><td style="padding:8px;font-weight:bold;color:#555;">Descrição</td><td style="padding:8px;">' . nl2br(htmlspecialchars($dadosAv['avaria'] ?? '')) . '</td></tr>
    </table>
  </div>
  <div style="background:#eee;padding:10px 24px;font-size:11px;color:#888;text-align:center;">Este email foi gerado automaticamente pelo SGEI.</div>
</div>';
                            $mail->send();
                        } catch (\Exception $e) {
                            error_log('avaria_link_acesso email error: ' . $e->getMessage());
                        }
                    }

                    mysqli_close($db);
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Reportar Avaria — SGEI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            max-width: 520px; width: 100%;
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
            font-size: 1.2rem; font-weight: 700;
            margin: 0 0 4px; color: #fff;
        }
        .card-header-qr p {
            font-size: .82rem; margin: 0;
            color: rgba(255,255,255,.65);
        }
        .card-body-qr { padding: 24px 28px; }
        .author-badge {
            display: flex; align-items: center; gap: 10px;
            background: #eafaf1;
            border: 1px solid #a9dfbf;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: .85rem; color: #1e8449;
            font-weight: 600;
        }
        .form-label-qr {
            font-size: .78rem; font-weight: 700;
            color: var(--muted); text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: 6px; display: block;
        }
        .form-control-qr {
            width: 100%; border: 1.5px solid var(--border);
            border-radius: 8px; padding: 10px 14px;
            font-size: .9rem; color: var(--text);
            background: #f7f9fe;
            transition: border .2s; margin-bottom: 16px;
            appearance: auto;
        }
        .form-control-qr:focus {
            outline: none; border-color: var(--accent);
            background: #fff;
        }
        textarea.form-control-qr { resize: vertical; min-height: 100px; }
        .btn-submit-qr {
            width: 100%; background: var(--primary); color: #fff;
            border: none; border-radius: 8px; padding: 12px;
            font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: background .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit-qr:hover { background: var(--accent); transform: translateY(-1px); }
        .alert-qr {
            padding: 12px 16px; border-radius: 8px;
            font-size: .85rem; font-weight: 600;
            margin-bottom: 16px; display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-danger-qr  { background: #fde8e6; color: #c0392b; border: 1px solid #f5c6c6; }
        .alert-success-qr { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
        .alert-warning-qr { background: #fff8e1; color: #856404; border: 1px solid #ffe082; }
        .error-page { text-align: center; padding: 36px 28px; }
        .error-page .icon-big { font-size: 2.5rem; color: var(--muted); margin-bottom: 12px; display: block; }
        .char-count { font-size: .72rem; color: var(--muted); text-align: right; margin-top: -12px; margin-bottom: 16px; }
        .equip-sub { font-size: .78rem; color: var(--muted); margin-top: -12px; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="card-qr">
    <div class="card-header-qr">
        <div class="logo-gei">SGEI — Sistema de Gestão de Equipamento Informático</div>
        <h1><i class="fas fa-exclamation-triangle" style="font-size:1rem;margin-right:8px;opacity:.85;"></i>Reportar Avaria</h1>
        <p>Formulário de reporte de avaria</p>
    </div>

    <div class="card-body-qr">

    <?php if ($token_invalido): ?>
        <div class="error-page">
            <i class="fas fa-unlink icon-big"></i>
            <p style="font-weight:700;color:var(--text);">Link inválido</p>
            <p style="font-size:.85rem;color:var(--muted);">Este link não existe ou foi mal copiado. Solicite um novo link através da página de reporte.</p>
        </div>

    <?php elseif ($token_expirado): ?>
        <div class="error-page">
            <i class="fas fa-clock icon-big"></i>
            <p style="font-weight:700;color:var(--text);">Link expirado</p>
            <p style="font-size:.85rem;color:var(--muted);">Este link expirou (validade de 30 minutos). Por favor solicite um novo link.</p>
            <a href="avaria_link.php?cod=<?php echo (int)$cod; ?>"
               style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;
                      color:#fff;background:var(--primary);text-decoration:none;padding:10px 20px;
                      border-radius:8px;margin-top:8px;">
               <i class="fas fa-redo"></i> Solicitar novo link
            </a>
        </div>

    <?php elseif ($token_usado): ?>
        <div class="error-page">
            <i class="fas fa-check-double icon-big" style="color:var(--success);"></i>
            <p style="font-weight:700;color:var(--text);">Link já utilizado</p>
            <p style="font-size:.85rem;color:var(--muted);">Este link já foi usado para registar uma avaria. Cada link só pode ser utilizado uma vez.</p>
            <a href="avaria_link.php?cod=<?php echo (int)$cod; ?>"
               style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;
                      color:#fff;background:var(--primary);text-decoration:none;padding:10px 20px;
                      border-radius:8px;margin-top:8px;">
               <i class="fas fa-plus"></i> Reportar outra avaria
            </a>
        </div>

    <?php elseif ($sucesso): ?>
        <div class="alert-qr alert-success-qr">
            <i class="fas fa-check-circle fa-lg" style="margin-top:1px;"></i>
            <div>
                <strong>Avaria registada com sucesso!</strong><br>
                <span style="font-weight:400;">Receberá um email de confirmação. A equipa de manutenção foi notificada.</span>
            </div>
        </div>
        <p style="font-size:.85rem;color:var(--muted);text-align:center;">
            Pode fechar esta página.
        </p>

    <?php else: ?>

        <?php if ($erro_msg): ?>
        <div class="alert-qr alert-danger-qr">
            <i class="fas fa-exclamation-circle" style="margin-top:1px;"></i>
            <?php echo htmlspecialchars($erro_msg); ?>
        </div>
        <?php endif; ?>

        <!-- Identificação do utilizador -->
        <div class="author-badge">
            <i class="fas fa-envelope-open-text"></i>
            A submeter como: <strong><?php echo htmlspecialchars($email_autor); ?></strong>
        </div>

        <form method="POST" action="avaria_link_acesso.php?t=<?php echo urlencode($token_raw); ?>&cod=<?php echo (int)$cod; ?>" id="frmAvaria">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_alacesso']); ?>">

            <!-- Sala -->
            <label class="form-label-qr" for="sel_sala">Sala</label>
            <select id="sel_sala" class="form-control-qr" onchange="filtrarEquipamentos()">
                <option value="">— Selecione a sala —</option>
                <?php foreach ($salas as $s): ?>
                <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['nome']); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Equipamento -->
            <label class="form-label-qr" for="sel_equip">Equipamento</label>
            <select id="sel_equip" name="id_equip" class="form-control-qr" required
                    onchange="preencherSalaEscola()">
                <option value="">— Selecione primeiro a sala —</option>
                <?php foreach ($equipamentos as $eq): ?>
                <option value="<?php echo (int)$eq['id']; ?>"
                        data-sala="<?php echo (int)$eq['id_sala']; ?>"
                        data-escola="<?php echo (int)$eq['id_escola']; ?>"
                        style="display:none">
                    <?php echo htmlspecialchars($eq['nomeequi']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <p class="equip-sub" id="equip-hint" style="display:none;">
                <i class="fas fa-info-circle"></i> Se o equipamento não aparecer, escolha outra sala ou descreva-o na descrição.
            </p>

            <input type="hidden" id="id_sala"   name="id_sala"   value="">
            <input type="hidden" id="id_escola" name="id_escola" value="">

            <!-- Descrição -->
            <label class="form-label-qr" for="avaria">Descrição da avaria</label>
            <textarea id="avaria" name="avaria" class="form-control-qr"
                      placeholder="Descreva o problema observado (ex: monitor sem imagem, teclado com teclas presas, computador não liga…)"
                      required maxlength="1000"
                      oninput="document.getElementById('cc').textContent=this.value.length+'/1000'"
                      ><?php echo htmlspecialchars($_POST['avaria'] ?? ''); ?></textarea>
            <div class="char-count"><span id="cc">0/1000</span></div>

            <button type="submit" class="btn-submit-qr" id="btnSubmit">
                <i class="fas fa-paper-plane"></i> Submeter avaria
            </button>
        </form>

        <p style="font-size:.72rem;color:var(--muted);text-align:center;margin-top:14px;">
            Este link é de uso único e expira 30 minutos após o envio.<br>
            O seu email será utilizado apenas para confirmação desta avaria.
        </p>

    <?php endif; ?>

    </div><!-- /.card-body-qr -->
</div><!-- /.card-qr -->

<script>
// Dados de equipamentos indexados por sala
const equips = <?php
    $byRoom = [];
    foreach ($equipamentos as $eq) {
        $byRoom[$eq['id_sala']][] = [
            'id'       => (int)$eq['id'],
            'nome'     => htmlspecialchars($eq['nomeequi'], ENT_QUOTES),
            'id_escola'=> (int)$eq['id_escola'],
        ];
    }
    echo json_encode($byRoom, JSON_UNESCAPED_UNICODE);
?>;

function filtrarEquipamentos() {
    const salaId  = parseInt(document.getElementById('sel_sala').value) || 0;
    const selEq   = document.getElementById('sel_equip');
    const hint    = document.getElementById('equip-hint');

    selEq.innerHTML = '<option value="">— Selecione o equipamento —</option>';
    document.getElementById('id_sala').value   = salaId || '';
    document.getElementById('id_escola').value = '';

    if (!salaId || !equips[salaId]) {
        selEq.innerHTML = '<option value="">— Selecione primeiro a sala —</option>';
        hint.style.display = 'none';
        return;
    }

    equips[salaId].forEach(eq => {
        const opt = document.createElement('option');
        opt.value            = eq.id;
        opt.dataset.escola   = eq.id_escola;
        opt.textContent      = eq.nome;
        selEq.appendChild(opt);
    });

    hint.style.display = 'block';
}

function preencherSalaEscola() {
    const selEq  = document.getElementById('sel_equip');
    const opt    = selEq.options[selEq.selectedIndex];
    document.getElementById('id_escola').value = opt?.dataset?.escola || '';
}

document.getElementById('frmAvaria')?.addEventListener('submit', function(e) {
    const equip = document.getElementById('sel_equip').value;
    if (!equip) {
        e.preventDefault();
        alert('Por favor selecione o equipamento com avaria.');
        return;
    }
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-spinner fa-spin"></i> A submeter…';
});
</script>
</body>
</html>
