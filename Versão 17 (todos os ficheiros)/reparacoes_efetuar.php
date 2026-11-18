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
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
<?php include ("head.php"); ?>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>
     <?php include("sessao_timeout.php"); ?>

<?php

$op = $_GET["op"] ?? '';

if ($op === 't') {
    $op2 = 'Todas';
} elseif ($op === 'al') {
    $op2 = 'Ano ';
} elseif ($op === 'm') {
    $op2 = 'Atribuídas a mim';
} else {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>reparafaz?op=t'; }, 10);</script>
<?php
}

$sql3    = "SELECT MAX(ano_lectivo) FROM periodos";
$result3 = mysqli_query($db, $sql3);
$rows3   = mysqli_fetch_row($result3);
$anoAtual = $rows3[0];

// Contagem total (mesmos JOINs da query principal, para o total coincidir com as linhas listadas)
$joinsContagem = "FROM avarias_reparacoes ar
         INNER JOIN equipamento e   ON e.id   = ar.id_equi
         INNER JOIN salas s         ON s.id   = ar.id_sala
         INNER JOIN escolas esc     ON esc.id = ar.id_escola";
$meuIdRep = (int)($_SESSION['user_id'] ?? 0);
if ($op === 't') {
    $result1     = mysqli_query($db, "SELECT COUNT(*) $joinsContagem WHERE ar.datareparacao IS NULL");
    $repTotal = (int)mysqli_fetch_row($result1)[0];
} elseif ($op === 'm') {
    $stmt_cnt = mysqli_prepare($db, "SELECT COUNT(*) $joinsContagem WHERE ar.datareparacao IS NULL AND ar.tecnico_id = ?");
    mysqli_stmt_bind_param($stmt_cnt, 'i', $meuIdRep);
    mysqli_stmt_execute($stmt_cnt);
    $repTotal = (int)mysqli_fetch_row(mysqli_stmt_get_result($stmt_cnt))[0];
} else {
    $stmt_cnt = mysqli_prepare($db, "SELECT COUNT(*) $joinsContagem WHERE ar.datareparacao IS NULL AND ar.ano_letivo = ?");
    mysqli_stmt_bind_param($stmt_cnt, 's', $anoAtual);
    mysqli_stmt_execute($stmt_cnt);
    $repTotal = (int)mysqli_fetch_row(mysqli_stmt_get_result($stmt_cnt))[0];
}

// Paginação
if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = $_POST['records-limit'];
}
$repLimit           = isset($_SESSION['records-limit']) ? (int)$_SESSION['records-limit'] : 10;
$repPage            = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$repStart = ($repPage - 1) * $repLimit;
$repPages      = ceil($repTotal / $repLimit);
$repPrev            = $repPage - 1;
$repNext            = $repPage + 1;

// Query principal
if ($op === 't') {
    $stmt = mysqli_prepare($db,
        "SELECT ar.*, e.nomeequi, s.nome AS nomsala, esc.nome_escola, ut.nome AS tecnico_nome
         FROM avarias_reparacoes ar
         INNER JOIN equipamento e   ON e.id   = ar.id_equi
         INNER JOIN salas s         ON s.id   = ar.id_sala
         INNER JOIN escolas esc     ON esc.id = ar.id_escola
         LEFT JOIN utilizadores ut  ON ut.id  = ar.tecnico_id
         WHERE ar.datareparacao IS NULL
         ORDER BY ar.dataavaria ASC
         LIMIT ?, ?"
    );
    mysqli_stmt_bind_param($stmt, 'ii', $repStart, $repLimit);
} elseif ($op === 'm') {
    $stmt = mysqli_prepare($db,
        "SELECT ar.*, e.nomeequi, s.nome AS nomsala, esc.nome_escola, ut.nome AS tecnico_nome
         FROM avarias_reparacoes ar
         INNER JOIN equipamento e   ON e.id   = ar.id_equi
         INNER JOIN salas s         ON s.id   = ar.id_sala
         INNER JOIN escolas esc     ON esc.id = ar.id_escola
         LEFT JOIN utilizadores ut  ON ut.id  = ar.tecnico_id
         WHERE ar.datareparacao IS NULL AND ar.tecnico_id = ?
         ORDER BY ar.dataavaria ASC
         LIMIT ?, ?"
    );
    mysqli_stmt_bind_param($stmt, 'iii', $meuIdRep, $repStart, $repLimit);
} else {
    $stmt = mysqli_prepare($db,
        "SELECT ar.*, e.nomeequi, s.nome AS nomsala, esc.nome_escola, ut.nome AS tecnico_nome
         FROM avarias_reparacoes ar
         INNER JOIN equipamento e   ON e.id   = ar.id_equi
         INNER JOIN salas s         ON s.id   = ar.id_sala
         INNER JOIN escolas esc     ON esc.id = ar.id_escola
         LEFT JOIN utilizadores ut  ON ut.id  = ar.tecnico_id
         WHERE ar.datareparacao IS NULL AND ar.ano_letivo = ?
         ORDER BY ar.dataavaria ASC
         LIMIT ?, ?"
    );
    mysqli_stmt_bind_param($stmt, 'sii', $anoAtual, $repStart, $repLimit);
}
mysqli_stmt_execute($stmt);
$repResult = mysqli_stmt_get_result($stmt);

