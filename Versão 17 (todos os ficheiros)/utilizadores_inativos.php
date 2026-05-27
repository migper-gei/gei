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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include("head.php"); ?>
</head>
<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php
include("sessao_timeout.php");

// Apenas administradores
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) { ?>
    <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>i'; },10);</script>
<?php exit; }

// ─────────────────────────────────────────────
//  LER CONFIGURAÇÃO DE RETENÇÃO
// ─────────────────────────────────────────────
$stmt_cfg = $db->prepare("SELECT retencao_anos, retencao_dias_aviso, retencao_ativa FROM settings LIMIT 1");
$stmt_cfg->execute();
$cfg = $stmt_cfg->get_result()->fetch_assoc();
$stmt_cfg->close();

$retencao_anos       = (int)($cfg['retencao_anos']       ?? 3);
$retencao_dias_aviso = (int)($cfg['retencao_dias_aviso'] ?? 30);
$retencao_ativa      = (int)($cfg['retencao_ativa']      ?? 0);

// ─────────────────────────────────────────────
//  PROCESSAR AÇÕES POST
// ─────────────────────────────────────────────
$msg_acao = '';
$msg_tipo = '';

// Validar CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $msg_acao = 'Erro de segurança: token inválido.';
        $msg_tipo = 'error';
    } else {
        unset($_SESSION['csrf_token']);
        $acao = $_POST['acao'] ?? '';

        // ── Guardar configuração ──────────────────────────────────
        if ($acao === 'config') {
            $novos_anos  = max(1, min(10, (int)($_POST['retencao_anos']       ?? 3)));
            $novos_dias  = max(7, min(90, (int)($_POST['retencao_dias_aviso'] ?? 30)));
            $nova_ativa  = isset($_POST['retencao_ativa']) ? 1 : 0;

            $stmt_upd = $db->prepare("UPDATE settings SET retencao_anos=?, retencao_dias_aviso=?, retencao_ativa=?");
            $stmt_upd->bind_param("iii", $novos_anos, $novos_dias, $nova_ativa);
            $stmt_upd->execute();
            $stmt_upd->close();

            $retencao_anos       = $novos_anos;
            $retencao_dias_aviso = $novos_dias;
            $retencao_ativa      = $nova_ativa;
            $msg_acao = 'Configuração guardada.';
            $msg_tipo = 'ok';
        }

        // ── Reativar utilizador (reset do contador) ───────────────
        if ($acao === 'reativar' && !empty($_POST['uid'])) {
            $uid = (int)$_POST['uid'];
            $stmt_r = $db->prepare("UPDATE utilizadores SET ultimo_login=NOW(), notificado_retencao=0 WHERE id=? AND tipo!=1");
            $stmt_r->bind_param("i", $uid);
            $stmt_r->execute();
            $stmt_r->close();
            $msg_acao = 'Utilizador reativado — contador de inatividade reposto.';
            $msg_tipo = 'ok';
        }

        // ── Eliminar utilizador ───────────────────────────────────
        if ($acao === 'eliminar' && !empty($_POST['uid'])) {
            $uid = (int)$_POST['uid'];
            // Salvaguarda: nunca eliminar administradores (tipo 1)
            // nem utilizadores com avarias activas
            $stmt_chk = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes WHERE autoravaria=(SELECT email FROM utilizadores WHERE id=?) AND datareparacao IS NULL");
            $stmt_chk->bind_param("i", $uid);
            $stmt_chk->execute();
            $n_avarias = $stmt_chk->get_result()->fetch_row()[0];
            $stmt_chk->close();

            if ($n_avarias > 0) {
                $msg_acao = 'Não é possível eliminar: utilizador tem avarias activas.';
                $msg_tipo = 'error';
            } else {
                $stmt_del = $db->prepare("DELETE FROM utilizadores WHERE id=? AND tipo!=1");
                $stmt_del->bind_param("i", $uid);
                $stmt_del->execute();
                $stmt_del->close();
                $msg_acao = 'Utilizador eliminado.';
                $msg_tipo = 'ok';
            }
        }

        // ── Eliminar selecionados ────────────────────────────────
        if ($acao === 'eliminar_selecionados' && !empty($_POST['uids'])) {
            $uids = array_map('intval', (array)$_POST['uids']);
            $eliminados = 0; $bloqueados = 0;
            foreach ($uids as $uid) {
                $stmt_chk2 = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes WHERE autoravaria=(SELECT email FROM utilizadores WHERE id=?) AND datareparacao IS NULL");
                $stmt_chk2->bind_param("i", $uid);
                $stmt_chk2->execute();
                $n = $stmt_chk2->get_result()->fetch_row()[0];
                $stmt_chk2->close();
                if ($n > 0) { $bloqueados++; continue; }
                $stmt_d = $db->prepare("DELETE FROM utilizadores WHERE id=? AND tipo!=1");
                $stmt_d->bind_param("i", $uid);
                $stmt_d->execute();
                $stmt_d->close();
                $eliminados++;
            }
            $msg_acao = "Eliminados: {$eliminados}.";
            if ($bloqueados > 0) $msg_acao .= " Bloqueados (avarias activas): {$bloqueados}.";
            $msg_tipo = $bloqueados > 0 ? 'warn' : 'ok';
        }
    }
}

