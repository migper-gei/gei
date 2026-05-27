/**
 * GEI — Timeline de Avarias por Equipamento
 * Ficheiro: js/equipamento-timeline.js
 *
 * Uso na página de detalhe do equipamento:
 * ─────────────────────────────────────────
 * <div id="timeline-wrap" data-id-equip="<?= $id_equip ?>"></div>
 * <script src="<?php echo SVRURL ?>js/equipamento-timeline.js"></script>
 *
 * Ou para carregar manualmente:
 *   GEITimeline.carregar(123);
 */

(function () {
  "use strict";

  const ENDPOINT = "equipamento_timeline.php";

  /* ── Carregar timeline via fetch ──────────────────────────── */
  function carregar(idEquip) {
    const wrap = document.getElementById("timeline-wrap");
    if (!wrap) return;

    idEquip = idEquip || parseInt(wrap.dataset.idEquip, 10);
    if (!idEquip || isNaN(idEquip)) {
      wrap.innerHTML = _erro("ID de equipamento em falta.");
      return;
    }

    // Estado de loading
    wrap.innerHTML = [
      '<div class="tl-loading">',
      '  <i class="fas fa-spinner fa-spin"></i> A carregar histórico de avarias...',
      '</div>',
    ].join("");

    fetch(ENDPOINT + "?id_equip=" + idEquip, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then(function (r) {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.text();
      })
      .then(function (html) {
        wrap.innerHTML = html;
        _animarEntrada(wrap);
      })
      .catch(function (err) {
        console.error("[GEITimeline] Erro ao carregar:", err);
        wrap.innerHTML = _erro("Não foi possível carregar o histórico de avarias.");
      });
  }

  /* ── Animação de entrada dos itens ────────────────────────── */
  function _animarEntrada(wrap) {
    var items = wrap.querySelectorAll(".tl-item");
    items.forEach(function (el, i) {
      el.style.opacity  = "0";
      el.style.transform = "translateY(14px)";
      el.style.transition = "opacity .35s ease, transform .35s ease";
      setTimeout(function () {
        el.style.opacity  = "1";
        el.style.transform = "translateY(0)";
      }, 60 + i * 70); // escalonado por item
    });

    var stats = wrap.querySelectorAll(".tl-stat");
    stats.forEach(function (el, i) {
      el.style.opacity  = "0";
      el.style.transform = "translateY(8px)";
      el.style.transition = "opacity .3s ease, transform .3s ease";
      setTimeout(function () {
        el.style.opacity  = "1";
        el.style.transform = "translateY(0)";
      }, i * 60);
    });
  }

  /* ── Mensagem de erro ─────────────────────────────────────── */
  function _erro(msg) {
    return (
      '<div class="tl-erro">' +
      '<i class="fas fa-exclamation-triangle"></i> ' +
      msg +
      "</div>"
    );
  }

  /* ── Auto-inicialização ───────────────────────────────────── */
  function init() {
    var wrap = document.getElementById("timeline-wrap");
    if (wrap && wrap.dataset.idEquip) {
      carregar(parseInt(wrap.dataset.idEquip, 10));
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  /* ── API pública ──────────────────────────────────────────── */
  window.GEITimeline = {
    carregar: carregar,
  };
})();
