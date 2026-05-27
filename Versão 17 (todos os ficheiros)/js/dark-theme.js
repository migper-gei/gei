/**
 * GEI — Dark Theme Toggle
 * Ficheiro: js/dark-theme.js
 *
 * Como funciona:
 *  - Aplica [data-theme="dark"] no <html> para ativar o tema escuro
 *  - Persiste a preferência em localStorage (escolha manual tem prioridade)
 *  - Respeita prefers-color-scheme na primeira visita e reage a mudanças em tempo real
 *  - Sincroniza entre abas abertas do mesmo browser
 *  - Inicializa ANTES do paint (sem flash) quando o snippet inline existe no <head>
 *
 * INSTALAÇÃO ANTI-FLASH — adicionar em head.php como PRIMEIRO elemento dentro de <head>:
 * ─────────────────────────────────────────────────────────────────────────────────────────
 * <script>
 * (function(){
 *   var s=localStorage.getItem('gei-theme');
 *   var t=s||(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
 *   document.documentElement.setAttribute('data-theme',t);
 * })();
 * </script>
 * <link rel="stylesheet" href="<?php echo SVRURL ?>css/dark-theme.css">
 * ─────────────────────────────────────────────────────────────────────────────────────────
 * O script inline define data-theme ANTES de qualquer CSS ser aplicado,
 * eliminando o flash de tema errado (FOUC).
 * O dark-theme.js continua a carregar no fim do <body> como habitualmente.
 */

(function () {
  "use strict";

  const STORAGE_KEY = "gei-theme";
  const DARK        = "dark";
  const LIGHT       = "light";

  /* ── 1. Lê preferência guardada (ou deteta sistema) ── */
  function getStoredTheme() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch (_) {
      return null;
    }
  }

  function getSystemTheme() {
    return window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
      ? DARK
      : LIGHT;
  }

  function resolveTheme() {
    return getStoredTheme() || getSystemTheme();
  }

  /* ── 2. Aplica / remove tema ── */
  function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
    // Atualiza meta-tag para mobile browser chrome
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
      meta.content = theme === DARK ? "#0f1117" : "#56baed";
    }
  }

  /* ── 3. Guarda e aplica ── */
  function setTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (_) {}
    applyTheme(theme);
    updateToggles(theme);
  }

  /* ── 4. Alterna entre claro e escuro ── */
  function toggleTheme() {
    const current = document.documentElement.getAttribute("data-theme");
    setTheme(current === DARK ? LIGHT : DARK);
  }

  /* ── 5. Atualiza estado visual de todos os toggles na página ── */
  function updateToggles(theme) {
    document
      .querySelectorAll(".gei-theme-toggle")
      .forEach(function (btn) {
        const icon  = btn.querySelector(".gei-toggle-icon");
        const label = btn.querySelector(".gei-toggle-label");
        if (icon)  icon.textContent  = theme === DARK ? "☀️" : "🌙";
        if (label) label.textContent = theme === DARK ? "Claro" : "Escuro";
        btn.setAttribute("aria-pressed", theme === DARK ? "true" : "false");
        btn.setAttribute(
          "title",
          theme === DARK ? "Ativar tema claro" : "Ativar tema escuro"
        );
      });
  }

  /* ── 6. Liga cliques nos botões toggle ── */
  function initToggles() {
    // Delegação de eventos — funciona mesmo para botões criados depois
    document.addEventListener("click", function (e) {
      if (e.target.closest(".gei-theme-toggle")) {
        toggleTheme();
      }
    });

    // Sincroniza estado visual com o tema atual
    updateToggles(resolveTheme());
  }

  /* ── 7. Reage a alterações no sistema (ex: SO muda de claro→escuro) ── */
  function watchSystemTheme() {
    if (!window.matchMedia) return;
    window
      .matchMedia("(prefers-color-scheme: dark)")
      .addEventListener("change", function (e) {
        // Só aplica automaticamente se o utilizador nunca escolheu manualmente
        if (!getStoredTheme()) {
          applyTheme(e.matches ? DARK : LIGHT);
          updateToggles(e.matches ? DARK : LIGHT);
        }
      });
  }

  /* ── 8. Sincroniza entre abas do mesmo browser ── */
  function watchStorage() {
    window.addEventListener("storage", function (e) {
      if (e.key === STORAGE_KEY && e.newValue) {
        applyTheme(e.newValue);
        updateToggles(e.newValue);
      }
    });
  }

  /* ════════════════════
     INICIALIZAÇÃO
     ════════════════════ */

  // Aplica o tema IMEDIATAMENTE (antes do DOMContentLoaded) para evitar flash
  applyTheme(resolveTheme());

  // Ativa transições CSS apenas DEPOIS do primeiro render (evita animação no load)
  window.addEventListener("load", function () {
    document.documentElement.classList.add("gei-theme-ready");
  });

  // Aguarda DOM para ligar eventos
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initToggles();
      watchSystemTheme();
      watchStorage();
    });
  } else {
    // DOM já está pronto
    initToggles();
    watchSystemTheme();
    watchStorage();
  }

  /* ── API pública (opcional, para uso via console ou outros scripts) ── */
  window.GEITheme = {
    toggle: toggleTheme,
    set:    setTheme,
    get:    function () {
      return document.documentElement.getAttribute("data-theme");
    }
  };
})();
