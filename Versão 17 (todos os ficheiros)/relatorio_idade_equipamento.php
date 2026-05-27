<?php
// ============================================================
// relatorio_idade_equipamento.php — GEI
// Relatório de equipamentos com X ou mais anos de idade
// ============================================================

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

if (!isset($_SESSION['login_user']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

include('sessao_timeout.php');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <style>
        :root {
            --primary:    #4b6cb7;
            --primary-dk: #182848;
            --accent:     #507feb;
            --accent2:    #36b9cc;
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
            --shadow-lg:  0 6px 24px rgba(75,108,183,.16);
        }

        .rel-wrap { padding: 28px 32px 48px; max-width: 1300px; margin: 0 auto; }

        /* ── Cabeçalho da página ── */
        .page-title {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 24px;
        }
        .page-title-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(75,108,183,.35);
        }
        .page-title h1 { font-size: 1.45rem; font-weight: 700; margin: 0; color: var(--primary-dk); }
        .page-title p  { margin: 0; font-size: .82rem; color: var(--muted); }

        /* ── Filtros ── */
        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-group label {
            font-size: .78rem; font-weight: 700;
            color: var(--muted); text-transform: uppercase; letter-spacing: .4px;
        }
        .filter-group select,
        .filter-group input[type="number"] {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-family: inherit;
            font-size: .9rem;
            color: var(--text);
            background: #f7f9fe;
            transition: border .2s;
            min-width: 140px;
        }
        .filter-group select:focus,
        .filter-group input[type="number"]:focus {
            outline: none; border-color: var(--accent);
        }
        .btn-filtrar {
            background: var(--primary); color: #fff; border: none;
            border-radius: 8px; padding: 9px 22px;
            font-size: .88rem; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: background .2s, transform .15s;
            white-space: nowrap;
        }
        .btn-filtrar:hover { background: var(--accent); transform: translateY(-1px); }

        .btn-pdf {
            background: #e74a3b; color: #fff; border: none;
            border-radius: 8px; padding: 9px 22px;
            font-size: .88rem; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: background .2s, transform .15s;
            white-space: nowrap; margin-left: auto;
        }
        .btn-pdf:hover { background: #c0392b; transform: translateY(-1px); }

        /* ── KPIs ── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px; margin-bottom: 20px;
        }
        .kpi {
            background: var(--surface); border-radius: var(--radius);
            padding: 16px 18px; box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
        }
        .kpi.c-danger  { border-color: var(--danger); }
        .kpi.c-warning { border-color: var(--warning); }
        .kpi.c-success { border-color: var(--success); }
        .kpi-val { font-size: 1.7rem; font-weight: 800; color: var(--primary-dk); line-height: 1; }
        .kpi-danger  .kpi-val { color: var(--danger); }
        .kpi-warning .kpi-val { color: #c8860a; }
        .kpi-success .kpi-val { color: #148f5a; }
        .kpi-lbl { font-size: .75rem; color: var(--muted); margin-top: 4px; }

        /* ── Tabela ── */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--primary-dk); color: #fff;
            padding: 12px 14px; font-size: .80rem;
            font-weight: 700; text-transform: uppercase;
            letter-spacing: .4px; white-space: nowrap;
        }
        .table-card thead th:first-child { border-radius: 0; }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:hover { background: #f4f7fd; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody td { padding: 11px 14px; font-size: .88rem; color: var(--text); vertical-align: middle; }

        .badge-idade {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: .75rem; font-weight: 700;
        }
        .badge-alta   { background: #fde8e6; color: #c0392b; }
        .badge-media  { background: #fef9e7; color: #9a7d0a; }
        .badge-baixa    { background: #eafaf1; color: #1e8449; }
        .badge-sem-data { background: #f0f4fb; color: #7b88a0; }

        .empty-state {
            padding: 48px; text-align: center; color: var(--muted);
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: 12px; display: block; }

        /* ═══ DARK MODE ══════════════════════════════════════════ */
        [data-theme="dark"] {
            --bg:         #0f1117;
            --surface:    #1a1d27;
            --primary:    #6489f5;
            --primary-dk: #e2e8f0;
            --accent:     #7b9bf7;
            --success:    #26d49a;
            --warning:    #f6c23e;
            --danger:     #f07167;
            --border:     #2d3348;
            --text:       #e2e8f0;
            --muted:      #94a3b8;
            --shadow:     0 2px 12px rgba(0,0,0,.4);
        }
        [data-theme="dark"] .filter-card              { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .filter-card select,
        [data-theme="dark"] .filter-group select,
        [data-theme="dark"] .filter-group input       { background: #1e2130 !important; color: var(--text); border-color: var(--border); }
        [data-theme="dark"] .kpi                      { background: var(--surface); }
        [data-theme="dark"] .kpi-val                  { color: var(--primary-dk); }
        [data-theme="dark"] .table-card               { background: var(--surface); border-color: var(--border); }
        [data-theme="dark"] .table-card tbody tr:hover{ background: #1e2130; }
        [data-theme="dark"] .table-card tbody td      { color: var(--text); border-color: var(--border); }
        [data-theme="dark"] .table-card tbody tr      { border-color: var(--border); }
        [data-theme="dark"] .badge-sem-data           { background: #2d3348; color: var(--muted); }
        [data-theme="dark"] .badge-alta               { background: rgba(240,113,103,.18); color: #f07167; }
        [data-theme="dark"] .badge-media              { background: rgba(246,194,62,.15);  color: #f6c23e; }
        [data-theme="dark"] .badge-baixa              { background: rgba(38,212,154,.15);  color: #26d49a; }
        [data-theme="dark"] .empty-state              { color: var(--muted); }
        [data-theme="dark"] .page-title h1            { color: var(--primary-dk); }
        /* ════════════════════════════════════════════════════════ */


    </style>
</head>

<body class="main-layout">
    <?php include('loader.php'); ?>
    <?php include('header.php'); ?>

    <?php

    // ── Parâmetros do filtro ──────────────────────────────────────────────────
    $anos_min = isset($_GET['anos']) ? max(0, (int)$_GET['anos']) : 5;
    $escola   = isset($_GET['esc'])  ? (int)$_GET['esc'] : 0;
    $tipo_sel = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
    $sala_sel = isset($_GET['sala']) ? (int)$_GET['sala'] : 0;
    $ordem    = isset($_GET['ordem']) && in_array($_GET['ordem'], ['nome','sala','tipo','idade','data_compra'])
                ? $_GET['ordem'] : 'idade';

    // ── Listar escolas disponíveis (para o select) ───────────────────────────
    $escolas = [];
    $res_esc = mysqli_query($db, "SELECT id, nome_escola FROM escolas ORDER BY id");
    while ($row = mysqli_fetch_assoc($res_esc)) {
        $escolas[] = $row;
    }
    if ($escola === 0 && !empty($escolas)) {
        $escola = (int)$escolas[0]['id'];
    }

    // ── Listar tipos de equipamento (para o select) ──────────────────────────
    $tipos = [];
    $res_tp = mysqli_query($db, "SELECT DISTINCT tipo FROM equipamento WHERE tipo IS NOT NULL AND tipo <> '' ORDER BY tipo");
    while ($row = mysqli_fetch_row($res_tp)) {
        $tipos[] = $row[0];
    }

    // ── Listar salas da escola selecionada (para o select) ──────────────────
    $salas = [];
    $res_sl = $db->prepare("SELECT id, nome FROM salas WHERE id_escola = ? ORDER BY nome");
    $res_sl->bind_param('i', $escola);
    $res_sl->execute();
    $res_sl_result = $res_sl->get_result();
    while ($row = $res_sl_result->fetch_assoc()) {
        $salas[] = $row;
    }
    $res_sl->close();
    // Reset sala_sel se não pertencer à escola atual
    if ($sala_sel > 0 && !in_array($sala_sel, array_column($salas, 'id'))) {
        $sala_sel = 0;
    }

    // ── Query principal ───────────────────────────────────────────────────────
    $where_tipo = '';
    $where_sala = '';
    $params     = [];
    $types      = '';

    if ($tipo_sel !== '') {
        $where_tipo = ' AND eq.tipo = ?';
        $params[]   = $tipo_sel;
        $types      .= 's';
    }
    if ($sala_sel > 0) {
        $where_sala = ' AND s.id = ?';
        $params[]   = $sala_sel;
        $types      .= 'i';
    }

    $order_map = [
        'nome'        => 'eq.nomeequi ASC',
        'sala'        => 's.nome ASC',
        'tipo'        => 'eq.tipo ASC',
        'idade'       => 'idade DESC',
        'data_compra' => 'eq.data_compra ASC',
    ];
    $order_sql = $order_map[$ordem];

    $sql = "
        SELECT
            eq.id,
            eq.nomeequi,
            eq.tipo,
            eq.marca_modelo,
            eq.numserie,
            eq.num_inv_dgest,
            eq.data_compra,
            s.nome          AS sala,
            TIMESTAMPDIFF(YEAR,  NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) AS idade,
            TIMESTAMPDIFF(MONTH, NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) AS meses
        FROM equipamento eq
        INNER JOIN salas s ON s.id = eq.id_sala
        WHERE s.id_escola = ?
          AND eq.data_compra IS NOT NULL
          AND eq.data_compra != '0000-00-00'
          AND TIMESTAMPDIFF(YEAR, NULLIF(eq.data_compra, '0000-00-00'), CURDATE()) >= ?
          $where_tipo
          $where_sala
        ORDER BY $order_sql
    ";

    // Desativar strict mode temporariamente (tolera datas 0000-00-00 na BD)
    $db->query("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

    $stmt = $db->prepare($sql);
    array_unshift($params, $escola, $anos_min);
    $types = 'ii' . $types;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipamentos = [];
    while ($row = $result->fetch_assoc()) {
        $equipamentos[] = $row;
    }
    $stmt->close();

    // Restaurar modo SQL
    $db->query("SET SESSION sql_mode = @@GLOBAL.sql_mode");

    // ── KPIs ─────────────────────────────────────────────────────────────────
    $total     = count($equipamentos);
    $mais10    = count(array_filter($equipamentos, function($r) { return $r['idade'] >= 10; }));
    $entre5e10 = count(array_filter($equipamentos, function($r) { return $r['idade'] >= 5 && $r['idade'] < 10; }));
    $semGarant = $mais10; // proxy: >10 anos = garantia expirada (coluna real não existe ainda)
    $mais_velho = $total > 0 ? max(array_column($equipamentos, 'idade')) : 0;

    // Nome da escola selecionada
    $nome_escola = '';
    foreach ($escolas as $esc) {
        if ((int)$esc['id'] === $escola) { $nome_escola = $esc['nome_escola']; break; }
    }
    ?>

    <div class="about">
      <div class="container-fluid">


  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
               
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        
                 <a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a>
            <li style="color:#c5cde0;">&#8250;</li>
                <li style="color:#1e2a45;">Relatório de Equipamentos por Idade</li>
</ol>


        <div class="rel-wrap">

          <!-- Título da página -->
          <div class="page-title">
            <div class="page-title-icon">
              <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
              <h1>
              Equipamentos com data de compra registada, ordenados por antiguidade</h1>
            </div>
          </div>

          <!-- Filtros -->
          <div class="filter-card">
            <form method="GET" action="" style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;width:100%">

              <div class="filter-group">
                <label><i class="fa fa-school"></i> Instituição</label>
                <select name="esc">
                  <?php foreach ($escolas as $esc): ?>
                    <option value="<?php echo (int)$esc['id']; ?>"
                      <?php echo ((int)$esc['id'] === $escola) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($esc['nome_escola']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="filter-group">
                <label><i class="fa fa-clock"></i> Idade mínima (anos)</label>
                <input type="number" name="anos" min="0" max="50"
                       value="<?php echo $anos_min; ?>" style="width:120px;">
              </div>

              <div class="filter-group">
                <label><i class="fa fa-tag"></i> Tipo de equipamento</label>
                <select name="tipo">
                  <option value="">— Todos —</option>
                  <?php foreach ($tipos as $t): ?>
                    <option value="<?php echo htmlspecialchars($t); ?>"
                      <?php echo ($t === $tipo_sel) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($t); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="filter-group">
                <label><i class="fa fa-door-open"></i> Sala</label>
                <select name="sala">
                  <option value="0">— Todas —</option>
                  <?php foreach ($salas as $sl): ?>
                    <option value="<?php echo (int)$sl['id']; ?>"
                      <?php echo ((int)$sl['id'] === $sala_sel) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($sl['nome']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="filter-group">
                <label><i class="fa fa-sort"></i> Ordenar por</label>
                <select name="ordem">
                  <option value="idade"       <?php echo $ordem==='idade'       ? 'selected':''; ?>>Mais antigos primeiro</option>
                  <option value="nome"        <?php echo $ordem==='nome'        ? 'selected':''; ?>>Nome</option>
                  <option value="sala"        <?php echo $ordem==='sala'        ? 'selected':''; ?>>Sala</option>
                  <option value="tipo"        <?php echo $ordem==='tipo'        ? 'selected':''; ?>>Tipo</option>
                  <option value="data_compra" <?php echo $ordem==='data_compra' ? 'selected':''; ?>>Data de compra</option>
                </select>
              </div>

              <button type="submit" class="btn-filtrar">
                <i class="fa fa-magnifying-glass"></i> Filtrar
              </button>

              <a href="relatorio_idade_equipamento_pdf.php?esc=<?php echo $escola; ?>&anos=<?php echo $anos_min; ?>&tipo=<?php echo urlencode($tipo_sel); ?>&sala=<?php echo $sala_sel; ?>&ordem=<?php echo $ordem; ?>"
                 target="_blank" class="btn-pdf" style="text-decoration:none;">
                <i class="fa fa-file-pdf"></i> Exportar PDF
              </a>

            </form>
          </div>

          <!-- KPIs -->
          <?php if ($total > 0): ?>
          <div class="kpi-row">
            <div class="kpi">
              <div class="kpi-val"><?php echo $total; ?></div>
              <div class="kpi-lbl">Equipamentos encontrados</div>
            </div>
            <div class="kpi c-danger kpi-danger">
              <div class="kpi-val"><?php echo $mais10; ?></div>
              <div class="kpi-lbl">Com 10 ou mais anos</div>
            </div>
            <div class="kpi c-warning kpi-warning">
              <div class="kpi-val"><?php echo $entre5e10; ?></div>
              <div class="kpi-lbl">Entre 5 e 9 anos</div>
            </div>
            <div class="kpi c-success">
              <div class="kpi-val"><?php echo $mais_velho; ?> anos</div>
              <div class="kpi-lbl">Equipamento mais antigo</div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Tabela -->
          <div class="table-card">
            <?php if ($total === 0): ?>
              <div class="empty-state">
                <i class="fa fa-box-open"></i>
                <p>Nenhum equipamento encontrado com os critérios selecionados.</p>
              </div>
            <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nome do equipamento</th>
                  <th>Tipo</th>
                  <th>Sala</th>
                  <th>Marca / Modelo</th>
                  <th>Nº Série</th>
                  <th>Nº Inventário</th>
                  <th>Data de compra</th>
                  <th>Idade</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($equipamentos as $i => $eq): ?>
                  <?php
                    $tem_data  = !empty($eq['data_compra']) && $eq['data_compra'] !== '0000-00-00';
                    $idade     = $tem_data ? (int)$eq['idade'] : null;
                    $meses     = $tem_data ? (int)$eq['meses'] % 12 : null;
                    $data_fmt  = $tem_data ? date('d/m/Y', strtotime($eq['data_compra'])) : '—';
                    if ($idade === null) {
                        $badge     = 'badge-sem-data';
                        $idade_str = 'sem data';
                    } elseif ($idade >= 10) {
                        $badge = 'badge-alta';  $idade_str = $idade . ' ano' . ($idade !== 1 ? 's' : '');
                    } elseif ($idade >= 5) {
                        $badge = 'badge-media'; $idade_str = $idade . ' ano' . ($idade !== 1 ? 's' : '');
                    } else {
                        $badge = 'badge-baixa'; $idade_str = $idade . ' ano' . ($idade !== 1 ? 's' : '');
                    }
                    if ($meses > 0) $idade_str .= ' e ' . $meses . ' ' . ($meses === 1 ? 'mês' : 'meses');
                  ?>
                  <tr>
                    <td style="color:var(--muted);font-size:.8rem;"><?php echo $i + 1; ?></td>
                    <td><strong><?php echo htmlspecialchars($eq['nomeequi']); ?></strong></td>
                    <td><?php echo htmlspecialchars($eq['tipo'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($eq['sala']); ?></td>
                    <td><?php echo htmlspecialchars($eq['marca_modelo'] ?: '—'); ?></td>
                    <td style="font-family:monospace;font-size:.82rem;"><?php echo htmlspecialchars($eq['numserie'] ?: '—'); ?></td>
                    <td style="font-size:.82rem;"><?php echo htmlspecialchars($eq['num_inv_dgest'] ?: '—'); ?></td>
                    <td><?php echo $data_fmt; ?></td>
                    <td>
                      <span class="badge-idade <?php echo $badge; ?>"><?php echo $idade_str; ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>

        </div><!-- /rel-wrap -->
      </div>
    </div>

    <?php include('footer.php'); ?>



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
