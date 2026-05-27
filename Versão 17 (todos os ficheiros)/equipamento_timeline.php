<?php
// ============================================================
// equipamento_timeline.php — Endpoint AJAX
// Devolve HTML da timeline de avarias de um equipamento
// Chamada: equipamento_timeline.php?id_equip=X
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_name('gei_session');
    session_start();
}

require_once 'config.php'; // $db (mysqli)

header('Content-Type: text/html; charset=utf-8');

// ── Validação ────────────────────────────────────────────────
$id_equip = (int)($_GET['id_equip'] ?? 0);
if ($id_equip <= 0) {
    http_response_code(400);
    echo '<p class="tl-erro"><i class="fas fa-exclamation-triangle"></i> ID de equipamento inválido.</p>';
    exit;
}

// ── Dados do equipamento ─────────────────────────────────────
$res_eq = mysqli_query($db,
    "SELECT e.nomeequi, e.tipo, s.nome AS sala
     FROM equipamento e
     JOIN salas s ON e.id_sala = s.id
     WHERE e.id = $id_equip
     LIMIT 1"
);
if (!$res_eq || mysqli_num_rows($res_eq) === 0) {
    http_response_code(404);
    echo '<p class="tl-erro"><i class="fas fa-exclamation-triangle"></i> Equipamento não encontrado.</p>';
    exit;
}
$equip = mysqli_fetch_assoc($res_eq);

// ── Avarias e reparações ordenadas cronologicamente ──────────
$res = mysqli_query($db,
    "SELECT
         ar.id,
         ar.dataavaria,
         ar.datareparacao,
         ar.avaria        AS descricao,
         ar.reparacao     AS solucao,
         ar.rep_efectuada_por AS tecnico,
         DATEDIFF(ar.datareparacao, ar.dataavaria) AS dias_resolucao
     FROM avarias_reparacoes ar
     WHERE ar.id_equi = $id_equip
     ORDER BY ar.dataavaria ASC, ar.id ASC"
);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}

// ── Estatísticas ─────────────────────────────────────────────
$total      = count($rows);
$abertas    = 0;
$resolvidas = 0;
$soma_dias  = 0;
$n_dias     = 0;

foreach ($rows as $r) {
    if ($r['datareparacao'] !== null && (int)$r['dias_resolucao'] >= 0) {
        $resolvidas++;
        $soma_dias += (int)$r['dias_resolucao'];
        $n_dias++;
    } else {
        $abertas++;
    }
}
$tempo_medio = $n_dias > 0 ? round($soma_dias / $n_dias, 1) : null;
$taxa_res    = $total > 0  ? round($resolvidas / $total * 100) : 0;

