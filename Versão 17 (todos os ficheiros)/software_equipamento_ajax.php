<?php
// ============================================================
// software_equipamento_ajax.php — versão AJAX (sem header/footer)
// ============================================================

// Mostrar todos os erros PHP directamente na resposta
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

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

// Lista base (inspirada no ninite.com): uma entrada por aplicação
function sw_importar_base($db) {
    $seed = [
            ['Windows 11','','Microsoft'],
            ['Windows 10','','Microsoft'],
            ['Microsoft Office 365','','Microsoft'],
            ['Microsoft Office 2021','','Microsoft'],
            ['Antivírus (Windows Defender)','','Microsoft'],
            ['Google Chrome','','Google'],
            ['Mozilla Firefox','','Mozilla'],
            ['Microsoft Edge','','Microsoft'],
            ['Opera','','Opera'],
            ['Brave','','Brave Software'],
            ['Vivaldi','','Vivaldi Technologies'],
            ['Zoom','','Zoom Video Communications'],
            ['Discord','','Discord'],
            ['Microsoft Teams','','Microsoft'],
            ['Slack','','Slack Technologies'],
            ['Telegram','','Telegram'],
            ['WhatsApp','','Meta'],
            ['Pidgin','','Pidgin'],
            ['Mozilla Thunderbird','','Mozilla'],
            ['iTunes','','Apple'],
            ['VLC Media Player','','VideoLAN'],
            ['AIMP','','AIMP'],
            ['foobar2000','','Peter Pawlowski'],
            ['Winamp','','Winamp'],
            ['MusicBee','','MusicBee'],
            ['Audacity','','Audacity Team'],
            ['K-Lite Codec Pack','','Codec Guide'],
            ['Spotify','','Spotify'],
            ['MediaMonkey','','Ventis Media'],
            ['HandBrake','','HandBrake Team'],
            ['OBS Studio','','OBS Project'],
            ['Kdenlive','','KDE'],
            ['.NET Framework 4.8.1','','Microsoft'],
            ['.NET Desktop Runtime 8','','Microsoft'],
            ['.NET Desktop Runtime 9','','Microsoft'],
            ['.NET Desktop Runtime 10','','Microsoft'],
            ['ASP.NET Core Runtime 8','','Microsoft'],
            ['ASP.NET Core Runtime 9','','Microsoft'],
            ['VSTO Runtime','','Microsoft'],
            ['Java JRE 8 (Amazon Corretto)','','Amazon'],
            ['JDK 8 (Amazon Corretto)','','Amazon'],
            ['JDK 11 (Amazon Corretto)','','Amazon'],
            ['JDK 17 (Amazon Corretto)','','Amazon'],
            ['JDK 21 (Amazon Corretto)','','Amazon'],
            ['JDK 25 (Amazon Corretto)','','Amazon'],
            ['JDK 8 (AdoptOpenJDK)','','Eclipse Adoptium'],
            ['JDK 11 (AdoptOpenJDK)','','Eclipse Adoptium'],
            ['JDK 17 (AdoptOpenJDK)','','Eclipse Adoptium'],
            ['JDK 21 (AdoptOpenJDK)','','Eclipse Adoptium'],
            ['Java JDK','','Oracle'],
            ['Visual C++ Redistributable 2015-2022','','Microsoft'],
            ['Visual C++ Redistributable 2013','','Microsoft'],
            ['Visual C++ Redistributable 2012','','Microsoft'],
            ['Visual C++ Redistributable 2010','','Microsoft'],
            ['Visual C++ Redistributable 2008','','Microsoft'],
            ['Visual C++ Redistributable 2005','','Microsoft'],
            ['Krita','','Krita Foundation'],
            ['Blender','','Blender Foundation'],
            ['Paint.NET','','dotPDN'],
            ['GIMP','','GIMP Team'],
            ['IrfanView','','Irfan Skiljan'],
            ['XnView','','XnSoft'],
            ['Inkscape','','Inkscape'],
            ['FastStone Image Viewer','','FastStone'],
            ['Greenshot','','Greenshot'],
            ['ShareX','','ShareX Team'],
            ['Bambu Studio','','Bambu Lab'],
            ['UltiMaker Cura','','UltiMaker'],
            ['Foxit PDF Reader','','Foxit'],
            ['Adobe Acrobat Reader','','Adobe'],
            ['LibreOffice','','The Document Foundation'],
            ['Apache OpenOffice','','Apache'],
            ['SumatraPDF','','Krzysztof Kowalczyk'],
            ['CutePDF Writer','','Acro Software'],
            ['PDF24 Creator','','Geek Software'],
            ['Ghostscript','','Artifex'],
            ['Malwarebytes','','Malwarebytes'],
            ['Avast Free Antivirus','','Avast'],
            ['AVG AntiVirus Free','','AVG'],
            ['Avira','','Avira'],
            ['Spybot Search & Destroy','','Safer-Networking'],
            ['SUPERAntiSpyware','','SUPERAntiSpyware'],
            ['qBittorrent','','qBittorrent Project'],
            ['Dropbox','','Dropbox'],
            ['Google Drive for Desktop','','Google'],
            ['OneDrive','','Microsoft'],
            ['SugarSync','','SugarSync'],
            ['Evernote','','Evernote'],
            ['Google Earth Pro','','Google'],
            ['Steam','','Valve'],
            ['Epic Games Launcher','','Epic Games'],
            ['KeePass 2','','Dominik Reichl'],
            ['1Password','','AgileBits'],
            ['Bitwarden','','Bitwarden'],
            ['Everything','','voidtools'],
            ['NVDA (NV Access)','','NV Access'],
            ['ChatGPT','','OpenAI'],
            ['Claude','','Anthropic'],
            ['Grammarly','','Grammarly'],
            ['Wireshark','','Wireshark Foundation'],
            ['Microsoft PowerToys','','Microsoft'],
            ['AnyDesk','','AnyDesk Software'],
            ['TeamViewer','','TeamViewer'],
            ['Windows App (Remote Desktop)','','Microsoft'],
            ['ImgBurn','','LIGHTNING UK!'],
            ['TightVNC','','GlavSoft'],
            ['UltraVNC','','UltraVNC'],
            ['RustDesk','','RustDesk'],
            ['Tailscale','','Tailscale'],
            ['WireGuard','','WireGuard'],
            ['TeraCopy','','Code Sector'],
            ['CDBurnerXP','','Canneverbe'],
            ['Revo Uninstaller','','VS Revo Group'],
            ['Launchy','','Launchy'],
            ['WinDirStat','','WinDirStat Team'],
            ['WizTree','','Antibody Software'],
            ['Advanced IP Scanner','','Famatech'],
            ['Glary Utilities','','Glarysoft'],
            ['InfraRecorder','','InfraRecorder'],
            ['Open-Shell','','Open-Shell'],
            ['CCleaner','','Gen Digital'],
            ['7-Zip','','Igor Pavlov'],
            ['PeaZip','','Giorgio Tani'],
            ['WinRAR','','win.rar GmbH'],
            ['Python 3','','Python Software Foundation'],
            ['Git','','Git'],
            ['GitHub Desktop','','GitHub'],
            ['Docker Desktop','','Docker'],
            ['Node.js LTS','','OpenJS Foundation'],
            ['FileZilla','','FileZilla Project'],
            ['Notepad++','','Don Ho'],
            ['Sublime Text 4','','Sublime HQ'],
            ['WinSCP','','Martin Prikryl'],
            ['PuTTY','','Simon Tatham'],
            ['WinMerge','','WinMerge'],
            ['Eclipse','','Eclipse Foundation'],
            ['Visual Studio Code','','Microsoft'],
            ['Visual Studio Community','','Microsoft'],
            ['Cursor','','Anysphere'],
            ['XAMPP','','Apache Friends'],
            ['Cisco Packet Tracer','','Cisco'],
            ['Scratch','','MIT'],
    ];
    $st = $db->prepare("INSERT IGNORE INTO software_catalogo (nome, versao, fabricante) VALUES (?,?,?)");
    $n = 0;
    foreach ($seed as $x) { $st->bind_param("sss", $x[0], $x[1], $x[2]); $st->execute(); $n += $st->affected_rows; }
    return $n;
}

