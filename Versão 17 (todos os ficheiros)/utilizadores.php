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

// ── Gerar tokens CSRF de eliminação (um por utilizador, array indexado por ID) ──
// Inicializar array se ainda não existir na sessão
if (!isset($_SESSION['csrf_delete_tokens']) || !is_array($_SESSION['csrf_delete_tokens'])) {
    $_SESSION['csrf_delete_tokens'] = [];
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
//session_start();



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
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
                     <li style="color:#1e2a45;">Utilizadores</li>
                  </ol>
               </nav>
               <div class="titlepage">
                    
                  </div>
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



<!--
                  <img src="images/informacao.svg" alt="Informação">
  Tipo: 1 - Administrador     &nbsp;&nbsp;&nbsp; &nbsp;    2 - Utilizador
&nbsp; &nbsp; &nbsp; &nbsp; 3 - Reparador
&nbsp; &nbsp; &nbsp; &nbsp; 
-->



<?php if ($_SESSION['tipo']==1): ?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 0;margin-bottom:8px;">

    <!-- Badges de tipo (esquerda) -->
    <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
        <span style="font-size:.78rem;font-weight:600;color:#7b88a0;text-transform:uppercase;letter-spacing:.5px;">Tipo:</span>
        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#ede8fc;color:#6f42c1;border:1.5px solid #6f42c1;">1 – Administrador</span>
        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#e0eeff;color:#00509e;border:1.5px solid #00509e;">2 – Utilizador</span>
        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#e0f5fb;color:#0891b2;border:1.5px solid #0891b2;">3 – Reparador</span>
        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:5px;font-size:.75rem;font-weight:700;background:#e0f7f0;color:#059669;border:1.5px solid #059669;">4 – Funcionário</span>
    </div>

    <!-- Botões de exportação (direita) -->
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="<?php echo SVRURL ?>userspdf" target="_blank" title="Exportar para PDF"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>
            </svg>
            Exportar PDF
        </a>
        <!--
        <a href="<?php echo SVRURL ?>userscsv" target="_blank" title="Exportar para CSV"
           style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);transition:opacity .15s,transform .15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
            </svg>
            Exportar CSV
        </a>
-->
    </div>

</div>
<?php else: ?>
<div style="display:flex;justify-content:flex-end;gap:8px;padding:10px 0;margin-bottom:8px;">
    <a href="<?php echo SVRURL ?>userspdf" target="_blank" title="Exportar para PDF"
       style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>
        </svg>
        Exportar PDF
    </a>
    <a href="<?php echo SVRURL ?>userscsv" target="_blank" title="Exportar para CSV"
       style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
        </svg>
        Exportar CSV
    </a>
</div>
<?php endif; ?>




  <?php 

if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}
if(isset($_POST['filtro-tipo'])){
    $_SESSION['filtro-tipo'] = $_POST['filtro-tipo'];
}
if(isset($_POST['filtro-ativo'])){
    $_SESSION['filtro-ativo'] = $_POST['filtro-ativo'];
}

$limit        = isset($_SESSION['records-limit']) ? (int)$_SESSION['records-limit'] : 10;
$filtro_tipo  = isset($_SESSION['filtro-tipo'])   ? (int)$_SESSION['filtro-tipo']   : 0;
$filtro_ativo = isset($_SESSION['filtro-ativo'])  ? $_SESSION['filtro-ativo']        : 'todos';
$page         = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

// WHERE condicional por tipo e estado
$conditions = [];
if ($filtro_tipo > 0)            { $conditions[] = "tipo = $filtro_tipo"; }
if ($filtro_ativo === 'ativo')   { $conditions[] = "COALESCE(ativo,1) = 1"; }
if ($filtro_ativo === 'inativo') { $conditions[] = "COALESCE(ativo,1) = 0"; }
$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

$sql  = "SELECT * FROM utilizadores $where ORDER BY nome LIMIT $paginationStart, $limit";
$result = mysqli_query($db, $sql);

