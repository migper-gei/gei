<?php
// ============================================================
// calendario_reservas.php — GEI
// Calendário semanal de disponibilidade dos equipamentos
// partilhados (salas com equip_requisitavel = 'Sim')
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
        .filter-card select, .filter-card input[type="date"] {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 7px 12px; font-family: inherit; font-size: .88rem;
            color: var(--text); background: #f7f9fe; cursor: pointer; transition: border .2s;
        }
        .filter-card select:focus, .filter-card input:focus { outline: none; border-color: var(--accent); }

        .btn-nav {
            background: var(--primary); border: none;
            border-radius: 8px; padding: 8px 16px; font-family: inherit;
            font-size: .9rem; color: #fff; cursor: pointer;
            transition: background .15s, transform .15s;
            display: inline-flex; align-items: center; gap: 4px;
            font-weight: 700; text-decoration: none;
        }
        .btn-nav:hover { background: var(--accent); color: #fff; transform: translateY(-1px); }

        .btn-requisitar {
            background: var(--primary); color: #fff; border: none;
            border-radius: 8px; padding: 9px 20px; font-family: inherit;
            font-size: .88rem; font-weight: 700; cursor: pointer;
            transition: background .2s; display: flex; align-items: center; gap: 7px;
            text-decoration: none; margin-left: auto;
        }
        .btn-requisitar:hover { background: var(--accent); color: #fff; }

        /* ── Semana label ── */
        .semana-label {
            font-size: .88rem; font-weight: 700; color: var(--primary-dk);
            padding: 0 8px;
        }

        /* ── Tabela calendário ── */
        .cal-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            overflow: hidden; margin-bottom: 24px;
        }
        .cal-table { width: 100%; border-collapse: collapse; min-width: 700px; }
        .cal-table th {
            background: var(--primary-dk); color: #fff;
            padding: 10px 12px; font-size: .78rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .4px; text-align: center;
        }
        .cal-table th.col-equip { text-align: left; width: 200px; }
        .cal-table th.hoje { background: var(--accent); }

        .cal-table td {
            padding: 0; border: 1px solid var(--border);
            vertical-align: top; min-height: 60px;
        }
        .cal-table td.col-equip {
            padding: 10px 14px; background: #f7f9fe;
            border-right: 2px solid var(--border);
        }
        .equip-nome { font-size: .84rem; font-weight: 700; color: var(--text); }
        .equip-sala { font-size: .72rem; color: var(--muted); margin-top: 2px; }

        .dia-cell {
            min-height: 60px; padding: 4px; background: var(--surface);
            cursor: pointer; transition: background .15s;
        }
        .dia-cell:hover { background: #f0f4fb; }
        .dia-cell.hoje  { background: #eef3ff; }
        .dia-cell.passado { background: #fafafa; cursor: default; }
        .dia-cell.passado:hover { background: #fafafa; }

        .reserva-bloco {
            background: #4b6cb7; color: #fff;
            border-radius: 4px; padding: 2px 6px; margin-bottom: 2px;
            font-size: .68rem; font-weight: 600; line-height: 1.4;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .reserva-bloco.minha { background: #1cc88a; }
        .reserva-bloco.entregue { background: var(--muted); }

        .livre-tag {
            font-size: .68rem; color: var(--muted); padding: 2px 4px;
            font-style: italic;
        }

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
        .painel-title { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 12px; }
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

        .overflow-x { overflow-x: auto; }

        .empty-state {
            text-align: center; padding: 40px; color: var(--muted);
        }
        .empty-state i { font-size: 2rem; display: block; margin-bottom: 10px; }
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
    $res_esc = $stmt_esc->get_result();
    while ($row = $res_esc->fetch_assoc()) { $escolas[] = $row; }
    $stmt_esc->close();

    $esc_id = 0;
    if (!empty($_POST['escola']))     $esc_id = (int)$_POST['escola'];
    elseif (!empty($_GET['esc']))     $esc_id = (int)base64_decode($_GET['esc']);
    if ($esc_id === 0 && !empty($escolas)) $esc_id = (int)$escolas[0]['id'];

    // ── Semana a visualizar ───────────────────────────────────────────────────
    // Segunda-feira da semana seleccionada
    $offset = isset($_GET['s']) ? (int)$_GET['s'] : (isset($_POST['semana_offset']) ? (int)$_POST['semana_offset'] : 0); // semanas relativas à actual
    $hoje   = new DateTime();
    $seg    = clone $hoje;
    $seg->modify('monday this week');
    if ($offset !== 0) $seg->modify("{$offset} week");
    $dias = [];
    for ($i = 0; $i < 7; $i++) {
        $d = clone $seg;
        $d->modify("+{$i} days");
        $dias[] = $d;
    }
    $sem_label = $dias[0]->format('d/m') . ' — ' . $dias[6]->format('d/m/Y');

    $offset_ant = $offset - 1;
    $offset_prx = $offset + 1;

    // ── Filtros ───────────────────────────────────────────────────────────────
    $sala_id   = 0;
    $tipo_fil  = '';

    if (!empty($_POST['sala_fil']))    $sala_id  = (int)$_POST['sala_fil'];
    elseif (!empty($_GET['sala']))     $sala_id  = (int)$_GET['sala'];
    if (!empty($_POST['tipo_fil']))    $tipo_fil = trim($_POST['tipo_fil']);
    elseif (isset($_GET['tipo']))      $tipo_fil = trim($_GET['tipo']);

    // ── Seleção de equipamentos guardada na sessão (persiste entre filtros) ───
    $sess_key = 'equips_sel_' . $esc_id; // chave por escola
    if (!empty($_POST['equips_active'])) {
        // Utilizador submeteu os checkboxes — actualiza a sessão
        $novos = !empty($_POST['equips_sel']) ? array_map('intval', $_POST['equips_sel']) : [];
        // Se vem do frmEquips (checkbox directo): substitui apenas os IDs visíveis naquele filtro
        // Os IDs doutras salas/tipos que já estavam seleccionados mantêm-se
        if (!empty($_POST['equips_visible'])) {
            $visiveis = array_map('intval', explode(',', $_POST['equips_visible']));
            $anteriores = isset($_SESSION[$sess_key]) ? $_SESSION[$sess_key] : [];
            // Remove da sessão os que estavam visíveis (vão ser substituídos)
            $anteriores = array_values(array_filter($anteriores, fn($id) => !in_array($id, $visiveis)));
            // Junta os novos marcados
            $sel_final = array_values(array_unique(array_merge($anteriores, $novos)));
            $_SESSION[$sess_key] = $sel_final;
        } else {
            $_SESSION[$sess_key] = $novos;
        }
    }
    if (!empty($_POST['limpar_sel'])) {
        $_SESSION[$sess_key] = [];
    }
    $equips_sel = isset($_SESSION[$sess_key]) ? $_SESSION[$sess_key] : null; // null = ainda não foi usada a sessão

    // Salas requisitáveis (para o select)
    $salas_fil = [];
    $stmt_sal = $db->prepare("SELECT id, nome FROM salas WHERE id_escola = ? AND equip_requisitavel = 'Sim' ORDER BY nome");
    $stmt_sal->bind_param('i', $esc_id);
    $stmt_sal->execute();
    $res_sal = $stmt_sal->get_result();
    while ($row_sal = $res_sal->fetch_assoc()) { $salas_fil[] = $row_sal; }
    $stmt_sal->close();

    // Tipos disponíveis (para o select)
    $tipos_fil = [];
    $stmt_tip = $db->prepare("SELECT DISTINCT eq.tipo FROM equipamento eq INNER JOIN salas s ON s.id = eq.id_sala WHERE s.id_escola = ? AND s.equip_requisitavel = 'Sim' AND eq.tipo IS NOT NULL AND eq.tipo <> '' AND eq.id NOT IN (SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL) ORDER BY eq.tipo");
    $stmt_tip->bind_param('i', $esc_id);
    $stmt_tip->execute();
    $res_tip = $stmt_tip->get_result();
    while ($row_tip = $res_tip->fetch_row()) { $tipos_fil[] = $row_tip[0]; }
    $stmt_tip->close();

    // ── Todos os equipamentos (para os checkboxes) ────────────────────────────
    $todos_equip = [];
    $sql_all  = "SELECT eq.id, eq.nomeequi, eq.tipo, s.nome AS nomsala
                 FROM equipamento eq
                 INNER JOIN salas s ON s.id = eq.id_sala
                 WHERE s.id_escola = ?
                   AND s.equip_requisitavel = 'Sim'
                   AND eq.id NOT IN (SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL)";
    $types_all  = 'i';
    $params_all = [$esc_id];
    if ($sala_id > 0)     { $sql_all .= " AND s.id = ?";       $types_all .= 'i'; $params_all[] = $sala_id; }
    if ($tipo_fil !== '') { $sql_all .= " AND eq.tipo = ?";     $types_all .= 's'; $params_all[] = $tipo_fil; }
    $sql_all .= " ORDER BY eq.tipo, eq.nomeequi";
    $stmt_all = $db->prepare($sql_all);
    $stmt_all->bind_param($types_all, ...$params_all);
    $stmt_all->execute();
    $res_all = $stmt_all->get_result();
    while ($row_all = $res_all->fetch_assoc()) { $todos_equip[] = $row_all; }
    $stmt_all->close();

    // ── Equipamentos a mostrar no calendário (filtrados pelos checkboxes) ─────
    if ($equips_sel !== null && count($equips_sel) > 0) {
        $equipamentos = array_filter($todos_equip, fn($e) => in_array((int)$e['id'], $equips_sel));
        $equipamentos = array_values($equipamentos);
    } elseif ($equips_sel !== null && count($equips_sel) === 0) {
        $equipamentos = []; // seleção explícita vazia = mostrar nenhum
    } else {
        $equipamentos = $todos_equip; // null = nunca foi usada a sessão, mostra todos
    }

    // ── Reservas da semana ────────────────────────────────────────────────────
    $data_ini = $dias[0]->format('Y-m-d');
    $data_fim = $dias[6]->format('Y-m-d');

    $stmt_res = $db->prepare("
        SELECT er.id_equip, r.id AS id_req, r.datautil, r.horainicio, r.horafim,
               r.email_util, r.dataentrega, s.nome AS nomsala
        FROM requisicao r
        INNER JOIN equip_requisitado er ON er.id_req = r.id
        INNER JOIN salas s ON s.id = r.id_sala
        WHERE s.id_escola = ?
          AND r.datautil BETWEEN STR_TO_DATE(?, '%Y-%m-%d') AND STR_TO_DATE(?, '%Y-%m-%d')
        ORDER BY r.datautil, r.horainicio
    ");
    $stmt_res->bind_param('iss', $esc_id, $data_ini, $data_fim);
    $stmt_res->execute();
    $res_result = $stmt_res->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_res->close();

    // Indexar por id_equip + data
    $reservas = [];
    foreach ($res_result as $r) {
        $key = $r['id_equip'] . '_' . $r['datautil'];
        $reservas[$key][] = $r;
    }

    $nome_escola_sel = '';
    foreach ($escolas as $e) {
        if ((int)$e['id'] === $esc_id) { $nome_escola_sel = $e['nome_escola']; break; }
    }
    ?>

    <div class="about">
      <div class="container-fluid">
        <div class="cal-wrap">

          <!-- Breadcrumb -->
          <nav style="margin-bottom:16px;">
            <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
              <li><a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;"><i class="fas fa-desktop"></i> Equipamentos</a></li>
              <li style="color:#c5cde0;">&#8250;</li>
              <li style="color:#1e2a45;">Calendário de reservas</li>
            </ol>
          </nav>

          <div class="page-title">
            <div class="page-title-icon"><i class="fa-solid fa-calendar-week"></i></div>
            <div>
           
              <p>Disponibilidade semanal dos equipamentos partilhados</p>
            </div>
          </div>

          <!-- Filtros + navegação -->
          <form method="POST" action="" id="frmFiltro">
          <input type="hidden" name="semana_offset" value="<?php echo $offset; ?>">
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

            <?php
              $nav_params = 'esc=' . base64_encode($esc_id);
              if ($sala_id > 0)   $nav_params .= '&sala=' . $sala_id;
              if ($tipo_fil !== '') $nav_params .= '&tipo=' . urlencode($tipo_fil);
            ?>
            <div style="display:flex;align-items:center;gap:8px;margin-top:16px;">
              <a href="?<?php echo $nav_params; ?>&s=<?php echo $offset_ant; ?>" class="btn-nav">
                <i class="fas fa-chevron-left"></i>
              </a>
              <span class="semana-label"><?php echo $sem_label; ?></span>
              <a href="?<?php echo $nav_params; ?>&s=<?php echo $offset_prx; ?>" class="btn-nav">
                <i class="fas fa-chevron-right"></i>
              </a>
              <?php if ($offset !== 0): ?>
              <a href="?<?php echo $nav_params; ?>&s=0" class="btn-nav" style="background:#f0f4fb;color:var(--primary-dk);border:1.5px solid var(--border);font-size:.8rem;">
                <i class="fas fa-calendar-day"></i> Hoje
              </a>
              <?php endif; ?>
            </div>

      

          </div>
          </form>

          <!-- Checkboxes equipamentos -->
          <?php
          // IDs de todos os equipamentos visíveis com o filtro actual
          $ids_visiveis = array_column($todos_equip, 'id');
          // Contagem total seleccionada (incluindo doutras salas/tipos)
          $total_sel = $equips_sel !== null ? count($equips_sel) : count($todos_equip);
          ?>
          <?php if (!empty($todos_equip)): ?>
          <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px 20px;margin-bottom:16px;box-shadow:var(--shadow);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
              <span style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">
                <i class="fas fa-check-square"></i> Equipamentos a mostrar
                <?php if ($equips_sel !== null && count($equips_sel) > 0): ?>
                  <span style="background:var(--primary);color:#fff;border-radius:20px;padding:1px 8px;font-size:.7rem;margin-left:6px;font-weight:700;">
                    <?php echo count($equips_sel); ?> seleccionado(s)
                  </span>
                <?php endif; ?>
              </span>
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="button" onclick="selecionarTodos(true)"  style="font-size:.72rem;padding:3px 10px;border-radius:5px;border:1px solid var(--border);background:#f0f4fb;color:var(--primary);cursor:pointer;">Todos visíveis</button>
                <button type="button" onclick="selecionarTodos(false)" style="font-size:.72rem;padding:3px 10px;border-radius:5px;border:1px solid var(--border);background:#f0f4fb;color:var(--muted);cursor:pointer;">Nenhum</button>
                <?php if ($equips_sel !== null && count($equips_sel) > 0): ?>
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="escola"        value="<?php echo $esc_id; ?>">
                  <input type="hidden" name="sala_fil"      value="<?php echo $sala_id; ?>">
                  <input type="hidden" name="tipo_fil"      value="<?php echo htmlspecialchars($tipo_fil); ?>">
                  <input type="hidden" name="semana_offset" value="<?php echo $offset; ?>">
                  <input type="hidden" name="limpar_sel"    value="1">
                  <button type="submit" style="font-size:.72rem;padding:3px 10px;border-radius:5px;border:1px solid #f6c23e;background:#fffbf0;color:#b8860b;cursor:pointer;">
                    <i class="fas fa-times"></i> Limpar seleção
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </div>
            <form method="POST" action="" id="frmEquips">
              <input type="hidden" name="escola"        value="<?php echo $esc_id; ?>">
              <input type="hidden" name="sala_fil"      value="<?php echo $sala_id; ?>">
              <input type="hidden" name="tipo_fil"      value="<?php echo htmlspecialchars($tipo_fil); ?>">
              <input type="hidden" name="equips_active" value="1">
              <input type="hidden" name="semana_offset" value="<?php echo $offset; ?>">
              <input type="hidden" name="equips_visible" value="<?php echo implode(',', $ids_visiveis); ?>">
              <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <?php foreach ($todos_equip as $te):
                  $marcado = ($equips_sel === null) || in_array((int)$te['id'], $equips_sel);
                ?>
                <label style="display:flex;align-items:center;gap:5px;font-size:.82rem;color:var(--text);cursor:pointer;padding:4px 10px;border-radius:6px;border:1px solid var(--border);background:<?php echo $marcado ? '#eef3ff' : '#f7f9fe'; ?>;">
                  <input type="checkbox" name="equips_sel[]" value="<?php echo $te['id']; ?>"
                    <?php echo $marcado ? 'checked' : ''; ?>
                    onchange="document.getElementById('frmEquips').submit()"
                    style="accent-color:var(--primary);">
                  <?php echo htmlspecialchars($te['nomeequi']); ?>
                  <span style="font-size:.68rem;color:var(--muted);">(<?php echo htmlspecialchars($te['tipo'] ?: '—'); ?>)</span>
                </label>
                <?php endforeach; ?>
              </div>
              <?php if ($equips_sel !== null): ?>
              <!-- Mostra equipamentos seleccionados de outras salas/tipos (não visíveis no filtro actual) -->
              <?php
              $ids_noutr = array_filter($equips_sel, fn($id) => !in_array($id, $ids_visiveis));
              if (!empty($ids_noutr)):
                // Busca nomes
                $placeholders = implode(',', array_fill(0, count($ids_noutr), '?'));
                $types_outr   = str_repeat('i', count($ids_noutr));
                $stmt_outr = $db->prepare("SELECT eq.id, eq.nomeequi, eq.tipo, s.nome AS nomsala FROM equipamento eq INNER JOIN salas s ON s.id = eq.id_sala WHERE eq.id IN ($placeholders) AND eq.id NOT IN (SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL)");
                $stmt_outr->bind_param($types_outr, ...$ids_noutr);
                $stmt_outr->execute();
                $res_outr = $stmt_outr->get_result();
                $outr_equip = [];
                while ($r = $res_outr->fetch_assoc()) $outr_equip[] = $r;
                $stmt_outr->close();
              ?>
              <?php if (!empty($outr_equip)): ?>
              <div style="margin-top:10px;padding-top:10px;border-top:1px dashed var(--border);">
                <span style="font-size:.72rem;color:var(--muted);font-weight:700;">Também seleccionados (de outros filtros):</span>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;">
                  <?php foreach ($outr_equip as $oe): ?>
                  <span style="font-size:.78rem;padding:3px 10px;border-radius:6px;border:1px solid var(--accent);background:#eef3ff;color:var(--primary-dk);">
                    <?php echo htmlspecialchars($oe['nomeequi']); ?>
                    <span style="color:var(--muted);font-size:.68rem;">(<?php echo htmlspecialchars($oe['nomsala']); ?>)</span>
                  </span>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endif; ?>
              <?php endif; ?>
              <?php endif; ?>
            </form>
          </div>
          <?php endif; ?>

          <!-- Legenda -->
          <div class="legend">
            <div class="leg"><div class="leg-box" style="background:#4b6cb7;"></div> Reservado (outro utilizador)</div>
            <div class="leg"><div class="leg-box" style="background:#1cc88a;"></div> Reservado por mim</div>
            <div class="leg"><div class="leg-box" style="background:#7b88a0;"></div> Entregue / concluído</div>
            <div class="leg"><div class="leg-box" style="background:#f0f4fb;border:1px solid #e3e8f4;"></div> Disponível</div>
          </div>

          <!-- Painel de detalhe da reserva -->
          <div class="painel-wrap" id="painelWrap">
            <div id="painelContent"></div>
          </div>

          <!-- Calendário -->
          <?php if (empty($equipamentos)): ?>
            <div class="empty-state">
              <i class="fa fa-box-open"></i>
              <p>Nenhum equipamento partilhado encontrado.<br>
                 <small>Configure salas com "Equipamentos requisitáveis: Sim" para activar as reservas.</small>
              </p>
            </div>
          <?php else: ?>
          <div class="cal-card overflow-x">
            <table class="cal-table">
              <thead>
                <tr>
                  <th class="col-equip">Equipamento</th>
                  <?php
                  $nomes_dias = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];
                  foreach ($dias as $i => $dia):
                    $is_hoje = $dia->format('Y-m-d') === $hoje->format('Y-m-d');
                  ?>
                  <th class="<?php echo $is_hoje ? 'hoje' : ''; ?>">
                    <?php echo $nomes_dias[$i]; ?><br>
                    <span style="font-size:.85rem;font-weight:400;"><?php echo $dia->format('d/m'); ?></span>
                  </th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($equipamentos as $eq): ?>
                <tr>
                  <td class="col-equip">
                    <div class="equip-nome"><?php echo htmlspecialchars($eq['nomeequi']); ?></div>
                    <div class="equip-sala">
                      <i class="fas fa-tag" style="font-size:.6rem;"></i>
                      <?php echo htmlspecialchars($eq['tipo'] ?: '—'); ?>
                      &nbsp;·&nbsp;
                      <?php echo htmlspecialchars($eq['nomsala']); ?>
                    </div>
                  </td>
                  <?php foreach ($dias as $dia):
                    $data_key = $eq['id'] . '_' . $dia->format('Y-m-d');
                    $reservas_dia = $reservas[$data_key] ?? [];
                    $is_hoje   = $dia->format('Y-m-d') === $hoje->format('Y-m-d');
                    $is_passado = $dia->format('Y-m-d') < $hoje->format('Y-m-d');
                  ?>
                  <td>
                    <div class="dia-cell <?php echo $is_hoje ? 'hoje' : ($is_passado ? 'passado' : ''); ?>"
                         <?php if (!$is_passado): ?>
                         onclick="abrirDia(<?php echo $eq['id']; ?>, '<?php echo addslashes(htmlspecialchars($eq['nomeequi'])); ?>', '<?php echo $dia->format('Y-m-d'); ?>', '<?php echo $dia->format('d/m/Y'); ?>', <?php echo $esc_id; ?>)"
                         <?php endif; ?>>
                      <?php if (empty($reservas_dia) && !$is_hoje && !$is_passado): ?>
                        <span class="livre-tag">livre</span>
                      <?php else:
                        foreach ($reservas_dia as $res):
                          $e_minha  = ($res['email_util'] === $_SESSION['email']);
                          $entregue = !empty($res['dataentrega']);
                          $classe   = $entregue ? 'entregue' : ($e_minha ? 'minha' : '');
                          $hi = substr($res['horainicio'], 0, 5);
                          $hf = substr($res['horafim'], 0, 5);
                      ?>
                        <div class="reserva-bloco <?php echo $classe; ?>"
                             onclick="event.stopPropagation(); abrirReserva(<?php echo $res['id_req']; ?>, '<?php echo addslashes(htmlspecialchars($eq['nomeequi'])); ?>', '<?php echo $dia->format('d/m/Y'); ?>', '<?php echo $hi; ?>', '<?php echo $hf; ?>', '<?php echo addslashes(htmlspecialchars($res['email_util'])); ?>', '<?php echo addslashes(htmlspecialchars($res['nomsala'])); ?>', <?php echo $entregue ? 'true' : 'false'; ?>, <?php echo $e_minha ? 'true' : 'false'; ?>, <?php echo $res['id_req']; ?>)"
                             title="<?php echo $hi . '–' . $hf . ' | ' . htmlspecialchars($res['email_util']); ?>">
                          <?php echo $hi . '–' . $hf; ?>
                        </div>
                      <?php endforeach; endif; ?>
                    </div>
                  </td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>

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
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;width:160px;white-space:nowrap;">Data utilização</th>
                    <th style="padding:10px 14px;color:#fff;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;width:150px;white-space:nowrap;">Horas</th>
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

    <?php
    // Todos os equipamentos seleccionados na sessão (inclui os de outras salas/tipos)
    $todos_sel_js = [];
    if (!empty($equips_sel)) {
        $placeholders_js = implode(',', array_fill(0, count($equips_sel), '?'));
        $types_js        = str_repeat('i', count($equips_sel));
        $stmt_js = $db->prepare("SELECT eq.id, eq.nomeequi, eq.tipo, s.nome AS nomsala FROM equipamento eq INNER JOIN salas s ON s.id = eq.id_sala WHERE eq.id IN ($placeholders_js) AND eq.id NOT IN (SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL)");
        $stmt_js->bind_param($types_js, ...$equips_sel);
        $stmt_js->execute();
        $res_js = $stmt_js->get_result();
        while ($r = $res_js->fetch_assoc()) {
            $todos_sel_js[] = ['id' => (int)$r['id'], 'nome' => $r['nomeequi'] . ' (' . ($r['tipo'] ?: '—') . ')', 'sala' => $r['nomsala']];
        }
        $stmt_js->close();
    }
    ?>
    // Todos os IDs e nomes seleccionados (sessão PHP → JS)
    const EQUIPS_SEL = <?php echo json_encode($todos_sel_js); ?>;

    function getEquipsSel() {
        // Usa sempre a lista completa da sessão (não só os checkboxes visíveis)
        return EQUIPS_SEL.map(e => e.id);
    }

    function abrirDia(idEquip, nomeEquip, data, dataFmt, escId) {
        // Se não há nada na sessão, usa o equipamento clicado
        const lista = EQUIPS_SEL.length > 0 ? EQUIPS_SEL : [{id: idEquip, nome: nomeEquip}];
        const selIds = lista.map(e => e.id);
        const eqParam = btoa(selIds.join(','));
        const nomesHtml = lista.map(e =>
            `<div style="display:flex;align-items:center;gap:6px;padding:3px 0;">
                <i class="fas fa-check" style="color:var(--success,#1cc88a);font-size:.7rem;"></i>
                <span>${e.nome} <span style="color:var(--muted,#7b88a0);font-size:.75rem;">— ${e.sala}</span></span>
             </div>`
        ).join('');
        document.getElementById('painelContent').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--text, #1e2a45);">${dataFmt}</div>
                    <div style="font-size:.78rem;color:var(--muted, #7b88a0);">${lista.length} equipamento(s) seleccionado(s)</div>
                </div>
                <button onclick="fecharPainel()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--muted,#7b88a0);">✕</button>
            </div>
            <p style="font-size:.82rem;color:var(--muted,#7b88a0);margin-bottom:6px;font-weight:600;">Equipamentos a requisitar:</p>
            <div style="font-size:.84rem;color:var(--text,#1e2a45);margin-bottom:14px;">${nomesHtml}</div>
            <div style="display:flex;gap:8px;">
                <a href="${SVRURL}reqequip?x=${btoa('1')}&rei=${btoa(String(escId))}&dr=${btoa(data)}&eq=${eqParam}"
                   class="btn-action primary">
                    <i class="fas fa-cart-flatbed"></i> Requisitar para este dia
                </a>
                <button onclick="fecharPainel()" class="btn-action secondary">Fechar</button>
            </div>
        `;
        const p = document.getElementById('painelWrap');
        p.classList.add('open');
        p.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function abrirReserva(idReq, nomeEquip, dataFmt, hi, hf, emailUtil, nomsala, entregue, eMinha, idReqNum) {
        const estado = entregue ? 'Entregue' : 'Activa';
        const corEstado = entregue ? '#7b88a0' : '#1cc88a';
        document.getElementById('painelContent').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div style="font-size:1rem;font-weight:700;color:var(--text, #1e2a45);">Reserva #${idReq}</div>
                <button onclick="fecharPainel()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--muted,#7b88a0);">✕</button>
            </div>
            <div class="painel-row"><span class="painel-lbl">Equipamento</span><span class="painel-val">${nomeEquip}</span></div>
            <div class="painel-row"><span class="painel-lbl">Data</span><span class="painel-val">${dataFmt}</span></div>
            <div class="painel-row"><span class="painel-lbl">Horas</span><span class="painel-val">${hi} — ${hf}</span></div>
            <div class="painel-row"><span class="painel-lbl">Sala destino</span><span class="painel-val">${nomsala}</span></div>
            <div class="painel-row"><span class="painel-lbl">Utilizador</span><span class="painel-val">${emailUtil}</span></div>
            <div class="painel-row"><span class="painel-lbl">Estado</span><span class="painel-val" style="color:${corEstado}">${estado}</span></div>
            <div class="painel-actions">
                <button onclick="fecharPainel()" class="btn-action secondary">Fechar</button>
            </div>
        `;
        const p = document.getElementById('painelWrap');
        p.classList.add('open');
        p.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function selecionarTodos(sel) {
        document.querySelectorAll('#frmEquips input[type=checkbox]').forEach(cb => cb.checked = sel);
        document.getElementById('frmEquips').submit();
    }

    function fecharPainel() {
        document.getElementById('painelWrap').classList.remove('open');
    }
    </script>

    <?php include('footer.php'); ?>

      <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->


            <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.gei-theme-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() { window.GEITheme.toggle(); }, true);
    });
});
</script>
</body>
</body>
</html>
