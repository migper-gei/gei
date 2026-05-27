<?php
// Sessão segura
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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include("head.php"); ?>
    <style>
        /* ── Variáveis (mesmo estilo planta_salas) ── */
        :root {
            --primary:    #4b6cb7;
            --primary-dk: #182848;
            --accent:     #507feb;
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
        }

        .rel-wrap { padding: 28px 32px 48px; max-width: 1300px; margin: 0 auto; }

        /* ── Título ── */
        .page-title {
            display: flex; align-items: center; gap: 14px; margin-bottom: 24px;
        }
        .page-title-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(75,108,183,.35);
        }
        .page-title h1 { font-size: 1.35rem; font-weight: 700; margin: 0; color: var(--primary-dk); }
        .page-title p  { margin: 0; font-size: .82rem; color: var(--muted); }

        /* ── Filtros (card estilo planta_salas) ── */
        .filter-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 16px 20px;
            margin-bottom: 20px; box-shadow: var(--shadow);
            display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .filter-card label { font-size: .78rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
        .filter-card select {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 7px 12px; font-family: inherit; font-size: .88rem;
            color: var(--text); background: #f7f9fe; cursor: pointer; transition: border .2s;
        }
        .filter-card select:focus { outline: none; border-color: var(--accent); }

        /* ── Portal Avarias ──────────── */
        .portal-filters {
            display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px;
        }
        .pf-btn {
            padding: 6px 16px; border-radius: 20px; font-size: .82rem;
            font-weight: 600; border: 1.5px solid #dee2e6;
            background: #fff; color: #6c757d; cursor: pointer;
            transition: all .15s; display: inline-flex; align-items: center; gap: 6px;
        }
        .pf-btn:hover { border-color: #4b6cb7; color: #4b6cb7; }
        .pf-btn.active { background: #4b6cb7; border-color: #4b6cb7; color: #fff; }
        .pf-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; display: inline-block; }

        .av-card {
            background: #fff; border: 1px solid #dee2e6;
            border-radius: 10px; margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: box-shadow .2s;
        }
        .av-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.10); }

        .av-card-header {
            padding: 14px 18px; cursor: pointer;
            display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid transparent; transition: border-color .2s;
        }
        .av-card-header.is-open { border-color: #dee2e6; }

        .av-num {
            font-family: monospace; font-size: .75rem; color: #6c757d;
            background: #f8f9fa; border: 1px solid #dee2e6;
            border-radius: 4px; padding: 2px 7px; white-space: nowrap; flex-shrink: 0;
        }
        .av-info { flex: 1; min-width: 0; }
        .av-titulo {
            font-weight: 600; font-size: .92rem; color: #212529;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .av-meta {
            font-size: .76rem; color: #6c757d; margin-top: 2px;
            display: flex; flex-wrap: wrap; gap: 10px;
        }
        .av-meta span { display: inline-flex; align-items: center; gap: 4px; }
        .av-chevron { color: #adb5bd; transition: transform .3s; flex-shrink: 0; font-size: .85rem; }
        .av-chevron.open { transform: rotate(180deg); }

        .estado-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 11px; border-radius: 20px;
            font-size: .74rem; font-weight: 600; flex-shrink: 0;
        }
        .estado-aberta    { background: #fde8e6; color: #c0392b; }
        .estado-resolvida { background: #d1f7e7; color: #0f6b47; }
        .estado-pulse { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }
        @keyframes pulse-anim { 0%,100%{opacity:1} 50%{opacity:.2} }

        .av-timeline { max-height: 0; overflow: hidden; transition: max-height .4s cubic-bezier(.4,0,.2,1); }
        .av-timeline.open { max-height: 800px; }
        .av-timeline-body { padding: 18px 20px 16px; }

        .steps-bar { display: flex; align-items: flex-start; margin-bottom: 22px; }
        .step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
        .step:not(:last-child)::after {
            content: ''; position: absolute; left: 50%; top: 14px;
            width: 100%; height: 2px; background: #dee2e6; z-index: 0;
        }
        .step.s-done:not(:last-child)::after   { background: #1cc88a; }
        .step.s-active:not(:last-child)::after { background: #dee2e6; }
        .step-circle {
            width: 28px; height: 28px; border-radius: 50%; z-index: 1; position: relative;
            display: flex; align-items: center; justify-content: center; font-size: .62rem;
            border: 2px solid #dee2e6; background: #f8f9fa; color: #adb5bd;
        }
        .step.s-done   .step-circle { background: #1cc88a; border-color: #1cc88a; color: #fff; }
        .step.s-active .step-circle { background: #4b6cb7; border-color: #4b6cb7; color: #fff; }
        .step-label { font-size: .68rem; font-weight: 600; color: #adb5bd; margin-top: 5px; text-align: center; line-height: 1.3; }
        .step.s-done   .step-label { color: #1cc88a; }
        .step.s-active .step-label { color: #4b6cb7; }

        .tl { position: relative; padding-left: 28px; margin-top: 4px; }
        .tl::before { content: ''; position: absolute; left: 9px; top: 6px; bottom: 6px; width: 2px; background: #dee2e6; border-radius: 2px; }
        .tl-item { position: relative; margin-bottom: 16px; animation: tl-in .3s ease both; }
        .tl-item:last-child { margin-bottom: 0; }
        @keyframes tl-in { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:none} }
        .tl-dot {
            position: absolute; left: -28px; top: 3px;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .58rem; color: #fff; font-weight: 700; border: 2px solid #fff;
        }
        .tl-dot.done    { background: #1cc88a; }
        .tl-dot.active  { background: #4b6cb7; }
        .tl-dot.waiting { background: #dee2e6; }
        .tl-dot.warn    { background: #f6c23e; }
        .tl-label     { font-size: .85rem; font-weight: 600; color: #212529; }
        .tl-label.dim { color: #adb5bd; font-weight: 500; }
        .tl-date  { font-size: .72rem; color: #adb5bd; margin-top: 1px; font-family: monospace; }
        .tl-desc  {
            margin-top: 6px; font-size: .8rem; color: #495057;
            background: #f8f9fa; border-left: 3px solid #dee2e6;
            border-radius: 0 5px 5px 0; padding: 7px 11px; line-height: 1.55;
        }
        .tl-desc.ok   { border-color: #1cc88a; background: #f0faf6; }
        .tl-desc.warn { border-color: #f6c23e; background: #fffdf0; }
        .tl-who { font-size: .74rem; color: #adb5bd; margin-top: 4px; display: flex; align-items: center; gap: 5px; }

        .portal-kpis {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(130px,1fr));
            gap: 12px; margin-bottom: 18px;
        }
        .pkpi {
            background: #fff; border: 1px solid #dee2e6;
            border-radius: 8px; padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
            border-left: 4px solid #dee2e6;
        }
        .pkpi.p-total  { border-left-color: #4b6cb7; }
        .pkpi.p-aberta { border-left-color: #e74a3b; }
        .pkpi.p-prog   { border-left-color: #f6c23e; }
        .pkpi.p-res    { border-left-color: #1cc88a; }
        .pkpi-icon {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; color: #fff; flex-shrink: 0;
        }
        .p-total  .pkpi-icon { background: #4b6cb7; }
        .p-aberta .pkpi-icon { background: #e74a3b; }
        .p-prog   .pkpi-icon { background: #f6c23e; }
        .p-res    .pkpi-icon { background: #1cc88a; }
        .pkpi-val   { font-size: 1.5rem; font-weight: 700; line-height: 1; font-family: monospace; color: #212529; }
        .pkpi-label { font-size: .7rem; font-weight: 600; color: #adb5bd; text-transform: uppercase; letter-spacing: .4px; }

        .portal-search { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
        .portal-search input {
            flex: 1; border: 1px solid #dee2e6; border-radius: 6px;
            padding: 7px 12px; font-size: .88rem; color: #495057;
        }
        .portal-search input:focus { outline: none; border-color: #4b6cb7; }

        @media (max-width: 576px) {
            .av-meta { display: none; }
            .portal-kpis { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>

<body class="main-layout">
    <?php include("loader.php"); ?>

    <?php include("header.php"); ?>
    <?php include("sessao_timeout.php"); ?>

    <?php
    $em   = $_SESSION['email'] ?? '';
    $tipo = $_SESSION['tipo']  ?? 0;

    $escola_id = 1;
    if (isset($_GET['esc']) && is_numeric($_GET['esc'])) $escola_id = (int)$_GET['esc'];

    $op = $_GET['op'] ?? 't';
    if ($op !== 't' && $op !== 'al') $op = 't';

    // Filtro de ano
    $ano_atual = (int)date('Y');
    $ano_sel   = (isset($_GET['ano']) && is_numeric($_GET['ano'])) ? (int)$_GET['ano'] : 0;
    $anos_disp = [];
    for ($y = $ano_atual; $y >= $ano_atual - 5; $y--) { $anos_disp[] = $y; }

    $r_per = mysqli_query($db, "SELECT max(ano_lectivo) FROM periodos");
    $ano_lectivo = mysqli_fetch_row($r_per)[0] ?? '';

    $escolas = [];
    $r_esc = mysqli_query($db, "SELECT id, nome_escola FROM escolas ORDER BY nome_escola");
    while ($row = mysqli_fetch_assoc($r_esc)) $escolas[] = $row;

    // Construir query com prepared statement — sem concatenação de variáveis
    // Os valores dinâmicos são passados via bind_param para evitar SQL Injection
    $sql = "SELECT ar.*, s.nome AS sala, eq.nomeequi AS equipamento, esc.nome_escola
            FROM avarias_reparacoes ar
            JOIN salas s        ON ar.id_sala   = s.id
            JOIN equipamento eq ON ar.id_equi   = eq.id
            JOIN escolas esc    ON ar.id_escola  = esc.id
            WHERE ar.autoravaria = ? AND ar.id_escola = ?";

    $types  = 'si';   // string, integer
    $params = [$em, $escola_id];

    // Filtro de ano letivo (valor provém da BD — mas mantemos prepared por consistência)
    if ($op === 'al' && $ano_lectivo !== '') {
        $sql   .= " AND ar.ano_letivo = ?";
        $types .= 's';
        $params[] = $ano_lectivo;
    }

    // Filtro de ano civil (valor numérico já validado com is_numeric + (int))
    if ($ano_sel > 0) {
        $sql   .= " AND YEAR(ar.dataavaria) = ?";
        $types .= 'i';
        $params[] = $ano_sel;
    }

    $sql .= " ORDER BY ar.dataavaria DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result  = $stmt->get_result();
    $avarias = [];
    while ($row = mysqli_fetch_assoc($result)) $avarias[] = $row;
    $stmt->close();

    function getEstado(array $av): string {
        if (!empty($av['datareparacao'])) return 'resolvida';
        return 'aberta';
    }

    $cnt = ['total' => count($avarias), 'aberta' => 0, 'resolvida' => 0];
    foreach ($avarias as $av) $cnt[getEstado($av)]++;
    ?>

    <div class="about">
        <div class="container">
           

       <div class="rel-wrap">

          <!-- Título da página -->
          <div class="page-title">
            <div class="page-title-icon">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
              <h1>Estado das minhas avarias</h1>
              <p>Acompanhe o estado das avarias que registou</p>
            </div>
          </div>

          <!-- Filtros -->
          <div class="filter-card">
            <div>
              <label>Instituição</label><br>
              <select onchange="window.location.href='?op=<?= $op ?>&esc='+this.value+'&ano=<?= $ano_sel ?>'">
                <?php foreach ($escolas as $esc): ?>
                  <option value="<?= $esc['id'] ?>" <?= $esc['id'] == $escola_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($esc['nome_escola']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label>Ano</label><br>
              <select onchange="window.location.href='?op=<?= $op ?>&esc=<?= $escola_id ?>&ano='+this.value">
                <option value="0" <?= $ano_sel === 0 ? 'selected' : '' ?>>Todos</option>
                <?php foreach ($anos_disp as $y): ?>
                  <option value="<?= $y ?>" <?= $y === $ano_sel ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

                        <!-- KPIs -->
                        <div class="portal-kpis">
                            <div class="pkpi p-total">
                                <div class="pkpi-icon"><i class="fas fa-list"></i></div>
                                <div><div class="pkpi-val"><?= $cnt['total'] ?></div><div class="pkpi-label">Total</div></div>
                            </div>
                            <div class="pkpi p-aberta">
                                <div class="pkpi-icon"><i class="fas fa-exclamation-circle"></i></div>
                                <div><div class="pkpi-val"><?= $cnt['aberta'] ?></div><div class="pkpi-label">Abertas</div></div>
                            </div>

                            <div class="pkpi p-res">
                                <div class="pkpi-icon"><i class="fas fa-check-circle"></i></div>
                                <div><div class="pkpi-val"><?= $cnt['resolvida'] ?></div><div class="pkpi-label">Resolvidas</div></div>
                            </div>
                        </div>

                        <!-- Pesquisa + filtros -->
                      
                        <div class="portal-filters" id="portal-filters">
                            <span class="pf-btn active" data-filter="todas">Todas</span>
                            <span class="pf-btn" data-filter="aberta">
                                <span class="pf-dot" style="color:#c0392b;"></span>Abertas
                            </span>
                            <span class="pf-btn" data-filter="resolvida">
                                <span class="pf-dot" style="color:#0f6b47;"></span>Resolvidas
                            </span>
                        </div>

                        <!-- Cards -->
                        <div id="av-list">
                        <?php if (empty($avarias)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-check-double fa-2x mb-2 d-block" style="opacity:.3;"></i>
                                Não tens avarias registadas.
                            </div>
                        <?php else: ?>
                        <?php foreach ($avarias as $i => $av):
                            $estado    = getEstado($av);
                            $resolvida = $estado === 'resolvida';
                            $aberta    = $estado === 'aberta';
                            $d_av  = !empty($av['dataavaria'])    ? date('d/m/Y', strtotime($av['dataavaria']))    : '—';
                            $d_rep = !empty($av['datareparacao']) ? date('d/m/Y', strtotime($av['datareparacao'])) : null;
                            $s1 = 's-done';
                            $s2 = $resolvida ? 's-done' : 's-active';
                            $s4 = $resolvida ? 's-done' : '';
                        ?>
                        <div class="av-card"
                             data-estado="<?= $estado ?>"
                             data-search="<?= htmlspecialchars(strtolower($av['avaria'].' '.$av['sala'].' '.$av['equipamento']), ENT_QUOTES) ?>">

                            <div class="av-card-header">
                                <span class="av-num">#AV-<?= str_pad($av['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <div class="av-info">
                                    <div class="av-titulo"><?= htmlspecialchars($av['avaria']) ?></div>
                                    <div class="av-meta">
                                        <span><i class="fas fa-door-open"></i> <?= htmlspecialchars($av['sala']) ?></span>
                                        <span><i class="fas fa-desktop"></i> <?= htmlspecialchars($av['equipamento']) ?></span>
                                        <span><i class="far fa-calendar-alt"></i> <?= $d_av ?></span>
                                    </div>
                                </div>
                                <?php if ($resolvida): ?>
                                    <span class="estado-badge estado-resolvida"><span class="estado-pulse"></span> Resolvida</span>
                                <?php else: ?>
                                    <span class="estado-badge estado-aberta"><span class="estado-pulse"></span> Aguarda reparação</span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down av-chevron"></i>
                            </div>

                            <div class="av-timeline">
                                <div class="av-timeline-body">

                                    <!-- Steps bar -->
                                    <div class="steps-bar">
                                        <div class="step <?= $s1 ?>">
                                            <div class="step-circle"><i class="fas fa-check"></i></div>
                                            <div class="step-label">Registada</div>
                                        </div>
                                        <div class="step <?= $s2 ?>">
                                            <div class="step-circle"><i class="fas fa-search"></i></div>
                                            <div class="step-label">Confirmada</div>
                                        </div>
                                        <div class="step <?= $s4 ?>">
                                            <div class="step-circle"><i class="fas fa-check-double"></i></div>
                                            <div class="step-label">Resolvida</div>
                                        </div>
                                    </div>

                                    <!-- Timeline vertical -->
                                    <div class="tl">

                                        <div class="tl-item" style="animation-delay:.04s">
                                            <div class="tl-dot done"><i class="fas fa-check" style="font-size:.55rem;"></i></div>
                                            <div class="tl-label">Avaria registada</div>
                                            <div class="tl-date"><?= $d_av ?></div>
                                            <div class="tl-desc"><?= htmlspecialchars($av['avaria']) ?></div>
                                            <div class="tl-who"><i class="fas fa-user"></i> <?= htmlspecialchars($av['autoravaria'] ?? '') ?></div>
                                        </div>

                                        <?php if ($resolvida): ?>
                                        <div class="tl-item" style="animation-delay:.09s">
                                            <div class="tl-dot done"><i class="fas fa-check" style="font-size:.55rem;"></i></div>
                                            <div class="tl-label">Avaria confirmada pelo técnico</div>
                                            <div class="tl-date">—</div>
                                            <div class="tl-desc">O técnico confirmou a avaria e iniciou o diagnóstico.</div>
                                        </div>
                                        <?php else: ?>
                                        <div class="tl-item" style="animation-delay:.09s">
                                            <div class="tl-dot warn"><i class="fas fa-clock" style="font-size:.55rem;"></i></div>
                                            <div class="tl-label">Aguarda intervenção técnica</div>
                                            <div class="tl-date">Desde <?= $d_av ?></div>
                                            <div class="tl-desc warn">O pedido foi registado e será atribuído a um técnico em breve.</div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($resolvida): ?>
                                        <div class="tl-item" style="animation-delay:.14s">
                                            <div class="tl-dot done"><i class="fas fa-check-double" style="font-size:.55rem;"></i></div>
                                            <div class="tl-label">Avaria encerrada</div>
                                            <div class="tl-date"><?= $d_rep ?></div>
                                            <?php if (!empty($av['reparacao'])): ?>
                                            <div class="tl-desc ok"><?= htmlspecialchars($av['reparacao']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($av['rep_efectuada_por'])): ?>
                                            <div class="tl-who"><i class="fas fa-user-cog"></i> <?= htmlspecialchars($av['rep_efectuada_por']) ?></div>
                                            <?php endif; ?>
                                            <div class="tl-desc ok" style="margin-top:6px;">Avaria resolvida com sucesso.</div>
                                        </div>
                                        <?php else: ?>
                                        <div class="tl-item" style="animation-delay:.14s">
                                            <div class="tl-dot waiting"></div>
                                            <div class="tl-label dim">Resolvida</div>
                                            <div class="tl-date">—</div>
                                        </div>
                                        <?php endif; ?>

                                    </div><!-- /tl -->

                                  

                                </div>
                            </div><!-- /av-timeline -->
                        </div><!-- /av-card -->
                        <?php endforeach; ?>
                        <?php endif; ?>

                        <div id="empty-filter" class="text-center py-4 text-muted" style="display:none;">
                            <i class="fas fa-filter fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Nenhuma avaria encontrada.
                        </div>

                        </div><!-- /av-list -->

                        <br>
                        <a href="<?= SVRURL ?>avaria">
                            <img src="<?= SVRURL ?>images/voltar.svg" alt="Voltar">
                        </a>
                        <br><br>

                    </div>
                </div>
            </div>
        </div>
    </div><!-- /about -->

    <?php mysqli_close($db); include("footer.php"); ?>

    <script>
    var activeFilter = 'todas';

    // Event delegation — filtros
    document.getElementById('portal-filters').addEventListener('click', function(e) {
        var btn = e.target.closest('.pf-btn');
        if (!btn) return;
        var estado = btn.dataset.filter;
        if (!estado) return;
        document.querySelectorAll('.pf-btn').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        activeFilter = estado;
        applyFilters();
    });

    // Event delegation — apanha cliques em qualquer ponto do av-card-header
    document.addEventListener('click', function(e) {
        var header = e.target.closest('.av-card-header');
        if (!header) return;
        var tl      = header.nextElementSibling;
        if (!tl) return;
        var chevron = header.querySelector('.av-chevron');
        var isOpen  = tl.classList.contains('open');

        // Fechar todos
        document.querySelectorAll('.av-timeline').forEach(function(t){ t.classList.remove('open'); });
        document.querySelectorAll('.av-card-header').forEach(function(h){ h.classList.remove('is-open'); });
        document.querySelectorAll('.av-chevron').forEach(function(c){ c.classList.remove('open'); });

        // Abrir o clicado (se estava fechado)
        if (!isOpen) {
            tl.classList.add('open');
            header.classList.add('is-open');
            if (chevron) chevron.classList.add('open');
        }
    });

    function setFilter(estado, el) {
        activeFilter = estado;
        document.querySelectorAll('.pf-btn').forEach(function(b){ b.classList.remove('active'); });
        el.classList.add('active');
        applyFilters();
    }

    function applyFilters() {
        var srchEl = document.getElementById('srch');
        var q = srchEl ? srchEl.value.toLowerCase() : '';
        var visible = 0;
        document.querySelectorAll('.av-card').forEach(function(card) {
            var matchEstado = activeFilter === 'todas' || card.dataset.estado === activeFilter;
            var txt = (card.dataset.search || '') + ' ' + (card.querySelector('.av-titulo') ? card.querySelector('.av-titulo').textContent.toLowerCase() : '');
            var matchSearch = !q || txt.indexOf(q) >= 0;
            var show = matchEstado && matchSearch;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        var emptyEl = document.getElementById('empty-filter');
        if (emptyEl) emptyEl.style.display = visible === 0 ? '' : 'none';
    }

    function confirmarEliminar(id) {
        swal({
            title: "Deseja eliminar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim",
            cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                window.location.href = '<?= SVRURL ?>eliminaavaria/' + id;
            } else {
                swal("Cancelado.");
            }
        });
    }
    </script>

      <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
      <!-- fix stopPropagation -->
      <script>
      document.addEventListener('DOMContentLoaded', function() {
          document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
              btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
          });
      });
      </script>
</body>
</html>