// Get total records
$sql1 = "SELECT COUNT(*) FROM utilizadores $where";
$result1 = mysqli_query($db, $sql1);
$rows = mysqli_fetch_row($result1);

$totallinhas = $rows[0];

// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<script>
function a(n, no, em) {

    var n1 = n; var no1 = no; var em1 = em;

    event.preventDefault();

    swal({
        title: "Deseja eliminar?",
        text: no1 + " - " + em1,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            // Obter o token CSRF correspondente ao ID do utilizador
            var token = document.getElementById('csrf_token_' + n1).value;
            window.setTimeout(function() {
                window.location.href = '<?php echo SVRURL ?>eliminauser/' + n1 + '?token=' + encodeURIComponent(token);
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}

function toggleUtiliz(id, nome, novoEstado) {
    var titulo  = novoEstado === 0 ? 'Desativar conta?' : 'Ativar conta?';
    var texto   = novoEstado === 0
        ? nome + ' deixará de conseguir fazer login.'
        : nome + ' voltará a poder fazer login.';
    swal({
        title: titulo,
        text:  texto,
        type:  'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText:  'Não',
        closeOnConfirm: false,
        closeOnCancel:  false
    }, function(isConfirm) {
        if (isConfirm) {
            window.location.href = '<?php echo SVRURL ?>toggle_ativo_user/' + id + '/' + novoEstado;
        } else {
            swal('Cancelado.');
        }
    });
}

</script>



<style>
/* ── Tabela utilizadores ── */
.gei-table-wrap {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(75,108,183,.10);
    border: 1px solid #e3e8f4;
    overflow: hidden;
    margin-bottom: 16px;
}
.gei-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding: 12px 16px;
    background: #f4f6fb;
    border-bottom: 1px solid #e3e8f4;
}
.gei-table-toolbar-left { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }

.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th {
    padding: 10px 14px;
    background: #182848;
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border: none;
    white-space: nowrap;
}
.gei-table thead th:first-child { border-radius: 0; }
.gei-table tbody tr {
    border-bottom: 1px solid #eef1f8;
    transition: background .15s;
}
.gei-table tbody tr:last-child { border-bottom: none; }
.gei-table tbody tr:hover { background: #f0f4fb; }
.gei-table tbody tr:nth-child(even) { background: #f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background: #eef2fb; }
.gei-table td { padding: 10px 14px; vertical-align: middle; color: #1e2a45; }

/* Badge tipo */
.gei-tipo-badge {
    display: inline-flex; align-items: center;
    padding: 2px 9px; border-radius: 5px;
    font-size: .72rem; font-weight: 700; border: 1.5px solid;
    white-space: nowrap;
}

/* Botões ação */
.gei-action-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 6px;
    font-size: .75rem; font-weight: 600;
    text-decoration: none !important; border: none;
    cursor: pointer; transition: opacity .15s, transform .12s;
    white-space: nowrap;
}
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-toggle-off { background:#fff3cd; color:#7d4e00 !important; border:1.5px solid #e67e22; }
.gei-btn-toggle-on  { background:#e9f7ef; color:#1e8449 !important; border:1.5px solid #1cc88a; }
.gei-btn-toggle-off { background:#fff3cd; color:#7d4e00 !important; border:1.5px solid #e67e22; }
.gei-btn-toggle-on  { background:#e9f7ef; color:#1e8449 !important; border:1.5px solid #1cc88a; }

/* Botão inserir no header */
.gei-insert-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 7px;
    font-size: .8rem; font-weight: 600;
    background: #1cc88a !important; color: #fff !important;
    text-decoration: none !important; border: none;
    box-shadow: 0 2px 8px rgba(28,200,138,.25);
    transition: opacity .15s, transform .12s;
}
.gei-insert-btn:hover { opacity:.88; transform:translateY(-1px); }

/* Paginação */
.gei-pagination {
    display: flex; align-items: center; justify-content: center;
    gap: 4px; flex-wrap: wrap; padding: 12px 0;
}
.gei-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 32px; padding: 0 10px;
    border-radius: 6px; font-size: .8rem; font-weight: 600;
    text-decoration: none !important; border: 1.5px solid #e3e8f4;
    color: #4b6cb7 !important; background: #fff;
    transition: all .15s;
}
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total {
    font-size: .78rem; color: #7b88a0; font-weight: 600;
    padding: 0 10px;
}

/* ── Responsivo mobile ── */
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
        content: attr(data-label); min-width:80px;
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

<!-- Toolbar -->
<div class="gei-table-wrap">
<div class="gei-table-toolbar">
    <div class="gei-table-toolbar-left">
        <form action="<?php echo SVRURL ?>utiliz" method="post" style="margin:0;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Tipo:
                <select name="filtro-tipo" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <option value="0" <?= ($filtro_tipo==0) ? 'selected' : '' ?>>Todos</option>
                    <option value="1" <?= ($filtro_tipo==1) ? 'selected' : '' ?>>Administrador</option>
                    <option value="2" <?= ($filtro_tipo==2) ? 'selected' : '' ?>>Utilizador</option>
                    <option value="3" <?= ($filtro_tipo==3) ? 'selected' : '' ?>>Reparador</option>
                    <option value="4" <?= ($filtro_tipo==4) ? 'selected' : '' ?>>Funcionário</option>
                </select>
            </label>
            <?php if ($_SESSION['tipo']==1): ?>
            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Estado:
                <select name="filtro-ativo" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;
                    border:1.5px solid <?php echo $filtro_ativo==='inativo' ? '#e67e22' : '#c7d4f0'; ?>;">
                    <option value="todos"   <?= ($filtro_ativo==='todos')   ? 'selected' : '' ?>>Todos</option>
                    <option value="ativo"   <?= ($filtro_ativo==='ativo')   ? 'selected' : '' ?>>✓ Ativos</option>
                    <option value="inativo" <?= ($filtro_ativo==='inativo') ? 'selected' : '' ?>>✕ Desativados</option>
                </select>
            </label>
            <?php endif; ?>
        </form>
      
    </div>
    <?php if ($_SESSION['tipo']==1): ?>
    <div class="gei-table-toolbar-right">
        <a href="<?php echo SVRURL ?>utiliz_inativos" title="Utilizadores inativos"
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;font-size:.8rem;font-weight:600;
            background:#fff3cd;color:#7d4e00;border:1.5px solid #e67e22;text-decoration:none;
            box-shadow:0 2px 8px rgba(230,126,34,.18);transition:opacity .15s,transform .12s;"
            onmouseover="this.style.opacity='.85';this.style.transform='translateY(-1px)'"
            onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#e67e22" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <line x1="17" y1="8" x2="23" y2="8"/><line x1="20" y1="5" x2="20" y2="11"/>
            </svg>
            Inativos
        </a>
        <a href="<?php echo SVRURL ?>inserirutil" class="gei-insert-btn" title="Inserir utilizador">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo utilizador
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Tabela -->
<table class="gei-table" id="js-sort-table">
    <thead>
        <tr>
            <?php if ($_SESSION['tipo']==1): ?>
            <th style="width:12%">Tipo</th>
            <?php endif; ?>
            <th>Nome</th>
            <th>Email</th>
            <?php if ($_SESSION['tipo']==1): ?>
            <th style="width:12%;text-align:center;">Ações</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php
    $tipo_styles = [
        1 => ['label'=>'Administrador', 'color'=>'#6f42c1', 'bg'=>'#ede8fc'],
        2 => ['label'=>'Utilizador',    'color'=>'#00509e', 'bg'=>'#e0eeff'],
        3 => ['label'=>'Reparador',     'color'=>'#0891b2', 'bg'=>'#e0f5fb'],
        4 => ['label'=>'Funcionário',   'color'=>'#059669', 'bg'=>'#e0f7f0'],
    ];
    while ($row = mysqli_fetch_array($result)):
        $t = (int)$row['tipo'];
        $ts = $tipo_styles[$t] ?? ['label'=>$t, 'color'=>'#7b88a0', 'bg'=>'#f0f0f0'];

        // ── Gerar token CSRF individual para este utilizador ──────────────────
        $uid = (int)$row['id'];
        if (empty($_SESSION['csrf_delete_tokens'][$uid])) {
            $_SESSION['csrf_delete_tokens'][$uid] = bin2hex(random_bytes(32));
        }
        $delete_token = $_SESSION['csrf_delete_tokens'][$uid];
        // ─────────────────────────────────────────────────────────────────────
    ?>
    <tr>
        <?php if ($_SESSION['tipo']==1): ?>
        <td data-label="Tipo">
            <span class="gei-tipo-badge" style="color:<?php echo $ts['color'];?>;background:<?php echo $ts['bg'];?>;border-color:<?php echo $ts['color'];?>;">
                <?php echo $ts['label']; ?>
            </span>
        </td>
        <?php endif; ?>
        <td data-label="Nome" style="font-weight:600;">
            <?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?>
            <?php if ((int)($row['ativo'] ?? 1) === 0): ?>
            <span style="margin-left:5px;display:inline-flex;align-items:center;padding:1px 7px;border-radius:20px;font-size:.68rem;font-weight:700;background:#fde8e6;color:#c0392b;border:1px solid #f5c0bb;">
                ✕ Desativado
            </span>
            <?php endif; ?>
        </td>
        <td data-label="Email" style="color:#5a6370;"><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
        <?php if ($_SESSION['tipo']==1): ?>
        <td data-label="Ações" style="text-align:center;white-space:nowrap;">
            <?php if ($_SESSION['tipo']==1 && $row['nome'] != $_SESSION['login_user']): ?>

            <!-- Token CSRF oculto para este utilizador (lido pelo JS antes do redirect) -->
            <input type="hidden" id="csrf_token_<?php echo $uid; ?>" value="<?php echo htmlspecialchars($delete_token, ENT_QUOTES, 'UTF-8'); ?>">

            <a class="gei-action-btn gei-btn-edit" title="Atualizar" href="<?php echo SVRURL ?>atualizautili/<?php echo base64_encode($row['id']) ?>">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar
            </a>

            <?php
            $is_ativo = (int)($row['ativo'] ?? 1);
            if ($is_ativo):
            ?>
            <a class="gei-action-btn gei-btn-toggle-off" title="Desativar conta — o utilizador deixa de conseguir fazer login"
               onclick="toggleUtiliz(<?php echo (int)$row['id']; ?>,'<?php echo htmlspecialchars($row['nome'],ENT_QUOTES,'UTF-8'); ?>',0); return false;"
               href="#">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="1" y="5" width="22" height="14" rx="7"/><circle cx="16" cy="12" r="3" fill="currentColor"/></svg>
                Desativar
            </a>
            <?php else: ?>
            <a class="gei-action-btn gei-btn-toggle-on" title="Ativar conta — o utilizador volta a poder fazer login"
               onclick="toggleUtiliz(<?php echo (int)$row['id']; ?>,'<?php echo htmlspecialchars($row['nome'],ENT_QUOTES,'UTF-8'); ?>',1); return false;"
               href="#">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="1" y="5" width="22" height="14" rx="7"/><circle cx="8" cy="12" r="3" fill="currentColor"/></svg>
                Ativar
            </a>
            <?php endif; ?>

            <a class="gei-action-btn gei-btn-delete" title="Eliminar"
               onclick="a(<?php echo $uid;?>,'<?php echo htmlspecialchars($row['nome'],ENT_QUOTES,'UTF-8');?>','<?php echo htmlspecialchars($row['email'],ENT_QUOTES,'UTF-8');?>'); return false;"
               href="#">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                Eliminar
            </a>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<!-- Ordenar info -->
<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>

<!-- Paginação -->
<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : '?page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>" href="utiliz?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>
       


        <?php include ("jquery_bootstrap.php");?>



<a href="<?php echo SVRURL ?>configura">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>



<br>



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