// Se a 1.ª página não devolve linhas, o total tem de ser 0 (evita "Total: 7" com tabela vazia)
$numLinhas = mysqli_num_rows($repResult);
if ($repPage <= 1 && $numLinhas === 0) {
    $repTotal = 0;
    $repPages  = 0;
}

// Diagnóstico: acrescentar &debug=1 ao URL (só administradores)
if (isset($_GET['debug']) && isset($_SESSION['tipo']) && (int)$_SESSION['tipo'] === 1) {
    echo '<pre style="background:#fff3cd;border:1px solid #ffe082;padding:8px;font-size:12px;margin:8px;">'
       . 'ficheiro: reparacoes_efetuar.php (versao com $joinsContagem)' . "\n"
       . 'op=' . htmlspecialchars($op) . ' | anoAtual=' . htmlspecialchars((string)$anoAtual)
       . ' | page=' . $repPage . ' | limit=' . $repLimit . ' | start=' . $repStart . "\n"
       . 'totallinhas=' . $repTotal . ' | linhas devolvidas=' . $numLinhas
       . '</pre>';
}
?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">

               <!-- Breadcrumb -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <a href="<?php echo SVRURL ?>avaria" style="color:#4b6cb7;text-decoration:none;">Avarias</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Reparações a efetuar</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

                  <!-- Cabeçalho título + filtros -->
                  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin:14px 0 6px;">
                     <div style="display:flex;align-items:center;gap:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span style="font-size:1.05rem;font-weight:700;color:#182848;">Reparações a efetuar</span>
                        <span style="font-size:.78rem;font-weight:700;padding:3px 12px;border-radius:20px;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;"><?php echo htmlspecialchars($op2, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <!-- Filtros -->
                     <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="<?php echo SVRURL ?>reparafaz?op=t"
                           style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;font-size:.8rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='t' ? '#182848' : '#c7d4f0'; ?>;background:<?php echo $op==='t' ? '#182848' : '#fff'; ?>;color:<?php echo $op==='t' ? '#fff' : '#4b6cb7'; ?>;">
                           Todas
                        </a>
                        <a href="<?php echo SVRURL ?>reparafaz?op=al"
                           style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;font-size:.8rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='al' ? '#182848' : '#c7d4f0'; ?>;background:<?php echo $op==='al' ? '#182848' : '#fff'; ?>;color:<?php echo $op==='al' ? '#fff' : '#4b6cb7'; ?>;">
                           Ano: <?php echo htmlspecialchars($anoAtual, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <a href="<?php echo SVRURL ?>reparafaz?op=m"
                           style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;font-size:.8rem;font-weight:700;text-decoration:none;border:1.5px solid <?php echo $op==='m' ? '#182848' : '#c7d4f0'; ?>;background:<?php echo $op==='m' ? '#182848' : '#fff'; ?>;color:<?php echo $op==='m' ? '#fff' : '#4b6cb7'; ?>;">
                           Atribuídas a mim
                        </a>
                     </div>
                  </div>

                  <!-- Nota informativa -->
                  <div style="display:flex;align-items:center;gap:8px;margin:10px 0 14px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                     <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                     Ao clicar em <img width="15" height="15" src="<?php echo SVRURL ?>images/checkbox.png" style="vertical-align:middle;margin:0 4px;">, o autor da avaria recebe um email com os dados da reparação.
                  </div>

<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:20px; }
.gei-table-section-header { padding:10px 16px; background:#182848; color:#fff; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#253d6e; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:top; color:#1e2a45; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-img { height:120px; width:200px; object-fit:cover; border-radius:6px; border:1px solid #e3e8f4; cursor:zoom-in; transition:transform .25s ease; }
.gei-img:hover { transform:scale(2.5); z-index:10; position:relative; }
.gei-badge-warn { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#fff8e1; color:#b07d00; border:1.5px solid #ffe082; }
.gei-badge-reassigned { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:5px; font-size:.75rem; font-weight:700; background:#eef2fb; color:#4b6cb7; border:1.5px solid #c7d4f0; margin-top:6px; cursor:default; }
.gei-rep-input { width:100%; padding:5px 8px; border:1.5px solid #c7d4f0; border-radius:6px; font-size:.83rem; color:#1e2a45; background:#f8faff; margin-bottom:6px; box-sizing:border-box; }
.gei-rep-input:focus { outline:none; border-color:#4b6cb7; background:#fff; }
.gei-rep-textarea { width:100%; padding:6px 8px; border:1.5px solid #c7d4f0; border-radius:6px; font-size:.83rem; color:#1e2a45; background:#f8faff; resize:vertical; box-sizing:border-box; }
.gei-rep-textarea:focus { outline:none; border-color:#4b6cb7; background:#fff; }
.gei-btn-rep { display:inline-flex; align-items:center; gap:6px; padding:6px 16px; border-radius:7px; font-size:.82rem; font-weight:700; border:none; background:#1a7f4b; color:#fff !important; cursor:pointer; transition:background .15s; }
.gei-btn-rep:hover { background:#145f38; }
.gei-btn-reassign { display:inline-flex; align-items:center; gap:6px; padding:6px 16px; border-radius:7px; font-size:.82rem; font-weight:700; border:none; background:#4b6cb7; color:#fff !important; cursor:pointer; transition:background .15s; }
.gei-btn-reassign:hover { background:#37539a; }
.gei-select { padding:5px 10px; border:1.5px solid #3a5a9a; border-radius:7px; font-size:.82rem; color:#fff; background:#253d6e; font-weight:600; cursor:pointer; }
.gei-select:focus { outline:none; }
.gei-pagination { display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap; padding:14px 0 4px; }
.gei-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:6px; font-size:.8rem; font-weight:600; text-decoration:none !important; border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff; transition:all .15s; }
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-total-badge { font-size:.78rem; font-weight:700; color:#7b88a0; padding:4px 14px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:20px; }
@media (max-width:768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content:attr(data-label); min-width:120px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>

<?php
// Lista de técnicos disponíveis para reatribuição (apenas para admins, tipo 1)
$tecnicos_disponiveis = [];
if (isset($_SESSION['tipo']) && (int)$_SESSION['tipo'] === 1) {
    $meu_user_id = (int)($_SESSION['user_id'] ?? 0);
    $stmt_tec = $db->prepare("SELECT id, nome FROM utilizadores WHERE tipo IN (1,3) AND ativo = 1 AND id != ? ORDER BY nome");
    $stmt_tec->bind_param("i", $meu_user_id);
    $stmt_tec->execute();
    $res_tec = $stmt_tec->get_result();
    while ($t = $res_tec->fetch_assoc()) {
        $tecnicos_disponiveis[] = $t;
    }
    $stmt_tec->close();
}
?>

<script>
// SweetAlert2 é necessário para a função reatribuirAvaria() (usa Swal.fire),
// mas o projeto só tem carregada a SweetAlert v1 (função swal() em minúsculas).
// Carregamos a v2 aqui só se ainda não estiver presente, para não conflituar
// com o swal() v1 usado no resto da página (nomes globais diferentes: swal vs Swal).
if (typeof window.Swal === 'undefined') {
    document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">');
    document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
}
</script>
<script>
function validarDataRep(form) {
    var dre = form.querySelector('[name="datarep"]').value;
    var dav = form.querySelector('[name="dav"]').value;
    if (dre < dav) {
        swal({ title: 'A data de reparação deve ser igual ou superior à data da avaria!', icon: 'error' });
        return false;
    }
    return true;
}

// Lista de técnicos disponíveis (apenas preenchida para admins)
var GEI_TECNICOS = <?php echo json_encode($tecnicos_disponiveis); ?>;
var GEI_CSRF      = <?php echo json_encode($csrf_token); ?>;

function reatribuirAvaria(avariaId, tecnicoAtualId, instituicao, sala, equipamento) {
    var opcoes = '<option value="">— Selecione um técnico —</option>';
    GEI_TECNICOS.forEach(function (t) {
        if (parseInt(t.id, 10) === parseInt(tecnicoAtualId, 10)) { return; }
        opcoes += '<option value="' + t.id + '">' + t.nome + '</option>';
    });

    var escapeHtml = function (s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    };

    Swal.fire({
        title: 'Reatribuir avaria #' + avariaId,
        html:
            '<div style="text-align:left;">' +
            '<div style="background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;padding:10px 12px;margin-bottom:14px;font-size:.82rem;">' +
                '<span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;display:block;">Instituição</span>' +
                '<span style="font-weight:700;color:#1e2a45;display:block;margin-bottom:6px;">' + escapeHtml(instituicao) + '</span>' +
                '<span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;display:block;">Sala</span>' +
                '<span style="color:#1e2a45;display:block;margin-bottom:6px;">' + escapeHtml(sala) + '</span>' +
                '<span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;display:block;">Equipamento</span>' +
                '<span style="color:#1e2a45;display:block;">' + escapeHtml(equipamento) + '</span>' +
            '</div>' +
            '<label style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:#7b88a0;display:block;margin-bottom:4px;">Novo técnico</label>' +
            '<select id="swal-novo-tecnico" class="swal2-select" style="width:100%;margin:0 0 14px;padding:8px;border-radius:6px;border:1.5px solid #c7d4f0;">' + opcoes + '</select>' +
            '<label style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:#7b88a0;display:block;margin-bottom:4px;">Motivo (opcional)</label>' +
            '<textarea id="swal-motivo" class="swal2-textarea" style="width:100%;margin:0;" rows="3"></textarea>' +
            '</div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Reatribuir',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        preConfirm: function () {
            var novoTecnico = document.getElementById('swal-novo-tecnico').value;
            if (!novoTecnico) {
                Swal.showValidationMessage('Selecione um técnico.');
                return false;
            }
            return {
                novo_tecnico_id: novoTecnico,
                motivo: document.getElementById('swal-motivo').value
            };
        }
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        var dados = new URLSearchParams();
        dados.append('avaria_id', avariaId);
        dados.append('novo_tecnico_id', result.value.novo_tecnico_id);
        dados.append('motivo', result.value.motivo);
        dados.append('csrf_token', GEI_CSRF);

        Swal.fire({ title: 'A reatribuir...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        fetch('<?php echo SVRURL ?>reatribuir_avaria_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: dados.toString()
        })
        .then(function (resp) { return resp.json(); })
        .then(function (data) {
            if (data.sucesso) {
                Swal.fire({ title: 'Reatribuída!', text: data.mensagem, icon: 'success' })
                    .then(function () { window.location.reload(); });
            } else {
                Swal.fire({ title: 'Erro', text: data.erro || 'Não foi possível reatribuir a avaria.', icon: 'error' });
            }
        })
        .catch(function () {
            Swal.fire({ title: 'Erro', text: 'Falha de comunicação com o servidor.', icon: 'error' });
        });
    });
}
</script>

<?php if ($repTotal == 0): ?>
<div style="padding:36px 16px;text-align:center;color:#7b88a0;font-size:.9rem;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 14px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Não existem avarias por reparar.
</div>
<div style="text-align:center;margin:16px 0;">
    <span class="gei-total-badge">Total: 0</span>
</div>
<?php else: ?>

<div class="gei-table-wrap">
    <!-- Toolbar -->
    <div class="gei-table-section-header">
        <span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Reparações a efetuar — <?php echo htmlspecialchars($op2, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <form action="<?php echo SVRURL ?>reparafaz?op=<?php echo $op ?>" method="post" style="display:inline;">
            <select name="records-limit" onchange="this.form.submit()" class="gei-select">
                <option disabled>Nº linhas</option>
                <?php foreach ([5, 10, 20, 30, 50, 100] as $lim): ?>
                <option value="<?= $lim ?>" <?php if (isset($_SESSION['records-limit']) && $_SESSION['records-limit'] == $lim) echo 'selected'; ?>>
                    <?= $lim ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <table class="gei-table">
        <thead>
            <tr>
                <th style="width:25%">Instituição / Sala / Equipamento</th>
                <th style="width:30%">Avaria</th>
                <th style="width:35%">Registar reparação</th>
                <th style="width:10%">Ação</th>
            </tr>
        </thead>
        <tbody>

<?php while ($row = mysqli_fetch_array($repResult)):
    $n  = $row['id'];
    $em = $row['autoravaria'];

    $stmt2 = mysqli_prepare($db, "SELECT nome FROM utilizadores WHERE email = ?");
    mysqli_stmt_bind_param($stmt2, 's', $em);
    mysqli_stmt_execute($stmt2);
    $r2        = mysqli_fetch_row(mysqli_stmt_get_result($stmt2));
    $nomeautor = $r2[0] ?? '—';
?>
            <tr>
                <td data-label="Localização">
                    <span class="gei-label">Instituição</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($row['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Sala</span>
                    <span><?php echo htmlspecialchars($row['nomsala'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Equipamento</span>
                    <span><?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <div style="margin-top:8px;">
                        <span class="gei-badge-warn">&#9679; Por reparar</span>
                    </div>
                    <?php if (!empty($row['tecnico_id']) && !empty($row['data_reatribuicao'])): ?>
                    <div>
                        <span class="gei-badge-reassigned"
                              title="<?php echo !empty($row['motivo_reatribuicao']) ? 'Motivo: ' . htmlspecialchars($row['motivo_reatribuicao'], ENT_QUOTES, 'UTF-8') : 'Sem motivo indicado'; ?>">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                            Reatribuída a <?php echo htmlspecialchars($row['tecnico_nome'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </td>

                <td data-label="Avaria">
                    <span class="gei-label">Autor</span>
                    <span style="font-weight:700;"><?php echo htmlspecialchars($nomeautor, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:4px;">Email</span>
                    <span style="font-size:.78rem;color:#7b88a0;"><?php echo htmlspecialchars($em, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Data da avaria</span>
                    <span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['dataavaria'])); ?></span>
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <span><?php echo htmlspecialchars($row['avaria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if (!empty($row['imgavaria'])): ?>
                        <?php
                        // FIX: detectar MIME real dos bytes (evita falha com PNG/GIF/BMP)
                        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
                        $imgMime = finfo_buffer($finfo, $row['imgavaria']) ?: 'image/jpeg';
                        finfo_close($finfo);
                        ?>
                        <div style="margin-top:8px;">
                            <img class="gei-img"
                                 src="data:<?php echo htmlspecialchars($imgMime, ENT_QUOTES, 'UTF-8'); ?>;base64,<?php echo base64_encode($row['imgavaria']); ?>"
                                 alt="Imagem avaria">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($row['video'])): ?>
                        <div style="margin-top:8px;">
                            <!-- FIX: usar streamvideo.php autónomo em vez de base64 inline -->
                            <video width="200" height="150" controls style="border-radius:6px;border:1px solid #e3e8f4;">
                                <source src="streamvideo.php?id=<?php echo base64_encode($row['id']); ?>" type="video/mp4">
                                O seu browser não suporta a reprodução de vídeo.
                            </video>
                        </div>
                    <?php endif; ?>
                </td>

                <!-- FIX: <form> já não envolve os <td> (HTML inválido); fica fora da tr,
                     ligado aos campos via form="<id>". -->
                <td data-label="Registar reparação">
                    <span class="gei-label">Data de reparação</span>
                    <input class="gei-rep-input" required type="date" name="datarep" value="<?php echo date('Y-m-d'); ?>" form="form-rep-<?php echo (int)$n; ?>">
                    <span class="gei-label" style="margin-top:6px;">Descrição</span>
                    <textarea class="gei-rep-textarea" required rows="4" name="reparacao" form="form-rep-<?php echo (int)$n; ?>"></textarea>
                    <span class="gei-label" style="margin-top:6px;">Reparado por</span>
                    <input class="gei-rep-input" required type="text" name="repar_por" form="form-rep-<?php echo (int)$n; ?>">
                </td>

                <?php if (isset($_SESSION['tipo']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3)): ?>
                <td data-label="Ação" style="vertical-align:middle;">
                    <div style="display:flex;flex-direction:column;align-items:flex-start;gap:10px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#4b6cb7;cursor:pointer;" title="Enviar email ao autor da avaria">
                            <input type="checkbox" name="my_check" value="yes" style="width:16px;height:16px;cursor:pointer;" form="form-rep-<?php echo (int)$n; ?>">
                            Email autor
                        </label>
                        <button type="submit" class="gei-btn-rep" title="Registar reparação" form="form-rep-<?php echo (int)$n; ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Reparar
                        </button>
                        <br>
                        <?php if (isset($_SESSION['tipo']) && (int)$_SESSION['tipo'] === 1): ?>
                        <button type="button" class="gei-btn-reassign" title="Reatribuir avaria a outro técnico"
                                onclick="reatribuirAvaria(<?php echo (int)$n; ?>, <?php echo (int)($row['tecnico_id'] ?? 0); ?>, <?php echo htmlspecialchars(json_encode($row['nome_escola'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($row['nomsala'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($row['nomeequi'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                            Reatribuir
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
                <?php else: ?>
                <td></td>
                <?php endif; ?>

            </tr>
            <!-- form vive fora da <tr>/<table>, ligado aos campos acima via atributo form= -->
            <form id="form-rep-<?php echo (int)$n; ?>" onsubmit="return validarDataRep(this);"
                  action="<?php echo SVRURL ?>repara_avaria.php?ia=<?php echo base64_encode($n); ?>&em=<?php echo urlencode($em); ?>"
                  method="post" style="display:none;">
                <input type="hidden" name="dav"        value="<?php echo htmlspecialchars($row['dataavaria'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            </form>
<?php endwhile; ?>

        </tbody>
    </table>
</div>

<!-- Paginação -->
<?php if ($repPages > 1): ?>
<div class="gei-pagination">
    <a class="gei-page-btn <?php if ($repPage <= 1) echo 'disabled'; ?>"
       href="<?php echo $repPage > 1 ? SVRURL . "reparafaz?op=$op&page=$repPrev" : '#'; ?>">&#8249;</a>

    <?php for ($i = 1; $i <= $repPages; $i++): ?>
    <a class="gei-page-btn <?php if ($repPage == $i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>reparafaz?op=<?php echo $op ?>&page=<?= $i ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>

    <a class="gei-page-btn <?php if ($repPage >= $repPages) echo 'disabled'; ?>"
       href="<?php echo $repPage < $repPages ? SVRURL . "reparafaz?op=$op&page=$repNext" : '#'; ?>">&#8250;</a>
</div>
<?php endif; ?>

<div style="text-align:center;margin-bottom:16px;">
    <span class="gei-total-badge">Total: <?php echo $repTotal; ?></span>
</div>

<?php endif; ?>

<?php include ("jquery_bootstrap.php"); ?>
<?php mysqli_close($db); ?>

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
