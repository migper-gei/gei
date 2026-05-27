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
<?php include ("head.php"); ?>
   </head>
   <body class="main-layout">
      <?php include("loader.php"); ?>
     <?php include ("header.php"); ?>
     <?php include("sessao_timeout.php"); ?>
<?php

$sql2a    = "SELECT MAX(id) AS me FROM escolas";
$result2a = mysqli_query($db, $sql2a);
$rows2a   = mysqli_fetch_row($result2a);
$maxesc   = $rows2a[0];

$x        = (int)base64_decode($_GET["x"]);
$idescola = (int)base64_decode($_GET["ies"]);

if (
    $idescola > $maxesc || $idescola < 0
    || $x > 1 || $x < 0
    || !isset($x)        || !is_numeric($x)
    || !isset($idescola) || empty($idescola) || !is_numeric($idescola)
) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>lista'; },10);</script>
<?php
}

$sql11    = "SELECT nome_escola FROM escolas WHERE id=$idescola";
$result11 = mysqli_query($db, $sql11);
$rows11   = mysqli_fetch_row($result11);
$ne       = $rows11[0];
$num_ne   = mysqli_num_rows($result11);

if ($num_ne == 0) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>lista'; },10);</script>
<?php } ?>

      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Listagens</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Quantidade de ratos por sala</li>
                  </ol>
               </nav>
               <div class="titlepage"></div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
<style>
/* === Desktop === */
.gei-table-wrap{background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(75,108,183,.10);border:1px solid #e3e8f4;overflow:hidden;margin-bottom:20px;}
.gei-table-section-header{padding:10px 16px;background:#182848;color:#fff;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.gei-table{width:100%;border-collapse:collapse;font-size:.84rem;}
.gei-table thead th{padding:10px 14px;background:#253d6e;color:#fff;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;border:none;}
.gei-table tbody tr{border-bottom:1px solid #eef1f8;transition:background .15s;}
.gei-table tbody tr:last-child{border-bottom:none;}
.gei-table tbody tr:hover{background:#f0f4fb;}
.gei-table tbody tr:nth-child(even){background:#f7f9fe;}
.gei-table td{padding:10px 14px;vertical-align:middle;color:#1e2a45;}
.gei-badge{display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.78rem;font-weight:700;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;}
.gei-total-badge{display:inline-flex;align-items:center;padding:1px 8px;border-radius:4px;font-size:.72rem;font-weight:600;background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.35);margin-left:6px;vertical-align:middle;}

/* === Mobile === */
@media(max-width:768px){

    /* Wrapper ocupa toda a largura, sem margens laterais */
    .gei-table-wrap{
        border-radius:8px;
        margin-bottom:14px;
        margin-left:0;
        margin-right:0;
    }

    /* Cabeçalho da sala com fonte ligeiramente maior */
    .gei-table-section-header{
        font-size:.80rem;
        padding:10px 14px;
    }

    /* Esconder cabeçalho da tabela — substituído pelos data-label */
    .gei-table thead{display:none;}

    /* Cada linha vira um "card" */
    .gei-table tbody tr{
        display:flex;
        flex-direction:column;
        border:none;
        border-bottom:1px solid #eef1f8;
        padding:10px 14px;
        background:#fff !important;
    }
    .gei-table tbody tr:last-child{border-bottom:none;}

    /* Cada célula com label à esquerda e valor à direita */
    .gei-table td{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:6px 0;
        border:none;
        font-size:.83rem;
    }

    /* Label gerado pelo data-label */
    .gei-table td::before{
        content:attr(data-label);
        font-size:.72rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.4px;
        color:#7b88a0;
        flex-shrink:0;
        margin-right:10px;
    }

    /* Badge de quantidade alinhado à direita */
    .gei-badge{
        font-size:.80rem;
        padding:3px 12px;
    }

    /* Total badge visível no header da sala em mobile */
    .gei-total-badge{
        font-size:.70rem;
        padding:2px 7px;
    }
}
</style>
</div>

<?php
// NOTA: antes de usar este ficheiro, adicionar coluna à BD:
//   ALTER TABLE equipamento ADD COLUMN ratointerface varchar(50) DEFAULT NULL AFTER rato;

$sql0 = "
    SELECT s.id, s.nome
    FROM salas s
    WHERE s.id_escola = $idescola
    AND EXISTS (
        SELECT 1 FROM equipamento e
        WHERE e.id_sala = s.id
        AND e.ratointerface IS NOT NULL AND e.ratointerface <> ''
    )
    ORDER BY s.nome
";
$result0 = mysqli_query($db, $sql0);
?>
<br>
<?php
while ($row0 = mysqli_fetch_array($result0)) {
    $idsa = $row0['id'];
    $nos  = $row0['nome'];

    // Buscar dados e calcular total ANTES de renderizar o cabeçalho
    $sql01 = "
        SELECT ratointerface, COUNT(id) AS qta
        FROM equipamento
        WHERE id_sala = $idsa
        AND ratointerface IS NOT NULL AND ratointerface <> ''
        GROUP BY ratointerface
        ORDER BY ratointerface ASC
    ";
    $result01   = mysqli_query($db, $sql01);
    $rows01     = mysqli_fetch_all($result01, MYSQLI_ASSOC);
    $total_sala = array_sum(array_column($rows01, 'qta'));
?>
<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        SALA: <?php echo htmlspecialchars($nos, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th>Interface - Rato</th>
                <th>Quantidade <span class="gei-total-badge">Total: <?php echo $total_sala; ?></span></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows01 as $row) { ?>
            <tr>
                <td data-label="Interface"><?php echo htmlspecialchars($row['ratointerface'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); ?></span></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br>
<?php } // fim while $row0 ?>

<?php mysqli_close($db); ?>

<?php include ("jquery_bootstrap.php"); ?>

<a href="<?php echo SVRURL ?>qta_equipamentos_total.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($idescola) ?>">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

                    </div>
               </div>
            </div>
         </div>
      </div>

      <?php include ("footer.php"); ?>
   </body>
</html>
