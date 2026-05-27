<?php
// ── Autenticação — deve ser a primeira coisa a executar, antes de qualquer output ──
include('database_connection.php');  // inicia sessão e define $db
include('../config.php');            // define SVRURL e constantes

if (!isset($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    header('Location: ' . SVRURL . 'l');
    exit;
}

$_uid_chat   = (int)$_SESSION['user_id'];
$_login_name = $_SESSION['login_user'] ?? 'U';

// Marcar utilizador como activo
$stmt_act = $db->prepare("UPDATE utilizadores SET sessao_ativa='1' WHERE id=?");
$stmt_act->bind_param("i", $_uid_chat);
$stmt_act->execute();
$stmt_act->close();

// Calcular iniciais para o avatar
$_parts    = explode(' ', trim($_login_name));
$_initials = '';
foreach ($_parts as $_p) { $_initials .= strtoupper(substr($_p, 0, 1)); }
$_initials = substr($_initials, 0, 2);

header("Content-type: text/html; charset=utf-8");
// ── fim autenticação ──────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SGEI - Chat</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Fontes carregadas globalmente em head.php via tokens.css -->
    <!-- Tema escuro -->
    <link rel="stylesheet" href="../css/dark-theme.css">
    <script>
    (function(){
        try {
            var t = localStorage.getItem('gei-theme');
            if (!t) t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', t);
        } catch(e) {}
    })();
    </script>
    <script src="../js/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<style>
/* ══════════════════════════════════════════════════
   GEI DESIGN SYSTEM — Chat
   ══════════════════════════════════════════════════ */
:root {
    --gei-blue:       #003366;
    --gei-blue-mid:   #00509e;
    --gei-blue-light: #d0e4f7;
    --gei-blue-pale:  #eaf3fb;
    --gei-orange:     #e87722;
    --gei-gray:       #f4f5f7;
    --gei-gray-mid:   #c8cdd3;
    --gei-gray-dark:  #5a6370;
    --gei-text:       #1a2330;
    --gei-border:     #cfd6de;
    --white:          #ffffff;
    --font:           var(--font-body, 'DM Sans', sans-serif);
}
*, *::before, *::after { box-sizing: border-box; }
body {
    font-family: var(--font);
    background: var(--gei-gray);
    color: var(--gei-text);
    margin: 0; padding: 0; min-height: 100vh;
}

/* ── NAVBAR ─────────────────────────────────────── */
.gei-navbar {
    background: var(--gei-blue);
    height: 56px;
    display: flex; align-items: center;
    padding: 0 20px; gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.28);
    position: sticky; top: 0; z-index: 9999;
}
.gei-navbar .logo-box {
    display: flex; align-items: center; gap: 10px; text-decoration: none;
}
.gei-navbar .logo-icon {
    width: 34px; height: 34px; background: var(--white);
    border-radius: 5px; display: flex; align-items: center;
    justify-content: center; font-size: 13px; font-weight: 800;
    color: var(--gei-blue); letter-spacing: -.5px; flex-shrink: 0;
}
.gei-navbar .logo-title { font-size: 14px; font-weight: 700; color: #fff; display: block; line-height: 1.15; }
.gei-navbar .logo-sub   { font-size: 10px; color: rgba(255,255,255,.6); display: block; }
.gei-navbar .nav-sep    { width: 1px; height: 26px; background: rgba(255,255,255,.2); margin: 0 2px; }
.gei-navbar .nav-section{ font-size: 12.5px; color: rgba(255,255,255,.75); display: flex; align-items: center; gap: 5px; }
.gei-navbar .nav-spacer { flex: 1; }
.gei-navbar .nav-user   {
    display: flex; align-items: center; gap: 9px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    border-radius: 5px; padding: 4px 12px 4px 7px;
}
.gei-navbar .nav-avatar {
    width: 26px; height: 26px; border-radius: 50%; background: var(--gei-orange);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
}
.gei-navbar .nav-uname  { font-size: 12px; font-weight: 600; color: #fff; }
.gei-navbar .nav-logout {
    font-size: 11.5px; color: rgba(255,255,255,.75); text-decoration: none;
    border: 1px solid rgba(255,255,255,.22); border-radius: 4px;
    padding: 4px 11px; transition: background .15s; display: flex; align-items: center; gap: 5px;
}
.gei-navbar .nav-logout:hover { background: rgba(255,255,255,.15); color: #fff; text-decoration: none; }

/* ── BREADCRUMB ─────────────────────────────────── */
.gei-breadcrumb {
    background: var(--white); border-bottom: 1px solid var(--gei-border);
    padding: 0 20px; height: 34px;
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; color: var(--gei-gray-dark);
}
.gei-breadcrumb a { color: var(--gei-blue-mid); text-decoration: none; }
.gei-breadcrumb a:hover { text-decoration: underline; }
.gei-breadcrumb .bc-sep { color: var(--gei-gray-mid); font-size: 9px; }
.gei-breadcrumb .bc-cur { color: var(--gei-text); font-weight: 600; }

/* ── PAGE ───────────────────────────────────────── */
.gei-page { max-width: 960px; margin: 28px auto; padding: 0 20px 40px; }

/* ── CARD ───────────────────────────────────────── */
.gei-card {
    background: var(--white); border: 1px solid var(--gei-border);
    border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.06); overflow: hidden;
}
.gei-card-header {
    background: var(--gei-blue); padding: 13px 20px;
    display: flex; align-items: center; gap: 10px;
}
.gei-card-header h3 {
    margin: 0; font-size: 15px; font-weight: 700; color: #fff; letter-spacing: .2px;
}
.gei-card-header .hdr-icon {
    width: 28px; height: 28px; background: rgba(255,255,255,.15);
    border-radius: 5px; display: flex; align-items: center; justify-content: center;
}
.gei-card-body { padding: 0; }

/* ── TABELA DE UTILIZADORES ─────────────────────── */
#user_details .table { margin-bottom: 0; border: none; }
#user_details .table > thead > tr > th {
    background: var(--gei-blue-pale); color: var(--gei-blue);
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
    border-bottom: 2px solid var(--gei-blue-light); padding: 10px 16px; vertical-align: middle;
}
#user_details .table > tbody > tr > td {
    padding: 10px 16px; vertical-align: middle;
    border-color: var(--gei-border); font-size: 13.5px; color: var(--gei-text);
}
#user_details .table-striped > tbody > tr:nth-of-type(odd) { background: var(--gei-gray); }
#user_details .table-striped > tbody > tr:hover { background: var(--gei-blue-pale); }