// ── CATÁLOGO DE PROGRAMAS BASE ───────────────────────────────
// A tabela é criada (e preenchida) automaticamente na 1.ª utilização.
$cat_erro = '';
try {
    $db->query("CREATE TABLE IF NOT EXISTS software_catalogo (
        id INT NOT NULL AUTO_INCREMENT,
        nome VARCHAR(150) NOT NULL,
        versao VARCHAR(50) DEFAULT NULL,
        fabricante VARCHAR(100) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uq_sw_cat_nome (nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Catálogo de programas base'");
    $n_cat = (int)$db->query("SELECT COUNT(*) FROM software_catalogo")->fetch_row()[0];
    if ($n_cat === 0) { sw_importar_base($db); }
} catch (mysqli_sql_exception $e) {
    $cat_erro = 'Não foi possível criar/ler o catálogo: ' . $e->getMessage();
}

function sw_json($arr) { header('Content-Type: application/json'); echo json_encode($arr); exit; }

// ── Ações do catálogo (POST) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    in_array(($_POST['acao'] ?? ''), ['cat_add_equip', 'cat_guardar', 'cat_eliminar', 'cat_importar_base'], true)) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        sw_json(['ok' => false, 'erro' => 'Token CSRF inválido.']);
    }
    try {
        $acao = $_POST['acao'];

        // Associar vários programas do catálogo ao equipamento
        if ($acao === 'cat_add_equip') {
            $ids  = array_values(array_filter(array_map('intval', (array)($_POST['cat_ids'] ?? []))));
            $dt   = !empty($_POST['data_instalacao']) ? $_POST['data_instalacao'] : null;
            $lic  = trim($_POST['licenca'] ?? '');
            $add  = 0;
            $stmt = $db->prepare("INSERT INTO software_instalado
                    (id_equipamento, nome, versao, fabricante, licenca, data_instalacao, observacoes)
                SELECT ?, c.nome, c.versao, c.fabricante, ?, ?, ''
                FROM software_catalogo c
                WHERE c.id = ?
                  AND NOT EXISTS (SELECT 1 FROM software_instalado s
                                  WHERE s.id_equipamento = ? AND s.nome = c.nome)");
            foreach ($ids as $cid) {
                $stmt->bind_param("issii", $id_equip, $lic, $dt, $cid, $id_equip);
                $stmt->execute();
                $add += $stmt->affected_rows;
            }
            sw_json(['ok' => true, 'adicionados' => $add]);
        }

        // Novo programa no catálogo
        if ($acao === 'cat_guardar') {
            $nome = trim($_POST['nome'] ?? '');
            $ver  = trim($_POST['versao'] ?? '');
            $fab  = trim($_POST['fabricante'] ?? '');
            if ($nome === '') sw_json(['ok' => false, 'erro' => 'Indique o nome.']);
            $stmt = $db->prepare("INSERT INTO software_catalogo (nome, versao, fabricante)
                                  VALUES (?,?,?)
                                  ON DUPLICATE KEY UPDATE versao=VALUES(versao), fabricante=VALUES(fabricante)");
            $stmt->bind_param("sss", $nome, $ver, $fab);
            $stmt->execute();
            sw_json(['ok' => true]);
        }

        // Importar/repor a lista base (só acrescenta o que falta)
        if ($acao === 'cat_importar_base') {
            sw_json(['ok' => true, 'novos' => sw_importar_base($db)]);
        }

        // Remover do catálogo (não afeta equipamentos já registados)
        if ($acao === 'cat_eliminar') {
            $cid  = (int)($_POST['cat_id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM software_catalogo WHERE id=?");
            $stmt->bind_param("i", $cid);
            $stmt->execute();
            sw_json(['ok' => true]);
        }
    } catch (mysqli_sql_exception $e) {
        sw_json(['ok' => false, 'erro' => $e->getMessage()]);
    }
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
        if (!empty($_POST['guardar_catalogo'])) {
            try {
                $sc = $db->prepare("INSERT IGNORE INTO software_catalogo (nome, versao, fabricante) VALUES (?,?,?)");
                $sc->bind_param("sss", $nome, $versao, $fab);
                $sc->execute();
            } catch (mysqli_sql_exception $e) { /* catálogo opcional */ }
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

$instalados = [];
$q = $db->prepare("SELECT nome FROM software_instalado WHERE id_equipamento = ?");
$q->bind_param("i", $id_equip);
$q->execute();
$rq = $q->get_result();
while ($r = $rq->fetch_assoc()) { $instalados[mb_strtolower($r['nome'])] = true; }

$catalogo = [];
if ($cat_erro === '') {
    $rc = $db->query("SELECT id, nome, versao, fabricante FROM software_catalogo ORDER BY nome ASC");
    while ($r = $rc->fetch_assoc()) { $catalogo[] = $r; }
}
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

<style>
.sw-cat-list{max-height:230px;overflow-y:auto;border:1px solid #dde3f0;border-radius:6px;padding:6px 10px;background:#fff}
.sw-cat-item{display:flex;align-items:center;gap:8px;padding:4px 2px;border-bottom:1px solid #f0f3fa;font-size:.9rem;margin:0;cursor:pointer}
.sw-cat-item:last-child{border-bottom:0}
.sw-cat-item small{color:#7b88a0}
.sw-cat-item.ja{opacity:.55;cursor:default}
.sw-cat-item .sw-cat-x{margin-left:auto;background:none;border:0;color:#dc3545;cursor:pointer}
.sw-cat-tag{font-size:.7rem;background:#e8f5e9;color:#2e7d32;border-radius:10px;padding:1px 8px}
</style>

<?php if ($cat_erro !== ''): ?>
<div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px;padding:10px 14px;margin-bottom:10px;">
    <strong>Catálogo indisponível:</strong> <?php echo htmlspecialchars($cat_erro, ENT_QUOTES, 'UTF-8'); ?>
</div>
<?php else: ?>
<div class="sw-form-card">
    <div class="sw-form-title">
        <i class="fas fa-list-check" style="color:#4b6cb7;"></i>
        Escolher do catálogo de programas
        <span class="sw-counter"><?php echo count($catalogo); ?> no catálogo</span>
    </div>
    <form id="sw-cat-form" onsubmit="return false;">
        <input type="hidden" name="acao" value="cat_add_equip">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

        <input type="text" id="sw-cat-filtro" class="form-control" placeholder="Pesquisar no catálogo..." style="margin-bottom:8px;">
        <div class="sw-cat-list" id="sw-cat-list">
        <?php if (!$catalogo): ?>
            <div class="sw-empty" style="padding:10px;">Catálogo vazio. Adicione programas em “Gerir catálogo”.</div>
        <?php endif; ?>
        <?php foreach ($catalogo as $c):
            $ja = isset($instalados[mb_strtolower($c['nome'])]); ?>
            <label class="sw-cat-item<?php echo $ja ? ' ja' : ''; ?>" data-nome="<?php echo htmlspecialchars(mb_strtolower($c['nome'] . ' ' . $c['fabricante']), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="checkbox" name="cat_ids[]" value="<?php echo (int)$c['id']; ?>" <?php echo $ja ? 'disabled checked' : ''; ?>>
                <span><?php echo htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php if ($c['versao'] !== '' && $c['versao'] !== null): ?><small>v<?php echo htmlspecialchars($c['versao'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?>
                    <?php if ($c['fabricante'] !== '' && $c['fabricante'] !== null): ?><small>— <?php echo htmlspecialchars($c['fabricante'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?>
                </span>
                <?php if ($ja): ?><span class="sw-cat-tag">instalado</span><?php endif; ?>
            </label>
        <?php endforeach; ?>
        </div>

        <div class="form-row" style="margin-top:10px;">
            <div class="form-group col-12 col-md-4">
                <label class="sw-label" for="swc_dt">Data de instalação (opcional):</label>
                <input type="date" name="data_instalacao" id="swc_dt" class="form-control">
            </div>
            <div class="form-group col-12 col-md-5">
                <label class="sw-label" for="swc_lic">Licença (opcional, igual para todos):</label>
                <input type="text" name="licenca" id="swc_lic" class="form-control" placeholder="ex: OEM / Volume">
            </div>
            <div class="form-group col-12 col-md-3" style="display:flex;align-items:flex-end;">
                <button type="button" id="sw-cat-btn" class="btn btn-custom" style="width:100%;">
                    <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> Adicionar selecionados
                </button>
            </div>
        </div>
    </form>

    <details style="margin-top:6px;">
        <summary style="cursor:pointer;font-weight:600;color:#4b6cb7;font-size:.85rem;">
            <i class="fas fa-gear"></i> Gerir catálogo
        </summary>
        <div style="margin-top:8px;">
            <form id="sw-catnew-form" onsubmit="return false;" class="form-row">
                <input type="hidden" name="acao" value="cat_guardar">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group col-12 col-md-4"><input type="text" name="nome" class="form-control" placeholder="Nome do programa *"></div>
                <div class="form-group col-6 col-md-2"><input type="text" name="versao" class="form-control" placeholder="Versão"></div>
                <div class="form-group col-6 col-md-3"><input type="text" name="fabricante" class="form-control" placeholder="Fabricante"></div>
                <div class="form-group col-12 col-md-3"><button type="button" id="sw-catnew-btn" class="btn btn-custom" style="width:100%;">Guardar no catálogo</button></div>
            </form>
            <div class="sw-cat-list" style="max-height:160px;">
            <?php foreach ($catalogo as $c): ?>
                <div class="sw-cat-item">
                    <span><?php echo htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="sw-cat-x" data-id="<?php echo (int)$c['id']; ?>"
                            data-nome="<?php echo htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                            title="Remover do catálogo"><i class="fas fa-trash-alt"></i></button>
                </div>
            <?php endforeach; ?>
            </div>
            <div style="margin:8px 0;">
                <button type="button" id="sw-cat-import" class="btn btn-custom" style="padding:4px 12px;font-size:.8rem;">
                    <i class="fas fa-cloud-arrow-down"></i> Importar lista base (Ninite)
                </button>
                <small style="color:#7b88a0;">Acrescenta os programas em falta; não altera nem duplica os existentes.</small>
            </div>
            <small style="color:#7b88a0;">Remover do catálogo não retira o programa dos equipamentos onde já está registado.</small>
        </div>
    </details>
</div>
<?php endif; ?>

<div class="sw-form-card">
    <div class="sw-form-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Adicionar programa manualmente
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
        <div style="margin-top:4px;">
            <label style="font-size:.85rem;cursor:pointer;">
                <input type="checkbox" name="guardar_catalogo" value="1"> Guardar também no catálogo de programas base
            </label>
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


    // ── Catálogo ──
    function swPost(form, cb) {
        fetch(window.SW_AJAX_URL + '?id=' + idEquip, { method: 'POST', body: new FormData(form) })
            .then(function(r){ return r.json(); })
            .then(function(d){ if (d.ok) { cb(d); } else { alert('Erro: ' + (d.erro || 'desconhecido')); } })
            .catch(function(e){ console.error(e); alert('Erro de comunicação com o servidor.'); });
    }
    var filtro = document.getElementById('sw-cat-filtro');
    if (filtro) {
        filtro.addEventListener('input', function() {
            var t = filtro.value.toLowerCase().trim();
            document.querySelectorAll('#sw-cat-list .sw-cat-item').forEach(function(it) {
                it.style.display = (it.dataset.nome || '').indexOf(t) !== -1 ? '' : 'none';
            });
        });
    }
    var catBtn = document.getElementById('sw-cat-btn');
    if (catBtn) {
        catBtn.addEventListener('click', function() {
            var form = document.getElementById('sw-cat-form');
            if (!form.querySelector('input[name="cat_ids[]"]:checked:not(:disabled)')) {
                alert('Selecione pelo menos um programa.'); return;
            }
            swPost(form, function(d) { swReload(); });
        });
    }
    var impBtn = document.getElementById('sw-cat-import');
    if (impBtn) {
        impBtn.addEventListener('click', function() {
            var fd = new FormData();
            fd.append('acao', 'cat_importar_base');
            fd.append('csrf_token', '<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>');
            fetch(window.SW_AJAX_URL + '?id=' + idEquip, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){ if (d.ok) { alert(d.novos + ' programa(s) novo(s) adicionado(s) ao catálogo.'); swReload(); } else alert('Erro: ' + d.erro); });
        });
    }
    var newBtn = document.getElementById('sw-catnew-btn');
    if (newBtn) {
        newBtn.addEventListener('click', function() {
            var form = document.getElementById('sw-catnew-form');
            if (!form.nome.value.trim()) { alert('Indique o nome do programa.'); return; }
            swPost(form, function() { swReload(); });
        });
    }
    document.querySelectorAll('.sw-cat-x').forEach(function(b) {
        b.addEventListener('click', function() {
            if (!confirm("Remover '" + b.dataset.nome + "' do catálogo?")) return;
            var fd = new FormData();
            fd.append('acao', 'cat_eliminar');
            fd.append('cat_id', b.dataset.id);
            fd.append('csrf_token', '<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>');
            fetch(window.SW_AJAX_URL + '?id=' + idEquip, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){ if (d.ok) swReload(); else alert('Erro: ' + d.erro); });
        });
    });

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
