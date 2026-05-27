<?php
// ============================================================
// calendario_mensal.php — GEI
// Calendário mensal de todas as reservas de equipamentos
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
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

        .cal-wrap { padding: 28px 32px 48px; max-width: 1300px; margin: 0 auto; }

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

        /* ── Filtros ── */
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

        .btn-nav {
            background: var(--primary); border: none;
            border-radius: 8px; padding: 8px 16px; font-family: inherit;
            font-size: .9rem; color: #fff; cursor: pointer;
            transition: background .15s, transform .15s;
            display: inline-flex; align-items: center; gap: 4px;
            font-weight: 700; text-decoration: none;
        }
        .btn-nav:hover { background: var(--accent); color: #fff; transform: translateY(-1px); }

        /* ── Toggle vista ── */
        .vista-toggle {
            display: flex; gap: 6px; margin-left: auto;
        }
        .btn-vista {
            padding: 7px 14px; border-radius: 8px; font-size: .82rem; font-weight: 700;
            border: 1.5px solid var(--border); background: #f0f4fb; color: var(--muted);
            cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
            transition: all .15s;
        }
        .btn-vista.active, .btn-vista:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Mês label ── */
        .mes-label {
            font-size: 1rem; font-weight: 700; color: var(--primary-dk); padding: 0 8px;
            white-space: nowrap;
        }

        /* ── Calendário mensal ── */
        .cal-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            overflow: hidden; margin-bottom: 24px;
        }

        .cal-month-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-left: 1px solid var(--border);
            border-top: 1px solid var(--border);
        }

        .cal-month-header {
            background: var(--primary-dk); display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
        .cal-month-header div {
            padding: 10px 8px; text-align: center;
            font-size: .75rem; font-weight: 700; color: #fff;
            text-transform: uppercase; letter-spacing: .4px;
        }
        .cal-month-header div.fim-semana { background: rgba(255,255,255,.08); }

        .dia-mes {
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            min-height: 110px; padding: 6px;
            background: var(--surface);
            vertical-align: top;
            position: relative;
        }
        .dia-mes.outro-mes { background: #f7f9fe; }
        .dia-mes.hoje      { background: #eef3ff; }
        .dia-mes.passado   { background: #fafafa; }
        .dia-mes.fim-semana { background: #fdfeff; }
        .dia-mes.outro-mes.fim-semana { background: #f5f7fb; }

        .dia-num {
            font-size: .78rem; font-weight: 700; color: var(--muted);
            margin-bottom: 4px; display: flex; align-items: center; justify-content: space-between;
        }
        .dia-num.hoje-num {
            background: var(--primary); color: #fff;
            border-radius: 50%; width: 22px; height: 22px;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; margin-bottom: 4px;
        }

        .reserva-pill {
            border-radius: 4px; padding: 2px 6px; margin-bottom: 2px;
            font-size: .65rem; font-weight: 600; line-height: 1.4;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            cursor: pointer; transition: opacity .15s;
        }
        .reserva-pill:hover { opacity: .82; }
        .reserva-pill.outra  { background: #4b6cb7; color: #fff; }
        .reserva-pill.minha  { background: #1cc88a; color: #fff; }
        .reserva-pill.entregue { background: var(--muted); color: #fff; }

        .mais-tag {
            font-size: .65rem; color: var(--primary); font-weight: 700;
            cursor: pointer; padding: 1px 4px;
        }
        .mais-tag:hover { text-decoration: underline; }

        /* ── Legenda ── */
        .legend {
            display: flex; gap: 16px; flex-wrap: wrap;
            margin-bottom: 20px; font-size: .78rem; color: var(--muted);
        }
        .leg { display: flex; align-items: center; gap: 6px; }
        .leg-box { width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0; }

        /* ── Painel detalhe ── */
        .painel-wrap {
            display: none; background: var(--surface);
            border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: var(--shadow); padding: 20px 24px; margin-bottom: 20px;
        }
        .painel-wrap.open { display: block; }
        .painel-row {
            display: flex; justify-content: space-between;
            padding: 7px 0; border-bottom: 1px solid var(--border); font-size: .85rem;
        }
        .painel-row:last-of-type { border-bottom: none; }
        .painel-lbl { color: var(--muted); }
        .painel-val { font-weight: 700; color: var(--text); }
        .painel-actions { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
        .btn-action {
            padding: 8px 16px; border-radius: 8px; font-size: .82rem;
            font-weight: 700; cursor: pointer; border: none;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-action.primary   { background: var(--primary); color: #fff; }
        .btn-action.secondary { background: #f0f4fb; color: var(--primary-dk); border: 1px solid var(--border); }
        .btn-action:hover { opacity: .85; }

        /* ── Modal dia ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(24,40,72,.45); z-index: 1000;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--surface); border-radius: 14px;
            box-shadow: 0 8px 40px rgba(24,40,72,.25);
            padding: 24px 28px; width: 100%; max-width: 480px;
            max-height: 80vh; overflow-y: auto;
            animation: modalIn .18s ease;
        }
        @keyframes modalIn { from { transform: scale(.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-title {
            font-size: 1rem; font-weight: 700; color: var(--primary-dk);
            margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;
        }
        .modal-res-item {
            padding: 10px 12px; border-radius: 8px; margin-bottom: 8px;
            border: 1px solid var(--border); font-size: .84rem;
            cursor: pointer; transition: background .12s;
        }
        .modal-res-item:hover { background: #f0f4fb; }
        .modal-res-item .equip-nome { font-weight: 700; color: var(--text); }
        .modal-res-item .res-info   { font-size: .76rem; color: var(--muted); margin-top: 2px; }
        .modal-empty { text-align: center; color: var(--muted); padding: 20px 0; font-size: .88rem; }
    </style>
</head>

<body class="main-layout">
    <?php include('loader.php'); ?>
    <?php include('header.php'); ?>
    <?php include('sessao_timeout.php'); ?>

    <?php
    // ── Escolas ───────────────────────────────────────────────────────────────
    $escolas = [];
    $stmt_esc = $db->prepare("SELECT id, nome_escola FROM escolas ORDER BY nome_escola");
    $stmt_esc->execute();
    $escolas = $stmt_esc->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_esc->close();

    $esc_id = 0;
    if (!empty($_POST['escola']))  $esc_id = (int)$_POST['escola'];
    elseif (!empty($_GET['esc']))  $esc_id = (int)base64_decode($_GET['esc']);
    if ($esc_id === 0 && !empty($escolas)) $esc_id = (int)$escolas[0]['id'];

    // ── Mês a visualizar ──────────────────────────────────────────────────────
    $hoje      = new DateTime();
    $ano_atual = (int)$hoje->format('Y');
    $mes_atual = (int)$hoje->format('m');

    $ano = isset($_GET['a']) ? (int)$_GET['a'] : (isset($_POST['ano_fil']) ? (int)$_POST['ano_fil'] : $ano_atual);
    $mes = isset($_GET['m']) ? (int)$_GET['m'] : (isset($_POST['mes_fil']) ? (int)$_POST['mes_fil'] : $mes_atual);

    // Sanitizar
    if ($mes < 1)  { $mes = 12; $ano--; }
    if ($mes > 12) { $mes = 1;  $ano++; }

    $mes_dt    = new DateTime(sprintf('%04d-%02d-01', $ano, $mes));
    $mes_label = strftime('%B %Y', $mes_dt->getTimestamp()); // ex: "Março 2026"
    // Fallback para PHP >= 8.1 sem strftime
    $meses_pt  = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $mes_label = $meses_pt[$mes] . ' ' . $ano;

    $ant_mes = $mes - 1; $ant_ano = $ano;
    if ($ant_mes < 1)  { $ant_mes = 12; $ant_ano--; }
    $prx_mes = $mes + 1; $prx_ano = $ano;
    if ($prx_mes > 12) { $prx_mes = 1;  $prx_ano++; }

    // ── Filtros ───────────────────────────────────────────────────────────────
    $sala_id  = 0;
    $tipo_fil = '';
    $email_fil = '';

    if (!empty($_POST['sala_fil']))   $sala_id   = (int)$_POST['sala_fil'];
    elseif (!empty($_GET['sala']))    $sala_id   = (int)$_GET['sala'];
    if (!empty($_POST['tipo_fil']))   $tipo_fil  = trim($_POST['tipo_fil']);
    elseif (isset($_GET['tipo']))     $tipo_fil  = trim($_GET['tipo']);
    if (!empty($_POST['email_fil']))  $email_fil = trim($_POST['email_fil']);
    elseif (isset($_GET['email']))    $email_fil = trim($_GET['email']);

    // Salas requisitáveis
    $salas_fil = [];
    $stmt_sal = $db->prepare("SELECT id, nome FROM salas WHERE id_escola = ? AND equip_requisitavel = 'Sim' ORDER BY nome");
    $stmt_sal->bind_param("i", $esc_id);
    $stmt_sal->execute();
    $salas_fil = $stmt_sal->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_sal->close();

    // Tipos de equipamento disponíveis (sem avaria aberta)
    $tipos_fil = [];
    $stmt_tip = $db->prepare("
        SELECT DISTINCT eq.tipo
        FROM equipamento eq
        INNER JOIN salas s ON s.id = eq.id_sala
        WHERE s.id_escola = ?
          AND s.equip_requisitavel = 'Sim'
          AND eq.tipo IS NOT NULL AND eq.tipo <> ''
          AND eq.id NOT IN (SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL)
        ORDER BY eq.tipo
    ");
    $stmt_tip->bind_param("i", $esc_id);
    $stmt_tip->execute();
    $res_tip = $stmt_tip->get_result();
    while ($r = $res_tip->fetch_row()) { $tipos_fil[] = $r[0]; }
    $stmt_tip->close();

    // ── Reservas do mês ───────────────────────────────────────────────────────
    $data_ini = sprintf('%04d-%02d-01', $ano, $mes);
    $data_fim = (clone $mes_dt)->modify('last day of this month')->format('Y-m-d');

    $where_res = "s.id_escola = ?";
    $params     = [$esc_id];
    $types      = 'i';

    if ($sala_id > 0)    { $where_res .= " AND r.id_sala = ?";    $params[] = $sala_id;  $types .= 'i'; }
    if ($tipo_fil !== '') { $where_res .= " AND eq.tipo = ?";      $params[] = $tipo_fil; $types .= 's'; }
    if ($email_fil !== '') { $where_res .= " AND r.email_util = ?"; $params[] = $email_fil; $types .= 's'; }

    $sql_res = "
        SELECT DISTINCT r.id AS id_req, r.datautil, r.horainicio, r.horafim,
               r.email_util, r.dataentrega,
               s.nome AS nomsala,
               GROUP_CONCAT(eq.nomeequi ORDER BY eq.nomeequi SEPARATOR ', ') AS equipamentos
        FROM requisicao r
        INNER JOIN salas s ON s.id = r.id_sala
        INNER JOIN equip_requisitado er ON er.id_req = r.id
        INNER JOIN equipamento eq ON eq.id = er.id_equip
        WHERE $where_res
          AND r.datautil BETWEEN ? AND ?
        GROUP BY r.id
        ORDER BY r.datautil, r.horainicio
    ";
    $params[] = $data_ini; $types .= 's';
    $params[] = $data_fim; $types .= 's';

    $stmt = $db->prepare($sql_res);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $todas_reservas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Indexar por data
    $reservas_por_dia = [];
    foreach ($todas_reservas as $r) {
        $reservas_por_dia[$r['datautil']][] = $r;
    }

    // ── Construir grelha do mês ───────────────────────────────────────────────
    // Dia da semana do 1º do mês (0=Dom, adaptado para segunda=0)
    $primeiro_dia = (int)$mes_dt->format('N') - 1; // 0=Seg … 6=Dom
    $dias_no_mes  = (int)(clone $mes_dt)->modify('last day of this month')->format('d');

    // Dias do mês anterior para preencher o início
    $mes_ant_dt   = new DateTime(sprintf('%04d-%02d-01', $ant_ano, $ant_mes));
    $dias_mes_ant = (int)(clone $mes_ant_dt)->modify('last day of this month')->format('d');

    $nav_base = 'esc=' . base64_encode($esc_id);
    if ($sala_id > 0)    $nav_base .= '&sala=' . $sala_id;
    if ($tipo_fil !== '') $nav_base .= '&tipo=' . urlencode($tipo_fil);
    if ($email_fil !== '') $nav_base .= '&email=' . urlencode($email_fil);
    ?>

    <div class="about">
      <div class="container-fluid">
        <div class="cal-wrap">

          <!-- Breadcrumb -->
          <nav style="margin-bottom:16px;">
            <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
              <li><a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;"><i class="fas fa-desktop"></i> Equipamentos</a></li>
              <li style="color:#c5cde0;">&#8250;</li>
              <li><a href="<?php echo SVRURL ?>calreservas" style="color:#4b6cb7;text-decoration:none;">Calendário semanal</a></li>
              <li style="color:#c5cde0;">&#8250;</li>
              <li style="color:#1e2a45;">Vista mensal</li>
            </ol>
          </nav>

          <div class="page-title">
            <div class="page-title-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div>
              <h1>Todas as reservas de equipamentos partilhados</h1>
            </div>
          </div>


          

          <!-- Filtros + navegação -->
          <form method="POST" action="" id="frmFiltro">
          <input type="hidden" name="ano_fil" value="<?php echo $ano; ?>">
          <input type="hidden" name="mes_fil" value="<?php echo $mes; ?>">
          <div class="filter-card">

            <div>
              <label>Instituição</label><br>
              <select name="escola" onchange="document.getElementById('frmFiltro').submit()">
                <?php foreach ($escolas as $e): ?>
                  <option value="<?php echo (int)$e['id']; ?>"
                    <?php echo ((int)$e['id'] === $esc_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($e['nome_escola']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label>Sala</label><br>
              <select name="sala_fil" onchange="document.getElementById('frmFiltro').submit()">
                <option value="0">— Todas —</option>
                <?php foreach ($salas_fil as $sl): ?>
                  <option value="<?php echo (int)$sl['id']; ?>"
                    <?php echo ((int)$sl['id'] === $sala_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sl['nome']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label>Tipo</label><br>
              <select name="tipo_fil" onchange="document.getElementById('frmFiltro').submit()">
                <option value="">— Todos —</option>
                <?php foreach ($tipos_fil as $tp): ?>
                  <option value="<?php echo htmlspecialchars($tp); ?>"
                    <?php echo ($tp === $tipo_fil) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tp); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Navegação mês -->
            <div style="display:flex;align-items:center;gap:8px;margin-left:8px;">
              <a href="?<?php echo $nav_base; ?>&a=<?php echo $ant_ano; ?>&m=<?php echo $ant_mes; ?>" class="btn-nav">
                <i class="fas fa-chevron-left"></i>
              </a>
              <span class="mes-label"><?php echo $mes_label; ?></span>
              <a href="?<?php echo $nav_base; ?>&a=<?php echo $prx_ano; ?>&m=<?php echo $prx_mes; ?>" class="btn-nav">
                <i class="fas fa-chevron-right"></i>
              </a>
              <?php if ($ano !== $ano_atual || $mes !== $mes_atual): ?>
              <a href="?<?php echo $nav_base; ?>&a=<?php echo $ano_atual; ?>&m=<?php echo $mes_atual; ?>" class="btn-nav" style="background:#f0f4fb;color:var(--primary-dk);border:1.5px solid var(--border);font-size:.8rem;">
                <i class="fas fa-calendar-day"></i> Hoje
              </a>
              <?php endif; ?>
            </div>

            <!-- Toggle vista -->
            <div class="vista-toggle">
              <a href="<?php echo SVRURL; ?>calreservas?esc=<?php echo base64_encode($esc_id); ?>" class="btn-vista">
                <i class="fas fa-calendar-week"></i> Semana
              </a>
              <span class="btn-vista active">
                <i class="fas fa-calendar-days"></i> Mês
              </span>
            </div>

          </div>
          </form>

          <!-- Legenda -->
          <div class="legend">
            <div class="leg"><div class="leg-box" style="background:#4b6cb7;"></div> Reservado (outro utilizador)</div>
            <div class="leg"><div class="leg-box" style="background:#1cc88a;"></div> Reservado por mim</div>
            <div class="leg"><div class="leg-box" style="background:#7b88a0;"></div> Entregue / concluído</div>
          </div>

          <!-- Painel de detalhe -->
          <div class="painel-wrap" id="painelWrap">
            <div id="painelContent"></div>
          </div>

          <!-- Calendário mensal -->
          <div class="cal-card">
            <!-- Cabeçalho dias da semana -->
            <div class="cal-month-header">
              <?php
              $dias_semana = ['Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'];
              foreach ($dias_semana as $i => $ds):
              ?>
              <div class="<?php echo $i >= 5 ? 'fim-semana' : ''; ?>"><?php echo $ds; ?></div>
              <?php endforeach; ?>
            </div>

            <!-- Grelha -->
            <div class="cal-month-grid">
              <?php
              $MAX_PILLS = 3; // máximo de pills por célula antes de "+N mais"
              $col = 0;

              // Células do mês anterior
              for ($i = 0; $i < $primeiro_dia; $i++, $col++) {
                  $d_num = $dias_mes_ant - $primeiro_dia + 1 + $i;
                  echo '<div class="dia-mes outro-mes' . ($col >= 5 ? ' fim-semana' : '') . '">';
                  echo '<div class="dia-num" style="color:#c5cde0;">' . $d_num . '</div>';
                  echo '</div>';
              }

              // Células do mês actual
              for ($d = 1; $d <= $dias_no_mes; $d++, $col++) {
                  $data_str  = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
                  $is_hoje   = ($data_str === $hoje->format('Y-m-d'));
                  $is_passado = ($data_str < $hoje->format('Y-m-d'));
                  $is_fds    = ($col % 7 >= 5);
                  $reservas_d = $reservas_por_dia[$data_str] ?? [];

                  $classes = 'dia-mes';
                  if ($is_hoje)    $classes .= ' hoje';
                  elseif ($is_passado) $classes .= ' passado';
                  if ($is_fds)     $classes .= ' fim-semana';

                  echo '<div class="' . $classes . '">';

                  // Número do dia
                  if ($is_hoje) {
                      echo '<div style="display:flex;align-items:center;margin-bottom:4px;">';
                      echo '<span style="background:var(--primary);color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;">' . $d . '</span>';
                      echo '</div>';
                  } else {
                      echo '<div class="dia-num">' . $d . '</div>';
                  }

                  // Pills de reservas
                  $mostrar   = array_slice($reservas_d, 0, $MAX_PILLS);
                  $restantes = count($reservas_d) - count($mostrar);

                  foreach ($mostrar as $res) {
                      $e_minha  = ($res['email_util'] === $_SESSION['email']);
                      $entregue = !empty($res['dataentrega']);
                      $classe   = $entregue ? 'entregue' : ($e_minha ? 'minha' : 'outra');
                      $hi = substr($res['horainicio'], 0, 5);
                      $hf = substr($res['horafim'], 0, 5);
                      $equips_esc = htmlspecialchars($res['equipamentos'], ENT_QUOTES);
                      $sala_esc   = htmlspecialchars($res['nomsala'], ENT_QUOTES);
                      $email_esc  = htmlspecialchars($res['email_util'], ENT_QUOTES);
                      $data_fmt   = date('d/m/Y', strtotime($data_str));
                      echo '<div class="reserva-pill ' . $classe . '" '
                          . 'onclick="abrirReserva(' . $res['id_req'] . ', \'' . addslashes($equips_esc) . '\', \'' . $data_fmt . '\', \'' . $hi . '\', \'' . $hf . '\', \'' . addslashes($email_esc) . '\', \'' . addslashes($sala_esc) . '\', ' . ($entregue ? 'true' : 'false') . ', ' . ($e_minha ? 'true' : 'false') . ')" '
                          . 'title="' . $hi . '–' . $hf . ' | ' . $equips_esc . ' | ' . $email_esc . '">'
                          . $hi . ' ' . mb_strimwidth($res['equipamentos'], 0, 18, '…')
                          . '</div>';
                  }

                  if ($restantes > 0) {
                      $data_fmt = date('d/m/Y', strtotime($data_str));
                      echo '<div class="mais-tag" onclick="abrirDia(\'' . $data_str . '\', \'' . $data_fmt . '\')">'
                          . '+' . $restantes . ' mais</div>';
                  }

                  echo '</div>';
              }

              // Células do mês seguinte
              $restam = (7 - ($col % 7)) % 7;
              for ($i = 1; $i <= $restam; $i++, $col++) {
                  $is_fds = ($col % 7 >= 5);
                  echo '<div class="dia-mes outro-mes' . ($is_fds ? ' fim-semana' : '') . '">';
                  echo '<div class="dia-num" style="color:#c5cde0;">' . $i . '</div>';
                  echo '</div>';
              }
              ?>
            </div>
          </div>

          <!-- Modal: todas as reservas de um dia -->
          <div class="modal-overlay" id="modalOverlay" onclick="fecharModal(event)">
            <div class="modal-box" id="modalBox">
              <div class="modal-title">
                <span id="modalTitulo"></span>
                <button onclick="fecharModal()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--muted);">✕</button>
              </div>
              <div id="modalContent"></div>
            </div>
          </div>

          <!-- As minhas requisições activas -->
          <?php
          $stmt_mn = $db->prepare("
              SELECT r.id, r.datautil, r.horainicio, r.horafim, r.dataentrega,
                     s.nome AS nomsala,
                     GROUP_CONCAT(eq.nomeequi ORDER BY eq.nomeequi SEPARATOR ', ') AS equipamentos
              FROM requisicao r
              INNER JOIN salas s ON s.id = r.id_sala
              INNER JOIN equip_requisitado er ON er.id_req = r.id
              INNER JOIN equipamento eq ON eq.id = er.id_equip
              WHERE r.email_util = ? AND s.id_escola = ? AND r.dataentrega IS NULL
                AND r.datautil >= CURDATE()
              GROUP BY r.id
              ORDER BY r.datautil, r.horainicio
          ");
          $stmt_mn->bind_param('si', $_SESSION['email'], $esc_id);
          $stmt_mn->execute();
          $minhas = $stmt_mn->get_result()->fetch_all(MYSQLI_ASSOC);
          $stmt_mn->close();
          ?>

          <?php if (!empty($minhas)): ?>
          <div style="margin-top: 8px;">
            <h3 style="font-size:.95rem;font-weight:700;color:var(--primary-dk);margin-bottom:12px;">
              <i class="fas fa-list-check"></i>&nbsp; As minhas reservas activas
            </h3>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;">
              <table style="width:100%;border-collapse:collapse;font-size:.84rem;">
                <thead>
                  <tr style="background:var(--primary-dk);">
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;width:140px;white-space:nowrap;">Data</th>
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;width:130px;white-space:nowrap;">Horas</th>
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;width:160px;white-space:nowrap;">Sala destino</th>
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Equipamentos</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($minhas as $i => $mn): ?>
                  <tr style="border-bottom:1px solid var(--border);<?php echo $i%2===0?'':'background:#f7f9fe;'; ?>">
                    <td style="padding:10px 14px;font-weight:700;white-space:nowrap;"><?php echo date('d/m/Y', strtotime($mn['datautil'])); ?></td>
                    <td style="padding:10px 14px;font-family:monospace;white-space:nowrap;"><?php echo substr($mn['horainicio'],0,5) . ' — ' . substr($mn['horafim'],0,5); ?></td>
                    <td style="padding:10px 14px;"><?php echo htmlspecialchars($mn['nomsala']); ?></td>
                    <td style="padding:10px 14px;color:var(--muted);"><?php echo htmlspecialchars($mn['equipamentos']); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <script>
    const SVRURL = '<?php echo SVRURL; ?>';

    // Reservas do mês em JS para o modal
    const RESERVAS_MES = <?php
        $res_js = [];
        foreach ($todas_reservas as $r) {
            $res_js[] = [
                'id'        => (int)$r['id_req'],
                'data'      => $r['datautil'],
                'dataFmt'   => date('d/m/Y', strtotime($r['datautil'])),
                'hi'        => substr($r['horainicio'], 0, 5),
                'hf'        => substr($r['horafim'], 0, 5),
                'email'     => $r['email_util'],
                'sala'      => $r['nomsala'],
                'equipamentos' => $r['equipamentos'],
                'entregue'  => !empty($r['dataentrega']),
                'minha'     => ($r['email_util'] === $_SESSION['email']),
            ];
        }
        echo json_encode($res_js);
    ?>;

    function abrirReserva(idReq, equipamentos, dataFmt, hi, hf, emailUtil, nomsala, entregue, eMinha) {
        const estado    = entregue ? 'Entregue' : 'Activa';
        const corEstado = entregue ? '#7b88a0' : '#1cc88a';
        const btnEntregar = eMinha && !entregue
            ? `<a href="${SVRURL}entregar_requisicao.php?ri=${btoa(String(idReq))}" class="btn-action primary">
                   <i class="fas fa-check"></i> Marcar como devolvido
               </a>`
            : '';
        const btnEliminar = `<button onclick="eliminarRequisicao(${idReq})" class="btn-action" style="background:#e74a3b;color:#fff;">
                   <i class="fas fa-trash-alt"></i> Eliminar
               </button>`;
        const acoes = btnEntregar + btnEliminar;
        document.getElementById('painelContent').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div style="font-size:1rem;font-weight:700;color:var(--text,#1e2a45);">Reserva #${idReq}</div>
                <button onclick="fecharPainel()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--muted,#7b88a0);">✕</button>
            </div>
            <div class="painel-row"><span class="painel-lbl">Equipamento(s)</span><span class="painel-val" style="max-width:60%;text-align:right;">${equipamentos}</span></div>
            <div class="painel-row"><span class="painel-lbl">Data</span><span class="painel-val">${dataFmt}</span></div>
            <div class="painel-row"><span class="painel-lbl">Horas</span><span class="painel-val">${hi} — ${hf}</span></div>
            <div class="painel-row"><span class="painel-lbl">Sala destino</span><span class="painel-val">${nomsala}</span></div>
            <div class="painel-row"><span class="painel-lbl">Utilizador</span><span class="painel-val">${emailUtil}</span></div>
            <div class="painel-row"><span class="painel-lbl">Estado</span><span class="painel-val" style="color:${corEstado}">${estado}</span></div>
            <div class="painel-actions">
                ${acoes}
                <button onclick="fecharPainel()" class="btn-action secondary">Fechar</button>
            </div>
        `;
        const p = document.getElementById('painelWrap');
        p.classList.add('open');
        p.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        fecharModal();
    }

    function abrirDia(dataStr, dataFmt) {
        const lista = RESERVAS_MES.filter(r => r.data === dataStr);
        document.getElementById('modalTitulo').textContent = dataFmt;
        if (lista.length === 0) {
            document.getElementById('modalContent').innerHTML = '<div class="modal-empty"><i class="fas fa-calendar-check" style="font-size:1.6rem;display:block;margin-bottom:8px;color:#c5cde0;"></i>Sem reservas neste dia.</div>';
        } else {
            document.getElementById('modalContent').innerHTML = lista.map(r => {
                const cor = r.entregue ? '#7b88a0' : (r.minha ? '#1cc88a' : '#4b6cb7');
                return `<div class="modal-res-item" onclick="abrirReserva(${r.id}, '${r.equipamentos.replace(/'/g,"\\'")}', '${r.dataFmt}', '${r.hi}', '${r.hf}', '${r.email.replace(/'/g,"\\'")}', '${r.sala.replace(/'/g,"\\'")}', ${r.entregue}, ${r.minha})">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:${cor};flex-shrink:0;"></span>
                        <span class="equip-nome">${r.equipamentos}</span>
                    </div>
                    <div class="res-info">${r.hi}–${r.hf} &nbsp;·&nbsp; ${r.sala} &nbsp;·&nbsp; ${r.email}</div>
                </div>`;
            }).join('');
        }
        document.getElementById('modalOverlay').classList.add('open');
    }

    function fecharModal(e) {
        if (!e || e.target === document.getElementById('modalOverlay')) {
            document.getElementById('modalOverlay').classList.remove('open');
        }
    }

    function fecharPainel() {
        document.getElementById('painelWrap').classList.remove('open');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') { fecharModal(); fecharPainel(); } });

    function eliminarRequisicao(idReq) {
        if (!confirm('Tem a certeza que pretende eliminar esta requisição?\nEsta acção não pode ser desfeita.')) return;
        window.location.href = SVRURL + 'elimina_requisicao.php?url=' + idReq;
    }
    </script>

    <?php include('footer.php'); ?>

</body>
</html>
