<?php
// Headers de segurança HTTP
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // HSTS — apenas activado em HTTPS para evitar quebrar HTTP local/dev
    // HTTP_X_FORWARDED_PROTO cobre cenários com reverse proxy (nginx, Apache, Cloudflare)
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
               || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

include ("svrurl.php");
?>

<!-- ═══ TEMA ESCURO — anti-flash: DEVE SER O PRIMEIRO ELEMENTO DO <head> ═══
     Define data-theme no <html> ANTES de qualquer CSS ser aplicado,
     eliminando o flash de tema errado (FOUC) na primeira visita.        ═══ -->
<script>
(function(){
  try {
    var t = localStorage.getItem('gei-theme');
    if (!t) t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', t);
  } catch(e) {}
})();
</script>
<!-- ════════════════════════════════════════════════════════════════════════ -->

<!-- basic -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- mobile metas -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- site metas -->
<meta name="keywords" content="GEI">
<meta name="description" content="Gestão do Equipamento Informático">
<meta name="author" content="">

<title>Sistema de Gestão de Equipamentos Informáticos</title>

<!-- favicon -->
<link rel="icon" href="<?php echo SVRURL ?>images/logo.png" type="image/gif">

<!-- Bootstrap 4.6 (apenas CDN — remover se preferir versão local) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Google Fonts — preconnect elimina latência DNS/TLS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Raleway (headings) + DM Sans (corpo) + DM Mono (valores numéricos) -->
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- SweetAlert 1 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

<!-- Estilos locais -->
<!-- tokens.css deve vir PRIMEIRO — define as variáveis --font-* usadas por todos os outros -->
<link rel="stylesheet" href="<?php echo SVRURL ?>css/tokens.css">
<link rel="stylesheet" href="<?php echo SVRURL ?>css/style.css">
<link rel="stylesheet" href="<?php echo SVRURL ?>css/style_login.css">
<link rel="stylesheet" href="<?php echo SVRURL ?>css/responsive.css">
<link rel="stylesheet" href="<?php echo SVRURL ?>css/jquery.mCustomScrollbar.min.css">

<!-- ═══ TEMA ESCURO — deve ser o ÚLTIMO CSS para sobrepor todos os outros ═══ -->
<link rel="stylesheet" href="<?php echo SVRURL ?>css/dark-theme.css">
<!-- ════════════════════════════════════════════════════════════════════════ -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Popper.js (necessário para Bootstrap 4 dropdowns/tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Bootstrap 4 JS (necessário para navbar collapse, dropdowns, modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<!-- SweetAlert 1 -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<!-- Scripts locais -->
<script src="<?php echo SVRURL ?>js/sort-table2.js"></script>

<!-- PWA: Manifest -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#4b6cb7">

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="GEI">
<link rel="apple-touch-icon" href="/images/logo.png">

<!-- PWA: Service Worker -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(function(reg) {
                setInterval(function() { reg.update(); }, 60 * 60 * 1000);
                reg.onupdatefound = function() {
                    var newWorker = reg.installing;
                    newWorker.onstatechange = function() {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            var bar = document.createElement('div');
                            bar.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:#4b6cb7;color:#fff;text-align:center;padding:10px 16px;font-size:.88rem;font-weight:600;z-index:99999;display:flex;align-items:center;justify-content:center;gap:12px;';
                            bar.innerHTML = '<span>Nova versão disponível.</span><button onclick="navigator.serviceWorker.controller.postMessage({type:\'SKIP_WAITING\'});window.location.reload();" style="background:#fff;color:#4b6cb7;border:none;border-radius:6px;padding:4px 14px;font-weight:700;cursor:pointer;">Atualizar</button>';
                            document.body.appendChild(bar);
                        }
                    };
                };
            })
            .catch(function(err) {
                console.warn('[PWA] Service Worker não registado:', err);
            });
    });
}
</script>

<!-- ═══ TEMA ESCURO — lógica principal carregada do ficheiro externo ═══
     Inclui: toggle, prefers-color-scheme, localStorage, sincronização
     entre abas. Carrega no fim do <head> após todos os outros scripts. ═══ -->
<script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
<!-- ════════════════════════════════════════════════════════════════════ -->
