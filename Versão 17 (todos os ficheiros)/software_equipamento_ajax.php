<?php
// ============================================================
// software_equipamento_ajax.php — versão AJAX (sem header/footer)
// ============================================================

// Mostrar todos os erros PHP directamente na resposta
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sessão (mesma config do projeto)
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $isHttps,
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// config.php define $db a partir dos dados de sessão (nobd, serverbd)
// Engolir qualquer output/redirect caso a sessão não tenha os dados
ob_start();
include("config.php");
$config_out = ob_get_clean();
// Se a sessão não tem os dados de BD, o config.php emitiu um redirect JS — rejeitar
if (!isset($db)) {
    http_response_code(401);
    echo '<p style="padding:20px;color:#721c24;">Sessão expirada. Recarregue a página.</p>';
    exit;
}

// sessao_timeout
ob_start();
include("sessao_timeout.php");
ob_end_clean();

// Aceitar ?id= (chamada AJAX standalone) ou ?qi= base64 (include directo via z=3)
if (!empty($_GET['id'])) {
    $id_equip = (int)$_GET['id'];
} elseif (!empty($_GET['qi'])) {
    $id_equip = (int)base64_decode($_GET['qi']);
} elseif (isset($id) && $id > 0) {
    // variável $id já definida pela página pai (dados_tec_redes.php)
    $id_equip = (int)$id;
} else {
    $id_equip = 0;
}
if ($id_equip <= 0) {
    http_response_code(400);
    echo '<p class="text-danger">ID de equipamento inválido.</p>';
    exit;
}

// ── INSERIR ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'inserir') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo '<p class="text-danger">Token CSRF inválido.</p>';
        exit;
    }
    $nome   = trim($_POST['nome']              ?? '');
    $versao = trim($_POST['versao']            ?? '');
    $fab    = trim($_POST['fabricante']        ?? '');
    $lic    = trim($_POST['licenca']           ?? '');
    $dt     = !empty($_POST['data_instalacao']) ? $_POST['data_instalacao'] : null;
    $obs    = trim($_POST['observacoes']       ?? '');
    if ($nome !== '') {
        $stmt = $db->prepare("INSERT INTO software_instalado
            (id_equipamento, nome, versao, fabricante, licenca, data_instalacao, observacoes)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'erro' => 'prepare: ' . $db->error]);
            exit;
        }
        $stmt->bind_param("issssss", $id_equip, $nome, $versao, $fab, $lic, $dt, $obs);
        if (!$stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'erro' => 'execute: ' . $stmt->error]);
            exit;
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// ── ELIMINAR ─────────────────────────────────────────────────
if (isset($_GET['del']) && isset($_GET['csrf']) &&
    hash_equals($_SESSION['csrf_token'], $_GET['csrf'])) {
    $del_id = (int)$_GET['del'];
    $stmt = $db->prepare("DELETE FROM software_instalado WHERE id=? AND id_equipamento=?");
    $stmt->bind_param("ii", $del_id, $id_equip);
    $stmt->execute();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// ── LISTAR ───────────────────────────────────────────────────
$stmt = $db->prepare("SELECT s.* FROM software_instalado s
                      WHERE s.id_equipamento = ?
                      ORDER BY s.nome ASC");
$stmt->bind_param("i", $id_equip);
$stmt->execute();
$lista    = $stmt->get_result();
$total_sw = $lista->num_rows;
?>
<style>
#sw-ajax-wrapper .sw-label,
.sw-label {
    font-weight: 600;
    font-size: .875rem;
    color: #2c3e50;
    margin-bottom: 4px;
    display: block;
}
#sw-ajax-wrapper .form-control,
#sw-ajax-wrapper textarea.form-control {
    width: 100%;
    box-sizing: border-box;
}
#sw-ajax-wrapper .form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -8px;
    margin-left: -8px;
}
#sw-ajax-wrapper .form-row > [class*="col-"] {
    padding-right: 8px;
    padding-left: 8px;
}
</style>
<div class="step-indicator" style="font-size:.95rem;font-weight:700;color:#182848;padding:10px 0 6px;border-bottom:2px solid #4b6cb7;margin-bottom:14px;">
    <i class="fas fa-box-open mr-2"></i>
    Software / Programas instalados
    <span class="sw-counter">
        <i class="fas fa-layer-group"></i>
        <?php echo $total_sw; ?> programa<?php echo $total_sw !== 1 ? 's' : ''; ?>
    </span>
</div>

<div id="sw-alert-ok" class="sw-alert-ok" style="display:none;">
    <i class="fas fa-check-circle"></i> Programa adicionado com sucesso.
</div>