/* Labels estado */
#user_details .label-success {
    background: #22c55e !important; border-radius: 10px !important;
    padding: 3px 9px !important; font-size: 10.5px !important; font-weight: 600 !important;
}
#user_details .label-danger {
    background: var(--gei-gray-mid) !important; color: var(--gei-gray-dark) !important;
    border-radius: 10px !important; padding: 3px 9px !important;
    font-size: 10.5px !important; font-weight: 600 !important;
}
/* Badge mensagens não lidas (label-success dentro de td de nome) */
#user_details td > .label-success {
    background: var(--gei-orange) !important;
    margin-left: 6px;
}

/* Botão Iniciar Chat */
#user_details .btn-info.btn-xs.start_chat {
    background: var(--gei-blue-mid) !important; border-color: var(--gei-blue-mid) !important;
    color: #fff !important; border-radius: 5px !important;
    font-size: 11.5px !important; font-weight: 600 !important;
    padding: 4px 12px !important; transition: background .15s !important;
}
#user_details .btn-info.btn-xs.start_chat:hover {
    background: var(--gei-blue) !important; border-color: var(--gei-blue) !important;
}

/* ── JQUERY UI DIALOG ───────────────────────────── */
.ui-dialog {
    border: 1px solid var(--gei-border) !important;
    border-radius: 8px !important;
    box-shadow: 0 8px 32px rgba(0,0,0,.18) !important;
    font-family: var(--font) !important;
    overflow: hidden !important;
    padding: 0 !important;
    /* Resetar tudo que o tema base possa adicionar */
    margin-top: 0 !important;
}
/* Esconder titlebar original do jQuery UI — usamos custom */
.ui-dialog .ui-dialog-titlebar {
    display: none !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    overflow: hidden !important;
}
/* O content começa imediatamente no topo — sem padding */
.ui-dialog .ui-dialog-content {
    padding: 0 !important;
    margin: 0 !important;
    background: #fff !important;
    position: relative !important;
    top: 0 !important;
}
/* Forçar widget jQuery UI a não adicionar espaço em cima */
.ui-widget-content {
    padding: 0 !important;
}
/* Titlebar custom GEI */
.gei-dialog-titlebar {
    background: var(--gei-blue);
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 0;
}
.gei-dialog-title {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}
.gei-dialog-close {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-left: 10px;
    line-height: 1;
    padding: 0;
    transition: background .15s;
}
.gei-dialog-close:hover {
    background: rgba(255,255,255,.35);
}
.gei-dialog-body {
    padding: 16px;
}

