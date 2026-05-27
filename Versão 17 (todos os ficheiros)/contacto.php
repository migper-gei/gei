<?php
// ── Sessão segura (igual aos outros ficheiros da app) ────────────────────────
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

// ── Requer sessão activa ─────────────────────────────────────────────────────
// Esta página só está disponível para utilizadores autenticados.
if (!isset($_SESSION['login_user']) || empty($_SESSION['nobd']) || empty($_SESSION['serverbd'])) {
    header('Location: index.php');
    exit;
}

// ── PHPMailer (instalação manual — ficheiros src) ────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';





// ── Ligação à BD da sessão ───────────────────────────────────────────────────
include_once('config_serverbd_settings.php'); // carrega .env e define DB_USERNAME/DB_PASSWORD

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = null;

try {
    $db = new mysqli($_SESSION['serverbd'], DB_USERNAME, DB_PASSWORD, $_SESSION['nobd']);
    $db->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log('contacto.php - Erro BD: ' . $e->getMessage());
    $db = null;
}

// ── Processamento do formulário ──────────────────────────────────────────────

$enviado       = false;
$erro          = '';
$email_destino = 'gei@miguelarpereira.pt';
$url_voltar    = 'acessorap';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim(strip_tags($_SESSION['login_user'] ?? $_POST['nome']  ?? ''));
    $email    = trim(strip_tags($_SESSION['email']     ?? $_POST['email'] ?? ''));
    $tipo     = trim(strip_tags($_POST['tipo']     ?? ''));
    $assunto  = trim(strip_tags($_POST['assunto']  ?? ''));
    $mensagem = trim(strip_tags($_POST['mensagem'] ?? ''));
    $url_pag  = trim(strip_tags($_POST['url_pag']  ?? ''));

    // Validação básica
    if (!$nome || !$email || !$tipo || !$assunto || !$mensagem) {
        $erro = 'Por favor preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Endereço de e-mail inválido.';
    } else {
        $tipos_label = [
            'erro'     => '🐛 Detecção de erro',
            'melhoria' => '💡 Sugestão de melhoria',
            'duvida'   => '❓ Dúvida / questão',
            'outro'    => '📩 Outro',
        ];
        $tipo_label = $tipos_label[$tipo] ?? $tipo;

        // ── Corpo HTML do e-mail ─────────────────────────────────────────────
        $corpo_html = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e3e8f4;border-radius:10px;overflow:hidden'>
            <div style='background:linear-gradient(135deg,#4b6cb7,#507feb);padding:22px 28px'>
                <h2 style='color:#fff;margin:0;font-size:1.1rem'>&#9993; Nova mensagem &mdash; GEI</h2>
            </div>
            <div style='padding:24px 28px;background:#fff'>
                <table style='width:100%;border-collapse:collapse;font-size:.88rem'>
                    <tr><td style='padding:7px 0;color:#7b88a0;width:110px;font-weight:700'>Tipo</td>
                        <td style='padding:7px 0;color:#1e2a45'>{$tipo_label}</td></tr>
                    <tr><td style='padding:7px 0;color:#7b88a0;font-weight:700'>Nome</td>
                        <td style='padding:7px 0;color:#1e2a45'>{$nome}</td></tr>
                    <tr><td style='padding:7px 0;color:#7b88a0;font-weight:700'>E-mail</td>
                        <td style='padding:7px 0;color:#1e2a45'><a href='mailto:{$email}' style='color:#507feb'>{$email}</a></td></tr>
                    <tr><td style='padding:7px 0;color:#7b88a0;font-weight:700'>Assunto</td>
                        <td style='padding:7px 0;color:#1e2a45'>{$assunto}</td></tr>";

        if ($url_pag) {
            $corpo_html .= "
                    <tr><td style='padding:7px 0;color:#7b88a0;font-weight:700'>Página</td>
                        <td style='padding:7px 0;color:#1e2a45'>{$url_pag}</td></tr>";
        }

        $corpo_html .= "
                </table>
                <hr style='border:none;border-top:1px solid #e3e8f4;margin:18px 0'>
                <p style='font-size:.8rem;color:#7b88a0;font-weight:700;margin:0 0 8px'>Mensagem</p>
                <p style='font-size:.88rem;color:#1e2a45;line-height:1.6;margin:0;white-space:pre-line'>" . nl2br(htmlspecialchars($mensagem)) . "</p>
            </div>
            <div style='background:#f0f4fb;padding:12px 28px;font-size:.75rem;color:#7b88a0'>
                Enviado em " . date('d/m/Y H:i:s') . " &nbsp;&middot;&nbsp; IP: " . ($_SERVER['REMOTE_ADDR'] ?? '—') . "
            </div>
        </div>";

        // ── Envio via PHPMailer com configurações SMTP da base de dados ──────
        if (!$db) {
            $erro = 'Não foi possível ligar à base de dados. Tente mais tarde.';
        } else {
            // $mail instanciado ANTES dos includes (padrão da app)
            $mail = new PHPMailer(true);
            try {
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();

                // 1. Configurações SMTP (host, porta, user, pass)
                include('email_settings.php');

                // 2. From/FromName/Sender
                include('dados_enviar_email.php');

                // Forçar From correcto após o include (evita cabeçalho inválido)
                if (isset($row00['email_user'])) {
                    $mail->From   = $row00['email_user'];
                    $mail->Sender = $row00['email_user'];
                }

                // 3. Destinatário e ReplyTo DEPOIS dos includes para não serem sobrescritos
                $mail->clearAddresses();
                $mail->clearReplyTos();
                $mail->addAddress($email_destino);
                $mail->addReplyTo($email, $nome);  // responder directamente ao remetente

                $mail->isHTML(true);
                $mail->Subject = "[GEI] {$tipo_label} — {$assunto}";
                $mail->Body    = $corpo_html;
                $mail->AltBody = "Tipo: {$tipo_label}\nNome: {$nome}\nEmail: {$email}\nAssunto: {$assunto}\nPágina: {$url_pag}\n\n{$mensagem}";

                $mail->send();

                $enviado = true;

            } catch (Exception $e) {
                $erro = 'Erro ao enviar a mensagem: ' . $mail->ErrorInfo;
                error_log('Erro PHPMailer contacto.php: ' . $mail->ErrorInfo);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<?php include("head.php"); ?>

<head>
    <style>
        /* Ocultar botão home */
        .home-button { display: none !important; }

        :root {
            --primary:    #4b6cb7;
            --primary-dk: #182848;
            --accent:     #507feb;
            --accent2:    #36b9cc;
            --success:    #1cc88a;
            --warning:    #f6c23e;
            --danger:     #e74a3b;
            --bg:         #f0f4fb;
            --surface:    #ffffff;
            --border:     #e3e8f4;
            --text:       #1e2a45;
            --muted:      #7b88a0;
            --radius:     10px;
            --shadow:     0 2px 12px rgba(75,108,183,.10);
            --shadow-lg:  0 6px 24px rgba(75,108,183,.16);
        }

        /* ── Página ── */
        .contact-wrap {
            max-width: 680px;
            margin: 40px auto 60px;
            padding: 0 15px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .contact-header .contact-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e8f0fe;
            color: var(--primary);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .contact-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dk);
            margin: 0 0 8px;
            letter-spacing: -.4px;
        }
        .contact-header p {
            color: var(--muted);
            font-size: .88rem;
            margin: 0;
        }
        .contact-divider {
            width: 48px; height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            border-radius: 99px;
            margin: 14px auto 0;
        }

        /* ── Card do formulário ── */
        .contact-card {
            background: var(--surface);
            border-radius: 14px;
            box-shadow: var(--shadow-lg);
            padding: 36px 40px;
            border: 1px solid var(--border);
        }
        @media (max-width: 520px) {
            .contact-card { padding: 24px 20px; }
        }

        /* ── Grupos ── */
        .form-group-ct {
            margin-bottom: 18px;
        }
        .form-group-ct label {
            display: block;
            font-size: .80rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 5px;
            letter-spacing: .1px;
        }
        .form-group-ct label .req {
            color: var(--danger);
            margin-left: 2px;
        }
        .form-group-ct .hint {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* ── Inputs bloqueados (readonly) ── */
        .ct-input-locked {
            background: #f0f4fb !important;
            color: var(--muted) !important;
            cursor: not-allowed !important;
            border-color: var(--border) !important;
            box-shadow: none !important;
            user-select: none;
        }
        .ct-input-locked:focus {
            border-color: var(--border) !important;
            box-shadow: none !important;
        }

        /* ── Inputs ── */
        .ct-input,
        .ct-select,
        .ct-textarea {
            width: 100%;
            padding: 9px 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: .88rem;
            color: var(--text);
            background: #fafbfe;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
        }
        .ct-input:focus,
        .ct-select:focus,
        .ct-textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(80,127,235,.12);
            background: #fff;
        }
        .ct-textarea {
            resize: vertical;
            min-height: 130px;
            line-height: 1.55;
        }
        .ct-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%237b88a0' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            padding-right: 34px;
            cursor: pointer;
        }

        /* ── Linha dupla ── */
        .form-row-ct {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 520px) {
            .form-row-ct { grid-template-columns: 1fr; }
        }

        /* ── Tipos de contacto (radio visual) ── */
        .tipo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .tipo-option {
            position: relative;
        }
        .tipo-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0; height: 0;
        }
        .tipo-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            font-size: .82rem;
            font-weight: 600;
            color: var(--muted);
            background: #fafbfe;
            transition: all .2s;
            margin: 0;
        }
        .tipo-option label .tipo-icon {
            font-size: 1rem;
        }
        .tipo-option input[type="radio"]:checked + label {
            border-color: var(--accent);
            background: #eef3ff;
            color: var(--primary);
            box-shadow: 0 0 0 3px rgba(80,127,235,.10);
        }
        .tipo-option label:hover {
            border-color: var(--accent);
            color: var(--primary);
        }

        /* ── Botão submit ── */
        .btn-ct-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            border: none;
            border-radius: var(--radius);
            padding: 11px 28px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(75,108,183,.35);
            transition: transform .15s, box-shadow .15s;
            letter-spacing: .2px;
        }
        .btn-ct-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(75,108,183,.40);
        }
        .btn-ct-submit:active {
            transform: translateY(0);
        }

        /* ── Alertas ── */
        .ct-alert {
            border-radius: var(--radius);
            padding: 13px 16px;
            margin-bottom: 22px;
            font-size: .86rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .ct-alert-success {
            background: #d4f5e9;
            color: #0d5e3f;
            border-left: 4px solid var(--success);
        }
        .ct-alert-danger {
            background: #fde8e6;
            color: #7a1c13;
            border-left: 4px solid var(--danger);
        }
        .ct-alert i { margin-top: 2px; flex-shrink: 0; }

        /* ── Sucesso total ── */
        .ct-success-box {
            text-align: center;
            padding: 40px 20px;
        }
        .ct-success-box .success-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success), #0fa76f);
            color: #fff;
            font-size: 1.8rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 4px 16px rgba(28,200,138,.35);
        }
        .ct-success-box h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-dk);
            margin: 0 0 8px;
        }
        .ct-success-box p {
            color: var(--muted);
            font-size: .88rem;
            margin: 0 0 24px;
        }
        .btn-ct-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            color: #e53e3e !important;
            border: 1.5px solid #e53e3e;
            border-radius: var(--radius);
            padding: 11px 22px;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s, border-color .2s, color .2s;
            letter-spacing: .1px;
        }
        .btn-ct-back:hover {
            background: #fff5f5;
            border-color: #c53030;
            text-decoration: none;
            color: #c53030 !important;
        }

        /* ── Info lateral ── */
        .contact-info {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: .78rem;
            color: var(--muted);
        }
        .contact-info a {
            color: var(--accent);
        }
    </style>