<div class="sw-form-card">
    <div class="sw-form-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Adicionar programa
    </div>
    <form id="sw-insert-form" class="needs-validation" novalidate action="#" onsubmit="return false;">
        <input type="hidden" name="acao"       value="inserir">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="id_equip"   value="<?php echo $id_equip; ?>">

        <div class="form-row">
            <div class="form-group col-12 col-md-6">
                <label for="sw_nome" class="sw-label">Nome do programa: <span style="color:#dc3545;">*</span></label>
                <input required type="text" name="nome" id="sw_nome"
                       class="form-control required-field" placeholder="ex: Microsoft Office 365">
                <div class="invalid-feedback">Campo obrigatório.</div>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="sw_versao" class="sw-label">Versão:</label>
                <input type="text" name="versao" id="sw_versao" class="form-control" placeholder="ex: 2024 / 16.0.1">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-md-6">
                <label for="sw_fab" class="sw-label">Fabricante:</label>
                <input type="text" name="fabricante" id="sw_fab" class="form-control" placeholder="ex: Microsoft">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="sw_lic" class="sw-label">Licença / Nº série:</label>
                <input type="text" name="licenca" id="sw_lic" class="form-control" placeholder="ex: XXXXX-XXXXX-XXXXX ou OEM">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-md-4">
                <label for="sw_dt" class="sw-label">Data de instalação:</label>
                <input type="date" name="data_instalacao" id="sw_dt" class="form-control">
            </div>
            <div class="form-group col-12 col-md-8">
                <label for="sw_obs" class="sw-label">Observações:</label>
                <textarea rows="1" name="observacoes" id="sw_obs" class="form-control" placeholder="Notas adicionais"></textarea>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="button" id="sw-btn-add" class="btn btn-custom">
                <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> Adicionar programa
            </button>
        </div>
    </form>
</div>

<div class="sw-table-wrap" id="sw-table-container">
    <table class="sw-table">
        <thead>
            <tr>
                <th>Programa</th><th>Versão</th><th>Fabricante</th>
                <th>Licença</th><th>Instalação</th><th>Observações</th>
                <th style="width:50px;text-align:center;">Ação</th>
            </tr>
        </thead>
        <tbody id="sw-tbody">
        <?php if ($total_sw === 0): ?>
            <tr>
                <td colspan="7" class="sw-empty">
                    <i class="fas fa-box-open" style="font-size:1.4rem;display:block;margin-bottom:6px;color:#c5cde0;"></i>
                    Nenhum programa registado. Adicione o primeiro acima.
                </td>
            </tr>
        <?php else: ?>
            <?php while ($row = $lista->fetch_assoc()): ?>
            <tr data-sw-id="<?php echo $row['id']; ?>">
                <td class="td-nome"><?php echo htmlspecialchars($row['nome'],       ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['versao']     ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['fabricante'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php if (!empty($row['licenca'])): ?>
                        <span class="badge-lic"><?php echo htmlspecialchars($row['licenca'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php else: echo '—'; endif; ?>
                </td>
                <td><?php echo !empty($row['data_instalacao']) ? date('d/m/Y', strtotime($row['data_instalacao'])) : '—'; ?></td>
                <td><?php echo htmlspecialchars($row['observacoes'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td style="text-align:center;">
                    <button class="btn-del btn-del-sw"
                            data-id="<?php echo $row['id']; ?>"
                            data-nome="<?php echo addslashes(htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8')); ?>"
                            data-csrf="<?php echo urlencode($csrf_token); ?>"
                            data-equip="<?php echo $id_equip; ?>"
                            title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
(function() {
    if (!window.SW_AJAX_URL) {
        window.SW_AJAX_URL = '<?php echo SVRURL ?>software_equipamento_ajax.php';
    }
    var idEquip = <?php echo (int)$id_equip; ?>;

    function swReload() {
        // Se estamos em modo include directo (z=3), recarregar a página
        // Se estamos em modo AJAX wrapper, recarregar o wrapper
        var w = document.getElementById('sw-ajax-wrapper');
        if (w) {
            fetch(window.SW_AJAX_URL + '?id=' + idEquip)
                .then(function(r){ return r.text(); })
                .then(function(html){
                    w.innerHTML = html;
                    w.querySelectorAll('script').forEach(function(s){
                        var ns = document.createElement('script');
                        ns.textContent = s.textContent;
                        document.body.appendChild(ns);
                    });
                });
        } else {
            // Modo include directo — recarregar página actual
            window.location.reload();
        }
    }

    var btn = document.getElementById('sw-btn-add');
    if (btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('sw-insert-form');
            if (!form) return;
            if (!form.checkValidity()) { form.classList.add('was-validated'); return; }

            var fd = new FormData(form);
            fetch(window.SW_AJAX_URL + '?id=' + idEquip, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    if (data.ok) {
                        var alertOk = document.getElementById('sw-alert-ok');
                        if (alertOk) { alertOk.style.display = 'flex'; setTimeout(function(){ alertOk.style.display='none'; }, 4000); }
                        var eb = document.getElementById('sw-alert-err');
                        if (eb) eb.style.display = 'none';
                        form.reset();
                        form.classList.remove('was-validated');
                        swReload();
                    } else {
                        console.error('Erro BD:', data.erro);
                        var eb2 = document.getElementById('sw-alert-err');
                        if (!eb2) {
                            eb2 = document.createElement('div');
                            eb2.id = 'sw-alert-err';
                            eb2.style.cssText = 'background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px;padding:10px 14px;margin-bottom:10px;';
                            form.parentNode.insertBefore(eb2, form);
                        }
                        eb2.innerHTML = '<strong>Erro:</strong> ' + (data.erro || 'desconhecido');
                        eb2.style.display = 'block';
                    }
                })
                .catch(function(err){ console.error('Fetch error:', err); });
        });
    }

    document.querySelectorAll('.btn-del-sw').forEach(function(delBtn) {
        delBtn.addEventListener('click', function() {
            if (!confirm("Eliminar '" + delBtn.dataset.nome + "'?")) return;
            fetch(window.SW_AJAX_URL + '?id=' + idEquip + '&del=' + delBtn.dataset.id + '&csrf=' + delBtn.dataset.csrf)
                .then(function(r){ return r.json(); })
                .then(function(data){ if (data.ok) swReload(); });
        });
    });
})();
</script>
