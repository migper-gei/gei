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

// [CORRIGIDO] Token CSRF único por sessão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// [CORRIGIDO] Validar CSRF em qualquer POST antes de usar os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die('Pedido inválido (token CSRF incorreto).');
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>

     <?php include("sessao_timeout.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Tarefas a realizar</li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
</div>

<script language="javascript" type="text/javascript">
function showesc(escola) {
    document.frm.submit();
}
</script>

<div style="text-align:center;">
    <!-- [CORRIGIDO] Form de navegação para resumo por instituição com token CSRF -->
    <form action="<?php echo SVRURL ?>tarescinst" method="post" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
        <button title="Ver nº de tarefas a realizar por escola" type="submit" class="btn btn-outline-primary">
            Ver nº de tarefas a realizar por instituição
        </button>
    </form>
</div>

<br>

<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>

<!-- [CORRIGIDO] Form de seleção de escola com token CSRF -->
<form name="frm" id="frm" action="" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

<div style="text-align:left;">

<?php
$x = (int)base64_decode($_GET["x"] ?? '');
$z = (int)base64_decode($_GET["z"] ?? '');

?>

<select title="Escolha a instituição" style="width:100%;" class="custom-select" name="escola" onChange="showesc(this.value);">

<?php

if (
    !isset($_GET["x"]) || $_GET["x"] === '' || !is_numeric($x) ||
    !isset($_GET["z"]) || $_GET["z"] === '' || !is_numeric($z)
) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}

$stmt2 = $db->prepare("SELECT * FROM escolas ORDER BY id");
$stmt2->execute();
$result2 = $stmt2->get_result();
$stmt2->close();

