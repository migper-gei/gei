<?php
// ============================================================
// ficha_equipamento.php — GEI
// Ficha de dados do equipamento (só leitura).
// Acesso: Administrador (tipo 1) e Reparador (tipo 3).
// URL: ficha_equipamento.php?ide=BASE64(id)&&sai=BASE64(sala)&&ies=BASE64(escola)
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

include_once('svrurl.php');
include_once('config.php');
include('sessao_timeout.php');

// Só admin e reparador
$tipo = (int)($_SESSION['tipo'] ?? 0);
if (!isset($_SESSION['login_user']) || !in_array($tipo, [1, 3])) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

// ── Parâmetros ────────────────────────────────────────────────────────────────
$id       = isset($_GET['ide']) ? (int)base64_decode($_GET['ide']) : 0;
$sa       = isset($_GET['sai']) ? (int)base64_decode($_GET['sai']) : 0;
$idescola = isset($_GET['ies']) ? (int)base64_decode($_GET['ies']) : 0;

if ($id <= 0 || $sa <= 0 || $idescola <= 0) {
    header('Location: ' . SVRURL . 'equip');
    exit();
}

// ── Query principal ───────────────────────────────────────────────────────────
$stmt = $db->prepare("
    SELECT
        eq.nomeequi, eq.tipo, eq.numserie, eq.marca_modelo,
        eq.processador, eq.memoria, eq.disco,
        eq.placagrafica, eq.placarede, eq.placasom,
        eq.monitor, eq.teclado, eq.tecladointerface,
        eq.rato, eq.ratointerface, eq.colunas, eq.cd_dvd,
        eq.dominio, eq.ip, eq.mascara_rede, eq.gateway,
        eq.dns_principal, eq.dns_alternativo,
        eq.data_compra, eq.observacoes,
        eq.escola_digital, eq.num_inv_dgest, eq.fornecedor,
        eq.email_fornecedor,
        s.nome   AS nomsala,
        es.nome_escola,
        EXISTS (
            SELECT 1 FROM avarias_reparacoes ar
            WHERE ar.id_equi = eq.id AND ar.datareparacao IS NULL
        ) AS avariado
    FROM equipamento eq
    INNER JOIN salas s    ON s.id  = eq.id_sala
    INNER JOIN escolas es ON es.id = s.id_escola
    WHERE eq.id = ? AND s.id = ? AND es.id = ?
    LIMIT 1
");
$stmt->bind_param('iii', $id, $sa, $idescola);
$stmt->execute();
$eq = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$eq) {
    header('Location: ' . SVRURL . 'equip');
    exit();
}

