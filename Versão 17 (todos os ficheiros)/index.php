<!DOCTYPE html>
<html lang="pt">

<?php 



include ("head.php"); ?>

<head>
    <style>
        :root {
            --primary:     #4b6cb7;
            --primary-dk:  #182848;
            --accent:      #507feb;
            --accent2:     #36b9cc;
            --success:     #1cc88a;
            --warning:     #f6c23e;
            --danger:      #e74a3b;
            --purple:      #6f42c1;
            --bg:          #f0f4fb;
            --surface:     #ffffff;
            --border:      #e3e8f4;
            --text:        #1e2a45;
            --muted:       #7b88a0;
            --radius:      10px;
            --shadow:      0 2px 12px rgba(75,108,183,.10);
            --shadow-lg:   0 6px 24px rgba(75,108,183,.16);
        }

        /* ══ HERO ══ */
        .gei-hero {
            background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 100%);
            border-radius: var(--radius);
            padding: 40px 32px;
            margin: 28px auto;
            max-width: 1080px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .gei-hero::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .gei-hero::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -30px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }
        .gei-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            color: var(--warning);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .72rem;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid rgba(246,194,62,0.30);
        }
        .gei-hero h2 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 10px;
            letter-spacing: -.4px;
            line-height: 1.3;
        }
        .gei-hero p {
            font-size: .90rem;
            color: rgba(255,255,255,0.65);
            margin: 0 0 24px;
            line-height: 1.65;
        }
        .gei-hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .gei-hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--warning);
            color: var(--primary-dk);
            border-radius: 8px;
            padding: 10px 26px;
            font-size: .86rem;
            font-weight: 700;
            text-decoration: none !important;
            transition: opacity .2s, transform .15s;
        }
        .gei-hero-btn:hover {
            opacity: .90;
            transform: translateY(-2px);
        }
        .gei-hero-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px; height: 42px;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.40);
            color: #fff !important;
            text-decoration: none !important;
            font-size: 1rem;
            transition: background .2s, transform .15s;
        }
        .gei-hero-home:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        /* ══ STATS ══ */
        .gei-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 1080px;
            margin: 0 auto 28px;
            padding: 0 15px;
        }
        .gei-stat {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 16px;
            text-align: center;
            box-shadow: var(--shadow);
            border-top: 3px solid var(--accent);
        }
        .gei-stat-num {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.1;
        }
        .gei-stat-label {
            font-size: .70rem;
            color: var(--muted);
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ══ SECÇÃO GERAL ══ */
        .gei-section {
            padding: 0 0 28px;
        }
        .gei-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 1080px;
            margin: 0 auto 14px;
            padding: 0 15px;
        }
        .gei-section-head h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-dk);
            margin: 0;
            white-space: nowrap;
        }
        .gei-section-head-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .gei-section-count {
            font-size: .72rem;
            color: var(--muted);
            background: var(--bg);
            border-radius: 20px;
            padding: 2px 10px;
            white-space: nowrap;
        }

        /* ══ FILTROS ══ */
        .gei-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            max-width: 1080px;
            margin: 0 auto 16px;
            padding: 0 15px;
        }
        .gei-filter-btn {
            font-size: .76rem;
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            font-weight: 500;
        }
        .gei-filter-btn:hover {
            border-color: var(--accent);
            color: var(--primary);
        }
        .gei-filter-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ══ GRELHA DE CARDS ══ */
        .feat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .feat-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: var(--shadow);
            border-left: 3px solid var(--accent);
            display: flex;
            align-items: center;
            gap: 13px;
            transition: transform .2s, box-shadow .2s;
        }
        .feat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        .feat-card.hidden {
            display: none;
        }

        /* Cores por categoria */
        .feat-card[data-cat="inventario"]   { border-left-color: #507feb; }
        .feat-card[data-cat="manutencao"]   { border-left-color: #e74a3b; }
        .feat-card[data-cat="gestao"]       { border-left-color: #6f42c1; }
        .feat-card[data-cat="relatorios"]   { border-left-color: #1cc88a; }
        .feat-card[data-cat="comunicacao"]  { border-left-color: #36b9cc; }

        .feat-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem;
            flex-shrink: 0;
        }
        .feat-card[data-cat="inventario"]  .feat-icon { background: #507feb; box-shadow: 0 2px 6px rgba(80,127,235,.30); }
        .feat-card[data-cat="manutencao"]  .feat-icon { background: #e74a3b; box-shadow: 0 2px 6px rgba(231,74,59,.30); }
        .feat-card[data-cat="gestao"]      .feat-icon { background: #6f42c1; box-shadow: 0 2px 6px rgba(111,66,193,.30); }
        .feat-card[data-cat="relatorios"]  .feat-icon { background: #1cc88a; box-shadow: 0 2px 6px rgba(28,200,138,.30); }
        .feat-card[data-cat="comunicacao"] .feat-icon { background: #36b9cc; box-shadow: 0 2px 6px rgba(54,185,204,.30); }

        .feat-body strong {
            display: block;
            font-size: .84rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
        }
        .feat-body p {
            font-size: .76rem;
            color: var(--muted);
            margin: 0;
            line-height: 1.4;
        }
        .feat-tag {
            display: inline-block;
            font-size: .65rem;
            padding: 2px 7px;
            border-radius: 20px;
            margin-top: 5px;
            font-weight: 600;
            background: #e1f5ee;
            color: #0f6e56;
        }

        /* ══ BANNER AVARIA PÚBLICA ══ */
        .gei-avaria-publica {
            max-width: 1080px;
            margin: 0 auto 20px;
            padding: 0 15px;
        }
        .gei-avaria-publica-inner {
            background: var(--surface);
            border: 1.5px solid #f5c6c6;
            border-left: 4px solid #e74a3b;
            border-radius: var(--radius);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            text-decoration: none;
            transition: background .2s, box-shadow .2s;
        }
        .gei-avaria-publica-inner:hover {
            background: #fdf3f3;
            box-shadow: 0 2px 12px rgba(231,74,59,.10);
            text-decoration: none;
        }
        .gei-avaria-publica-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .gei-avaria-publica-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: #fde8e6;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
            color: #e74a3b;
            flex-shrink: 0;
        }
        .gei-avaria-publica-copy strong {
            display: block;
            font-size: .88rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
        }
        .gei-avaria-publica-copy p {
            font-size: .78rem;
            color: var(--muted);
            margin: 0;
        }
        .gei-avaria-publica-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fde8e6;
            color: #c0392b !important;
            border-radius: 7px;
            padding: 7px 16px;
            font-size: .80rem;
            font-weight: 700;
            text-decoration: none !important;
            white-space: nowrap;
            transition: background .2s;
            flex-shrink: 0;
        }
        .gei-avaria-publica-btn:hover {
            background: #f9c9c5;
            text-decoration: none !important;
        }
        .gei-avaria-publica-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fde8e6;
            color: #c0392b;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* ══ BANNER LOGIN ══ */
        .gei-login-banner {
            max-width: 1080px;
            margin: 28px auto 0;
            padding: 0 15px;
        }
        .gei-login-inner {
            background: linear-gradient(135deg, #eef2fb 0%, #f7f9fe 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .gei-login-copy p {
            font-size: .86rem;
            color: var(--muted);
            margin: 0;
            line-height: 1.5;
        }
        .gei-login-copy strong {
            display: block;
            font-size: .95rem;
            color: var(--primary-dk);
            font-weight: 700;
            margin-bottom: 3px;
        }
        .gei-login-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff !important;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: .84rem;
            font-weight: 600;
            text-decoration: none !important;
            box-shadow: 0 3px 10px rgba(75,108,183,.35);
            transition: background .2s, transform .15s;
            white-space: nowrap;
        }
        .gei-login-cta:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        /* ══ FUNDO — respeita o tema ══ */
        html, body, body.main-layout, .about,
        .wrapper, #wrapper, #page-content-wrapper {
            background: var(--bg);
            background-image: none;
        }

        /* ══ DARK MODE ══ */
        :root,
        [data-theme="light"] {
            --bg:      #f0f4fb;
            --surface: #ffffff;
            --border:  #e3e8f4;
            --text:    #1e2a45;
            --muted:   #7b88a0;
        }

        [data-theme="dark"] {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --border:    #2a2f45;
            --text:      #e8eaf0;
            --muted:     #8b95b0;
            --primary-dk:#a8b8e8;
            --shadow:    0 2px 12px rgba(0,0,0,.35);
            --shadow-lg: 0 6px 24px rgba(0,0,0,.50);
        }

        /* Transição suave apenas após load (evita flash) */
        .gei-theme-ready * {
            transition: background-color .25s ease, color .2s ease, border-color .2s ease, box-shadow .2s ease !important;
        }

        /* Elementos que precisam de cor de texto explícita */
        [data-theme="dark"] body,
        [data-theme="dark"] .about {
            color: var(--text);
        }

        [data-theme="dark"] .gei-section-head h3 {
            color: var(--text);
        }

        [data-theme="dark"] .gei-section-count {
            background: var(--surface);
            color: var(--muted);
        }

        [data-theme="dark"] .gei-filter-btn {
            background: var(--surface);
            color: var(--muted);
            border-color: var(--border);
        }

        [data-theme="dark"] .gei-filter-btn:hover {
            color: var(--text);
        }

        [data-theme="dark"] .feat-body strong {
            color: var(--text);
        }

        [data-theme="dark"] .gei-login-inner {
            background: linear-gradient(135deg, #1a1d27 0%, #1e2230 100%);
        }

        [data-theme="dark"] .gei-login-copy strong {
            color: var(--text);
        }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 768px) {
            .feat-grid { grid-template-columns: 1fr 1fr; }
            .gei-hero  { padding: 28px 18px; margin: 16px 15px; }
            .gei-hero h2 { font-size: 1.2rem; }
        }
        @media (max-width: 480px) {
            .feat-grid { grid-template-columns: 1fr; }
            .gei-stats { grid-template-columns: repeat(3,1fr); gap: 8px; }
            .gei-stat-num { font-size: 1.2rem; }
        }
    </style>
</head>

<!-- body -->
<body class="main-layout">
    <?php include("loader.php"); ?>
    <?php include ("header2.php"); ?>

    <div class="about">

        <!-- ══ HERO ══ -->
    
            <br>
  

        <!-- ══ FUNCIONALIDADES ══ -->
        <div class="gei-section">

            <!-- Banner de acesso público — destaque máximo -->
            <div class="gei-avaria-publica">
                <a href="avaria_link.php" class="gei-avaria-publica-inner">
                    <div class="gei-avaria-publica-left">
                        <div class="gei-avaria-publica-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="gei-avaria-publica-copy">
                            <div class="gei-avaria-publica-badge">
                                <i class="fas fa-unlock-alt"></i> Acesso público — sem login
                            </div>
                            <strong>Reportar uma Avaria</strong>
                           
                        
                        </div>
                    </div>
                    <span class="gei-avaria-publica-btn">
                        <i class="fas fa-envelope-open-text"></i> Reportar agora
                    </span>
                </a>
            </div>

            <div class="gei-filters">
                <button class="gei-filter-btn active" data-filter="todas">Todas</button>
                <button class="gei-filter-btn" data-filter="inventario">Inventário</button>
                <button class="gei-filter-btn" data-filter="manutencao">Manutenção</button>
                <button class="gei-filter-btn" data-filter="gestao">Gestão</button>
                <button class="gei-filter-btn" data-filter="relatorios">Relatórios</button>
                <button class="gei-filter-btn" data-filter="comunicacao">Comunicação</button>
            </div>

            <div class="feat-grid" id="feat-grid">

                <div class="feat-card" data-cat="inventario">
                    <div class="feat-icon"><i class="fas fa-desktop"></i></div>
                    <div class="feat-body">
                        <strong>Equipamentos Diversos</strong>
                        <p>Gestão flexível de qualquer tipo de equipamento</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="gestao">
                    <div class="feat-icon"><i class="fas fa-building"></i></div>
                    <div class="feat-body">
                        <strong>Multi-Instituição</strong>
                        <p>Gestão centralizada de uma ou múltiplas instituições</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="inventario">
                    <div class="feat-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="feat-body">
                        <strong>Inventário Detalhado</strong>
                        <p>Registo completo de equipamentos e características técnicas</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="manutencao">
                    <div class="feat-icon"><i class="fas fa-tools"></i></div>
                    <div class="feat-body">
                        <strong>Gestão de Manutenção</strong>
                        <p>Histórico de avarias, reparações e tarefas preventivas</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="manutencao">
                    <div class="feat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="feat-body">
                        <strong>Reporte de Avarias</strong>
                        <p>Sistema simplificado para registo de problemas</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="gestao">
                    <div class="feat-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div class="feat-body">
                        <strong>Gestão de Movimentos</strong>
                        <p>Requisições e transferências entre salas com controlo total</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="inventario">
                    <div class="feat-icon"><i class="fas fa-qrcode"></i></div>
                    <div class="feat-body">
                        <strong>QR Codes</strong>
                        <p>Registo de avarias e identificação de equipamentos</p>
                
                    </div>
                </div>

                <div class="feat-card" data-cat="comunicacao">
                    <div class="feat-icon"><i class="fas fa-bell"></i></div>
                    <div class="feat-body">
                        <strong>Notificações Automáticas</strong>
                        <p>Alertas de reparações e atualizações de estado</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="relatorios">
                    <div class="feat-icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="feat-body">
                        <strong>Análise e Estatísticas</strong>
                        <p>Relatórios e estatísticas para apoio à decisão</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="gestao">
                    <div class="feat-icon"><i class="fas fa-file-export"></i></div>
                    <div class="feat-body">
                        <strong>Transferência de Dados</strong>
                        <p>Importação e exportação em diversos formatos</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="comunicacao">
                    <div class="feat-icon"><i class="fas fa-comments"></i></div>
                    <div class="feat-body">
                        <strong>Comunicação Integrada</strong>
                        <p>Chat entre utilizadores para colaboração em tempo real</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="gestao">
                    <div class="feat-icon"><i class="fas fa-cog"></i></div>
                    <div class="feat-body">
                        <strong>Configuração Personalizada</strong>
                        <p>Emails, tempos de sessão e políticas de password e de retenção de utilizadores adaptáveis</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="relatorios">
                    <div class="feat-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="feat-body">
                        <strong>Dashboards</strong>
                        <p>Visão geral dos equipamentos, planta das salas, calendário de reservas</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="manutencao">
                    <div class="feat-icon"><i class="fas fa-search"></i></div>
                    <div class="feat-body">
                        <strong>Estado das Avarias</strong>
                        <p>Utilizadores acompanham em tempo real o estado das suas avarias</p>
                    </div>
                </div>

                <div class="feat-card" data-cat="gestao">
                    <div class="feat-icon"><i class="fas fa-database"></i></div>
                    <div class="feat-body">
                        <strong>Backup &amp; Restauro</strong>
                        <p>Cópia e recuperação de dados de forma simples e segura</p>
                    </div>
                </div>

            </div>
        </div><!-- /gei-section -->

  

        

    </div><!-- /about -->

    <?php include ("footer.php"); ?>

    <script>
    (function(){
        var btns  = document.querySelectorAll('.gei-filter-btn');
        var cards = document.querySelectorAll('.feat-card');
        var count = document.getElementById('feat-count');

        btns.forEach(function(btn){
            btn.addEventListener('click', function(){
                btns.forEach(function(b){ b.classList.remove('active'); });
                btn.classList.add('active');

                var filter = btn.getAttribute('data-filter');
                var visible = 0;

                cards.forEach(function(card){
                    if(filter === 'todas' || card.getAttribute('data-cat') === filter){
                        card.classList.remove('hidden');
                        visible++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                count.textContent = visible + (visible === 1 ? ' funcionalidade' : ' funcionalidades');
            });
        });
    })();
    </script>

</body>
</html>