/* Histórico no dialog */
.chat_history {
    background: var(--gei-gray) !important;
    border: 1px solid var(--gei-border) !important;
    border-radius: 6px !important;
    overflow-y: scroll !important;
}
.chat_history .list-unstyled { margin: 0; padding: 0; }
.chat_history li {
    border-bottom: 1px dotted var(--gei-border) !important;
    padding: 9px 13px !important;
    font-size: 13px !important; line-height: 1.5 !important;
}
.chat_history li:last-child { border-bottom: none !important; }
/* Mensagens próprias — fundo azul pálido */
.chat_history li[style*="ffe6e6"] {
    background: var(--gei-blue-pale) !important;
    border-left: 3px solid var(--gei-blue-mid) !important;
    padding-left: 12px !important;
}
/* Mensagens do outro — fundo branco com borda laranja */
.chat_history li[style*="ffffe6"] {
    background: var(--white) !important;
    border-left: 3px solid var(--gei-orange) !important;
    padding-left: 12px !important;
}
.chat_history .text-success { color: var(--gei-blue-mid) !important; font-weight: 700; }
.chat_history .text-danger  { color: var(--gei-orange)   !important; font-weight: 700; }
.chat_history small em { color: var(--gei-gray-dark); font-size: 10.5px; }

/* Botão apagar */
.chat_history .btn-danger.btn-xs.remove_chat {
    background: transparent !important; border: 1px solid #fca5a5 !important;
    color: #ef4444 !important; border-radius: 4px !important;
    font-size: 10px !important; padding: 1px 6px !important;
    font-weight: 700 !important; transition: all .15s !important;
}
.chat_history .btn-danger.btn-xs.remove_chat:hover { background: #fee2e2 !important; }

/* Textarea + botão enviar */
.ui-dialog textarea.form-control.chat_message {
    border: 1px solid var(--gei-border) !important; border-radius: 6px !important;
    font-family: var(--font) !important; font-size: 13px !important;
    color: var(--gei-text) !important; resize: vertical !important;
    min-height: 60px !important; transition: border-color .15s, box-shadow .15s !important;
}
.ui-dialog textarea.form-control.chat_message:focus {
    border-color: var(--gei-blue-mid) !important;
    box-shadow: 0 0 0 3px rgba(0,80,158,.1) !important;
    outline: none !important;
}
.ui-dialog .btn-info.send_chat {
    background: var(--gei-blue) !important; border-color: var(--gei-blue) !important;
    color: #fff !important; border-radius: 6px !important;
    font-size: 12.5px !important; font-weight: 600 !important;
    padding: 6px 20px !important; transition: background .15s !important;
}
.ui-dialog .btn-info.send_chat:hover {
    background: var(--gei-blue-mid) !important; border-color: var(--gei-blue-mid) !important;
}

/* EmojiOne */
.emojionearea {
    border: 1px solid var(--gei-border) !important; border-radius: 6px !important;
}
.emojionearea.focused {
    border-color: var(--gei-blue-mid) !important;
    box-shadow: 0 0 0 3px rgba(0,80,158,.1) !important;
}

/* ═══ DARK MODE ═══════════════════════════════════════════ */
[data-theme="dark"] {
    --gei-blue:       #1a3a6b;
    --gei-blue-mid:   #4b7fd4;
    --gei-blue-light: #1e3a5f;
    --gei-blue-pale:  #1a2a3d;
    --gei-orange:     #e87722;
    --gei-gray:       #0f1117;
    --gei-gray-mid:   #3d4455;
    --gei-gray-dark:  #94a3b8;
    --gei-text:       #e2e8f0;
    --gei-border:     #2d3348;
    --white:          #1a1d27;
}
[data-theme="dark"] body                             { background: #0f1117; color: #e2e8f0; }
[data-theme="dark"] .gei-navbar                     { background: #0e1e38; }
[data-theme="dark"] .gei-breadcrumb                 { background: #1a1d27; border-color: #2d3348; }
[data-theme="dark"] .gei-card                       { background: #1a1d27; border-color: #2d3348; }
[data-theme="dark"] .gei-card-header                { background: #0e1e38; }
[data-theme="dark"] #user_details .table > thead > tr > th { background: #1e2a3d; color: #7aa2f7; border-color: #2d3348; }
[data-theme="dark"] #user_details .table > tbody > tr > td { border-color: #2d3348; color: #e2e8f0; }
[data-theme="dark"] #user_details .table-striped > tbody > tr:nth-of-type(odd) { background: #1e2130; }
[data-theme="dark"] #user_details .table-striped > tbody > tr:hover { background: #1a2a3d; }
[data-theme="dark"] .ui-dialog .ui-dialog-content   { background: #1a1d27 !important; }
[data-theme="dark"] .gei-dialog-body                { background: #1a1d27; }
[data-theme="dark"] .chat_history                   { background: #0f1117 !important; border-color: #2d3348 !important; }
[data-theme="dark"] .chat_history li                { border-color: #2d3348 !important; color: #e2e8f0 !important; }
[data-theme="dark"] .chat_history li[style*="ffe6e6"]{ background: #1a2a3d !important; }
[data-theme="dark"] .chat_history li[style*="ffffe6"]{ background: #1e2130 !important; }
[data-theme="dark"] .ui-dialog textarea.form-control.chat_message { background: #252836 !important; color: #e2e8f0 !important; border-color: #2d3348 !important; }
[data-theme="dark"] .ui-widget-content              { background: #1a1d27 !important; }
/* Toggle button na navbar */
.gei-theme-toggle-chat {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    border-radius: 5px; padding: 4px 10px; cursor: pointer;
    font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85);
    transition: background .15s; white-space: nowrap;
}
.gei-theme-toggle-chat:hover { background: rgba(255,255,255,.2); color: #fff; }
/* ═══════════════════════════════════════════════════════════ */
</style>
</head>
<body>

<?php /* variáveis $_uid_chat, $_login_name e $_initials já definidas no topo */ ?>

<!-- NAVBAR -->
<nav class="gei-navbar">
    <a class="logo-box" href="#">
        <div class="logo-icon">GEI</div>
        <div class="logo-title">Portal GEI</div>
    </a>
    <div class="nav-sep"></div>
    <div class="nav-section">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Mensagens / Chat
    </div>
    <div class="nav-spacer"></div>
    <div class="nav-user">
        <div class="nav-avatar"><?php echo htmlspecialchars($_initials, ENT_QUOTES, 'UTF-8'); ?></div>
        <span class="nav-uname"><?php echo htmlspecialchars($_login_name, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
    <a class="nav-logout" href="logout_chat.php" title="Terminar sessão">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sair
    </a>
</nav>



<!-- CONTEÚDO -->
<div class="gei-page">
    <input type="hidden" id="is_active_group_chat_window" value="no" />

    <div class="gei-card">
        <div class="gei-card-header">
            <div class="hdr-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>SGEI — Utilizadores</h3>
        </div>
        <div class="gei-card-body">
            <div class="table-responsive">
                <div id="user_details"></div>
            </div>
        </div>
    </div>

    <div id="user_model_details"></div>
</div>

<script>
$(document).ready(function(){

    fetch_user();

    setInterval(function(){
        fetch_user();
        update_chat_history_data();
    }, 5000);

    function fetch_user(){
        $.ajax({
            url: "fetch_user.php",
            method: "POST",
            success: function(data){
                if(data.trim() === '') {
                    $('#user_details').html('<div style="padding:20px;color:red;font-weight:bold;">ERRO: fetch_user.php devolveu resposta vazia.</div>');
                } else {
                    $('#user_details').html(data);
                }
            },
            error: function(xhr, status, err){
                $('#user_details').html('<div style="padding:20px;color:red;font-weight:bold;">ERRO AJAX: ' + xhr.status + ' — ' + xhr.responseText + '</div>');
            }
        });
    }

    function make_chat_dialog_box(to_user_id, to_user_name){
        // Remover dialog anterior do mesmo utilizador se existir
        $('#user_dialog_'+to_user_id).remove();

        var modal_content = '<div id="user_dialog_'+to_user_id+'" style="'
            + 'position:fixed;bottom:20px;right:20px;width:380px;z-index:9999;'
            + 'background:#fff;border:1px solid #cfd6de;border-radius:8px;'
            + 'box-shadow:0 8px 32px rgba(0,0,0,.22);font-family:inherit;overflow:hidden;">';
        modal_content += '<div class="gei-dialog-titlebar" style="cursor:move;" data-dialogid="'+to_user_id+'">';
        modal_content += '<span class="gei-dialog-title">Em conversa com: '+to_user_name+'</span>';
        modal_content += '<button class="gei-clear-history" data-touserid="'+to_user_id+'" title="Limpar histórico" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.35);border-radius:4px;color:#fff;cursor:pointer;font-size:11px;font-weight:600;padding:2px 8px;margin-right:6px;white-space:nowrap;">&#128465; Limpar</button>';
        modal_content += '<button class="gei-dialog-close" data-closeid="'+to_user_id+'" title="Fechar">&#x2715;</button>';
        modal_content += '</div>';
        modal_content += '<div class="gei-dialog-body">';
        modal_content += '<div style="height:220px;border:1px solid #cfd6de;overflow-y:scroll;margin-bottom:12px;border-radius:6px;background:#f4f5f7;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'"></div>';
        modal_content += '<div style="margin-bottom:10px;">';
        modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="chat_message" rows="2" placeholder="Escreva uma mensagem..." style="border:1px solid #cfd6de;border-radius:6px;font-family:inherit;font-size:13px;resize:none;height:52px;width:100%;padding:7px 10px;box-sizing:border-box;"></textarea>';
        modal_content += '</div>';
        modal_content += '<div style="text-align:right;">';
        modal_content += '<button type="button" name="send_chat" id="'+to_user_id+'" class="send_chat" style="background:#003366;border:none;color:#fff;border-radius:6px;font-size:12.5px;font-weight:600;padding:6px 20px;cursor:pointer;">Enviar</button>';
        modal_content += '</div>';
        modal_content += '</div></div>';

        $('body').append(modal_content);

        // Tornar a janela arrastável pela titlebar
        var $dlg = $('#user_dialog_'+to_user_id);
        var isDragging = false, startX, startY, startLeft, startTop;

        $dlg.find('.gei-dialog-titlebar').on('mousedown', function(e){
            if ($(e.target).hasClass('gei-dialog-close')) return;
            isDragging = true;
            // Converter bottom/right para top/left para poder arrastar
            var offset = $dlg.offset();
            $dlg.css({
                top:    offset.top + 'px',
                left:   offset.left + 'px',
                bottom: 'auto',
                right:  'auto'
            });
            startX = e.clientX;
            startY = e.clientY;
            startLeft = offset.left;
            startTop  = offset.top;
            e.preventDefault();
        });

        $(document).on('mousemove.drag_'+to_user_id, function(e){
            if (!isDragging) return;
            var dx = e.clientX - startX;
            var dy = e.clientY - startY;
            $dlg.css({
                left: Math.max(0, startLeft + dx) + 'px',
                top:  Math.max(0, startTop  + dy) + 'px'
            });
        });

        $(document).on('mouseup.drag_'+to_user_id, function(){
            isDragging = false;
        });

        fetch_user_chat_history(to_user_id, true);
    }

    // Limpar histórico
    $(document).on('click', '.gei-clear-history', function(){
        var to_user_id = $(this).data('touserid');
        if (!confirm('Tem a certeza que quer limpar todo o histórico desta conversa?')) return;
        $.ajax({
            url: "clear_chat_history.php",
            method: "POST",
            data: {to_user_id: to_user_id},
            success: function(){
                $('#chat_history_'+to_user_id).html('');
            }
        });
    });

    // Fechar dialog pelo botão custom
    $(document).on('click', '.gei-dialog-close', function(){
        var uid = $(this).data('closeid');
        $(document).off('mousemove.drag_'+uid).off('mouseup.drag_'+uid);
        $('#user_dialog_'+uid).remove();
    });

    $(document).on('click', '.start_chat', function(){
        var to_user_id   = $(this).data('touserid');
        var to_user_name = $(this).data('tousername');
        make_chat_dialog_box(to_user_id, to_user_name);
    });

    $(document).on('click', '.send_chat', function(){
        var to_user_id   = $(this).attr('id');
        var chat_message = $.trim($('#chat_message_'+to_user_id).val());
        if(chat_message !== ''){
            $.ajax({
                url: "insert_chat.php",
                method: "POST",
                data: {to_user_id: to_user_id, chat_message: chat_message},
                success: function(data){
                    $('#chat_message_'+to_user_id).val('');
                    $('#chat_history_'+to_user_id).html(data);
                    var $h = $('#chat_history_'+to_user_id);
                    $h.scrollTop($h[0].scrollHeight);
                }
            });
        } else {
            alert('Escreva algo antes de enviar!');
        }
    });

    function fetch_user_chat_history(to_user_id, forceScroll){
        $.ajax({
            url: "fetch_user_chat_history.php",
            method: "POST",
            data: {to_user_id: to_user_id},
            success: function(data){
                var $h = $('#chat_history_'+to_user_id);
                // Verificar se o utilizador está perto do fundo (margem de 40px)
                var atBottom = $h[0].scrollHeight - $h.scrollTop() - $h.outerHeight() < 40;
                $h.html(data);
                // Só fazer scroll automático se já estava no fundo ou se é a primeira carga
                if (atBottom || forceScroll) {
                    $h.scrollTop($h[0].scrollHeight);
                }
            }
        });
    }

    function update_chat_history_data(){
        $('.chat_history').each(function(){
            var to_user_id = $(this).data('touserid');
            fetch_user_chat_history(to_user_id, false);
        });
    }

    // Fechar via tecla Escape (jQuery UI nativo) — sem handler adicional necessário

    $(document).on('focus', '.chat_message', function(){
        $.ajax({ url: "update_is_type_status.php", method: "POST", data: {is_type:'yes'} });
    });
    $(document).on('blur', '.chat_message', function(){
        $.ajax({ url: "update_is_type_status.php", method: "POST", data: {is_type:'no'} });
    });

    $(document).on('click', '.remove_chat', function(){
        var chat_message_id = $(this).attr('id');
        if(confirm("Tem a certeza que quer eliminar a mensagem?")){
            $.ajax({
                url: "remove_chat.php",
                method: "POST",
                data: {chat_message_id: chat_message_id},
                success: function(){ update_chat_history_data(); }
            });
        }
    });
});
</script>




   <!-- ═══ TEMA ESCURO ═══ -->
      <script src="../js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->


     <script>
// Botão toggle inserido na navbar antes do bloco do utilizador
(function() {
    function insertToggle() {
        var navUser = document.querySelector('.nav-user');
        if (!navUser) { setTimeout(insertToggle, 50); return; }

        var btn = document.createElement('button');
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        btn.innerHTML = isDark ? '☀️ Claro' : '🌙 Escuro';
        btn.style.cssText = [
            'background:rgba(255,255,255,.12)',
            'border:1px solid rgba(255,255,255,.25)',
            'border-radius:5px',
            'color:#fff',
            'font-size:12px',
            'font-weight:600',
            'padding:5px 12px',
            'cursor:pointer',
            'font-family:inherit',
            'transition:background .15s',
            'white-space:nowrap',
            'margin-right:8px'
        ].join(';');

        btn.addEventListener('click', function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            window.GEITheme.toggle();
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            btn.innerHTML = dark ? '☀️ Claro' : '🌙 Escuro';
        }, true);

        navUser.parentNode.insertBefore(btn, navUser);
    }
    document.addEventListener('DOMContentLoaded', insertToggle);
})();
</script>



</body>
</html>
