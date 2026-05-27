/**
 * GEI - Aviso de timeout de sessão
 * Mostra modal 2 minutos antes do timeout com contagem decrescente.
 * Requer: GEI_SESSION_TIMEOUT e GEI_SVRURL definidos antes deste script.
 */
(function () {
    'use strict';

    // ── Configuração ────────────────────────────────────────────────────────
    const WARN_BEFORE_SEC = 300;   // mostrar aviso 5 min antes
    const POLL_INTERVAL   = 5000;  // verificar a cada 5 s

    const timeout = (typeof GEI_SESSION_TIMEOUT !== 'undefined') ? GEI_SESSION_TIMEOUT : 5400;
    const svrurl  = (typeof GEI_SVRURL         !== 'undefined') ? GEI_SVRURL          : '/';

    let warnTimer     = null;
    let countdownTimer= null;
    let sessionStart  = Date.now();
    let warningShown  = false;

    // ── Injectar estilos ────────────────────────────────────────────────────
    const css = `
    #gei-session-overlay {
        position: fixed; inset: 0; z-index: 99998;
        background: rgba(10,15,30,.55);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity .25s;
        pointer-events: none;
    }
    #gei-session-overlay.visible {
        opacity: 1; pointer-events: all;
    }
    #gei-session-modal {
        background: #fff; border-radius: 14px;
        box-shadow: 0 8px 40px rgba(0,0,0,.22);
        padding: 32px 36px; max-width: 400px; width: 90%;
        text-align: center; position: relative;
        transform: translateY(18px); transition: transform .25s;
    }
    #gei-session-overlay.visible #gei-session-modal {
        transform: translateY(0);
    }
    [data-theme="dark"] #gei-session-modal {
        background: #1a1d27; color: #e2e8f0;
        box-shadow: 0 8px 40px rgba(0,0,0,.55);
    }
    .gei-sw-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: #fff4e5; border: 2px solid #f6c23e;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; font-size: 1.6rem;
    }
    [data-theme="dark"] .gei-sw-icon { background: #2d2510; border-color: #c8860a; }
    .gei-sw-title {
        font-size: 1.05rem; font-weight: 800; color: #1e2a45; margin-bottom: 6px;
    }
    [data-theme="dark"] .gei-sw-title { color: #e2e8f0; }
    .gei-sw-sub {
        font-size: .84rem; color: #7b88a0; margin-bottom: 20px; line-height: 1.5;
    }
    .gei-sw-countdown {
        font-size: 2.4rem; font-weight: 900; color: #e74a3b;
        letter-spacing: -1px; margin-bottom: 6px; line-height: 1;
    }
    .gei-sw-countdown-label {
        font-size: .72rem; color: #7b88a0; margin-bottom: 24px;
    }
    .gei-sw-progress {
        height: 5px; background: #f0f4fb; border-radius: 3px;
        margin-bottom: 24px; overflow: hidden;
    }
    [data-theme="dark"] .gei-sw-progress { background: #252836; }
    .gei-sw-progress-bar {
        height: 100%; border-radius: 3px;
        background: linear-gradient(90deg, #f6c23e, #e74a3b);
        transition: width .9s linear;
    }
    .gei-sw-actions {
        display: flex; gap: 10px; justify-content: center;
    }
    .gei-sw-btn {
        padding: 10px 22px; border-radius: 8px; font-size: .88rem;
        font-weight: 700; cursor: pointer; border: none;
        transition: opacity .15s, transform .1s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 7px;
    }
    .gei-sw-btn:hover { opacity: .85; transform: translateY(-1px); }
    .gei-sw-btn.primary { background: #4b6cb7; color: #fff; }
    .gei-sw-btn.danger  { background: #f0f4fb; color: #e74a3b;
                          border: 1.5px solid #f5c6c6; }
    [data-theme="dark"] .gei-sw-btn.danger { background: #1e2130; border-color: #5a2020; }
    `;

    const styleEl = document.createElement('style');
    styleEl.textContent = css;
    document.head.appendChild(styleEl);

    // ── Construir modal ─────────────────────────────────────────────────────
    const overlay = document.createElement('div');
    overlay.id = 'gei-session-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'gei-sw-title-el');
    overlay.innerHTML = `
        <div id="gei-session-modal">
            <div class="gei-sw-icon">⏱️</div>
            <div class="gei-sw-title" id="gei-sw-title-el">Sessão prestes a expirar</div>
            <div class="gei-sw-sub">A sua sessão vai terminar automaticamente por inatividade.</div>
            <div class="gei-sw-countdown" id="gei-sw-countdown">5:00</div>
            <div class="gei-sw-countdown-label">restantes</div>
            <div class="gei-sw-progress">
                <div class="gei-sw-progress-bar" id="gei-sw-bar" style="width:100%"></div>
            </div>
            <div class="gei-sw-actions">
                <button class="gei-sw-btn primary" id="gei-sw-continue">
                    ✓ Continuar sessão
                </button>
                <a class="gei-sw-btn danger" id="gei-sw-logout" href="${svrurl}sair">
                    → Terminar sessão
                </a>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    // ── Mostrar aviso ───────────────────────────────────────────────────────
    function showWarning() {
        if (warningShown) return;
        warningShown = true;
        overlay.classList.add('visible');

        let remaining = WARN_BEFORE_SEC;
        updateCountdown(remaining);

        countdownTimer = setInterval(() => {
            remaining--;
            updateCountdown(remaining);
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                expireSession();
            }
        }, 1000);
    }

    function updateCountdown(secs) {
        const m = Math.floor(secs / 60);
        const s = secs % 60;
        document.getElementById('gei-sw-countdown').textContent =
            `${m}:${String(s).padStart(2, '0')}`;
        const pct = (secs / WARN_BEFORE_SEC) * 100;
        document.getElementById('gei-sw-bar').style.width = pct + '%';
    }

    function hideWarning() {
        overlay.classList.remove('visible');
        warningShown = false;
        if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
    }

    function expireSession() {
        // Redirecionar para login com flag de timeout
        window.location.href = svrurl + 'i?timeout=1';
    }

    // ── Renovar sessão (botão Continuar) ────────────────────────────────────
    document.getElementById('gei-sw-continue').addEventListener('click', function () {
        this.disabled = true;
        this.textContent = 'A renovar…';

        fetch(svrurl + 'sessao_keepalive.php', { method: 'POST', credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    sessionStart = Date.now();
                    hideWarning();
                    scheduleWarning();
                    this.disabled = false;
                    this.innerHTML = '✓ Continuar sessão';
                } else {
                    expireSession();
                }
            })
            .catch(() => expireSession());
    });

    // ── Agendar aviso ────────────────────────────────────────────────────────
    function scheduleWarning() {
        if (warnTimer) clearTimeout(warnTimer);
        const warnAt = (timeout - WARN_BEFORE_SEC) * 1000;
        if (warnAt <= 0) {
            // Timeout muito curto — mostrar já
            showWarning();
            return;
        }
        warnTimer = setTimeout(showWarning, warnAt);
    }

    // ── Resetar contagem em actividade do utilizador ────────────────────────
    // (apenas eventos significativos — não resetar no mousemove para não
    //  fazer chamadas desnecessárias ao servidor)
    let lastPing = Date.now();
    const ACTIVITY_THROTTLE = 60000; // no máximo 1 ping/min por actividade

    function onActivity() {
        const now = Date.now();
        if (warningShown) return; // já em aviso, não resetar
        if (now - lastPing < ACTIVITY_THROTTLE) return;
        lastPing = now;

        // Ping silencioso ao servidor para manter sessão viva
        fetch(svrurl + 'sessao_keepalive.php', { method: 'POST', credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    sessionStart = Date.now();
                    scheduleWarning();
                } else if (data.expired) {
                    expireSession();
                }
            })
            .catch(() => {}); // silencioso
    }

    ['click', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
        document.addEventListener(evt, onActivity, { passive: true });
    });

    // ── Arrancar ─────────────────────────────────────────────────────────────
    scheduleWarning();

})();