// ─────────────────────────────────────────────
//  GERAR TOKEN CSRF
// ─────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ─────────────────────────────────────────────
//  QUERY: UTILIZADORES INATIVOS
// ─────────────────────────────────────────────
// Inativo = sem login há mais de X anos, OU nunca fez login e foi criado há mais de X anos
// Nunca eliminar tipo 1 (administrador)
$stmt_in = $db->prepare("
    SELECT id, nome, email, tipo, ultimo_login, created_at, notificado_retencao,
           CASE
               WHEN ultimo_login IS NOT NULL
                    THEN DATEDIFF(NOW(), ultimo_login)
               WHEN created_at IS NOT NULL
                    THEN DATEDIFF(NOW(), created_at)
               ELSE NULL
           END AS dias_inativos
    FROM utilizadores
    WHERE tipo != 1
      AND (
          (ultimo_login IS NOT NULL AND ultimo_login < DATE_SUB(NOW(), INTERVAL ? YEAR))
          OR
          (ultimo_login IS NULL AND (created_at IS NULL OR created_at < DATE_SUB(NOW(), INTERVAL ? YEAR)))
      )
    ORDER BY dias_inativos DESC
");
$stmt_in->bind_param("ii", $retencao_anos, $retencao_anos);
$stmt_in->execute();
$result_in = $stmt_in->get_result();
$total_inativos = $result_in->num_rows;
$stmt_in->close();

// ─────────────────────────────────────────────
//  ESTILOS
// ─────────────────────────────────────────────
$tipo_styles = [
    1 => ['label'=>'Administrador', 'color'=>'#6f42c1', 'bg'=>'#ede8fc'],
    2 => ['label'=>'Utilizador',    'color'=>'#00509e', 'bg'=>'#e0eeff'],
    3 => ['label'=>'Reparador',     'color'=>'#0891b2', 'bg'=>'#e0f5fb'],
    4 => ['label'=>'Funcionário',   'color'=>'#059669', 'bg'=>'#e0f7f0'],
];
?>

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
                        <li style="color:#1e2a45;">
                            <a href="<?php echo SVRURL ?>utiliz" style="color:#4b6cb7;text-decoration:none;">Utilizadores</a>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Utilizadores Inativos</li>
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

                    <?php if ($msg_acao): ?>
                    <div style="padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:.85rem;font-weight:600;
                        background:<?php echo $msg_tipo==='ok'?'#e9f7ef':($msg_tipo==='warn'?'#fff3cd':'#fdecea'); ?>;
                        color:<?php echo $msg_tipo==='ok'?'#1e8449':($msg_tipo==='warn'?'#7d4e00':'#c0392b'); ?>;
                        border-left:4px solid <?php echo $msg_tipo==='ok'?'#1cc88a':($msg_tipo==='warn'?'#e67e22':'#e74c3c'); ?>;">
                        <?php echo htmlspecialchars($msg_acao, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- ── Card de configuração ── -->
                    <div style="background:#fff;border:1px solid #e3e8f4;border-radius:10px;padding:18px 22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(75,108,183,.07);">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            <strong style="color:#182848;font-size:.9rem;">Política de Retenção</strong>
                            <span style="margin-left:auto;display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;
                                background:<?php echo $retencao_ativa?'#e9f7ef':'#f4f6fb'; ?>;
                                color:<?php echo $retencao_ativa?'#1e8449':'#7b88a0'; ?>;
                                border:1.5px solid <?php echo $retencao_ativa?'#1cc88a':'#c5cde0'; ?>;">
                                <?php echo $retencao_ativa ? '● Ativa' : '○ Inativa'; ?>
                            </span>
                        </div>
                        <form method="post" action="<?php echo SVRURL ?>utiliz_inativos" style="display:flex;align-items:flex-end;flex-wrap:wrap;gap:14px;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                            <input type="hidden" name="acao" value="config">
                            <div>
                                <label style="font-size:.75rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:4px;">Anos de inatividade</label>
                                <input type="number" name="retencao_anos" value="<?php echo $retencao_anos; ?>" min="1" max="10"
                                    style="width:70px;padding:6px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.85rem;font-weight:700;text-align:center;">
                            </div>
                            <div>
                                <label style="font-size:.75rem;font-weight:700;color:#7b88a0;display:block;margin-bottom:4px;">Dias de aviso prévio</label>
                                <input type="number" name="retencao_dias_aviso" value="<?php echo $retencao_dias_aviso; ?>" min="7" max="90"
                                    style="width:70px;padding:6px 8px;border:1.5px solid #c7d4f0;border-radius:6px;font-size:.85rem;font-weight:700;text-align:center;">
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;padding-bottom:2px;">
                                <label style="font-size:.8rem;font-weight:700;color:#1e2a45;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                    <input type="checkbox" name="retencao_ativa" value="1" <?php echo $retencao_ativa?'checked':''; ?>
                                        style="width:16px;height:16px;cursor:pointer;">
                                    Política ativa
                                </label>
                            </div>
                            <button type="submit" style="padding:7px 16px;border-radius:6px;background:#4b6cb7;color:#fff;border:none;font-size:.82rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                                Guardar configuração
                            </button>
                        </form>
                    </div>

                    <!-- ── Resumo ── -->
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
                        <div style="display:inline-flex;align-items:center;gap:7px;background:<?php echo $total_inativos>0?'#fff3cd':'#e9f7ef'; ?>;
                            color:<?php echo $total_inativos>0?'#7d4e00':'#1e8449'; ?>;
                            border:1px solid <?php echo $total_inativos>0?'#e67e22':'#1cc88a'; ?>;
                            border-radius:7px;padding:7px 14px;font-size:.82rem;font-weight:600;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <?php echo $total_inativos; ?> utilizador(es) inativo(s) há mais de <?php echo $retencao_anos; ?> ano(s)
                        </div>
                        <span style="font-size:.75rem;color:#7b88a0;">Administradores excluídos desta lista.</span>
                    </div>

                    <?php if ($total_inativos > 0): ?>
                    <!-- ── Tabela de inativos ── -->
                    <form method="post" action="<?php echo SVRURL ?>utiliz_inativos" id="form_selecionados">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                        <input type="hidden" name="acao" value="eliminar_selecionados">

                    <div style="background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(75,108,183,.10);border:1px solid #e3e8f4;overflow:hidden;margin-bottom:12px;">

                        <!-- Toolbar -->
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 16px;background:#f4f6fb;border-bottom:1px solid #e3e8f4;">
                            <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" id="selecionar_todos" style="width:15px;height:15px;">
                                Selecionar todos
                            </label>
                            <button type="submit" onclick="return confirm('Eliminar todos os utilizadores selecionados? Esta ação é irreversível.')"
                                style="padding:5px 14px;border-radius:6px;background:#fde8e6;color:#c0392b;border:1.5px solid #f5c0bb;font-size:.78rem;font-weight:700;cursor:pointer;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="vertical-align:middle;margin-right:3px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                Eliminar selecionados
                            </button>
                        </div>

                        <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
                            <thead>
                                <tr style="background:#182848;">
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;width:36px;"></th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;text-align:left;">Nome</th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;text-align:left;">Email</th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Tipo</th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Último login</th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Inativo há</th>
                                    <th style="padding:10px 12px;color:#fff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;text-align:center;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; while ($row = $result_in->fetch_assoc()): $i++;
                                $ts = $row['tipo'];
                                $tstyle = $tipo_styles[$ts] ?? ['label'=>$ts, 'color'=>'#7b88a0', 'bg'=>'#f0f0f0'];
                                $dias = (int)$row['dias_inativos'];
                                $anos_str = $dias >= 365 ? floor($dias/365).'a '.($dias%365).'d' : $dias.'d';
                                $ul = $row['ultimo_login'] ? date('d/m/Y H:i', strtotime($row['ultimo_login'])) : '—';
                                $notif = (int)$row['notificado_retencao'];
                                $row_bg = $i%2===0 ? '#f7f9fe' : '#fff';
                            ?>
                            <tr style="border-bottom:1px solid #eef1f8;background:<?php echo $row_bg; ?>;">
                                <td style="padding:10px 12px;text-align:center;">
                                    <input type="checkbox" name="uids[]" value="<?php echo $row['id']; ?>" class="chk_user" style="width:15px;height:15px;">
                                </td>
                                <td style="padding:10px 12px;font-weight:600;color:#1e2a45;">
                                    <?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ($notif): ?>
                                    <span style="margin-left:6px;display:inline-flex;align-items:center;padding:1px 7px;border-radius:20px;font-size:.68rem;font-weight:700;background:#fff3cd;color:#7d4e00;border:1px solid #e67e22;" title="Email de aviso enviado">
                                        ⚠ Notificado
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px;color:#5a6370;font-family:monospace;font-size:.8rem;">
                                    <?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td style="padding:10px 12px;text-align:center;">
                                    <span style="display:inline-flex;align-items:center;padding:2px 9px;border-radius:5px;font-size:.72rem;font-weight:700;
                                        background:<?php echo $tstyle['bg']; ?>;color:<?php echo $tstyle['color']; ?>;border:1.5px solid <?php echo $tstyle['color']; ?>;">
                                        <?php echo htmlspecialchars($tstyle['label'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td style="padding:10px 12px;text-align:center;color:#7b88a0;font-family:monospace;font-size:.78rem;">
                                    <?php echo $ul; ?>
                                </td>
                                <td style="padding:10px 12px;text-align:center;">
                                    <span style="font-weight:700;color:<?php echo $dias > 365*($retencao_anos+1) ? '#c0392b' : '#e67e22'; ?>;">
                                        <?php echo $anos_str; ?>
                                    </span>
                                </td>
                                <td style="padding:10px 12px;text-align:center;white-space:nowrap;">
                                    <!-- Reativar -->
                                    <form method="post" action="<?php echo SVRURL ?>utiliz_inativos" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                                        <input type="hidden" name="acao" value="reativar">
                                        <input type="hidden" name="uid" value="<?php echo $row['id']; ?>">
                                        <button type="submit" title="Reativar — repõe o contador de inatividade"
                                            style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:.73rem;font-weight:700;cursor:pointer;
                                            background:#e0f5fb;color:#0891b2;border:1.5px solid #0891b2;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                            Reativar
                                        </button>
                                    </form>
                                    <!-- Eliminar -->
                                    <form method="post" action="<?php echo SVRURL ?>utiliz_inativos" style="display:inline;margin-left:4px;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                                        <input type="hidden" name="acao" value="eliminar">
                                        <input type="hidden" name="uid" value="<?php echo $row['id']; ?>">
                                        <button type="submit"
                                            onclick="return confirm('Eliminar <?php echo htmlspecialchars(addslashes($row['nome']), ENT_QUOTES, 'UTF-8'); ?>? Esta ação é irreversível.')"
                                            title="Eliminar utilizador"
                                            style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:.73rem;font-weight:700;cursor:pointer;
                                            background:#fde8e6;color:#c0392b;border:1.5px solid #f5c0bb;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    </form>

                    <?php else: ?>
                    <!-- Sem inativos -->
                    <div style="text-align:center;padding:50px 20px;background:#fff;border-radius:10px;border:1px solid #e3e8f4;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1cc88a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:14px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <div style="font-size:1rem;font-weight:700;color:#182848;margin-bottom:6px;">Sem utilizadores inativos</div>
                        <div style="font-size:.85rem;color:#7b88a0;">Todos os utilizadores iniciaram sessão nos últimos <?php echo $retencao_anos; ?> ano(s).</div>
                    </div>
                    <?php endif; ?>

                    <br>
                
                 


<a href="<?php echo SVRURL ?>utiliz" title="Voltar">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Selecionar/desselecionar todos os checkboxes
document.getElementById('selecionar_todos')?.addEventListener('change', function() {
    document.querySelectorAll('.chk_user').forEach(c => c.checked = this.checked);
});
</script>

<?php include("footer.php"); ?>
</body>
</html>
