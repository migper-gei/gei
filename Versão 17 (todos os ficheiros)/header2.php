<!-- header -->
<header>
  <style>
    /* Fontes carregadas globalmente em head.php — usar var(--font-heading) e var(--font-body) */

    :root {
      --primary-color: #4b6cb7;
      --header-accent: #f39c12;
    }

    /* ── Logo block ── */
    .gei-logo-block {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .gei-logo-icon {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      background: rgba(255,255,255,0.12);
    }

    .gei-logo-icon i {
      font-size: 22px;
      color: rgba(255,255,255,0.90);
    }

    .gei-logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1.15;
    }

    .gei-logo-name {
      font-family: var(--font-heading);
      font-size: 1.05rem;
      font-weight: 700;
      color: rgba(255,255,255,0.88);
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    /* ── Title block ── */
    .header-title-block {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      gap: 2px;
    }

    .header-main-title {
      font-family: var(--font-heading);
      font-size: clamp(0.78rem, 2vw, 1.15rem);
      font-weight: 800;
      color: #ffffff;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      line-height: 1.18;
      white-space: nowrap;
      margin: 0;
      text-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }

    .header-title-accent {
      display: block;
      width: 44px;
      height: 3px;
      background: linear-gradient(90deg, #f39c12, #e67e22);
      border-radius: 2px;
      margin: 4px 0 3px;
    }

    .header-subtitle {
      font-family: var(--font-heading);
      font-size: clamp(0.62rem, 1.2vw, 0.82rem);
      font-weight: 500;
      color: rgba(255,255,255,0.65);
      letter-spacing: 0.14em;
      text-transform: uppercase;
      margin: 0;
      white-space: nowrap;
    }

    /* ── Right-side controls ── */
    .header-nav-buttons {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    /* Vertical separator */
    .gei-divider {
      width: 1px;
      height: 32px;
      background: rgba(255,255,255,0.3);
      flex-shrink: 0;
    }

    /* Dark-mode toggle */
    .gei-theme-toggle {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      background: none;
      border: none;
      padding: 4px 6px;
      flex-shrink: 0;
    }

    .gei-toggle-icon {
      font-size: 16px;
      line-height: 1;
      transition: transform .4s ease;
    }

    .gei-toggle-track {
      position: relative;
      width: 36px;
      height: 20px;
      background: rgba(255,255,255,0.28);
      border-radius: 10px;
      flex-shrink: 0;
      transition: background .3s ease;
    }

    .gei-toggle-knob {
      position: absolute;
      top: 3px;
      left: 3px;
      width: 14px;
      height: 14px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 1px 4px rgba(0,0,0,.3);
      transition: transform .3s cubic-bezier(.34,1.56,.64,1);
    }

    /* Home button — sem borda */
    .home-button {
      background-color: rgba(255,255,255,0.12);
      border: none;
      color: #ffffff;
      border-radius: 10px;
      width: 42px;
      height: 42px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      text-decoration: none;
      flex-shrink: 0;
    }

    .home-button i {
      font-size: 18px;
    }

    .home-button:hover {
      background-color: rgba(255,255,255,0.22);
      transform: translateY(-2px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }

    /* Login button — sem borda */
    .login-link {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-family: var(--font-heading);
      font-size: clamp(0.72rem, 1.2vw, 0.88rem);
      font-weight: 700;
      letter-spacing: 0.10em;
      text-transform: uppercase;
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: 10px;
      padding: 8px 16px;
      white-space: nowrap;
      flex-shrink: 0;
      background: rgba(243,156,18,0.85);
      transition: all 0.25s ease;
    }

    .login-link .login-icon {
      font-size: 16px;
    }

    .login-link:hover {
      background: rgba(243,156,18,1);
      transform: translateY(-1px);
      box-shadow: 0 3px 10px rgba(243,156,18,0.35);
    }

    /* Row layout */
    .header .row {
      flex-wrap: nowrap !important;
      align-items: center;
      gap: 16px;
    }

    /* ── Mobile ── */
    @media (max-width: 576px) {
      .header .row {
        flex-wrap: wrap !important;
        justify-content: space-between;
        gap: 6px;
        padding: 6px 0;
      }

      .gei-logo-block {
        order: 0;
        flex: 0 0 auto;
      }

      .header-nav-buttons {
        order: 0;
        flex: 0 0 auto;
        gap: 7px;
      }

      .header-title-block-wrap {
        width: 100%;
        order: 1;
      }

      .home-button {
        width: 36px !important;
        height: 36px !important;
      }

      .gei-logo-icon {
        width: 38px !important;
        height: 38px !important;
      }
    }

    @media (max-width: 400px) {
      .header-subtitle   { display: none; }
      .header-title-accent { display: none; }
    }

    /* ── Dark theme states ── */
    [data-theme="dark"] .gei-theme-toggle { color: rgba(255,255,255,0.9) !important; }
    [data-theme="dark"] .gei-toggle-knob  { transform: translateX(16px) !important; }
    [data-theme="dark"] .gei-toggle-icon  { transform: rotate(180deg) !important; }
    [data-theme="dark"] .gei-toggle-track { background: #4e73df !important; }
  </style>

  <!-- header inner -->
  <div class="header">
    <div class="container-fluid">
      <div class="row">

        <!-- ── Logo ── -->
        <div class="col-auto logo_section" style="padding-right:0;">
          <div class="gei-logo-block">
            <div class="gei-logo-icon">
              <i class="fas fa-desktop"></i>
            </div>
            <div class="gei-logo-text">
              <span class="gei-logo-name">SGEI</span>
            </div>
          </div>
        </div>

        <!-- ── Title ── -->
        <div class="header-title-block-wrap" style="flex: 1 1 auto; min-width: 0;">
          <div class="header-title-block">
            <h2 class="header-main-title">
              Sistema de Gestão de Equipamentos Informáticos
            </h2>
            <span class="header-title-accent"></span>
            <h4 class="header-subtitle">
              Inventário &amp; Manutenção · Solução Completa
            </h4>
          </div>
        </div>

        <!-- ── Right controls ── -->
        <div class="header-nav-buttons">

          <!-- Dark-mode toggle -->
          <button class="gei-theme-toggle" type="button"
                  aria-pressed="false" title="Ativar tema escuro">
            <span class="gei-toggle-icon">🌙</span>
            <span class="gei-toggle-track">
              <span class="gei-toggle-knob"></span>
            </span>
          </button>

          <!-- Separator -->
          <div class="gei-divider"></div>

          <?php if(isset($_SESSION['login_user'])): ?>

            <!-- HOME — autenticado -->
            <a title="Início" href="<?php echo SVRURL; ?>i" class="home-button">
              <i class="fas fa-home"></i>
            </a>

          <?php else: ?>

            <!-- HOME — visitante -->
            <a title="Início" href="<?php echo SVRURL; ?>i" class="home-button">
              <i class="fas fa-home"></i>
            </a>

            <!-- LOGIN -->
            <?php if (empty($hideLoginButton)): ?>
              <a title="Login" href="<?php echo SVRURL; ?>l" class="login-link">
                <span class="login-icon">→</span>
                <span>Login</span>
              </a>
            <?php endif; ?>

          <?php endif; ?>

        </div><!-- /header-nav-buttons -->

      </div>
    </div>
  </div>
</header>
<!-- end header -->

<!-- ═══ TEMA ESCURO — JS ═══ -->
<script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
<!-- ═══════════════════════ -->