while ($row2 = mysqli_fetch_array($result2)) {

    if ($x == 0) {
        if ($row2['id'] == ($_REQUEST["escola"] ?? null)) {
            echo('<option selected value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        } else {
            echo('<option value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        }
    }

    if ($x == 1 && !is_numeric($_GET["esi"] ?? '') && !is_numeric($_REQUEST["escola"] ?? '')) {
        if ($row2['id'] == (int)base64_decode($_GET["esi"] ?? '')) {
            echo('<option selected value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        } else {
            echo('<option value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        }
    }

    if ($x == 1 && !is_numeric($_GET["esi"] ?? '') && is_numeric($_REQUEST["escola"] ?? '')) {
        if ($row2['id'] == ($_REQUEST["escola"] ?? null)) {
            echo('<option selected value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        } else {
            echo('<option value="' . $row2['id'] . '">' . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8') . '</option>');
        }
    }
}

echo('</select>');
?>
</div>
</form>

<?php

$stmt4 = $db->prepare("SELECT id FROM escolas LIMIT 1");
$stmt4->execute();
$result4 = $stmt4->get_result();
$rows4   = $result4->fetch_row();
$stmt4->close();
$nes = $rows4[0];

if (!empty($_POST["escola"])) {
    $esc = (int)$_POST["escola"];
} elseif ($x == 0) {
    $esc = (int)$nes;
} elseif ($x == 1 && (!empty($_GET["esi"]) || isset($_GET["esi"]))) {
    $esc = (int)base64_decode($_GET['esi']);
}

if ($x == 1) {
    if (empty($esc) || !isset($esc) || !is_numeric($esc)) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
    }
}

$stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt11->bind_param("i", $esc);
$stmt11->execute();
$result11 = $stmt11->get_result();
$rows11   = $result11->fetch_row();
$conta    = $result11->num_rows;
$stmt11->close();

if ($conta > 0) {
    $ne = $rows11[0];
} else {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}

$stmt1 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt1->bind_param("i", $esc);
$stmt1->execute();
$result1 = $stmt1->get_result();
$rows     = $result1->fetch_row();
$stmt1->close();
$ne = $rows[0];
?>

<div class="text-center mt-3">
    <span class="badge badge-primary p-2" style="font-size:1rem;">
        <i class="fas fa-building btn-icon"></i> <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
    </span>
</div>
</div>

<?php
// [CORRIGIDO] Validar e guardar records-limit com whitelist (CSRF já validado acima)
if (isset($_POST['records-limit'])) {
    $allowed_limits  = [5, 10, 20, 30, 50, 100];
    $posted_limit    = (int)$_POST['records-limit'];
    if (in_array($posted_limit, $allowed_limits, true)) {
        $_SESSION['records-limit'] = $posted_limit;
    }
}

$limit           = isset($_SESSION['records-limit']) ? (int)$_SESSION['records-limit'] : 10;
$page            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

if ($z == 0) {
    $stmt = $db->prepare("SELECT * FROM tarefas WHERE id_escola = ? AND data_conclusao IS NULL ORDER BY data_criacao DESC, data_conclusao LIMIT ?, ?");
    $stmt->bind_param("iii", $esc, $paginationStart, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
}

if (($x == 0 || $x == 1) && ($z != 0)) {
    $stmt = $db->prepare("SELECT * FROM tarefas WHERE id_escola = ? ORDER BY data_criacao DESC, data_conclusao LIMIT ?, ?");
    $stmt->bind_param("iii", $esc, $paginationStart, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($x == 0 || $x == 1) {
    $stmt1b = $db->prepare("SELECT COUNT(*) AS cs FROM tarefas WHERE id_escola = ?");
    $stmt1b->bind_param("i", $esc);
    $stmt1b->execute();
    $result1b   = $stmt1b->get_result();
    $rows        = $result1b->fetch_row();
    $stmt1b->close();
    $totallinhas = $rows[0];
}

$totoalPages = $totallinhas > 0 ? ceil($totallinhas / $limit) : 1;
$prev = $page - 1;
$next = $page + 1;
?>

<script>
function a(id, ns, esc, idesc) {
    var id1 = id, ns1 = ns, esc1 = esc, idesc1 = idesc;
    event.preventDefault();
    swal({
        title: "Deseja eliminar?",
        text: "Tarefa da sala " + ns1 + " da " + esc1,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            window.setTimeout(function() {
                window.location.href = '<?php echo SVRURL ?>eliminatarefa/' + id1 + '/' + idesc1;
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<style>
.gei-table-wrap {
    background:#fff; border-radius:10px;
    box-shadow:0 2px 12px rgba(75,108,183,.10);
    border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px;
}
.gei-table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px; padding:12px 16px;
    background:#f4f6fb; border-bottom:1px solid #e3e8f4;
}
.gei-table-toolbar-left  { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th {
    padding:10px 14px; background:#182848; color:#fff;
    font-size:.75rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.5px; border:none; white-space:nowrap;
}
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background:#eef2fb; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-insert-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:7px; font-size:.8rem; font-weight:600;
    background:#1cc88a !important; color:#fff !important;
    text-decoration:none !important; border:none;
    box-shadow:0 2px 8px rgba(28,200,138,.25); transition:opacity .15s,transform .12s;
}
.gei-insert-btn:hover { opacity:.88; transform:translateY(-1px); }
.gei-action-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:opacity .15s,transform .12s; white-space:nowrap;
}
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-locked { background:#f4f6fb; color:#aab0bb !important; border:1.5px solid #dde2ec; cursor:not-allowed; opacity:.7; }
.gei-pagination {
    display:flex; align-items:center; justify-content:center;
    gap:4px; flex-wrap:wrap; padding:12px 0;
}
.gei-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:32px; height:32px; padding:0 10px; border-radius:6px;
    font-size:.8rem; font-weight:600; text-decoration:none !important;
    border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff;
    transition:all .15s;
}
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
@media (max-width: 768px) {
    .gei-table-wrap { border-radius:8px; }
    .gei-table thead { display:none; }
    .gei-table tbody tr {
        display:block; border:1px solid #e3e8f4; border-radius:8px;
        margin-bottom:10px; padding:10px 12px; background:#fff;
        box-shadow:0 1px 6px rgba(75,108,183,.08);
    }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table tbody tr:hover { background:#f5f8ff; }
    .gei-table td {
        display:flex; align-items:flex-start; gap:8px;
        padding:5px 2px; border:none; font-size:.83rem;
    }
    .gei-table td::before {
        content: attr(data-label); min-width:110px;
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0;
    }
    .gei-table td[data-label="Ações"] {
        flex-wrap:wrap; gap:6px; padding-top:8px;
        border-top:1px dashed #e3e8f4; margin-top:4px;
    }
    .gei-table td[data-label="Ações"]::before { display:none; }
}
</style>

<!-- Barra info + exportar -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 0;margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:7px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e87722" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span style="font-size:.8rem;color:#7d4e00;font-weight:500;">Só é possível atualizar e eliminar tarefas não concluídas.</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="<?php echo SVRURL ?>tarefas_pdf.php?id=<?php echo base64_encode($esc); ?>&z=<?php echo base64_encode($z); ?>" target="_blank" title="Exportar para PDF"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#c0392b !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(192,57,43,0.25);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/><line x1="9" y1="9" x2="10" y2="9"/>
            </svg>
            Exportar PDF
        </a>
    </div>
</div>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <!-- [CORRIGIDO] Token CSRF no form de paginação -->
            <form action="" method="post" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="escola" value="<?php echo (int)$esc; ?>">
                <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            </form>
            <!-- Filtro tarefas concluídas -->
            <div style="display:flex;align-items:center;gap:6px;">
                <a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1); ?>&amp;esi=<?php echo base64_encode($esc); ?>&amp;z=<?php echo base64_encode(0); ?>&amp;page=1"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:.78rem;font-weight:600;text-decoration:none !important;border:1.5px solid <?php echo ($z==0) ? '#182848' : '#c7d4f0'; ?>;background:<?php echo ($z==0) ? '#182848' : '#fff'; ?>;color:<?php echo ($z==0) ? '#fff' : '#4b6cb7'; ?> !important;transition:all .15s;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Não concluídas
                </a>
                <a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1); ?>&amp;esi=<?php echo base64_encode($esc); ?>&amp;z=<?php echo base64_encode(1); ?>&amp;page=1"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:.78rem;font-weight:600;text-decoration:none !important;border:1.5px solid <?php echo ($z!=0) ? '#1cc88a' : '#c7d4f0'; ?>;background:<?php echo ($z!=0) ? '#1cc88a' : '#fff'; ?>;color:<?php echo ($z!=0) ? '#fff' : '#4b6cb7'; ?> !important;transition:all .15s;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Todas
                </a>
            </div>
        </div>
        <?php if ($_SESSION['tipo']==1): ?>
        <div class="gei-table-toolbar-right">
            <a href="<?php echo SVRURL ?>inserir_tarefa.php?ti=<?php echo base64_encode($esc) ?>" class="gei-insert-btn" title="Inserir tarefa">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova tarefa
            </a>
        </div>
        <?php endif; ?>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th style="width:14%">Sala</th>
                <th>Descrição</th>
                <th style="width:10%">Urgência</th>
                <th style="width:16%">Criado por / Data</th>
                <th style="width:16%">Concluído por / Data</th>
                <?php if ($_SESSION['tipo']==1): ?>
                <th style="text-align:center;width:18%">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()):
            $n = $row['id_sala'];
            $stmt_sala = $db->prepare("SELECT nome FROM salas WHERE id = ?");
            $stmt_sala->bind_param("i", $n);
            $stmt_sala->execute();
            $result_sala = $stmt_sala->get_result();
            $rows2       = $result_sala->fetch_row();
            $nomesala    = $rows2[0];
            $stmt_sala->close();

            $concluida = ($row['data_conclusao'] != "");
        ?>
        <tr>
            <td data-label="Sala" style="font-weight:600;"><?php echo htmlspecialchars($nomesala, ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Descrição"><?php echo htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Urgência">
                <?php
                $urg = htmlspecialchars($row['urgencia'], ENT_QUOTES, 'UTF-8');
                $_urg_lower = strtolower($urg);
                if ($_urg_lower === 'alta') {
                    $urgColor = ['#fde8e6','#c0392b','#f5c0bb'];
                } elseif ($_urg_lower === 'média' || $_urg_lower === 'media') {
                    $urgColor = ['#fff3cd','#7d4e00','#f0c040'];
                } else {
                    $urgColor = ['#e8f0fe','#4b6cb7','#c7d4f0'];
                }
                ?>
                <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:<?= $urgColor[0] ?>;color:<?= $urgColor[1] ?>;border:1.5px solid <?= $urgColor[2] ?>;">
                    <?= $urg ?>
                </span>
            </td>
            <td data-label="Criado por" style="font-size:.82rem;">
                <span style="font-weight:600;"><?php echo htmlspecialchars($row['criado_por'], ENT_QUOTES, 'UTF-8'); ?></span>
                <br><span style="color:#7b88a0;font-family:monospace;"><?php echo date('d/m/Y', strtotime($row['data_criacao'])); ?></span>
            </td>
            <td data-label="Concluído por" style="font-size:.82rem;">
                <?php if ($concluida): ?>
                <span style="font-weight:600;"><?php echo htmlspecialchars($row['concluido_por'], ENT_QUOTES, 'UTF-8'); ?></span>
                <br><span style="color:#7b88a0;font-family:monospace;"><?php echo date('d/m/Y', strtotime($row['data_conclusao'])); ?></span>
                <?php else: ?>
                <span style="color:#aab0bb;font-size:.75rem;">—</span>
                <?php endif; ?>
            </td>
            <?php if ($_SESSION['tipo']==1): ?>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                <?php if (!$concluida): ?>
                    <?php
                    $stmt2b = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
                    $stmt2b->bind_param("i", $esc);
                    $stmt2b->execute();
                    $result2b = $stmt2b->get_result();
                    $rows2b   = $result2b->fetch_row();
                    $noesc    = $rows2b[0];
                    $stmt2b->close();
                    ?>
                    <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                       href="<?php echo SVRURL ?>atualtarefa/<?php echo base64_encode($row['id']); ?>/<?php echo base64_encode($esc); ?>">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar
                    </a>
                    <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                       onclick="a('<?php echo htmlspecialchars($row['id'],ENT_QUOTES,'UTF-8'); ?>','<?php echo htmlspecialchars($nomesala,ENT_QUOTES,'UTF-8'); ?>','<?php echo htmlspecialchars($noesc,ENT_QUOTES,'UTF-8'); ?>','<?php echo (int)$esc; ?>');"
                       href="<?php echo SVRURL ?>eliminatarefa">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                        Eliminar
                    </a>
                <?php else: ?>
                    <span class="gei-action-btn gei-btn-locked" title="Tarefa concluída">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Concluída
                    </span>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>

<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : '?x='.base64_encode(1).'&&esi='.base64_encode($esc).'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>taref?x=<?php echo base64_encode(1); ?>&&esi=<?php echo base64_encode($esc); ?>&&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?x='.base64_encode(1).'&&esi='.base64_encode($esc).'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>

<a href="<?php echo SVRURL ?>configura">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br>

        <?php include ("jquery_bootstrap.php"); ?>

<br>

                    </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
