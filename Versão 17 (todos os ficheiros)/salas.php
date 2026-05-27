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
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}
// Gerar token CSRF se ainda não existir (fora do if, para cobrir sessões já iniciadas)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php
 include ("head.php");
?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php");?>

     <?php
include("sessao_timeout.php");
?>

   <?php

$stmt2a = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt2a->execute();
$result2a = $stmt2a->get_result();
$rows2a = $result2a->fetch_row();
$stmt2a->close();
$maxesc = $rows2a[0];

if (!isset($_GET["x"]) || base64_decode($_GET["x"]) === false) {
    $x = 0;
} else {
    $x = (int)base64_decode($_GET["x"]);
}

if ($x > 1 || $x < 0) {
?>
<script>
window.setTimeout(function() {
   window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}
?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">

               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Salas</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
<?php
include("msg_bemvindo.php");
?>
</div>


<script language="javascript" type="text/javascript">
function showesc(escola) {
    document.frm.submit();
}
</script>

<div style="text-align:center;">
<form action="<?php echo SVRURL ?>salasnum" method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<button title="Ver nº de salas por escola" type="submit" class="btn btn-outline-primary">Ver nº de salas por instituição</button>
</form>
</div>

<br>

<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>

<br>

<form name="frm" id="frm" action="" method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<div style="text-align:left;">

<?php
if (!isset($_GET['esi']) && $x==1) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}
?>

<select style="width:100%;" class="custom-select" name="escola" onChange="showesc(this.value);">

<?php
$stmt2 = $db->prepare("SELECT * FROM escolas ORDER BY id");
$stmt2->execute();
$result2 = $stmt2->get_result();
$stmt2->close();

while($row2 = mysqli_fetch_array($result2)) {
    if ($x==0) {
        if ($row2['id']==($_REQUEST["escola"])) {
            echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
        } else {
            echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
        }
    }

    if ($x==1 && !is_numeric($_GET["esi"]) && !is_numeric($_REQUEST["escola"])) {
        if ($row2['id']== (int)base64_decode($_GET["esi"])) {
            echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
        } else {
            echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
        }
    }

    if ($x==1 && !is_numeric($_GET["esi"]) && is_numeric($_REQUEST["escola"])) {
        if ($row2['id']==($_REQUEST["escola"])) {
            echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
        } else {
            echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
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
$rows4 = $result4->fetch_row();
$stmt4->close();
$nes = $rows4[0];

if (!empty($_POST["escola"]) && is_numeric($x)) {
    $esc = $_POST["escola"];
} elseif ($x==0) {
    $esc = $nes;
} elseif ($x==1) {
    $esc = (int)base64_decode($_GET['esi']);
}

if (!is_numeric($esc)) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>configura';
}, 10);
</script>
<?php
}
?>

<?php
$stmt1 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt1->bind_param("i", $esc);
$stmt1->execute();
$result1 = $stmt1->get_result();
$rows = $result1->fetch_row();
$stmt1->close();
$ne = $rows[0];
?>

<div class="text-center mt-3">
    <span class="badge badge-primary p-2" style="font-size:1rem;">
        <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
    </span>
</div>
</div>

<br>

<?php
// ── Filtros — inicializar ANTES dos botões de exportação ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
?>
<script>
swal({
    title: 'Ação não autorizada!',
    text: 'Token de segurança inválido ou expirado.',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>configura";
});
</script>
<?php
        exit;
    }
}
if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}
$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

$filtro_loc = isset($_POST['filtro_loc']) ? trim($_POST['filtro_loc']) : (isset($_GET['filtro_loc']) ? trim($_GET['filtro_loc']) : '');
$filtro_req = isset($_POST['filtro_req']) ? trim($_POST['filtro_req']) : (isset($_GET['filtro_req']) ? trim($_GET['filtro_req']) : '');
if (isset($_POST['filtro_loc']) || isset($_POST['filtro_req'])) { $page = 1; }

$paginationStart = ($page - 1) * $limit;

// Localizações distintas para o dropdown
$stmt_locs = $db->prepare("SELECT DISTINCT localizacao FROM salas WHERE id_escola = ? AND localizacao IS NOT NULL AND localizacao <> '' ORDER BY localizacao");
$stmt_locs->bind_param("i", $esc);
$stmt_locs->execute();
$result_locs = $stmt_locs->get_result();
$stmt_locs->close();
$locs_list = [];
while ($r = $result_locs->fetch_row()) { $locs_list[] = $r[0]; }

// Construir WHERE com filtros
$where_parts = ["s.id_escola = e.id", "e.id = ?"];
$bind_types  = "i";
$bind_values = [$esc];

if ($filtro_loc !== '') {
    $where_parts[] = "s.localizacao = ?";
    $bind_types .= "s";
    $bind_values[] = $filtro_loc;
}
if ($filtro_req !== '') {
    $where_parts[] = "s.equip_requisitavel = ?";
    $bind_types .= "s";
    $bind_values[] = $filtro_req;
}
$where_sql = implode(" AND ", $where_parts);

