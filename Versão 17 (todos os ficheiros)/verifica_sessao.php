<style>

/* ── Estilos legados (mantidos por compatibilidade) ── */
.welcome-section {
    padding: 15px; background-color: #f8f9fc; border-radius: 6px;
    margin-bottom: 20px; border-left: 4px solid #36b9cc;
}
.action-section {
    background-color: white; border-radius: 8px; padding: 20px;
    margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 4px solid #4e73df;
}
.section-title {
    font-size: 1.1rem; font-weight: 600; color: #4e73df;
    margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e3e6f0;
}
.action-button {
    width: 100%; padding: 12px 15px; margin-bottom: 15px; border-radius: 6px;
    font-weight: 500; transition: all 0.3s ease; border: none;
    display: flex; align-items: center; justify-content: center;font-size: 14px !important;
}
.btn-primary-action   { background-color:#4e73df; color:white; box-shadow:0 4px 6px rgba(78,115,223,0.25); }
.btn-primary-action:hover   { background-color:#3a5ccc; transform:translateY(-2px); }
.btn-secondary-action { background-color:#fff; color:#4e73df; box-shadow:0 4px 6px rgba(0,0,0,0.08); border:1px solid #e3e6f0; }
.btn-secondary-action:hover { background-color:#f8f9fc; transform:translateY(-2px); }
.btn-danger-action    { background-color:#e74a3b; color:white; box-shadow:0 4px 6px rgba(231,74,59,0.25); }
.btn-danger-action:hover    { background-color:#d52a1a; transform:translateY(-2px); }
.btn-outline-action   { background-color:transparent; color:#4e73df; border:1px solid #4e73df; }
.btn-outline-action:hover   { background-color:#eaecf4; transform:translateY(-2px); }

/* ════════════════════════════════════════
   BARRA DE UTILIZADOR — 1 linha
   ════════════════════════════════════════ */
.gei-userbar-wrap {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 5px;
    background: #f4f6fb;
    border-radius: 8px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    border: 1px solid #e4e9f0;
    padding: 5px 10px;
    margin-bottom: 6px;
}

.gei-userbar-left  { display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
.gei-userbar-right { display:flex; align-items:center; gap:5px; flex-wrap:wrap; }

/* Elementos comuns */
.gei-username {
    font-weight: 700; font-size: 12px; color: #1a2330; white-space: nowrap;
}
.gei-tipo-badge {
    display: inline-flex; align-items: center; gap: 4px;
    border-radius: 5px; padding: 2px 7px;
    font-size: 11px; font-weight: 700; border: 1.5px solid; white-space: nowrap;
}
.gei-sep {
    width: 1px; height: 16px; background: #d1d8e0;
    display: inline-block; flex-shrink: 0;
}

/* Botões genéricos */
.gei-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 8px; border-radius: 5px;
    font-size: 11px; font-weight: 600; border: none;
    text-decoration: none !important; white-space: nowrap;
    transition: opacity .15s, transform .15s; cursor: pointer; line-height: 1.4;
}
.gei-btn:hover { opacity:.87; transform:translateY(-1px); text-decoration:none !important; }
.gei-btn span  { color:#fff !important; }
.gei-btn-blue { background-color:#00509e !important; color:#fff !important; box-shadow:0 2px 6px rgba(0,80,158,0.25); }
.gei-btn-red  { background-color:#c0392b !important; color:#fff !important; box-shadow:0 2px 6px rgba(192,57,43,0.25); }
.gei-btn-grey { background-color:#5a6370 !important; color:#fff !important; box-shadow:0 2px 6px rgba(90,99,112,0.20); }

/* Password warning — inline compacto */
.gei-pass-warn {
    display: inline-flex; align-items: center; gap: 3px;
    color: #e87722; font-size: .65rem; font-weight: 700;
    white-space: nowrap;
}

@keyframes gei-warn-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(232,119,34,0.4); }
    50%       { box-shadow: 0 0 0 4px rgba(232,119,34,0); }
}

/* Chat */
.gei-chat-link {
    display: inline-flex; align-items: center; gap: 5px;
    text-decoration: none !important; padding: 3px 8px; border-radius: 5px;
    font-weight: 600; font-size: 11px; transition: all .2s ease;
    border: 1.5px solid #003366; color: #003366 !important; background: #fff; white-space: nowrap;
}
.gei-chat-link:hover {
    background: #003366; color: #fff !important;
    box-shadow: 0 4px 12px rgba(0,51,102,0.22); transform: translateY(-1px);
}
.gei-chat-link:hover .gei-chat-icon { stroke: #fff !important; }
.gei-chat-link:hover .gei-chat-label { color:#fff !important; }
.gei-chat-icon { flex-shrink:0; transition:stroke .2s; }

.gei-chat-badge {
    display: inline-flex; align-items: center; gap: 3px;
    background: #e87722; color: #fff; border-radius: 12px;
    padding: 1px 6px 1px 4px; font-size: 10px; font-weight: 700;
    animation: gei-pulse 1.8s infinite;
}
.gei-chat-badge-dot { width:5px; height:5px; background:#fff; border-radius:50%; opacity:.85; flex-shrink:0; }
.gei-chat-ok {
    display: inline-flex; align-items: center; gap: 3px;
    color: #5a6370; font-size: 10px; font-weight: 400;
}

/* Ano letivo */
.gei-ano-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #003366; border-radius: 5px; padding: 3px 8px;
    font-size: 11px; font-weight: 700; color: #fff;
    box-shadow: 0 2px 8px rgba(0,51,102,0.18); white-space: nowrap;
}
.gei-ano-label { opacity:.7; font-weight:500; font-size:10px; }

@keyframes gei-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(232,119,34,0.5); }
    50%       { box-shadow: 0 0 0 5px rgba(232,119,34,0); }
}

/* Responsivo — ecrãs pequenos */
@media (max-width: 600px) {
    .gei-userbar-wrap { padding: 4px 7px; }
    .gei-btn { padding: 2px 6px; font-size: 10px; }
    .gei-chat-link { padding: 2px 6px; font-size: 10px; }
}
</style>

<?php

// Validação criptográfica da sessão via token secundário (cookie HttpOnly gei_sec).
// Mais robusto que comparar User-Agent, que é falsificável e causa logouts
// indevidos em atualizações de browser ou extensões.
$_gei_sec_cookie  = $_COOKIE['gei_sec']   ?? '';
$_gei_sec_session = $_SESSION['sec_token'] ?? '';
if (
    empty($_gei_sec_cookie) ||
    empty($_gei_sec_session) ||
    !hash_equals($_gei_sec_session, $_gei_sec_cookie)
) {
    session_unset();
    session_destroy();
    header('Location: ' . (defined('SVRURL') ? SVRURL : '/') . 'l');
    exit;
}

if (isset($_SESSION['login_user'])):

$tipo    = $_SESSION['tipo'];
$id_user = $_SESSION['user_id'];

$tipo_labels = [1=>'Administrador', 2=>'Utilizador', 3=>'Reparador', 4=>'Funcionário'];
$tipo_colors = [1=>'#7c3aed', 2=>'#00509e', 3=>'#0891b2', 4=>'#059669'];
$tipo_label  = $tipo_labels[$tipo] ?? 'Utilizador';
$tipo_color  = $tipo_colors[$tipo] ?? '#00509e';

// Query 1 — data de alteração de password
$stmt22 = mysqli_prepare($db, "SELECT dataalteracaopass FROM utilizadores WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt22, 'i', $id_user);
mysqli_stmt_execute($stmt22);
$result22 = mysqli_stmt_get_result($stmt22);
$rows22   = mysqli_fetch_row($result22);
mysqli_stmt_close($stmt22);
$dataatual     = date('Y-m-d');
$pass_expirada = ($rows22[0] == null) || ($dataatual >= $rows22[0]);

// Query 2 — ano letivo atual (sem parâmetros, sem risco de injeção)
$result_ano = mysqli_query($db, "SELECT MAX(ano_lectivo) FROM periodos");
$rows_ano   = mysqli_fetch_row($result_ano);
$conta      = $rows_ano[0];


// Verificar e disparar backup agendado em background
include_once __DIR__ . '/backup_check.php';


// Query 3 — mensagens de chat não lidas
$stmt_chat = mysqli_prepare($db, "SELECT COUNT(*) FROM chat_message WHERE to_user_id = ? AND status = 1");
mysqli_stmt_bind_param($stmt_chat, 'i', $id_user);
mysqli_stmt_execute($stmt_chat);
$result_chat = mysqli_stmt_get_result($stmt_chat);
$rows_chat   = mysqli_fetch_row($result_chat);
mysqli_stmt_close($stmt_chat);
$nummsg = (int)$rows_chat[0];
?>

<div class="gei-userbar-wrap">

    <!-- ══ ESQUERDA — Identidade · Conta · Chat ══ -->
    <div class="gei-userbar-left">

        <!-- Nome -->
        <span class="gei-username"><?php echo htmlspecialchars($_SESSION['login_user'], ENT_QUOTES, 'UTF-8'); ?></span>

        <!-- Badge tipo de utilizador -->
        <span class="gei-tipo-badge" style="color:<?php echo $tipo_color;?>;border-color:<?php echo $tipo_color;?>;background:<?php echo $tipo_color;?>18;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="<?php echo $tipo_color;?>" stroke-width="2.5" stroke-linecap="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <?php echo $tipo_label; ?>
        </span>

        <span class="gei-sep"></span>

        <!-- Mudar password -->
        <span style="display:inline-flex;flex-direction:column;align-items:center;gap:2px;">
            <a href="<?php echo SVRURL ?>reset_pass.php" class="gei-btn gei-btn-blue" title="Mudar password">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Password</span>
            </a>
            <?php if ($pass_expirada): ?>
            <span class="gei-pass-warn">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#e87722" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Deve mudar a password
            </span>
            <?php endif; ?>
        </span>

        <!-- Terminar sessão -->
        <a href="<?php echo SVRURL ?>sair" class="gei-btn gei-btn-red" title="Terminar sessão">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Terminar sessão</span>
        </a>

        <span class="gei-sep"></span>

        <!-- Chat -->
        <a target="_new" class="gei-chat-link" title="Abrir Chat" href="<?php echo SVRURL ?>chat/index.php">
            <svg class="gei-chat-icon" width="13" height="13" viewBox="0 0 24 24" fill="none"
                 stroke="#003366" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span class="gei-chat-label" style="color:#003366;font-weight:700;">Chat</span>
            <?php if ($nummsg > 0): ?>
                <span class="gei-chat-badge">
                    <span class="gei-chat-badge-dot"></span>
                    <?php echo $nummsg; ?> nova<?php echo $nummsg > 1 ? 's' : ''; ?>
                </span>
            <?php else: ?>
                <span class="gei-chat-ok">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Sem mensagens
                </span>
            <?php endif; ?>
        </a>

    </div><!-- /esquerda -->

    <!-- ══ DIREITA — Ano letivo ══ -->
    <div class="gei-userbar-right">
        <span class="gei-ano-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8"  y1="2" x2="8"  y2="6"/>
                <line x1="3"  y1="10" x2="21" y2="10"/>
            </svg>
            <span class="gei-ano-label">Ano</span>
            <?php echo $conta ?? '<em style="opacity:.7">não definido</em>'; ?>
        </span>
    </div><!-- /direita -->

</div><!-- /gei-userbar-wrap -->

<?php
else:
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>i';
}, 10);
</script>
<?php
endif;
?>