</head>

<body class="main-layout">
    <?php include("loader.php"); ?>
    <?php include("header2.php"); ?>

    <div class="about">
        <div class="container">

            <div class="contact-wrap">

                <!-- Cabeçalho -->
                <div class="contact-header">
                    <div class="contact-badge"><i class="fas fa-envelope"></i> Contacto</div>
              
                    <p>Reporte um erro, sugira uma melhoria ou coloque uma questão.<br>Respondemos com a maior brevidade possível.</p>
                    <div class="contact-divider"></div>
                </div>

                <div class="contact-card">

                    <?php if ($enviado): ?>
                    <!-- ── Estado: enviado com sucesso ── -->
                    <div class="ct-success-box">
                        <div class="success-icon"><i class="fas fa-check"></i></div>
                        <h3>Mensagem enviada!</h3>
                        <p>A sua mensagem foi recebida com sucesso.<br>Entraremos em contacto através do e-mail indicado.</p>
                        <a href="<?= $url_voltar ?>" class="btn-ct-back"><i class="fas fa-arrow-left"></i> Voltar ao início</a>
                    </div>

                    <?php else: ?>

                    <?php if ($erro): ?>
                    <div class="ct-alert ct-alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($erro) ?></span>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="contacto.php" novalidate>

                        <!-- Tipo de contacto -->
                        <div class="form-group-ct">
                            <label>Tipo de contacto <span class="req">*</span></label>
                            <div class="tipo-grid">
                                <div class="tipo-option">
                                    <input type="radio" name="tipo" id="t-erro" value="erro"
                                        <?= (($_POST['tipo'] ?? '') === 'erro') ? 'checked' : '' ?>>
                                    <label for="t-erro">
                                        <span class="tipo-icon">🐛</span> Detecção de erro
                                    </label>
                                </div>
                                <div class="tipo-option">
                                    <input type="radio" name="tipo" id="t-melhoria" value="melhoria"
                                        <?= (($_POST['tipo'] ?? '') === 'melhoria') ? 'checked' : '' ?>>
                                    <label for="t-melhoria">
                                        <span class="tipo-icon">💡</span> Sugestão de melhoria
                                    </label>
                                </div>
                                <div class="tipo-option">
                                    <input type="radio" name="tipo" id="t-duvida" value="duvida"
                                        <?= (($_POST['tipo'] ?? '') === 'duvida') ? 'checked' : '' ?>>
                                    <label for="t-duvida">
                                        <span class="tipo-icon">❓</span> Dúvida / questão
                                    </label>
                                </div>
                                <div class="tipo-option">
                                    <input type="radio" name="tipo" id="t-outro" value="outro"
                                        <?= (($_POST['tipo'] ?? '') === 'outro') ? 'checked' : '' ?>>
                                    <label for="t-outro">
                                        <span class="tipo-icon">📩</span> Outro
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Nome + Email -->
                        <div class="form-row-ct">
                            <div class="form-group-ct">
                                <label for="nome">Nome <i class="fas fa-lock" style="font-size:.7rem;color:var(--muted);margin-left:3px" title="Preenchido automaticamente"></i></label>
                                <input type="text" id="nome" name="nome" class="ct-input ct-input-locked"
                                    value="<?= htmlspecialchars($_SESSION['login_user'] ?? '') ?>"
                                    readonly tabindex="-1">
                            </div>
                            <div class="form-group-ct">
                                <label for="email">E-mail <i class="fas fa-lock" style="font-size:.7rem;color:var(--muted);margin-left:3px" title="Preenchido automaticamente"></i></label>
                                <input type="email" id="email" name="email" class="ct-input ct-input-locked"
                                    value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                                    readonly tabindex="-1">
                            </div>
                        </div>

                        <!-- Assunto -->
                        <div class="form-group-ct">
                            <label for="assunto">Assunto <span class="req">*</span></label>
                            <input type="text" id="assunto" name="assunto" class="ct-input"
                                placeholder="Breve descrição do assunto"
                                value="<?= htmlspecialchars($_POST['assunto'] ?? '') ?>" required>
                        </div>

                        <!-- Página onde ocorreu (opcional) -->
                        <div class="form-group-ct">
                            <label for="url_pag">Página onde ocorreu <span style="color:var(--muted);font-weight:400">(opcional)</span></label>
                            <input type="text" id="url_pag" name="url_pag" class="ct-input"
                                placeholder="ex: equipamentos.php, manutenção, ..."
                                value="<?= htmlspecialchars($_POST['url_pag'] ?? '') ?>">
                            <div class="hint">Indica a secção ou página onde encontrou o problema / tem a sugestão.</div>
                        </div>

                        <!-- Mensagem -->
                        <div class="form-group-ct">
                            <label for="mensagem">Mensagem <span class="req">*</span></label>
                            <textarea id="mensagem" name="mensagem" class="ct-textarea"
                                placeholder="Descreva detalhadamente o erro, sugestão ou questão..."
                                required><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                        </div>

                        <!-- Submit -->
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:4px;">
                            <button type="submit" class="btn-ct-submit">
                                <i class="fas fa-paper-plane"></i> Enviar mensagem
                            </button>
                            <a href="<?= $url_voltar ?>" class="btn-ct-back">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>

                    </form>
                    <?php endif; ?>

                </div><!-- /contact-card -->

            </div><!-- /contact-wrap -->

        </div>
    </div>

    <?php include("footer.php"); ?>

</body>
</html>
