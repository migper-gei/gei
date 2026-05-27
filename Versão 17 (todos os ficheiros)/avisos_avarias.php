<?php
// ============================================================
// avisos_avarias.php
// Banner de alerta para avarias pendentes há mais de X dias
// ============================================================

// Só mostra para utilizadores autenticados
if (!isset($_SESSION['login_user'])) return;

// ── Configuração ─────────────────────────────────────────────
$DIAS_ALERTA = 7; // Avarias pendentes há mais de X dias disparam alerta

// ── Query: avarias sem reparação há mais de $DIAS_ALERTA dias ─
$sql_avisos = "
    SELECT
        ar.id,
        ar.dataavaria,
        DATEDIFF(CURDATE(), ar.dataavaria) AS dias_pendente,
        e.nomeequi,
        e.id AS id_equip,
        s.nome AS sala,
        es.nome_escola AS escola
    FROM avarias_reparacoes ar
    JOIN equipamento e  ON ar.id_equi  = e.id
    JOIN salas s        ON ar.id_sala  = s.id
    JOIN escolas es     ON ar.id_escola = es.id
    WHERE ar.datareparacao IS NULL
      AND DATEDIFF(CURDATE(), ar.dataavaria) > $DIAS_ALERTA
    ORDER BY dias_pendente DESC
    LIMIT 10
";

$res_avisos = mysqli_query($db, $sql_avisos);
if (!$res_avisos || mysqli_num_rows($res_avisos) === 0) return;

$avisos = [];
while ($av = mysqli_fetch_assoc($res_avisos)) {
    $avisos[] = $av;
}
$total_avisos = count($avisos);
?>

<!-- ═══ BANNER AVARIAS PENDENTES ════════════════════════════ -->
<style>
.gei-avisos-bar {
    background: linear-gradient(90deg, #7b1c1c 0%, #c0392b 100%);
    color: #fff;
    padding: 0;
    font-size: 13px;
    position: relative;
    z-index: 999;
    box-shadow: 0 2px 8px rgba(192,57,43,.25);
}
.gei-avisos-inner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 18px;
    flex-wrap: wrap;
}
.gei-avisos-icon {
    font-size: 18px;
    flex-shrink: 0;
    animation: gei-pulse 1.5s infinite;
}
@keyframes gei-pulse {
    0%,100% { opacity: 1; }
    50%      { opacity: .5; }
}
.gei-avisos-texto {
    flex: 1;
    font-weight: 600;
    min-width: 0;
}
.gei-avisos-texto span {
    font-weight: 400;
    opacity: .85;
    margin-left: 6px;
}
.gei-avisos-toggle {
    background: rgba(255,255,255,.18);
    border: none;
    color: #fff;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px;
    cursor: pointer;
    white-space: nowrap;
    transition: background .2s;
}
.gei-avisos-toggle:hover { background: rgba(255,255,255,.3); }
.gei-avisos-fechar {
    background: none;
    border: none;
    color: rgba(255,255,255,.7);
    font-size: 18px;
    cursor: pointer;
    line-height: 1;
    padding: 0 2px;
    transition: color .2s;
}
.gei-avisos-fechar:hover { color: #fff; }

/* Lista expandida */
.gei-avisos-lista {
    display: none;
    background: #fff;
    border-top: 3px solid #c0392b;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
}
.gei-avisos-lista.aberta { display: block; }
.gei-avisos-lista-inner {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px 18px;
    max-height: 260px;
    overflow-y: auto;
}
.gei-aviso-card {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fff5f5;
    border: 1px solid #f5c6c6;
    border-left: 4px solid #c0392b;
    border-radius: 8px;
    padding: 10px 14px;
    min-width: 220px;
    max-width: 300px;
    flex: 1;
}
.gei-aviso-card-icon {
    color: #c0392b;
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}
.gei-aviso-card-info { flex: 1; min-width: 0; }
.gei-aviso-card-nome {
    font-weight: 700;
    color: #1e2a45;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.gei-aviso-card-local {
    font-size: 11px;
    color: #7b88a0;
    margin-top: 2px;
}
.gei-aviso-card-dias {
    display: inline-block;
    margin-top: 5px;
    background: #c0392b;
    color: #fff;
    border-radius: 4px;
    padding: 1px 7px;
    font-size: 11px;
    font-weight: 700;
}
.gei-aviso-card-data {
    font-size: 11px;
    color: #a0aab8;
    margin-top: 3px;
}
</style>

<div class="gei-avisos-bar" id="gei-avisos-bar">
    <div class="gei-avisos-inner">
        <span class="gei-avisos-icon">⚠️</span>
        <div class="gei-avisos-texto">
            <?php if ($total_avisos === 1): ?>
                1 avaria pendente há mais de <?= $DIAS_ALERTA ?> dias
                <span>— <?= htmlspecialchars($avisos[0]['nomeequip'] ?? $avisos[0]['nomeequi'], ENT_QUOTES, 'UTF-8') ?> (<?= (int)$avisos[0]['dias_pendente'] ?> dias)</span>
            <?php else: ?>
                <?= $total_avisos ?> avarias pendentes há mais de <?= $DIAS_ALERTA ?> dias
                <span>— a mais antiga há <?= (int)$avisos[0]['dias_pendente'] ?> dias</span>
            <?php endif; ?>
        </div>
        <button class="gei-avisos-toggle" onclick="geiAvisosToggle()">
            <i class="fas fa-chevron-down" id="gei-avisos-chevron"></i> Ver detalhes
        </button>
        <button class="gei-avisos-fechar" onclick="geiAvisosFechar()" title="Fechar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Lista expandida -->
    <div class="gei-avisos-lista" id="gei-avisos-lista">
        <div class="gei-avisos-lista-inner">
            <?php foreach ($avisos as $av):
                $dias = (int)$av['dias_pendente'];
                $cor  = $dias > 30 ? '#7b1c1c' : ($dias > 14 ? '#c0392b' : '#e67e22');
            ?>
            <div class="gei-aviso-card">
                <div class="gei-aviso-card-icon">
                    <i class="fas fa-exclamation-circle" style="color:<?= $cor ?>"></i>
                </div>
                <div class="gei-aviso-card-info">
                    <div class="gei-aviso-card-nome">
                        <?= htmlspecialchars($av['nomeequi'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="gei-aviso-card-local">
                        <i class="fas fa-door-open"></i>
                        <?= htmlspecialchars($av['sala'], ENT_QUOTES, 'UTF-8') ?> —
                        <?= htmlspecialchars($av['escola'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <span class="gei-aviso-card-dias" style="background:<?= $cor ?>">
                        <?= $dias ?> dia<?= $dias === 1 ? '' : 's' ?> sem reparação
                    </span>
                    <div class="gei-aviso-card-data">
                        <i class="far fa-calendar-alt"></i>
                        Avaria registada em <?= date('d/m/Y', strtotime($av['dataavaria'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function geiAvisosToggle() {
    var lista   = document.getElementById('gei-avisos-lista');
    var chevron = document.getElementById('gei-avisos-chevron');
    var aberta  = lista.classList.toggle('aberta');
    chevron.className = aberta ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
}
function geiAvisosFechar() {
    document.getElementById('gei-avisos-bar').style.display = 'none';
}
</script>
<!-- ═══════════════════════════════════════════════════════════ -->
