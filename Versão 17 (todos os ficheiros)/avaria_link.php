<?php
ob_start();
// ============================================================
// avaria_link.php — GEI
// Página pública: solicitar link de acesso ao formulário de avaria por email.
// Não requer autenticação.
// URL: avaria_link.php              (resolve automaticamente se 1 instituição)
//      avaria_link.php?cod=CODIGO   (acesso direto com código conhecido)
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('svrurl.php');
include_once('config_serverbd_settings.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$_codigo = isset($_GET['cod']) ? (int)$_GET['cod'] : 0;

// Ligar à BD de settings
try {
    $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $db0->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('avaria_link BD settings error: ' . $e->getMessage());
    die('Erro de configuração. Contacte o administrador.');
}

// Se não veio código no URL, tentar resolver automaticamente
if ($_codigo <= 0) {
    $resCount = $db0->query("SELECT COUNT(*) FROM settingsbd");
    $total    = (int)$resCount->fetch_row()[0];

    if ($total === 1) {
        // Apenas uma instituição — usar automaticamente
        $resAuto = $db0->query("SELECT codigo FROM settingsbd LIMIT 1");
        $_codigo = (int)$resAuto->fetch_row()[0];
    } elseif ($total > 1) {
        // Múltiplas instituições — mostrar selector
        $resInst  = $db0->query("SELECT codigo, nome_esc_inst FROM settingsbd ORDER BY nome_esc_inst");
        $inst_list = [];
        while ($r = $resInst->fetch_assoc()) { $inst_list[] = $r; }
        $db0->close();
        // Renderizar página de seleção de instituição
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
        :root { --primary:#4b6cb7; --primary-dk:#182848; --bg:#f0f4fb; --surface:#fff; --border:#e3e8f4; --text:#1e2a45; --muted:#7b88a0; --radius:12px; }
        * { box-sizing:border-box; }
        body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:var(--bg); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px 16px; }
        .card-qr { background:var(--surface); border-radius:var(--radius); box-shadow:0 4px 24px rgba(75,108,183,.14); max-width:440px; width:100%; overflow:hidden; }
        .card-header-qr { background:var(--primary-dk); padding:22px 28px 18px; color:#fff; }
        .card-header-qr .logo-gei { font-size:.75rem; font-weight:700; letter-spacing:1px; opacity:.7; text-transform:uppercase; margin-bottom:8px; }
        .card-header-qr h1 { font-size:1.2rem; font-weight:700; margin:0 0 4px; color:#fff; }
        .card-header-qr p { font-size:.82rem; margin:0; color:rgba(255,255,255,.65); }
        .card-body-qr { padding:24px 28px; }
        .inst-btn { display:flex; align-items:center; gap:12px; width:100%; background:#f7f9fe; border:1.5px solid var(--border); border-radius:8px; padding:13px 16px; margin-bottom:10px; cursor:pointer; text-decoration:none; color:var(--text); font-size:.9rem; font-weight:600; transition:background .2s, border-color .2s; }
        .inst-btn:hover { background:#e8ecf4; border-color:var(--primary); color:var(--primary); text-decoration:none; }
        .inst-btn i { color:var(--primary); font-size:1rem; flex-shrink:0; }
    </style>
</head>
<body>
<div class="card-qr">
    <div class="card-header-qr">
        <div class="logo-gei">SGEI — Sistema de Gestão de Equipamento Informático</div>
        <h1><i class="fas fa-envelope-open-text" style="font-size:1rem;margin-right:8px;opacity:.85;"></i>Reportar Avaria</h1>
        <p>Selecione a sua instituição</p>
    </div>
    <div class="card-body-qr">
        <?php foreach ($inst_list as $inst): ?>
        <a href="avaria_link.php?cod=<?php echo (int)$inst['codigo']; ?>" class="inst-btn">
            <i class="fas fa-school"></i>
            <?php echo htmlspecialchars($inst['nome_esc_inst']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
        <?php
        exit;
    } else {
        $db0->close();
        die('Nenhuma instituição configurada. Contacte o administrador.');
    }
}

// Verificar que o código existe
$stmt0 = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
$stmt0->bind_param('i', $_codigo);
$stmt0->execute();
$stmt0->bind_result($_nomebd, $_serverbd);
$_found = $stmt0->fetch();
$stmt0->close();
$db0->close();

if (!$_found || empty($_nomebd)) {
    die('Código de instituição inválido.');
}

// Gerar token CSRF
if (empty($_SESSION['csrf_avlink']) || empty($_SESSION['csrf_avlink_time']) ||
    (time() - $_SESSION['csrf_avlink_time']) > 1800) {
    $_SESSION['csrf_avlink']      = bin2hex(random_bytes(32));
    $_SESSION['csrf_avlink_time'] = time();
}

$enviado  = false;
$erro_msg = '';

// Mensagem de feedback vinda da sessão (após redirect de avaria_link_send.php)
if (!empty($_SESSION['avlink_feedback'])) {
    $feedback = $_SESSION['avlink_feedback'];
    unset($_SESSION['avlink_feedback']);
    if ($feedback === 'ok') {
        $enviado = true;
    } else {
        $erro_msg = $feedback;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Reportar Avaria por Email — SGEI</title>
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
            max-width: 440px; width: 100%;
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
        .info-box {
            display: flex; align-items: flex-start; gap: 12px;
            background: #f0f4fb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 22px;
            font-size: .85rem; color: var(--muted);
            line-height: 1.5;
        }
        .info-box i { color: var(--accent); margin-top: 2px; flex-shrink: 0; }
        .form-label-qr {
            font-size: .78rem; font-weight: 700;
            color: var(--muted); text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: 6px; display: block;
        }
        .form-control-qr {
            width: 100%; border: 1.5px solid var(--border);
            border-radius: 8px; padding: 11px 14px;
            font-size: .9rem; color: var(--text);
            background: #f7f9fe;
            transition: border .2s; margin-bottom: 18px;
        }
        .form-control-qr:focus {
            outline: none; border-color: var(--accent);
            background: #fff;
        }
        .btn-submit-qr {
            width: 100%; background: var(--primary); color: #fff;
            border: none; border-radius: 8px; padding: 12px;
            font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: background .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit-qr:hover { background: var(--accent); transform: translateY(-1px); }
        .btn-submit-qr:disabled { background: var(--muted); cursor: not-allowed; transform: none; }
        .alert-qr {
            padding: 12px 16px; border-radius: 8px;
            font-size: .85rem; font-weight: 600;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }
        .alert-danger-qr  { background: #fde8e6; color: #c0392b; border: 1px solid #f5c6c6; }
        .alert-success-qr { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
        .privacy-note {
            font-size: .72rem; color: var(--muted);
            text-align: center; margin-top: 14px; line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="card-qr">
    <div class="card-header-qr">
        <div class="logo-gei">SGEI — Sistema de Gestão de Equipamento Informático</div>
        <h1><i class="fas fa-envelope-open-text" style="font-size:1rem;margin-right:8px;opacity:.85;"></i>Reportar Avaria</h1>
        <p>Receba um link no seu email para submeter a avaria</p>
    </div>

    <div class="card-body-qr">

    <?php if ($enviado): ?>

        <div class="alert-qr alert-success-qr">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>
                <strong>Email enviado!</strong><br>
                <span style="font-weight:400;">Verifique a sua caixa de correio (e a pasta de spam). O link é válido durante <strong>30 minutos</strong>.</span>
            </div>
        </div>
        <p class="privacy-note">
            Pode fechar esta janela e abrir o link no email recebido.
        </p>

    <?php else: ?>

        <?php if ($erro_msg): ?>
        <div class="alert-qr alert-danger-qr">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($erro_msg); ?>
        </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <span>Introduza o seu endereço de email institucional. Iremos enviar-lhe um link único para reportar a avaria — sem necessidade de conta ou password.</span>
        </div>

        <form method="POST" action="avaria_link_send.php" id="frmLink">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_avlink']); ?>">
            <input type="hidden" name="cod" value="<?php echo (int)$_codigo; ?>">

            <label class="form-label-qr" for="email_avlink">O seu email</label>
            <input type="email" id="email_avlink" name="email" class="form-control-qr"
                   placeholder="nome@exemplo.pt" required autofocus
                   autocomplete="email">

            <button type="submit" class="btn-submit-qr" id="btnEnviar">
                <i class="fas fa-paper-plane"></i>
                <span id="btnLabel">Enviar link por email</span>
            </button>
        </form>

        <p class="privacy-note">
            O seu email será utilizado apenas para o envio deste link de acesso.<br>
            Não é criada qualquer conta.
        </p>

    <?php endif; ?>

    <div style="margin-top:18px;text-align:center;">
        <a href="<?php echo SVRURL; ?>index.php"
           style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:var(--muted);text-decoration:none;padding:8px 16px;border:1.5px solid var(--border);border-radius:8px;background:#f7f9fe;transition:background .2s;"
           onmouseover="this.style.background='#e8ecf4'" onmouseout="this.style.background='#f7f9fe'">
            <i class="fas fa-arrow-left"></i> Voltar ao início
        </a>
    </div>

    </div><!-- /.card-body-qr -->
</div><!-- /.card-qr -->

<script>
document.getElementById('frmLink')?.addEventListener('submit', function() {
    const btn   = document.getElementById('btnEnviar');
    const label = document.getElementById('btnLabel');
    btn.disabled = true;
    label.textContent = 'A enviar…';
});
</script>
</body>
</html>