// Query principal paginada
$stmtz = $db->prepare("SELECT s.id AS idsala, s.* FROM salas s, escolas e WHERE $where_sql ORDER BY s.nome LIMIT ?, ?");
$bind_types_pag = $bind_types . "ii";
$bind_values_pag = array_merge($bind_values, [$paginationStart, $limit]);
$stmtz->bind_param($bind_types_pag, ...$bind_values_pag);
$stmtz->execute();
$resultz = $stmtz->get_result();
$stmtz->close();

// Count total
$stmt1b = $db->prepare("SELECT COUNT(*) AS cs FROM salas s, escolas e WHERE $where_sql");
$stmt1b->bind_param($bind_types, ...$bind_values);
$stmt1b->execute();
$result1b = $stmt1b->get_result();
$rows = $result1b->fetch_row();
$stmt1b->close();
$totallinhas = $rows[0];

$totoalPages = ceil($totallinhas / $limit);
$prev = $page - 1;
$next = $page + 1;

// Query string para exportação com filtros ativos
$export_qs = '';
if ($filtro_loc !== '') $export_qs .= '&filtro_loc=' . urlencode($filtro_loc);
if ($filtro_req !== '') $export_qs .= '&filtro_req=' . urlencode($filtro_req);
?>

<?php
if ($_SESSION['tipo']==1) {
    $stmt3 = $db->prepare("SELECT COUNT(*) AS conta, id, nome FROM salas WHERE id_escola = ? AND id NOT IN (SELECT s.id FROM equipamento e, salas s WHERE s.id = e.id_sala AND s.id_escola = ?) GROUP BY id, nome");
    $stmt3->bind_param("ii", $esc, $esc);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $rows3 = $result3->fetch_row();
    $stmt3->close();
    $contasalas = $rows3[0];
?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 0;margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:7px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e87722" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span style="font-size:.8rem;color:#7d4e00;font-weight:500;">Só é possível eliminar salas sem equipamento associado e sem requisições.</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="<?php echo SVRURL ?>salas_csv.php?id=<?php echo base64_encode($esc) . $export_qs; ?>" target="_blank" title="Exportar para CSV"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
            </svg>
            Exportar CSV
        </a>
        <a href="<?php echo SVRURL ?>salas_pdf.php?id=<?php echo base64_encode($esc) . $export_qs; ?>" target="_blank" title="Exportar para PDF"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#c0392b !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(192,57,43,0.20);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 8 9 8 7"/>
            </svg>
            Exportar PDF
        </a>
        <?php if ($contasalas > 0): ?>
        <a onclick="a1('<?php echo (int)$esc;?>','<?php echo htmlspecialchars($ne,ENT_QUOTES,'UTF-8');?>')"
           href="<?php echo SVRURL ?>elimina_salas_semequi.php?id=<?php echo base64_encode($esc);?>" target="_blank"
           title="Eliminar todas as salas sem equipamento"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#e74a3b !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(231,74,59,0.20);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
            </svg>
            Eliminar todas
        </a>
        <?php endif; ?>
    </div>
</div>
<br>
<?php
}
?>

