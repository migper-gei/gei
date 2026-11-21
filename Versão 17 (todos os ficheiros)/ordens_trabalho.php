<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
    if (!isset($_SESSION['_created'])) { $_SESSION['_created'] = time(); }
    elseif (time() - $_SESSION['_created'] > 1800) { session_regenerate_id(true); $_SESSION['_created'] = time(); }
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head><?php include("head.php"); ?></head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>
      <?php
include("css_inserir.php");
include("sessao_timeout.php");

// Filtro por plano (opcional)
$filtro_plano = isset($_GET['plano']) ? (int)base64_decode(urldecode($_GET['plano'])) : 0;
$filtro_estado = trim($_GET['estado'] ?? '');
$hoje = date('Y-m-d');
?>

<style>
.gei-table-wrap{background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(75,108,183,.10);border:1px solid #e3e8f4;overflow:hidden;margin-bottom:16px}
.gei-table-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:12px 16px;background:#f4f6fb;border-bottom:1px solid #e3e8f4}
.gei-table{width:100%;border-collapse:collapse;font-size:.84rem}
.gei-table thead th{padding:10px 14px;background:#182848;color:#fff;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;border:none;white-space:nowrap}
.gei-table tbody tr{border-bottom:1px solid #eef1f8;transition:background .15s}
.gei-table tbody tr:last-child{border-bottom:none}
.gei-table tbody tr:hover{background:#f0f4fb}
.gei-table tbody tr:nth-child(even){background:#f7f9fe}
.gei-table tbody tr:nth-child(even):hover{background:#eef2fb}
.gei-table td{padding:9px 14px;vertical-align:middle;color:#1e2a45}
.gei-action-btn{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none !important;border:none;cursor:pointer;transition:opacity .15s;white-space:nowrap}
.gei-action-btn:hover{opacity:.82}
body .gei-action-btn.gei-btn-concluir{background:#c3f0dc !important;color:#065f34 !important;-webkit-text-fill-color:#065f34 !important;border:1.5px solid #6fcf9f !important;font-weight:700}
body .gei-action-btn.gei-btn-concluir:hover{background:#0d6e3f !important;color:#fff !important;-webkit-text-fill-color:#fff !important;border-color:#0d6e3f !important;opacity:1}
body .gei-action-btn.gei-btn-concluir svg{stroke:currentColor !important}
body .gei-action-btn.gei-btn-cancelar{background:#fcd9d5 !important;color:#a52a1d !important;-webkit-text-fill-color:#a52a1d !important;border:1.5px solid #ee9d95 !important;font-weight:700}
body .gei-action-btn.gei-btn-cancelar:hover{background:#c0392b !important;color:#fff !important;-webkit-text-fill-color:#fff !important;border-color:#c0392b !important;opacity:1}
body .gei-action-btn.gei-btn-cancelar svg{stroke:currentColor !important}
body a.gei-btn-back{-webkit-text-fill-color:#fff !important}
.gei-btn-back{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:700;background:#4b6cb7 !important;color:#fff !important;border:1.5px solid #3a5599 !important;text-decoration:none !important;box-shadow:0 1px 3px rgba(24,40,72,.25);transition:background .15s,box-shadow .15s}
body a.gei-btn-back:hover,body a.gei-btn-back:focus,.gei-btn-back:hover,.gei-btn-back:focus{-webkit-text-fill-color:#fff !important;background:#182848 !important;border-color:#182848 !important;color:#fff !important;box-shadow:0 2px 6px rgba(24,40,72,.35)}
.gei-btn-back svg{stroke:#fff}
.badge-pendente{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;background:#fff3e0;color:#e65100}
.badge-em_curso{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;background:#e3f2fd;color:#1565c0}
.badge-concluida{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;background:#d4f7ea;color:#0d6e3f}
.badge-cancelada{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;background:#f4f6fb;color:#6c757d}
.filter-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;padding:10px 14px;background:#f4f6fb;border-radius:8px;border:1px solid #e3e8f4}
.filter-bar label{font-size:.78rem;font-weight:600;color:#4b6cb7;margin:0}
.filter-bar select{padding:5px 10px;border:1.5px solid #d1d9ed;border-radius:6px;font-size:.8rem;color:#1e2a45}
</style>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <a href="<?php echo SVRURL ?>planos_manutencao.php" style="color:#4b6cb7;text-decoration:none;">Planos Recorrentes</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Ordens de Trabalho</li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
                  <div class="welcome-section"><?php include("msg_bemvindo.php"); ?></div>

<?php
// ── Info do plano (se filtrado) ────────────────────────────────────────────
$nome_plano = '';
if ($filtro_plano > 0) {
    $stmt_pn = $db->prepare("SELECT nome FROM plano_manutencao WHERE id = ?");
    $stmt_pn->bind_param('i', $filtro_plano);
    $stmt_pn->execute();
    $row_pn = $stmt_pn->get_result()->fetch_assoc();
    $stmt_pn->close();
    $nome_plano = $row_pn['nome'] ?? '';
}

// ── Processar ação de mudar estado (AJAX-less, POST) ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_POST['acao'])
    && !empty($_POST['csrf_token'])
    && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {

    $id_ot   = (int)($_POST['id_ot'] ?? 0);
    $acao    = $_POST['acao'];
    $obs_ot  = trim($_POST['obs'] ?? '');
    $data_ex = trim($_POST['data_execucao'] ?? '');

    if ($id_ot > 0 && in_array($acao, ['concluir','cancelar','em_curso'])) {
        $map_estado = ['concluir' => 'concluida', 'cancelar' => 'cancelada', 'em_curso' => 'em_curso'];
        $novo_estado = $map_estado[$acao];

        if ($acao === 'concluir') {
            // Registar também na tabela manutencao (histórico principal)
            $stmt_det = $db->prepare("SELECT ot.id_equipamento, ot.id_plano, ot.data_prevista, p.id_tipo_manuten, ot.responsavel, ot.descricao FROM ordem_trabalho ot JOIN plano_manutencao p ON p.id=ot.id_plano WHERE ot.id=?");
            $stmt_det->bind_param('i', $id_ot);
            $stmt_det->execute();
            $det = $stmt_det->get_result()->fetch_assoc();
            $stmt_det->close();

            if ($det) {
                $data_real = (!empty($data_ex) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_ex)) ? $data_ex : date('Y-m-d');
                $descr_man = $det['descricao'] . ' [Plano recorrente #' . $det['id_plano'] . ']';
                $obs_final  = $obs_ot ?: 'Gerado automaticamente por plano recorrente.';

                $stmt_mn = $db->prepare("INSERT INTO manutencao (id_equi, data_manutencao, pessoa, descricao, observacoes) VALUES (?, STR_TO_DATE(?,'%Y-%m-%d'), ?, ?, ?)");
                $stmt_mn->bind_param('issss', $det['id_equipamento'], $data_real, $det['responsavel'], $descr_man, $obs_final);
                $stmt_mn->execute();
                $stmt_mn->close();

                $stmt_upd = $db->prepare("UPDATE ordem_trabalho SET estado='concluida', data_execucao=?, observacoes=? WHERE id=?");
                $stmt_upd->bind_param('ssi', $data_real, $obs_final, $id_ot);
                $stmt_upd->execute();
                $stmt_upd->close();
            }
        } else {
            $stmt_upd = $db->prepare("UPDATE ordem_trabalho SET estado=?, observacoes=? WHERE id=?");
            $stmt_upd->bind_param('ssi', $novo_estado, $obs_ot, $id_ot);
            $stmt_upd->execute();
            $stmt_upd->close();
        }
    }
}

// ── Construir query ────────────────────────────────────────────────────────
$where_parts = [];
$params      = [];
$types       = '';

if ($filtro_plano > 0) {
    $where_parts[] = 'ot.id_plano = ?';
    $params[]      = $filtro_plano;
    $types        .= 'i';
}
if (!empty($filtro_estado) && in_array($filtro_estado, ['pendente','em_curso','concluida','cancelada'])) {
    $where_parts[] = 'ot.estado = ?';
    $params[]      = $filtro_estado;
    $types        .= 's';
}

$where_sql = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$sql_ot = "
    SELECT ot.id, ot.data_prevista, ot.data_execucao, ot.estado,
           ot.responsavel, ot.descricao, ot.observacoes,
           e.nomeequi, e.tipo AS tipo_equip,
           s.nome AS nome_sala,
           p.nome AS nome_plano,
           p.periodicidade
    FROM ordem_trabalho ot
    JOIN equipamento      e ON e.id  = ot.id_equipamento
    JOIN salas            s ON s.id  = e.id_sala
    JOIN plano_manutencao p ON p.id  = ot.id_plano
    $where_sql
    ORDER BY
        FIELD(ot.estado,'em_curso','pendente','concluida','cancelada'),
        ot.data_prevista ASC
";

$stmt_ot = $db->prepare($sql_ot);
if ($types) $stmt_ot->bind_param($types, ...$params);
$stmt_ot->execute();
$res_ot = $stmt_ot->get_result();
$total_ot = $res_ot->num_rows;
?>

<!-- Filtro de estado -->
<form method="GET" action="">
<?php if ($filtro_plano > 0): ?>
<input type="hidden" name="plano" value="<?php echo urlencode(base64_encode($filtro_plano)); ?>">
<?php endif; ?>
<div class="filter-bar">
    <label>Estado:</label>
    <select name="estado" onchange="this.form.submit()">
        <option value="" <?php echo $filtro_estado==='' ? 'selected' : ''; ?>>Todos</option>
        <option value="pendente"  <?php echo $filtro_estado==='pendente'  ? 'selected' : ''; ?>>Pendente</option>
        <option value="em_curso"  <?php echo $filtro_estado==='em_curso'  ? 'selected' : ''; ?>>Em curso</option>
        <option value="concluida" <?php echo $filtro_estado==='concluida' ? 'selected' : ''; ?>>Concluída</option>
        <option value="cancelada" <?php echo $filtro_estado==='cancelada' ? 'selected' : ''; ?>>Cancelada</option>
    </select>
    <?php if ($nome_plano): ?>
    <span style="font-size:.8rem;color:#4b6cb7;font-weight:600;">Plano: <?php echo htmlspecialchars($nome_plano, ENT_QUOTES, 'UTF-8'); ?></span>
    <?php endif; ?>
    <a href="<?php echo SVRURL ?>planos_manutencao.php" class="gei-btn-back" style="margin-left:auto;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Planos
    </a>
</div>
</form>

<!-- Modal de conclusão -->
<div id="modal-concluir" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:28px 30px;max-width:440px;width:92%;box-shadow:0 8px 40px rgba(0,0,0,.18);">
        <h5 style="margin:0 0 16px;font-size:1rem;color:#182848;font-weight:700;">Concluir Ordem de Trabalho</h5>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="acao" value="concluir">
            <input type="hidden" name="id_ot" id="modal_id_ot" value="">
            <?php if ($filtro_plano > 0): ?>
            <input type="hidden" name="plano" value="<?php echo urlencode(base64_encode($filtro_plano)); ?>">
            <?php endif; ?>
            <div style="margin-bottom:12px;">
                <label style="font-size:.82rem;font-weight:600;color:#3d4e6b;display:block;margin-bottom:5px;">Data de execução real</label>
                <input type="date" name="data_execucao" value="<?php echo $hoje; ?>" style="width:100%;padding:8px 12px;border:1.5px solid #d1d9ed;border-radius:7px;font-size:.87rem;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:.82rem;font-weight:600;color:#3d4e6b;display:block;margin-bottom:5px;">Observações</label>
                <textarea name="obs" rows="3" maxlength="255" style="width:100%;padding:8px 12px;border:1.5px solid #d1d9ed;border-radius:7px;font-size:.87rem;" placeholder="Observações sobre a manutenção realizada…"></textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" style="flex:1;padding:9px;border-radius:7px;background:#1cc88a;color:#fff;border:none;font-weight:700;cursor:pointer;">Confirmar conclusão</button>
                <button type="button" onclick="fecharModal()" style="flex:1;padding:9px;border-radius:7px;background:#f4f6fb;color:#4b6cb7;border:1.5px solid #d1d9ed;font-weight:600;cursor:pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <span style="font-size:.85rem;font-weight:700;color:#1e2a45;">Ordens de Trabalho</span>
        <span style="font-size:.78rem;background:#e8edf8;color:#4b6cb7;padding:2px 9px;border-radius:10px;font-weight:600;"><?php echo $total_ot; ?></span>
    </div>

    <?php if ($total_ot === 0): ?>
    <div style="padding:40px;text-align:center;color:#7b88a0;">
        <p style="font-size:.9rem;">Nenhuma ordem de trabalho encontrada.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
    <table class="gei-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Plano</th>
                <th>Equipamento</th>
                <th>Sala</th>
                <th>Data Prevista</th>
                <th>Responsável</th>
                <th>Estado</th>
                <th>Data Execução</th>
                <?php if ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3): ?>
                <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($ot = $res_ot->fetch_assoc()):
            $atrasado = ($ot['estado'] === 'pendente' && $ot['data_prevista'] < $hoje);
            $dprev = date('d/m/Y', strtotime($ot['data_prevista']));
            $dexec = $ot['data_execucao'] ? date('d/m/Y', strtotime($ot['data_execucao'])) : '—';
        ?>
        <tr>
            <td style="color:#aab0bb;font-size:.78rem;"><?php echo $ot['id']; ?></td>
            <td style="font-size:.8rem;color:#4b6cb7;"><?php echo htmlspecialchars($ot['nome_plano'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="font-weight:600;"><?php echo htmlspecialchars($ot['nomeequip'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="font-size:.8rem;"><?php echo htmlspecialchars($ot['nome_sala'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="<?php echo $atrasado ? 'color:#c0392b;font-weight:700;' : ''; ?>"><?php echo $dprev; ?></td>
            <td><?php echo htmlspecialchars($ot['responsavel'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <?php
                $badge = ['pendente'=>'badge-pendente','em_curso'=>'badge-em_curso','concluida'=>'badge-concluida','cancelada'=>'badge-cancelada'];
                $label = ['pendente'=>'Pendente','em_curso'=>'Em curso','concluida'=>'Concluída','cancelada'=>'Cancelada'];
                echo '<span class="'.$badge[$ot['estado']].'">'.$label[$ot['estado']].'</span>';
                ?>
            </td>
            <td><?php echo $dexec; ?></td>
            <?php if ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3): ?>
            <td>
                <?php if (in_array($ot['estado'], ['pendente','em_curso'])): ?>
                <div style="display:flex;gap:5px;">
                    <button onclick="abrirModal(<?php echo $ot['id']; ?>)" class="gei-action-btn gei-btn-concluir">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Concluir
                    </button>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="acao" value="cancelar">
                        <input type="hidden" name="id_ot" value="<?php echo $ot['id']; ?>">
                        <?php if ($filtro_plano > 0): ?>
                        <input type="hidden" name="plano" value="<?php echo urlencode(base64_encode($filtro_plano)); ?>">
                        <?php endif; ?>
                        <button type="submit" class="gei-action-btn gei-btn-cancelar" onclick="return confirm('Cancelar esta ordem de trabalho?')">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Cancelar
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <span style="color:#aab0bb;font-size:.78rem;">—</span>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php $stmt_ot->close(); ?>

<script>
function abrirModal(id) {
    document.getElementById('modal_id_ot').value = id;
    document.getElementById('modal-concluir').style.display = 'flex';
}
function fecharModal() {
    document.getElementById('modal-concluir').style.display = 'none';
}
</script>

                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
