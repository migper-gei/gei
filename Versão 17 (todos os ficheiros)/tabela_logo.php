<style>
    .gei-logo-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 6px 0;
        text-decoration: none;
    }

    .gei-logo-img-box {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 10px;
        overflow: hidden;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(56,189,248,0.20);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.25), 0 0 0 1px rgba(56,189,248,0.08);
        transition: box-shadow 0.25s ease;
    }

    .gei-logo-img-box:hover {
        box-shadow: 0 4px 18px rgba(56,189,248,0.20), 0 0 0 1px rgba(56,189,248,0.25);
    }

    .gei-logo-img-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .gei-logo-no-img {
        font-size: 10px;
        color: rgba(255,255,255,0.4);
        font-family: var(--font-body);
        text-align: center;
        padding: 4px;
        line-height: 1.2;
    }

    .gei-logo-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow: hidden;
    }

    .gei-logo-school {
        font-family: var(--font-heading); /* Syne substituída por Raleway */
        font-size: 13.5px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.01em;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .gei-logo-site {
        font-family: var(--font-body);
        font-size: 10.5px;
        font-weight: 400;
        color: rgba(56,189,248,0.80);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        text-decoration: none;
        transition: color 0.2s;
        letter-spacing: 0.01em;
    }

    .gei-logo-site:hover {
        color: #38bdf8;
        text-decoration: underline;
    }

    .gei-logo-badge {
        font-family: var(--font-body);
        font-size: 9.5px;
        font-weight: 500;
        color: rgba(255,255,255,0.35);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
</style>

<?php
$sql    = "SELECT COUNT(*) FROM logotipo";
$result = mysqli_query($db, $sql);
$count  = mysqli_fetch_row($result);

$hasRecord = ($count[0] == 1);
$row2      = null;

if ($hasRecord) {
    $sql2   = "SELECT * FROM logotipo";
    $result2 = mysqli_query($db, $sql2);
    $row2   = mysqli_fetch_array($result2);
}

$hasLogo    = $hasRecord && !empty($row2['logotipo']);
$hasName    = $hasRecord && !empty($row2['nomeescola']);
$hasSite    = $hasRecord && !empty($row2['site']);
?>

<div class="gei-logo-wrap">

    <!-- Imagem do logotipo -->
    <div class="gei-logo-img-box">
        <?php if ($hasLogo): ?>
            <img title="Logotipo"
                 src="data:image/jpeg;base64,<?php echo base64_encode($row2['logotipo']); ?>"
                 alt="Logotipo">
        <?php elseif ($hasRecord): ?>
            <img title="Logotipo"
                 src="<?php echo SVRURL ?>images/gei_icon_2.png"
                 alt="GEI">
        <?php else: ?>
            <img title="Logotipo"
                 src="<?php echo SVRURL ?>images/gei_icon_2.png"
                 alt="GEI">
        <?php endif; ?>
    </div>

    <!-- Texto / nome da escola -->
    <div class="gei-logo-text">
        <?php if ($hasName): ?>
            <span class="gei-logo-school"><?php echo htmlspecialchars($row2['nomeescola']); ?></span>
        <?php else: ?>
            <span class="gei-logo-school">GEI</span>
            <?php if (!$hasRecord): ?>
                <span class="gei-logo-badge">Logotipo não inserido</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($hasSite): ?>
            <a class="gei-logo-site"
               title="<?php echo htmlspecialchars($row2['site']); ?>"
               href="<?php echo htmlspecialchars($row2['site']); ?>"
               target="_blank" rel="noopener">
                <?php echo htmlspecialchars($row2['site']); ?>
            </a>
        <?php endif; ?>
    </div>

</div>