// ── Helper: formatar data ─────────────────────────────────────
function fmt_data_longa(?string $d): string {
    if (!$d || $d === '0000-00-00') return '—';
    $dt = DateTime::createFromFormat('Y-m-d', substr($d, 0, 10));
    if (!$dt) return $d;
    $meses = ['','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
    return $dt->format('d') . ' ' . $meses[(int)$dt->format('m')] . ' ' . $dt->format('Y');
}
?>

<!-- ══ Botão exportar PDF ════════════════════════════════════ -->
<div class="tl-pdf-bar">
    <a href="equipamento_timeline_pdf.php?id_equip=<?= $id_equip ?>" target="_blank" class="tl-btn-pdf">
        <i class="fas fa-file-pdf"></i> Exportar PDF
    </a>
</div>

<!-- ══ Estatísticas ══════════════════════════════════════════ -->
<div class="tl-stats-bar">
    <div class="tl-stat">
        <span class="tl-stat-val"><?= $total ?></span>
        <span class="tl-stat-label"><i class="fas fa-bolt"></i> Total avarias</span>
    </div>
    <div class="tl-stat tl-stat--danger">
        <span class="tl-stat-val"><?= $abertas ?></span>
        <span class="tl-stat-label"><i class="fas fa-exclamation-circle"></i> Em aberto</span>
    </div>
    <div class="tl-stat tl-stat--success">
        <span class="tl-stat-val"><?= $resolvidas ?></span>
        <span class="tl-stat-label"><i class="fas fa-check-circle"></i> Resolvidas</span>
    </div>
    <div class="tl-stat tl-stat--info">
        <span class="tl-stat-val"><?= $taxa_res ?>%</span>
        <span class="tl-stat-label"><i class="fas fa-chart-pie"></i> Taxa resolução</span>
    </div>
    <div class="tl-stat tl-stat--primary">
        <span class="tl-stat-val">
            <?= $tempo_medio !== null ? $tempo_medio : '—' ?>
            <?= $tempo_medio !== null ? '<small>dias</small>' : '' ?>
        </span>
        <span class="tl-stat-label"><i class="fas fa-hourglass-half"></i> Tempo médio</span>
    </div>
</div>

<?php if ($total === 0): ?>
<!-- ══ Estado vazio ══════════════════════════════════════════ -->
<div class="tl-empty">
    <i class="fas fa-check-double"></i>
    <p>Sem avarias registadas para este equipamento.</p>
</div>

<?php else: ?>
<!-- ══ Timeline ══════════════════════════════════════════════ -->
<div class="gei-timeline">
    <?php foreach ($rows as $i => $r):
        $resolvida  = $r['datareparacao'] !== null && (int)$r['dias_resolucao'] >= 0;
        $cls        = $resolvida ? 'resolved' : 'open';
        $icon       = $resolvida ? 'fa-check-circle' : 'fa-exclamation-circle';
        $data_av    = fmt_data_longa($r['dataavaria']);
        $data_rep   = fmt_data_longa($r['datareparacao']);
        $dias       = $resolvida ? (int)$r['dias_resolucao'] : null;
        $tecnico    = htmlspecialchars($r['tecnico'] ?? '', ENT_QUOTES, 'UTF-8');
        $descricao  = htmlspecialchars($r['descricao'] ?? '', ENT_QUOTES, 'UTF-8');
        $solucao    = htmlspecialchars($r['solucao'] ?? '', ENT_QUOTES, 'UTF-8');
        $num        = $i + 1;
    ?>
    <div class="tl-item <?= $cls ?>">

        <!-- Ponto / ícone -->
        <div class="tl-dot-wrap">
            <div class="tl-dot">
                <i class="fas <?= $icon ?>"></i>
            </div>
            <?php if ($i < $total - 1): ?>
            <div class="tl-line"></div>
            <?php endif; ?>
        </div>

        <!-- Card -->
        <div class="tl-card">
            <div class="tl-card-header">
                <div class="tl-card-meta">
                    <span class="tl-num">#<?= $num ?></span>
                    <span class="tl-date-av">
                        <i class="far fa-calendar-alt"></i> <?= $data_av ?>
                    </span>
                    <?php if ($tecnico): ?>
                    <span class="tl-tecnico">
                        <i class="fas fa-user-cog"></i> <?= $tecnico ?>
                    </span>
                    <?php endif; ?>
                </div>
                <span class="tl-badge tl-badge--<?= $cls ?>">
                    <i class="fas <?= $icon ?>"></i>
                    <?= $resolvida ? 'Resolvida' : 'Em aberto' ?>
                </span>
            </div>

            <?php if ($descricao): ?>
            <div class="tl-section">
                <span class="tl-section-label"><i class="fas fa-align-left"></i> Descrição</span>
                <p class="tl-text"><?= $descricao ?></p>
            </div>
            <?php endif; ?>

            <?php if ($resolvida): ?>
            <div class="tl-resolution">
                <div class="tl-resolution-header">
                    <i class="fas fa-tools"></i>
                    <span>Reparação — <?= $data_rep ?></span>
                    <?php if ($dias !== null): ?>
                    <span class="tl-dias">
                        <i class="fas fa-clock"></i>
                        <?= $dias === 0 ? 'mesmo dia' : "$dias dia" . ($dias === 1 ? '' : 's') ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php if ($solucao): ?>
                <p class="tl-text tl-solucao"><?= $solucao ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
