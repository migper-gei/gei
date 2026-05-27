    <?php
           
  $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

include("verifica_sessao.php");
  
  ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap');

/* ══ Barra de ações ══ */
.gei-action-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 6px;
    margin-bottom: 4px;
    padding: 6px 10px;
    background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid rgba(226,232,245,0.8);
    position: relative;
    overflow: hidden;
}

.gei-action-bar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, #e87722, #00509e, #6f42c1, #1cc88a);
    opacity: 0.6;
}

/* Divisor */
.gei-action-divider {
    flex-basis: 100%;
    height: 0;
    border-top: 1px solid rgba(228, 233, 240, 0.8);
    margin: 3px 0;
}

/* Botões de ação */
.gei-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: .70rem;
    font-weight: 700;
    font-family: 'Figtree', sans-serif;
    border: none;
    text-decoration: none !important;
    white-space: nowrap;
    transition: all .2s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    color: #fff !important;
    position: relative;
    letter-spacing: 0.01em;
}

.gei-action-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 8px;
    background: rgba(255,255,255,0);
    transition: background .2s;
}

.gei-action-btn:hover {
    transform: translateY(-2px) scale(1.03);
    text-decoration: none !important;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2) !important;
}

.gei-action-btn:hover::after {
    background: rgba(255,255,255,0.12);
}

.gei-action-btn:active {
    transform: translateY(0) scale(0.98);
}

