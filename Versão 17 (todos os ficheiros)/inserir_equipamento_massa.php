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

// Token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include("head.php"); ?>
<style>
.massa-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(75,108,183,.10);
    padding: 28px 32px 32px;
    margin-bottom: 30px;
}
.massa-header-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin: 14px 0 20px;
    padding: 10px 16px;
    background: #f4f6fb;
    border: 1px solid #e3e8f4;
    border-radius: 10px;
}
.massa-header-bar span {
    font-size: 1.05rem;
    font-weight: 700;
    color: #182848;
    display: inline-flex;
    align-items: center;
    gap: 7px;
}
.config-section {
    background: #f8f9fd;
    border: 1px solid #e3e8f4;
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 24px;
}
.config-section h6 {
    font-weight: 700;
    color: #4b6cb7;
    margin-bottom: 16px;
    font-size: .9rem;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.tabela-equipamentos {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 6px;
}
.tabela-equipamentos thead th {
    background: #4b6cb7;
    color: #fff;
    font-size: .82rem;
    font-weight: 600;
    padding: 10px 12px;
    text-align: left;
    border: none;
}
.tabela-equipamentos thead th:first-child { border-radius: 8px 0 0 8px; }
.tabela-equipamentos thead th:last-child  { border-radius: 0 8px 8px 0; }
.tabela-equipamentos tbody tr {
    background: #fff;
    box-shadow: 0 1px 4px rgba(75,108,183,.08);
    border-radius: 8px;
}
.tabela-equipamentos tbody td {
    padding: 7px 8px;
    border: none;
    vertical-align: middle;
}
.tabela-equipamentos tbody td:first-child {
    border-radius: 8px 0 0 8px;
    width: 40px;
    text-align: center;
    color: #aab;
    font-size: .82rem;
    font-weight: 600;
}
.tabela-equipamentos tbody td:last-child {
    border-radius: 0 8px 8px 0;
    width: 44px;
    text-align: center;
}
.tabela-equipamentos input[type="text"],
.tabela-equipamentos input[type="date"] {
    width: 100%;
    border: 1px solid #dde3f0;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: .88rem;
    color: #1e2a45;
    background: #fafbff;
    transition: border .2s;
}
.tabela-equipamentos input[type="text"]:focus,
.tabela-equipamentos input[type="date"]:focus {
    border-color: #4b6cb7;
    outline: none;
    background: #fff;
}
.tabela-equipamentos input.input-erro {
    border-color: #dc3545 !important;
    background: #fff5f5 !important;
}
.btn-add-linha {
    background: #4b6cb7;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 9px 20px;
    font-size: .88rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .2s;
}
.btn-add-linha:hover { background: #3a5499; }
.btn-remover {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 1.1rem;
    padding: 2px 6px;
    border-radius: 5px;
    transition: background .15s;
}
.btn-remover:hover { background: #fff0f0; }
.btn-submit-massa {
    background: linear-gradient(90deg,#4b6cb7,#182848);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 36px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: opacity .2s;
}
.btn-submit-massa:hover { opacity: .9; }
.contador-badge {
    display: inline-block;
    background: #4b6cb7;
    color: #fff;
    border-radius: 20px;
    padding: 2px 12px;
    font-size: .82rem;
    font-weight: 700;
    margin-left: 8px;
}
.info-tip {
    background: #eef2fb;
    border-left: 4px solid #4b6cb7;
    border-radius: 0 8px 8px 0;
    padding: 10px 16px;
    font-size: .84rem;
    color: #3a5499;
    margin-bottom: 18px;
}
</style>
</head>

<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php include("sessao_timeout.php"); ?>

<?php
// Validar escola
$sql_maxesc = $db->prepare("SELECT MAX(id) FROM escolas");
$sql_maxesc->execute();
$maxesc = $sql_maxesc->get_result()->fetch_row()[0];

$idescola = (int)base64_decode($_GET["ies"] ?? '');
if ($idescola < 1 || $idescola > $maxesc) {
    echo '<script>window.location.href="' . SVRURL . 'equip";</script>';
    exit;
}

// Nome da escola
$sql_esc = $db->prepare("SELECT nome_escola FROM escolas WHERE id=?");
$sql_esc->bind_param("i", $idescola);
$sql_esc->execute();
$ne = $sql_esc->get_result()->fetch_row()[0];

// Tipos de equipamento
$sql_tipos = $db->prepare("SELECT DISTINCT nome FROM tipos_equipamento ORDER BY nome");
$sql_tipos->execute();
$result_tipos = $sql_tipos->get_result();
$tipos = [];
while ($row = $result_tipos->fetch_assoc()) {
    $tipos[] = $row['nome'];
}

// Salas da escola
$sql_salas = $db->prepare("SELECT id, nome FROM salas WHERE id_escola=? ORDER BY nome");
$sql_salas->bind_param("i", $idescola);
$sql_salas->execute();
$result_salas = $sql_salas->get_result();
$salas = [];
while ($row = $result_salas->fetch_assoc()) {
    $salas[] = $row;
}
?>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb -->
                <nav style="margin-bottom:10px;">
                    <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                            <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamento</a>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Inserção em Massa</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-11 offset-md-1">

                    <!-- Cabeçalho escola -->
                    <div class="massa-header-bar">
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div class="massa-container">

                        <div class="info-tip">
                            <i class="bi bi-info-circle"></i>
                            Selecione a <strong>sala</strong> e o <strong>tipo</strong> comuns a todos os equipamentos. Depois preencha o nome de cada equipamento na tabela. Pode adicionar ou remover linhas conforme necessário.
                        </div>

                        <?php if (empty($salas)): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                            </div>
                        <?php else: ?>

                        <form id="frmMassa" action="<?php echo SVRURL ?>grava_equipamento_massa.php?ies=<?php echo base64_encode($idescola); ?>" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                            <!-- Configurações comuns -->
                            <div class="config-section">
                                <h6><i class="bi bi-sliders"></i> Configurações Comuns</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label fw-600" style="font-weight:600;font-size:.88rem;">Sala <span style="color:#dc3545;">*</span></label>
                                        <select name="sala" id="sala" class="form-control" required>
                                            <option value=""> -- Selecione -- </option>
                                            <?php foreach ($salas as $s): ?>
                                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nome']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Tipo de Equipamento <span style="color:#dc3545;">*</span></label>
                                        <select name="tipoeq" id="tipoeq" class="form-control" required>
                                            <option value=""> -- Selecione -- </option>
                                            <?php foreach ($tipos as $t): ?>
                                                <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Marca/Modelo <small style="color:#888;">(opcional, aplica a todos)</small></label>
                                        <input type="text" name="marcamod_global" id="marcamod_global" class="form-control" placeholder="Ex: Dell OptiPlex 7090">
                                    </div>
                                </div>

                                <!-- Campos técnicos globais -->
                                <hr style="border-color:#e3e8f4;margin:8px 0 16px;">
                                <h6 style="font-weight:700;color:#4b6cb7;margin-bottom:14px;font-size:.85rem;text-transform:uppercase;letter-spacing:.04em;">
                                    <i class="bi bi-cpu"></i> Características Técnicas <small style="font-weight:400;color:#888;text-transform:none;letter-spacing:0;">(opcionais — aplicam-se a todos)</small>
                                </h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Processador</label>
                                        <input type="text" name="cpu" class="form-control" placeholder="Ex: Intel Core i7-1165G7">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Memória RAM (GB)</label>
                                        <input type="text" name="ram" class="form-control" placeholder="Ex: 16">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Disco (GB)</label>
                                        <input type="text" name="disco" class="form-control" placeholder="Ex: 512">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Monitor</label>
                                        <input type="text" name="monitor" class="form-control" placeholder="Ex: Samsung 24''">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Teclado</label>
                                        <input type="text" name="teclado" class="form-control" placeholder="Ex: Logitech K120">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Interface do Teclado</label>
                                        <select name="tecladointerface" class="form-control">
                                            <option value=""> -- </option>
                                            <option value="USB">USB</option>
                                            <option value="PS/2">PS/2</option>
                                            <option value="Sem fios">Sem fios</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Rato</label>
                                        <input type="text" name="rato" class="form-control" placeholder="Ex: Logitech M100">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="col-form-label" style="font-weight:600;font-size:.88rem;">Interface do Rato</label>
                                        <select name="ratointerface" class="form-control">
                                            <option value=""> -- </option>
                                            <option value="USB">USB</option>
                                            <option value="PS/2">PS/2</option>
                                            <option value="Sem fios">Sem fios</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de equipamentos -->
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                                <div style="font-weight:700;color:#182848;font-size:.95rem;">
                                    Equipamentos
                                    <span class="contador-badge" id="contadorLinhas">1</span>
                                </div>
                                <button type="button" class="btn-add-linha" onclick="adicionarLinha()">
                                    <i class="bi bi-plus-circle"></i> Adicionar linha
                                </button>
                            </div>

                            <div style="overflow-x:auto;">
                                <table class="tabela-equipamentos" id="tabelaEquip">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome do Equipamento <span style="color:#ffcdd2;">*</span></th>
                                            <th>Nº Série <small style="font-weight:400;opacity:.8;">(opcional)</small></th>
                                            <th>Data Compra <small style="font-weight:400;opacity:.8;">(opcional)</small></th>
                                            <th>Observações <small style="font-weight:400;opacity:.8;">(opcional)</small></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpoTabela">
                                        <!-- Linha inicial gerada por JS -->
                                    </tbody>
                                </table>
                            </div>

                            <div style="text-align:center;margin-top:28px;">
                                <button type="submit" class="btn-submit-massa" id="btnSubmit">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Guardar Todos os Equipamentos
                                </button>
                            </div>
                        </form>

                        <?php endif; ?>
                    </div>

                    <a href="<?php echo SVRURL ?>equip">
                        <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
                    </a>
                    <br><br>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
var numLinhas = 0;

function adicionarLinha(nome, serie, data, obs) {
    numLinhas++;
    var tbody = document.getElementById('corpoTabela');
    var tr = document.createElement('tr');
    tr.id = 'linha_' + numLinhas;
    tr.innerHTML =
        '<td>' + numLinhas + '</td>' +
        '<td><input type="text" name="nomeq[]" placeholder="Nome do equipamento" required maxlength="120" value="' + (nome||'') + '"></td>' +
        '<td><input type="text" name="nserie[]" placeholder="Nº de série" maxlength="80" value="' + (serie||'') + '"></td>' +
        '<td><input type="date" name="datacompra[]" value="' + (data||'') + '"></td>' +
        '<td><input type="text" name="obs[]" placeholder="Observações" maxlength="255" value="' + (obs||'') + '"></td>' +
        '<td>' +
            (numLinhas > 1
                ? '<button type="button" class="btn-remover" onclick="removerLinha(' + numLinhas + ')" title="Remover linha">&#10005;</button>'
                : '<span style="color:#ccc;font-size:.8rem;">—</span>') +
        '</td>';
    tbody.appendChild(tr);
    atualizarContador();
    tr.querySelector('input[name="nomeq[]"]').focus();
}

function removerLinha(id) {
    var el = document.getElementById('linha_' + id);
    if (el) el.remove();
    renumerarLinhas();
    atualizarContador();
}

function renumerarLinhas() {
    var rows = document.querySelectorAll('#corpoTabela tr');
    rows.forEach(function(tr, i) {
        tr.cells[0].textContent = i + 1;
        if (i === 0) {
            tr.cells[5].innerHTML = '<span style="color:#ccc;font-size:.8rem;">—</span>';
        } else {
            var id = tr.id.replace('linha_', '');
            tr.cells[5].innerHTML = '<button type="button" class="btn-remover" onclick="removerLinha(' + id + ')" title="Remover linha">&#10005;</button>';
        }
    });
}

function atualizarContador() {
    document.getElementById('contadorLinhas').textContent =
        document.querySelectorAll('#corpoTabela tr').length;
}

// Validação e submissão
document.getElementById('frmMassa').addEventListener('submit', function(e) {
    e.preventDefault();
    var sala   = document.getElementById('sala').value;
    var tipo   = document.getElementById('tipoeq').value;
    var nomes  = document.querySelectorAll('input[name="nomeq[]"]');
    var valido = true;

    if (!sala || !tipo) {
        swal('Atenção', 'Selecione a sala e o tipo de equipamento.', 'warning');
        return;
    }

    var nomesVisto = {};
    nomes.forEach(function(inp) {
        inp.classList.remove('input-erro');
        var v = inp.value.trim();
        if (!v) {
            inp.classList.add('input-erro');
            valido = false;
        } else if (nomesVisto[v.toLowerCase()]) {
            inp.classList.add('input-erro');
            valido = false;
        } else {
            nomesVisto[v.toLowerCase()] = true;
        }
    });

    if (!valido) {
        swal('Atenção', 'Existem nomes em branco ou repetidos. Corrija antes de guardar.', 'warning');
        return;
    }

    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-hourglass-split"></i> A guardar...';
    this.submit();
});

// Tecla Enter avança campo ou cria nova linha
document.getElementById('corpoTabela').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        var inputs = Array.from(document.querySelectorAll('#corpoTabela input'));
        var idx = inputs.indexOf(document.activeElement);
        if (idx >= 0 && idx < inputs.length - 1) {
            inputs[idx + 1].focus();
        } else {
            adicionarLinha();
        }
    }
});

// Inicializar com 3 linhas
adicionarLinha();
adicionarLinha();
adicionarLinha();
</script>

<?php include("footer.php"); ?>
      <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>
