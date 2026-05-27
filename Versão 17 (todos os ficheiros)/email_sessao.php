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
?>

<?php
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php include("head.php"); ?>

<script>
function myFunction() {
    var x = document.getElementById("mypass");
    x.type = (x.type === "password") ? "text" : "password";
}

// Mostrar/ocultar código QR de uma linha específica
function toggleQR(id) {
    var span = document.getElementById("qr_mask_" + id);
    var val  = document.getElementById("qr_val_"  + id);
    var ico  = document.getElementById("qr_ico_"  + id);
    if (span.style.display !== "none") {
        span.style.display = "none";
        val.style.display  = "inline";
        ico.className = "fa-regular fa-eye-slash";
    } else {
        span.style.display = "inline";
        val.style.display  = "none";
        ico.className = "fa-regular fa-eye";
    }
}
</script>

<script>
function a() {
    event.preventDefault();
    swal({
        title: "Deseja eliminar?",
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
                window.location.href = '<?php echo SVRURL ?>eliminaemsess.php';
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>

      <?php include("sessao_timeout.php"); ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <!-- Breadcrumb -->
                  <nav style="margin-bottom:10px;">
                     <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                           <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Email/Sessão</li>
                     </ol>
                  </nav>
                  <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-12">

                     <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                     </div>

<?php
$stmt_es = $db->prepare("SELECT * FROM settings");
$stmt_es->execute();
$result = $stmt_es->get_result();

$_first_row = $result->fetch_assoc();
$row0 = [$_first_row['pass'] ?? ''];

$stmt_es2 = $db->prepare("SELECT * FROM settings");
$stmt_es2->execute();
$result = $stmt_es2->get_result();
?>

<style>
.gei-table-wrap {
    background:#fff; border-radius:10px;
    box-shadow:0 2px 12px rgba(75,108,183,.10);
    border:1px solid #e3e8f4; overflow-x:auto; margin-bottom:16px;
}
.gei-table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px; padding:12px 16px;
    background:#f4f6fb; border-bottom:1px solid #e3e8f4;
}
.gei-table-toolbar-left  { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; table-layout:auto; }
.gei-table thead th {
    padding:10px 10px; background:#182848; color:#fff;
    font-size:.70rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.3px; border:none; white-space:normal; word-break:break-word;
}
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background:#eef2fb; }
.gei-table td { padding:10px 10px; vertical-align:middle; color:#1e2a45; }
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
    padding:4px 8px; border-radius:6px; font-size:.75rem; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:opacity .15s, transform .12s; white-space:nowrap;
}
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }

/* Botão olho para código QR */
.btn-qr-toggle {
    background:none; border:none; padding:0 0 0 6px;
    cursor:pointer; color:#4b6cb7; font-size:.85rem;
    vertical-align:middle; line-height:1;
}
.btn-qr-toggle:hover { color:#1e3a8a; }

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
        content: attr(data-label); min-width:130px;
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

<?php
$stmt_c0 = $db->prepare("SELECT count(*) FROM settings");
$stmt_c0->execute();
$count0 = $stmt_c0->get_result()->fetch_row();
$stmt_c0->close();
?>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left"></div>
        <?php if ($_SESSION['tipo'] == 1 && $count0[0] == 0): ?>
        <div class="gei-table-toolbar-right">
            <a href="<?php echo SVRURL ?>inseriremse" class="gei-insert-btn" title="Inserir configuração">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova configuração
            </a>
        </div>
        <?php endif; ?>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th style="width:20%">Email / Sigla</th>
                <th style="width:15%">SMTP / Porta</th>
                <th style="width:11%">Duração Sessão (seg)</th>
                <th style="width:11%">Duração Password (dias)</th>
                <th style="width:12%">Código QR</th>
                <th style="width:14%">Retenção de dados</th>
                <?php if ($_SESSION['tipo'] == 1): ?>
                <th style="text-align:center;width:17%;">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_array($result)):
            $row_id    = (int)$row['id'];
            $codigo_qr = htmlspecialchars($row['codigo_acesso_qr'] ?? '', ENT_QUOTES, 'UTF-8');
            $mascara   = $codigo_qr !== '' ? str_repeat('●', min(strlen($row['codigo_acesso_qr'] ?? ''), 8)) : '—';
        ?>
        <tr>
            <!-- Email / Sigla -->
            <td data-label="Email / Sigla">
                <span style="font-weight:700;"><?php echo htmlspecialchars($row['email_user'], ENT_QUOTES, 'UTF-8'); ?></span>
                <br>
                <span style="font-size:.78rem;color:#7b88a0;"><?php echo htmlspecialchars($row['nome_app'], ENT_QUOTES, 'UTF-8'); ?></span>
            </td>

            <!-- SMTP / Porta -->
            <td data-label="SMTP / Porta">
                <span style="font-family:monospace;font-size:.82rem;"><?php echo htmlspecialchars($row['email_smtp'], ENT_QUOTES, 'UTF-8'); ?></span>
                <br>
                <span style="font-size:.78rem;color:#7b88a0;"><?php echo htmlspecialchars($row['email_smtpport'], ENT_QUOTES, 'UTF-8'); ?></span>
            </td>

            <!-- Duração Sessão -->
            <td data-label="Duração Sessão">
                <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.78rem;font-weight:700;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;">
                    <?php echo htmlspecialchars($row['sessao_timeout'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </td>

            <!-- Duração Password -->
            <td data-label="Duração Password">
                <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.78rem;font-weight:700;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;">
                    <?php echo htmlspecialchars($row['tempoduracaopass'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </td>

            <!-- Código QR — só visível para admin (tipo 1) -->
            <td data-label="Código QR">
                <?php if ($_SESSION['tipo'] == 1): ?>
                    <?php if ($codigo_qr !== ''): ?>
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:5px;font-size:.78rem;font-weight:700;background:#f0f4fb;color:#4b6cb7;border:1.5px solid #c7d4f0;font-family:monospace;letter-spacing:2px;">
                            <!-- Máscara -->
                            <span id="qr_mask_<?php echo $row_id; ?>"><?php echo $mascara; ?></span>
                            <!-- Valor real (oculto por defeito) -->
                            <span id="qr_val_<?php echo $row_id; ?>" style="display:none;"><?php echo $codigo_qr; ?></span>
                        </span>
                        <button class="btn-qr-toggle" onclick="toggleQR(<?php echo $row_id; ?>)" title="Mostrar/ocultar código">
                            <i class="fa-regular fa-eye" id="qr_ico_<?php echo $row_id; ?>"></i>
                        </button>
                    <?php else: ?>
                        <span style="font-size:.78rem;color:#bbb;">Não definido</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="font-size:.78rem;color:#bbb;">—</span>
                <?php endif; ?>
            </td>

            <!-- Retenção de dados -->
            <td data-label="Retenção de dados">
                <?php
                $ret_ativa = (int)($row['retencao_ativa'] ?? 0);
                $ret_anos  = (int)($row['retencao_anos']  ?? 3);
                $ret_dias  = (int)($row['retencao_dias_aviso'] ?? 30);
                ?>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 9px;border-radius:5px;font-size:.75rem;font-weight:700;
                    background:<?php echo $ret_ativa ? '#e9f7ef' : '#f4f6fb'; ?>;
                    color:<?php echo $ret_ativa ? '#1e8449' : '#7b88a0'; ?>;
                    border:1.5px solid <?php echo $ret_ativa ? '#1cc88a' : '#c5cde0'; ?>;">
                    <?php echo $ret_ativa ? '● Ativa' : '○ Inativa'; ?>
                </span>
                <?php if ($ret_ativa): ?>
                <br>
                <span style="font-size:.72rem;color:#7b88a0;margin-top:3px;display:block;">
                    <?php echo $ret_anos; ?> ano(s) · aviso <?php echo $ret_dias; ?> dias
                </span>
                <?php endif; ?>
            </td>

            <!-- Ações -->
            <?php if ($_SESSION['tipo'] == 1): ?>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                   href="<?php echo SVRURL ?>atualtemse/<?php echo base64_encode($row['id']); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                </a>
                <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                   onclick="a();"
                   href="<?php echo SVRURL ?>eliminaemsess.php">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Eliminar
                </a>
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

<br>

<a href="<?php echo SVRURL ?>configura">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br>

<?php include("jquery_bootstrap.php"); ?>

<br>

                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php include("footer.php"); ?>
   </body>
</html>
