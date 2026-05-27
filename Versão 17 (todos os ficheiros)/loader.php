<!-- ══ LOADER ══════════════════════════════════════════════════ -->
<style>
#gei-loader-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(255, 255, 255, 0.92);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 18px;
    transition: opacity .35s ease;
}

#gei-loader-overlay.gei-loader-hide {
    opacity: 0;
    pointer-events: none;
}

.gei-spinner {
    width: 52px;
    height: 52px;
    border: 5px solid #e4e9f0;
    border-top-color: #003366;
    border-radius: 50%;
    animation: gei-spin .75s linear infinite;
}

.gei-loader-text {
    font-family: 'Nunito', sans-serif;
    font-size: .82rem;
    font-weight: 600;
    color: #5a6370;
    letter-spacing: .06em;
    text-transform: uppercase;
}

@keyframes gei-spin {
    to { transform: rotate(360deg); }
}
</style>

<div id="gei-loader-overlay">
    <div class="gei-spinner"></div>
    <span class="gei-loader-text">A carregar…</span>
</div>

<script>
(function () {
    function geiHideLoader() {
        var el = document.getElementById('gei-loader-overlay');
        if (!el) return;
        el.classList.add('gei-loader-hide');
        setTimeout(function () {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 380);
    }
    // Esconder assim que possível — independentemente do estado do readyState
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(geiHideLoader, 0);
    } else {
        window.addEventListener('DOMContentLoaded', geiHideLoader);
        window.addEventListener('load', geiHideLoader);
    }
    // Segurança: forçar remoção ao fim de 2s mesmo que os eventos falhem
    setTimeout(geiHideLoader, 2000);
})();
</script>
<!-- ══ FIM LOADER ══════════════════════════════════════════════ -->
