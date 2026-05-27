<?php
// ============================================================
// qr_acesso.php — GEI
// Página intermédia para QR Code sem sessão ativa.
// Permite acesso à ficha do equipamento via código definido
// nas settings, ou redireciona para registo de avaria.
// ============================================================

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

include_once('svrurl.php');
include_once('config_serverbd_settings.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ── Parâmetros do QR Code ─────────────────────────────────────────────────────
$id_equip = isset($_GET['eq'])   ? (int)$_GET['eq']   : 0;
$id_sala  = isset($_GET['sala']) ? (int)$_GET['sala']  : 0;
$id_esc   = isset($_GET['esc'])  ? (int)$_GET['esc']   : 0;
$_codigo  = isset($_GET['cod'])  ? (int)$_GET['cod']   : 0;

if ($id_equip <= 0 || $_codigo <= 0) {
    die('QR Code inválido — parâmetros em falta.');
}

// ── CSRF ──────────────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$erro = '';

// ── Processar POST ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Pedido inválido.');
    }

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'ficha') {
        $pin_inserido = trim($_POST['pin'] ?? '');

        // Buscar código da tabela settings
        // 1. Ligar à BD de settings e obter nomebd + serverbd pelo código do QR
        try {
            $db0 = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            $db0->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('qr_acesso settings BD error: ' . $e->getMessage());
            $erro = 'Erro de configuração. Contacte o administrador.';
            goto fim_post;
        }

        $stmtBd = $db0->prepare("SELECT nomebd, serverbd FROM settingsbd WHERE codigo = ? LIMIT 1");
        $stmtBd->bind_param('i', $_codigo);
        $stmtBd->execute();
        $stmtBd->bind_result($_nomebd, $_serverbd);
        $_found = $stmtBd->fetch();
        $stmtBd->close();
        $db0->close();

        if (!$_found || empty($_nomebd)) {
            $erro = 'QR Code inválido. Por favor use o QR Code afixado no equipamento.';
            goto fim_post;
        }

        // 2. Ligar à BD da escola e verificar o código de acesso
        try {
            $db = new mysqli($_serverbd, DB_USERNAME, DB_PASSWORD, $_nomebd);
            $db->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('qr_acesso escola BD error: ' . $e->getMessage());
            $erro = 'Erro de ligação à base de dados. Contacte o administrador.';
            goto fim_post;
        }

        $stmt = $db->prepare("SELECT codigo_acesso_qr FROM settings LIMIT 1");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $db->close();

        $codigo_bd = $row['codigo_acesso_qr'] ?? '';

        if ($pin_inserido !== '' && $codigo_bd !== '' && hash_equals($codigo_bd, $pin_inserido)) {
            // Código correto → criar sessão temporária para acesso à ficha (só leitura)
            session_regenerate_id(true);
            $_SESSION['login_user']  = 'qr_acesso';
            $_SESSION['tipo']        = 3;          // reparador — só leitura
            $_SESSION['nobd']        = $_nomebd;
            $_SESSION['serverbd']    = $_serverbd;
            $_SESSION['qr_temp']     = true;       // marca para saber que é sessão QR temporária
            $_SESSION['_codigo_qr']  = $_codigo;   // para reconstruir URL de retorno
            $_SESSION['_created']    = time();

            $url = SVRURL . 'ficha_equipamento.php'
                 . '?ide=' . base64_encode($id_equip)
                 . '&&sai=' . base64_encode($id_sala)
                 . '&&ies=' . base64_encode($id_esc);
            header('Location: ' . $url);
            exit();
        } else {
            $erro = 'Código incorreto. Tente novamente.';
        }

        fim_post:

    } elseif ($acao === 'avaria') {
        // Formulário público de avaria
        $url = SVRURL . 'avaria_qr.php'
             . '?eq='   . $id_equip
             . '&sala='  . $id_sala
             . '&esc='   . $id_esc
             . '&cod='   . $_codigo;
        header('Location: ' . $url);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include('head.php'); ?>
<style>
.qr-card {
    max-width: 480px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(0,0,0,.10);
    padding: 36px 32px 28px;
}
.qr-card h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1e2a45;
    margin-bottom: 4px;
}
.qr-card .sub {
    font-size: .85rem;
    color: #7b88a0;
    margin-bottom: 24px;
}
.qr-divider {
    border: none;
    border-top: 1px solid #e8ecf2;
    margin: 24px 0;
}
.qr-label {
    font-size: .82rem;
    font-weight: 600;
    color: #4b6cb7;
    margin-bottom: 6px;
    display: block;
}
.qr-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d0d7e6;
    border-radius: 8px;
    font-size: .95rem;
    margin-bottom: 12px;
    box-sizing: border-box;
    letter-spacing: 2px;
}
.qr-input:focus {
    outline: none;
    border-color: #4b6cb7;
    box-shadow: 0 0 0 3px rgba(75,108,183,.12);
}
.btn-ficha {
    width: 100%;
    padding: 11px;
    background: #4b6cb7;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: .95rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-ficha:hover { background: #3a559a; }
.btn-avaria {
    width: 100%;
    padding: 11px;
    background: #f5f7fb;
    color: #4b6cb7;
    border: 1.5px solid #d0d7e6;
    border-radius: 8px;
    font-size: .95rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-avaria:hover { background: #e8ecf4; }
.qr-erro {
    background: #fff0f0;
    border: 1px solid #f5c6c6;
    color: #c0392b;
    border-radius: 7px;
    padding: 9px 14px;
    font-size: .87rem;
    margin-bottom: 14px;
}
.qr-icon {
    text-align: center;
    margin-bottom: 18px;
}
.qr-icon svg {
    width: 52px;
    height: 52px;
    opacity: .85;
}
</style>
</head>
<body class="main-layout">
<?php include('loader.php'); ?>
<?php include('header_qr.php'); ?>

<div class="about">
    <div class="container">

        <div class="qr-card">

            <div class="qr-icon">
                <!-- Ícone equipamento -->
                <svg viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
            </div>

            <h2>Acesso ao equipamento #<?= $id_equip ?></h2>
            <p class="sub">Selecione uma das opções abaixo.</p>

            <?php if ($erro): ?>
                <div class="qr-erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <!-- Opção 1: acesso técnico com código -->
            <?php $action_url = '?eq=' . $id_equip . '&sala=' . $id_sala . '&esc=' . $id_esc . '&cod=' . $_codigo; ?>
            <form method="POST" action="<?= htmlspecialchars($action_url) ?>" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="acao"  value="ficha">

                <label class="qr-label">Código de acesso (admins / reparadores)</label>
                <input class="qr-input" type="password" name="pin"
                       placeholder="••••••" maxlength="20" autocomplete="off">

                <button type="submit" class="btn-ficha">
                    <i class="fas fa-file-alt"></i>&nbsp; Ver ficha do equipamento
                </button>
            </form>

            <hr class="qr-divider">

            <!-- Opção 2: registar avaria (público) -->
            <form method="POST" action="<?= htmlspecialchars($action_url) ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="acao"  value="avaria">

                <button type="submit" class="btn-avaria">
                    <i class="fas fa-exclamation-triangle"></i>&nbsp; Registar avaria
                </button>
            </form>

        </div>

    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