// Helper: mostra valor ou "—"
function val($v) {
    $v = trim((string)$v);
    return $v !== '' ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : '<span style="color:#b0bac8;">—</span>';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <style>
        :root {
            --primary:    #182848;
            --primary-lt: #4b6cb7;
            --accent:     #507feb;
            --success:    #1cc88a;
            --danger:     #e74a3b;
            --warn:       #f6a623;
            --bg:         #f0f4fb;
            --surface:    #ffffff;
            --border:     #e3e8f4;
            --text:       #1e2a45;
            --muted:      #7b88a0;
            --radius:     10px;
        }

        .ficha-wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 12px 40px;
        }

        /* ── Cabeçalho do equipamento ── */
        .ficha-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            background: var(--primary);
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 16px 22px;
            color: #fff;
        }
        .ficha-header-icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,.12);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ficha-header-title { font-size: 1.15rem; font-weight: 700; }
        .ficha-header-sub   { font-size: .8rem; color: rgba(255,255,255,.65); margin-top: 2px; }
        .ficha-estado {
            margin-left: auto;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .estado-ok       { background: #e0f7f0; color: #13a073; }
        .estado-avariado { background: #fde8e8; color: var(--danger); }

        /* ── Grid de 3 colunas ── */
        .ficha-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 var(--radius) var(--radius);
            overflow: hidden;
            background: var(--surface);
        }
        @media (max-width: 680px) {
            .ficha-grid { grid-template-columns: 1fr; }
        }

        /* ── Coluna ── */
        .ficha-col { padding: 0; }
        .ficha-col + .ficha-col { border-left: 1px solid var(--border); }

        .ficha-col-head {
            background: #1e2e50;
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 9px 16px;
        }

        /* ── Linhas de dados ── */
        .ficha-row {
            display: flex;
            flex-direction: column;
            padding: 9px 16px;
            border-bottom: 1px solid var(--border);
            font-size: .84rem;
        }
        .ficha-row:last-child { border-bottom: none; }
        .ficha-row-label {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .ficha-row-value {
            color: var(--text);
            font-weight: 600;
            word-break: break-word;
        }

        /* ── Observações (largura total) ── */
        .ficha-obs {
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 var(--radius) var(--radius);
            background: var(--surface);
            padding: 14px 18px;
            font-size: .84rem;
            color: var(--text);
        }
        .ficha-obs-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--muted);
            margin-bottom: 5px;
        }

        /* ── Botões de acção ── */
        .ficha-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .btn-ficha {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: .83rem;
            font-weight: 700;
            text-decoration: none;
            transition: opacity .15s;
            border: none;
            cursor: pointer;
        }
        .btn-ficha:hover { opacity: .85; text-decoration: none; }
        .btn-primary-f { background: var(--primary-lt); color: #fff; }
        .btn-danger-f  { background: var(--danger);     color: #fff; }
        .btn-back-f    { background: var(--border);     color: var(--text); }

        /* ── Escola Digital badge ── */
        .badge-escdig {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            background: #e8f4fd;
            color: #1a6fa8;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            margin-top: 4px;
        }
    </style>
</head>
<body class="main-layout">
    <?php include('loader.php'); ?>
    <?php include('header.php'); ?>
    <?php include('sessao_timeout.php'); ?>

    <div class="about">
        <div class="container">
            <div class="ficha-wrap">

                <!-- Breadcrumb -->
                <nav style="margin-bottom:14px;">
                    <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li>
                            <a href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($sa) ?>&&ies=<?php echo base64_encode($idescola) ?>"
                               style="color:#4b6cb7;text-decoration:none;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                Equipamentos
                            </a>
                        </li>
                        <li style="color:#c5cde0;">&#8250;</li>
                        <li style="color:#1e2a45;"><?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></li>
                    </ol>
                </nav>

                <!-- Cabeçalho -->
                <div class="ficha-header">
                    <div class="ficha-header-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    </div>
                    <div>
                        <div class="ficha-header-title"><?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="ficha-header-sub">
                            <?php echo htmlspecialchars($eq['nomsala'], ENT_QUOTES, 'UTF-8'); ?>
                            &nbsp;·&nbsp;
                            <?php echo htmlspecialchars($eq['nome_escola'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php if ($eq['escola_digital'] === 'Sim'): ?>
                        <span class="badge-escdig">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#1a6fa8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Escola Digital
                        </span>
                        <?php endif; ?>
                    </div>
                    <span class="ficha-estado <?php echo $eq['avariado'] ? 'estado-avariado' : 'estado-ok'; ?>">
                        <?php if ($eq['avariado']): ?>
                            🔧 Avariado
                        <?php else: ?>
                            ✓ Operacional
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Grid 3 colunas -->
                <div class="ficha-grid">

                    <!-- COLUNA 1: TIPO / NOME -->
                    <div class="ficha-col">
                        <div class="ficha-col-head">Tipo / Nome</div>

                        <div class="ficha-row">
                            <span class="ficha-row-label">Tipo</span>
                            <span class="ficha-row-value"><?php echo val($eq['tipo']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Nome</span>
                            <span class="ficha-row-value"><?php echo val($eq['nomeequi']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Sala</span>
                            <span class="ficha-row-value"><?php echo val($eq['nomsala']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Escola Digital</span>
                            <span class="ficha-row-value"><?php echo val($eq['escola_digital']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Estado</span>
                            <span class="ficha-row-value" style="color:<?php echo $eq['avariado'] ? '#e74a3b' : '#13a073'; ?>; font-weight:700;">
                                <?php echo $eq['avariado'] ? '🔧 Avariado' : '✓ Operacional'; ?>
                            </span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Data da compra</span>
                            <span class="ficha-row-value">
                                <?php
                                $dc = trim($eq['data_compra'] ?? '');
                                echo $dc ? htmlspecialchars($dc, ENT_QUOTES, 'UTF-8') : '<span style="color:#b0bac8;">—</span>';
                                ?>
                            </span>
                        </div>
                        <?php if ($eq['escola_digital'] === 'Sim'): ?>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Nº Inv. DGEST</span>
                            <span class="ficha-row-value"><?php echo val($eq['num_inv_dgest']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Fornecedor</span>
                            <span class="ficha-row-value"><?php echo val($eq['fornecedor']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- COLUNA 2: DADOS TÉCNICOS -->
                    <div class="ficha-col">
                        <div class="ficha-col-head">Dados Técnicos</div>

                        <div class="ficha-row">
                            <span class="ficha-row-label">Nº Série</span>
                            <span class="ficha-row-value"><?php echo val($eq['numserie']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Marca / Modelo</span>
                            <span class="ficha-row-value"><?php echo val($eq['marca_modelo']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">CPU</span>
                            <span class="ficha-row-value"><?php echo val($eq['processador']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">RAM (GB)</span>
                            <span class="ficha-row-value"><?php echo val($eq['memoria']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Disco (GB)</span>
                            <span class="ficha-row-value"><?php echo val($eq['disco']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Gráfica</span>
                            <span class="ficha-row-value"><?php echo val($eq['placagrafica']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Som</span>
                            <span class="ficha-row-value"><?php echo val($eq['placasom']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Rede</span>
                            <span class="ficha-row-value"><?php echo val($eq['placarede']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Monitor</span>
                            <span class="ficha-row-value"><?php echo val($eq['monitor']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Teclado</span>
                            <span class="ficha-row-value">
                                <?php echo val($eq['teclado']); ?>
                                <?php if (trim($eq['tecladointerface'] ?? '') !== ''): ?>
                                    <span style="color:var(--muted);font-size:.8em;margin-left:4px;">(<?php echo val($eq['tecladointerface']); ?>)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Rato</span>
                            <span class="ficha-row-value">
                                <?php echo val($eq['rato']); ?>
                                <?php if (trim($eq['ratointerface'] ?? '') !== ''): ?>
                                    <span style="color:var(--muted);font-size:.8em;margin-left:4px;">(<?php echo val($eq['ratointerface']); ?>)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if (trim($eq['colunas'] ?? '') !== ''): ?>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Colunas</span>
                            <span class="ficha-row-value"><?php echo val($eq['colunas']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (trim($eq['cd_dvd'] ?? '') !== ''): ?>
                        <div class="ficha-row">
                            <span class="ficha-row-label">CD/DVD</span>
                            <span class="ficha-row-value"><?php echo val($eq['cd_dvd']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- COLUNA 3: DADOS DE REDE -->
                    <div class="ficha-col">
                        <div class="ficha-col-head">Dados de Rede</div>

                        <div class="ficha-row">
                            <span class="ficha-row-label">Domínio</span>
                            <span class="ficha-row-value"><?php echo val($eq['dominio']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">IP</span>
                            <span class="ficha-row-value"><?php echo val($eq['ip']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Máscara</span>
                            <span class="ficha-row-value"><?php echo val($eq['mascara_rede']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">Gateway</span>
                            <span class="ficha-row-value"><?php echo val($eq['gateway']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">DNS Principal</span>
                            <span class="ficha-row-value"><?php echo val($eq['dns_principal']); ?></span>
                        </div>
                        <div class="ficha-row">
                            <span class="ficha-row-label">DNS Alternativo</span>
                            <span class="ficha-row-value"><?php echo val($eq['dns_alternativo']); ?></span>
                        </div>

                        <?php if (trim($eq['observacoes'] ?? '') !== ''): ?>
                        <div class="ficha-row" style="flex:1;">
                            <span class="ficha-row-label" style="font-style:italic;">Observações</span>
                            <span class="ficha-row-value" style="white-space:pre-line;font-weight:400;">
                                <?php echo htmlspecialchars($eq['observacoes'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="ficha-row">
                            <span class="ficha-row-label" style="font-style:italic;">Observações</span>
                            <span class="ficha-row-value"><span style="color:#b0bac8;">—</span></span>
                        </div>
                        <?php endif; ?>
                    </div>

                </div><!-- /ficha-grid -->

                <!-- Botões de acção -->
                <div class="ficha-actions">
                    <?php if ($tipo === 1 && empty($_SESSION['qr_temp'])): // Só admin autenticado pode editar ?>
                    <a href="<?php echo SVRURL ?>atualiequip?ide=<?php echo base64_encode($id) ?>&&sai=<?php echo base64_encode($sa) ?>&&ies=<?php echo base64_encode($idescola) ?>"
                       class="btn-ficha btn-primary-f">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar equipamento
                    </a>
                    <?php endif; ?>

                    <?php if (empty($_SESSION['qr_temp'])): ?>
                    <a href="<?php echo SVRURL ?>avarias_equipamento.php?ei=<?php echo base64_encode($id) ?>&&ies=<?php echo base64_encode($idescola) ?>"
                       class="btn-ficha btn-danger-f">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Ver avarias
                    </a>

                    <a href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($sa) ?>&&ies=<?php echo base64_encode($idescola) ?>"
                       class="btn-ficha btn-back-f">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Voltar à sala
                    </a>

                    <a href="<?php echo SVRURL ?>planta_salas.php?esc=<?php echo base64_encode($idescola) ?>"
                       class="btn-ficha btn-back-f">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Voltar à Planta
                    </a>
                    <?php else: ?>
                    <?php
                    $url_qr = SVRURL . 'qr_acesso.php'
                        . '?eq='   . $id
                        . '&sala='  . $sa
                        . '&esc='   . $idescola
                        . '&cod='   . (int)($_SESSION['_codigo_qr'] ?? 0);
                    // limpar sessão temporária ao sair
                    ?>
                    <a href="<?php echo htmlspecialchars($url_qr); ?>"
                       class="btn-ficha btn-back-f"
                       onclick="fetch('<?php echo SVRURL ?>logout_qr.php');">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Voltar ao QR
                    </a>
                    <?php endif; ?>
                </div>

            </div><!-- /ficha-wrap -->
        </div><!-- /container -->
    </div><!-- /about -->

    <?php include('footer.php'); ?>
</body>
</html>