.gei-action-btn span { color: #fff !important; }

/* Badge de contagem */
.gei-action-count {
    background: rgba(255,255,255,0.25);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 20px;
    padding: 1px 7px;
    font-size: 10px;
    font-weight: 800;
    color: #fff !important;
    min-width: 20px;
    text-align: center;
    letter-spacing: 0;
}

/* Responsivo */
@media (max-width: 600px) {
    .gei-action-btn {
        flex: 1 1 calc(50% - 5px);
        justify-content: center;
        font-size: .70rem;
    }
}

/* ── Cores dos botões ── */
.gei-action-btn-orange {
    background: linear-gradient(135deg, #f5911e 0%, #e87722 100%) !important;
    box-shadow: 0 3px 10px rgba(232,119,34,0.40);
    animation: gei-pulse-orange 2.5s infinite;
}
.gei-action-btn-navy {
    background: linear-gradient(135deg, #1a6fc4 0%, #00509e 100%) !important;
    box-shadow: 0 3px 10px rgba(0,80,158,0.35);
    animation: gei-pulse-blue 2.5s infinite;
}
.gei-action-btn-indigo {
    background: linear-gradient(135deg, #5c7fd4 0%, #4b6cb7 100%) !important;
    box-shadow: 0 2px 10px rgba(75,108,183,0.35);
}
.gei-action-btn-green {
    background: linear-gradient(135deg, #28dfa0 0%, #1cc88a 100%) !important;
    box-shadow: 0 2px 10px rgba(28,200,138,0.35);
}
.gei-action-btn-purple {
    background: linear-gradient(135deg, #8558d8 0%, #6f42c1 100%) !important;
    box-shadow: 0 2px 10px rgba(111,66,193,0.35);
}
.gei-action-btn-amber {
    background: linear-gradient(135deg, #f39c45 0%, #e67e22 100%) !important;
    box-shadow: 0 2px 10px rgba(230,126,34,0.35);
}
.gei-action-btn-steel {
    background: linear-gradient(135deg, #4a9fd4 0%, #2980b9 100%) !important;
    box-shadow: 0 2px 10px rgba(41,128,185,0.35);
}
.gei-action-btn-slate {
    background: linear-gradient(135deg, #8a94a2 0%, #6c757d 100%) !important;
    box-shadow: 0 2px 10px rgba(108,117,125,0.30);
}

@keyframes gei-pulse-orange {
    0%, 100% { box-shadow: 0 3px 10px rgba(232,119,34,0.40), 0 0 0 0 rgba(232,119,34,0.30); }
    60%       { box-shadow: 0 3px 10px rgba(232,119,34,0.40), 0 0 0 6px rgba(232,119,34,0); }
}
@keyframes gei-pulse-blue {
    0%, 100% { box-shadow: 0 3px 10px rgba(0,80,158,0.35), 0 0 0 0 rgba(0,80,158,0.25); }
    60%       { box-shadow: 0 3px 10px rgba(0,80,158,0.35), 0 0 0 6px rgba(0,80,158,0); }
}

/* ══ Banner de avarias pendentes ══ */
.gei-pend-bar {
    margin-top: 8px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(155,27,27,0.18), 0 1px 4px rgba(155,27,27,0.10);
    border: 1px solid rgba(220,80,80,0.20);
}

.gei-pend-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 14px;
    background: linear-gradient(90deg, #6b1414 0%, #9b1b1b 40%, #c0392b 100%);
    cursor: pointer;
    user-select: none;
    position: relative;
    overflow: hidden;
}

.gei-pend-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: repeating-linear-gradient(
        -45deg,
        rgba(255,255,255,0) 0px,
        rgba(255,255,255,0) 10px,
        rgba(255,255,255,0.025) 10px,
        rgba(255,255,255,0.025) 20px
    );
}

.gei-pend-header-icon {
    font-size: 14px;
    animation: gei-pend-pulse 1.6s infinite;
    position: relative;
    z-index: 1;
}

@keyframes gei-pend-pulse {
    0%,100% { opacity:1; transform: scale(1); }
    50%      { opacity:.6; transform: scale(0.9); }
}

.gei-pend-header-texto {
    flex: 1;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    font-family: 'Figtree', sans-serif;
    letter-spacing: 0.01em;
    position: relative;
    z-index: 1;
}

.gei-pend-header-texto small {
    font-weight: 400;
    opacity: .75;
    margin-left: 6px;
    font-size: 11px;
}

.gei-pend-chevron {
    color: rgba(255,255,255,.7);
    font-size: 11px;
    transition: transform .3s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    z-index: 1;
}
.gei-pend-chevron.aberto { transform: rotate(180deg); }

.gei-pend-fechar {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,.8);
    font-size: 12px;
    cursor: pointer;
    line-height: 1;
    padding: 3px 7px;
    border-radius: 6px;
    transition: all .2s;
    position: relative;
    z-index: 1;
}
.gei-pend-fechar:hover {
    background: rgba(255,255,255,0.22);
    color: #fff;
    transform: scale(1.05);
}

/* Botão de reabrir banner */
.gei-pend-reabrir {
    display: none;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 6px 13px;
    background: linear-gradient(90deg, #6b1414 0%, #9b1b1b 40%, #c0392b 100%);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    font-family: 'Figtree', sans-serif;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(155,27,27,0.30);
    transition: all .2s cubic-bezier(0.34, 1.56, 0.64, 1);
    animation: gei-pend-pulse 1.6s infinite;
}
.gei-pend-reabrir:hover {
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 6px 16px rgba(155,27,27,0.40);
}
.gei-pend-reabrir.visivel { display: inline-flex; }

/* Lista */
.gei-pend-lista {
    display: none;
    background: #fafafa;
    padding: 12px;
    gap: 8px;
    flex-wrap: wrap;
    border-top: 1px solid rgba(192,57,43,0.12);
}
.gei-pend-lista.aberta { display: flex; }

.gei-pend-card {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fff;
    border: 1px solid #f0d0d0;
    border-left: 4px solid #c0392b;
    border-radius: 10px;
    padding: 10px 13px;
    flex: 1 1 210px;
    max-width: 290px;
    transition: all .2s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.gei-pend-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(192,57,43,0.18);
    border-color: #e08080;
    text-decoration: none;
}

.gei-pend-card-icon {
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 2px;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(192,57,43,0.08);
    border-radius: 6px;
}

.gei-pend-card-nome {
    font-weight: 700;
    color: #1e2a45;
    font-size: 12px;
    font-family: 'Figtree', sans-serif;
    line-height: 1.3;
}
.gei-pend-card-local {
    font-size: 11px;
    color: #7b88a0;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.gei-pend-card-dias {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-top: 5px;
    color: #fff;
    border-radius: 5px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.02em;
}
.gei-pend-card-data {
    font-size: 10px;
    color: #a0aab8;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.gei-pend-card-action {
    font-size: 10px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 700;
    opacity: 0.85;
}
</style>

<?php
  $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null";
  $result1 = mysqli_query($db,$sql1); 
  $rows = mysqli_fetch_row($result1);
  $totallinhas = $rows[0];

  $sql2 = "select count(*) from tarefas where data_conclusao is null";
  $result2 = mysqli_query($db,$sql2); 
  $rows2 = mysqli_fetch_row($result2);
  $totallinhas2 = $rows2[0];

  $mostrar_reparacoes = ($totallinhas > 0 && isset($_SESSION['login_user']) && ($_SESSION['tipo']==1 || $_SESSION['tipo']==3));
  $mostrar_tarefas    = ($totallinhas2 > 0 && isset($_SESSION['login_user']) && $_SESSION['tipo']==1);
  $mostrar_dashboard  = (isset($_SESSION['login_user']) && $_SESSION['tipo']==1);
  $mostrar_avarias    = isset($_SESSION['login_user']);
  $mostrar_calendario = (isset($_SESSION['login_user']) && ($_SESSION['tipo']==1 || $_SESSION['tipo']==4));
  $mostrar_barra      = $mostrar_reparacoes || $mostrar_tarefas || $mostrar_dashboard || $mostrar_avarias || $mostrar_calendario || (isset($_SESSION['login_user']) && $_SESSION['tipo']==1);
?>

<?php if ($mostrar_barra): ?>
<div class="gei-action-bar">

    <?php if ($mostrar_reparacoes): ?>
    <a href="<?php echo SVRURL ?>reparafaz?op=t" class="gei-action-btn gei-action-btn-orange" title="Ver reparações a efetuar">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
        <span>Reparações a efetuar</span>
        <span class="gei-action-count"><?php echo $totallinhas; ?></span>
    </a>
    <?php endif; ?>

    <?php if ($mostrar_tarefas): ?>
    <a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&&z=<?php echo base64_encode(0) ?>" class="gei-action-btn gei-action-btn-navy" title="Ver tarefas a realizar">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
        <span>Tarefas por realizar</span>
        <span class="gei-action-count"><?php echo $totallinhas2; ?></span>
    </a>
    <?php endif; ?>

    <?php if (($mostrar_reparacoes || $mostrar_tarefas) && ($mostrar_dashboard || $mostrar_avarias || $mostrar_calendario)): ?>
    <div class="gei-action-divider"></div>
    <?php endif; ?>

    <?php if ($mostrar_dashboard): ?>
    <a href="<?php echo SVRURL ?>dash" class="gei-action-btn gei-action-btn-indigo" title="Ir para o Dashboard">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        <span>Dashboard</span>
    </a>
    <?php endif; ?>

    <?php if ($mostrar_avarias): ?>
    <a href="<?php echo SVRURL ?>poravar" class="gei-action-btn gei-action-btn-green" title="Estado das minhas avarias">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/><line x1="9" y1="9" x2="12" y2="9"/>
        </svg>
        <span>Estado das minhas avarias</span>
    </a>
    <?php endif; ?>

    <?php if ($mostrar_calendario): ?>
    <a href="<?php echo SVRURL ?>requisicoes_calendario_mensal.php" class="gei-action-btn gei-action-btn-purple" title="Calendário mensal de reservas">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span>Calendário de reservas</span>
    </a>
    <?php endif; ?>

    <?php if (isset($_SESSION['login_user']) && $_SESSION['tipo']==1): ?>
    <a href="<?php echo SVRURL ?>relatorio_idade_equipamento.php" class="gei-action-btn gei-action-btn-amber" title="Relatório de equipamentos por idade">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="8" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="13" y2="18"/>
        </svg>
        <span>Relatório por idade</span>
    </a>
    <a href="<?php echo SVRURL ?>planta_salas.php" class="gei-action-btn gei-action-btn-steel" title="Planta interactiva da instituição">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
            <line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/>
        </svg>
        <span>Planta da instituição</span>
    </a>
    <a href="<?php echo SVRURL ?>auditoria.php" class="gei-action-btn gei-action-btn-slate" title="Registos de auditoria do sistema">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="9" y1="13" x2="15" y2="13"/>
            <line x1="9" y1="17" x2="15" y2="17"/>
            <line x1="9" y1="9" x2="12" y2="9"/>
        </svg>
        <span>Auditoria</span>
    </a>
    <?php endif; ?>

</div>
<?php endif; ?>

<?php
// ── Banner avarias pendentes ──────────────────────────────────
$DIAS_ALERTA = 7;
if (isset($_SESSION['login_user']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3)):
    $sql_pend = "
        SELECT
            ar.id,
            ar.dataavaria,
            DATEDIFF(CURDATE(), ar.dataavaria) AS dias_pendente,
            e.nomeequi,
            e.id  AS id_equip,
            s.id  AS id_sala,
            es.id AS id_escola,
            s.nome AS sala,
            es.nome_escola AS escola
        FROM avarias_reparacoes ar
        JOIN equipamento e  ON ar.id_equi   = e.id
        JOIN salas s        ON ar.id_sala   = s.id
        JOIN escolas es     ON ar.id_escola = es.id
        WHERE ar.datareparacao IS NULL
          AND DATEDIFF(CURDATE(), ar.dataavaria) > $DIAS_ALERTA
        ORDER BY dias_pendente DESC
        LIMIT 10
    ";
    $res_pend    = mysqli_query($db, $sql_pend);
    $avisos_pend = [];
    if ($res_pend) {
        while ($av = mysqli_fetch_assoc($res_pend)) {
            $avisos_pend[] = $av;
        }
    }
    $total_pend = count($avisos_pend);
endif;
?>

<?php if (!empty($avisos_pend)): ?>
<div class="gei-pend-bar" id="gei-pend-bar">
    <div class="gei-pend-header" onclick="geiPendToggle()">
        <span class="gei-pend-header-icon">⚠️</span>
        <div class="gei-pend-header-texto">
            <?php if ($total_pend === 1): ?>
                1 avaria pendente há mais de <?= $DIAS_ALERTA ?> dias
                <small>— <?= htmlspecialchars($avisos_pend[0]['nomeequi'], ENT_QUOTES, 'UTF-8') ?> (<?= (int)$avisos_pend[0]['dias_pendente'] ?> dias)</small>
            <?php else: ?>
                <?= $total_pend ?> avarias pendentes há mais de <?= $DIAS_ALERTA ?> dias
                <small>— a mais antiga há <?= (int)$avisos_pend[0]['dias_pendente'] ?> dias</small>
            <?php endif; ?>
        </div>
        <i class="fas fa-chevron-down gei-pend-chevron" id="gei-pend-chevron"></i>
        <button class="gei-pend-fechar" onclick="event.stopPropagation();geiPendFechar()" title="Fechar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="gei-pend-lista" id="gei-pend-lista">
        <?php foreach ($avisos_pend as $av):
            $dias    = (int)$av['dias_pendente'];
            $cor     = $dias > 30 ? '#7b1c1c' : ($dias > 14 ? '#c0392b' : '#e67e22');
            $url_rep = SVRURL . 'reparacoes_efetuar_equip.php'
                     . '?ieq=' . base64_encode($av['id_equip'])
                     . '&&sai=' . base64_encode($av['id_sala'])
                     . '&&ies=' . base64_encode($av['id_escola']);
        ?>
        <a href="<?= htmlspecialchars($url_rep, ENT_QUOTES, 'UTF-8') ?>" class="gei-pend-card" title="Clique para reparar" style="text-decoration:none;">
            <div class="gei-pend-card-icon">
                <i class="fas fa-exclamation-circle" style="color:<?= $cor ?>;font-size:15px;"></i>
            </div>
            <div style="min-width:0;">
                <div class="gei-pend-card-nome">
                    <?= htmlspecialchars($av['nomeequi'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="gei-pend-card-local">
                    <i class="fas fa-door-open" style="font-size:10px;"></i>
                    <?= htmlspecialchars($av['sala'], ENT_QUOTES, 'UTF-8') ?> —
                    <?= htmlspecialchars($av['escola'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <span class="gei-pend-card-dias" style="background:<?= $cor ?>">
                    <i class="fas fa-clock" style="font-size:9px;"></i>
                    <?= $dias ?> dia<?= $dias === 1 ? '' : 's' ?> sem reparação
                </span>
                <div class="gei-pend-card-data">
                    <i class="far fa-calendar-alt" style="font-size:9px;"></i>
                    Avaria em <?= date('d/m/Y', strtotime($av['dataavaria'])) ?>
                </div>
                <div class="gei-pend-card-action" style="color:<?= $cor ?>">
                    <i class="fas fa-tools" style="font-size:9px;"></i> Clique para reparar
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<button class="gei-pend-reabrir" id="gei-pend-reabrir" onclick="geiPendReabrir()" title="Ver avarias pendentes">
    ⚠️ <?= $total_pend ?> avaria<?= $total_pend === 1 ? '' : 's' ?> pendente<?= $total_pend === 1 ? '' : 's' ?> — clique para ver
</button>

<script>
function geiPendToggle() {
    var lista   = document.getElementById('gei-pend-lista');
    var chevron = document.getElementById('gei-pend-chevron');
    var aberta  = lista.classList.toggle('aberta');
    chevron.classList.toggle('aberto', aberta);
}
function geiPendFechar() {
    var bar = document.getElementById('gei-pend-bar');
    var btn = document.getElementById('gei-pend-reabrir');
    sessionStorage.setItem('gei_pend_fechado', '1');
    bar.style.opacity = '0';
    bar.style.transform = 'translateY(-4px)';
    bar.style.transition = 'opacity .25s, transform .25s';
    setTimeout(function(){
        bar.style.display = 'none';
        if (btn) btn.classList.add('visivel');
    }, 250);
}
function geiPendReabrir() {
    var bar = document.getElementById('gei-pend-bar');
    var btn = document.getElementById('gei-pend-reabrir');
    sessionStorage.removeItem('gei_pend_fechado');
    bar.style.display = '';
    bar.style.opacity = '0';
    bar.style.transform = 'translateY(-4px)';
    bar.style.transition = 'opacity .25s, transform .25s';
    if (btn) btn.classList.remove('visivel');
    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            bar.style.opacity = '1';
            bar.style.transform = 'translateY(0)';
        });
    });
}
// Aplicar estado guardado assim que o DOM estiver pronto
(function(){
    if (sessionStorage.getItem('gei_pend_fechado') === '1') {
        var bar = document.getElementById('gei-pend-bar');
        var btn = document.getElementById('gei-pend-reabrir');
        if (bar) bar.style.display = 'none';
        if (btn) btn.classList.add('visivel');
    }
})();
</script>
<?php endif; ?>