<script>
function a1(ne,es) {
    var es1 = es, ne1 = ne;
    event.preventDefault();
    swal({
        title: "Deseja eliminar todas as salas sem equipamento?",
        text: "Instituição: " + es1 + " ",
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
                window.location.href = '<?php echo SVRURL ?>eliminasalasemequi/' + ne1;
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}

function a(n,s,es,ne) {
    var n1=n, s1=s, es1=es, ne1=ne;
    event.preventDefault();
    swal({
        title: "Deseja eliminar?",
        text: "Sala: " + s1 + " (Instituição: " + ne1 + ")",
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
                window.location.href = '<?php echo SVRURL ?>eliminasala/' + n1 + '/' + es1;
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
    box-shadow:0 2px 8px rgba(28,200,138,.25);
    transition:opacity .15s, transform .12s;
}
.gei-insert-btn:hover { opacity:.88; transform:translateY(-1px); }
.gei-action-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:opacity .15s, transform .12s; white-space:nowrap;
}
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-locked { background:#f4f6fb; color:#aab0bb !important; border:1.5px solid #dde2ec; cursor:not-allowed; opacity:.7; }
.gei-badge-sim  { display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#e0f7f0;color:#059669;border:1.5px solid #059669; }
.gei-badge-nao  { display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#f4f6fb;color:#7b88a0;border:1.5px solid #dde2ec; }
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
        content: attr(data-label); min-width:120px;
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

<div class="gei-table-wrap">
    <div class="gei-table-toolbar" style="flex-wrap:wrap;gap:10px;">
        <form method="post" action="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($esc);?>" style="display:contents;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="gei-table-toolbar-left" style="flex-wrap:wrap;gap:10px;flex:1;">
            <!-- Linhas por página -->
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;white-space:nowrap;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <!-- Filtro Localização -->
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;white-space:nowrap;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Localização:
                <select name="filtro_loc" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <option value="">Todas</option>
                    <?php foreach ($locs_list as $loc): ?>
                    <option value="<?php echo htmlspecialchars($loc, ENT_QUOTES, 'UTF-8'); ?>"
                        <?php if ($filtro_loc === $loc) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($loc, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <!-- Filtro Equip. Requisitável -->
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;white-space:nowrap;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
                Equip. Requisitável:
                <select name="filtro_req" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <option value="" <?= ($filtro_req=='') ? 'selected' : '' ?>>Todos</option>
                    <option value="Sim" <?= ($filtro_req=='Sim') ? 'selected' : '' ?>>Sim</option>
                    <option value="Não" <?= ($filtro_req=='Não') ? 'selected' : '' ?>>Não</option>
                </select>
            </label>
            <!-- Botão aplicar / limpar -->
            <button type="submit" title="Aplicar filtros"
                style="padding:5px 12px;border-radius:7px;border:1.5px solid #4b6cb7;background:#4b6cb7;color:#fff;font-size:.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                Filtrar
            </button>
            <?php if ($filtro_loc !== '' || $filtro_req !== ''): ?>
            <a href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($esc);?>" title="Limpar filtros"
                style="padding:5px 12px;border-radius:7px;border:1.5px solid #c0392b;background:#fde8e6;color:#c0392b;font-size:.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Limpar
            </a>
            <?php endif; ?>
        </div>
        <?php if ($_SESSION['tipo']==1): ?>
        <div class="gei-table-toolbar-right">
            <a href="<?php echo SVRURL ?>inserirsala?ie=<?php echo base64_encode($esc) ?>" class="gei-insert-btn" title="Inserir sala">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova sala
            </a>
        </div>
        <?php endif; ?>
        </form>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Localização</th>
                <th>Departamento / Grupo / Serviço</th>
                <th style="text-align:center;">Equip. Requisitável?</th>
                <?php if ($_SESSION['tipo']==1): ?>
                <th style="text-align:center;width:18%">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_array($resultz)):
            $n  = $row['idsala'];
            $sa = $row['nome'];

            $stmt_ceq = $db->prepare("SELECT COUNT(*) FROM equipamento, salas WHERE salas.id = equipamento.id_sala AND salas.id = ? AND salas.id_escola = ?");
            $stmt_ceq->bind_param("ii", $n, $esc);
            $stmt_ceq->execute();
            $result_ceq = $stmt_ceq->get_result();
            $rows2 = $result_ceq->fetch_row();
            $contasala = $rows2[0];
            $stmt_ceq->close();

            $stmt_coe = $db->prepare("SELECT COUNT(*) FROM outro_equipamento, salas WHERE salas.id = outro_equipamento.id_sala AND salas.id = ?");
            $stmt_coe->bind_param("i", $n);
            $stmt_coe->execute();
            $result_coe = $stmt_coe->get_result();
            $rows2a = $result_coe->fetch_row();
            $contasalaoe = $rows2a[0];
            $stmt_coe->close();

            $req = htmlspecialchars($row['equip_requisitavel'], ENT_QUOTES, 'UTF-8');
        ?>
        <tr>
            <td data-label="Nome" style="font-weight:600;"><?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Localização"><?php echo htmlspecialchars($row['localizacao'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Departamento" style="color:#5a6370;"><?php echo htmlspecialchars($row['departamento'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Equip. Requisitável" style="text-align:center;">
                <?php if (strtolower($req)=='sim'): ?>
                <span class="gei-badge-sim"><?php echo $req; ?></span>
                <?php else: ?>
                <span class="gei-badge-nao"><?php echo $req; ?></span>
                <?php endif; ?>
            </td>
            <?php if ($_SESSION['tipo']==1): ?>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                   href="<?php echo SVRURL ?>atualizasala/<?php echo base64_encode($n); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                </a>
                <?php if ($contasala==0 && $contasalaoe==0): ?>
                <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                   onclick="a(<?php echo (int)$n;?>,'<?php echo htmlspecialchars($sa,ENT_QUOTES,'UTF-8');?>','<?php echo (int)$esc;?>','<?php echo htmlspecialchars($ne,ENT_QUOTES,'UTF-8');?>');"
                   href="<?php echo SVRURL ?>eliminasala">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Eliminar
                </a>
                <?php else: ?>
                <span class="gei-action-btn gei-btn-locked" title="Com equipamento associado">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Bloqueado
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

<?php
$filtro_qs = '';
if ($filtro_loc !== '') $filtro_qs .= '&&filtro_loc='.urlencode($filtro_loc);
if ($filtro_req !== '') $filtro_qs .= '&&filtro_req='.urlencode($filtro_req);
$base_url = SVRURL.'sala?x='.base64_encode(1).'&&esi='.base64_encode($esc).$filtro_qs;
?>
<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : $base_url.'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo $base_url.'&&page='.$i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : $base_url.'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>

<a href="<?php echo SVRURL ?>configura">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br>

        <?php include ("jquery_bootstrap.php");?>

<br>

                    </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php");?>

   </body>
</html>
