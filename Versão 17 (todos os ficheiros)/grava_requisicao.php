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

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include ("header.php"); ?>
      <?php include("sessao_timeout.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Requisições &gt;&gt; Inserir</li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
                  <?php include("msg_bemvindo.php"); ?>
                  </div>

<?php

// ── Parâmetros de URL ───────────────────────────────────────────────────────
$idesc = (int)base64_decode($_GET["rei"]);
$dr    = base64_decode($_GET['dr']);

// ── Validação básica dos dados POST ────────────────────────────────────────
if (
    !isset($_POST['horafim'])   || empty($_POST['horafim'])  ||
    !isset($_POST['horainicio'])|| empty($_POST['horainicio'])||
    !isset($_POST['sala'])      || empty($_POST['sala'])      ||
    !isset($idesc) || !isset($dr) || empty($idesc) || empty($dr) ||
    !is_numeric($idesc) ||
    empty($_POST['eqrequi'])    || !isset($_POST['eqrequi'])
) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(1) ?>&&rei=<?php echo base64_encode($idesc) ?>&&dr=<?php echo base64_encode($dr) ?>';
}, 10);
</script>
<?php
} else {

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['horafim'])) {

    // ── Sanitização dos campos principais ──────────────────────────────────
    $horainicio  = mysqli_real_escape_string($db, $_POST['horainicio']);
    $horafim     = mysqli_real_escape_string($db, $_POST['horafim']);
    $idSala      = (int)$_POST['sala'];
    $datautil    = mysqli_real_escape_string($db, $_POST['datautil']);
    $datareq     = mysqli_real_escape_string($db, $_POST['datareq']);
    $emailUtil   = mysqli_real_escape_string($db, $_SESSION['email']);
    $escolhas    = $_POST['eqrequi']; // array de IDs (já validado acima)

    // IDs dos equipamentos como inteiros
    $eqIds = array_map('intval', $escolhas);

    // ── Recorrência ────────────────────────────────────────────────────────
    // recorrente=1, recorrencia_dia: 0=Dom,1=Seg,...,6=Sab (PHP getDay())
    // recorrencia_fim: data de fim no formato Y-m-d
    $recorrente      = isset($_POST['recorrente']) && (int)$_POST['recorrente'] === 1;
    $recorrenciaDia  = isset($_POST['recorrencia_dia'])  ? (int)$_POST['recorrencia_dia']  : null;
    $recorrenciaFim  = isset($_POST['recorrencia_fim'])  && !empty($_POST['recorrencia_fim'])
                       ? $_POST['recorrencia_fim'] : null;

    // ── Gerar lista de datas a processar ───────────────────────────────────
    // A primeira data é sempre a data de utilização base.
    // Para recorrência, adiciona as ocorrências semanais seguintes até recorrenciaFim.
    $datas = [$datautil];

    if ($recorrente && $recorrenciaDia !== null && !empty($recorrenciaFim)) {
        try {
            $cursor = new DateTime($datautil);
            $fim    = new DateTime($recorrenciaFim);
            $cursor->modify('+1 day'); // começar no dia seguinte à data base

            while ($cursor <= $fim) {
                // getDay() em JS → 0=Dom,1=Seg,...,6=Sab
                // PHP date('w') → 0=Dom,1=Seg,...,6=Sab  (mesmo mapeamento)
                if ((int)$cursor->format('w') === $recorrenciaDia) {
                    $datas[] = $cursor->format('Y-m-d');
                    $cursor->modify('+7 days'); // saltar para a semana seguinte
                } else {
                    $cursor->modify('+1 day');
                }
            }
        } catch (Exception $e) {
            // data inválida — ignora recorrência, prossegue só com a data base
        }
    }

    // ── Processar cada data ────────────────────────────────────────────────
    $inseridas   = 0;  // requisições criadas com sucesso
    $conflitos   = 0;  // datas ignoradas por conflito
    $datasConflito = [];

    foreach ($datas as $dataAtual) {
        $dataAtualEsc = mysqli_real_escape_string($db, $dataAtual);

        // 1) Verificar disponibilidade da sala (horário)
        $sqlSala = "SELECT count(*) as c1
                    FROM requisicao
                    WHERE datautil = STR_TO_DATE('$dataAtualEsc','%Y-%m-%d')
                    AND horainicio < '$horafim'
                    AND horafim    > '$horainicio'
                    AND id_sala    = $idSala
                    AND dataentrega IS NULL";
        $resSala  = mysqli_query($db, $sqlSala);
        $rowSala  = mysqli_fetch_row($resSala);
        $contaSala = (int)$rowSala[0];

        if ($contaSala > 0) {
            // Sala ocupada nesta data — registar conflito e continuar
            $conflitos++;
            $datasConflito[] = date('d/m/Y', strtotime($dataAtual));
            continue;
        }

        // 2) Verificar disponibilidade de cada equipamento
        $eqIdsStr  = implode(',', $eqIds);
        $sqlEq = "SELECT count(*) as c3
                  FROM requisicao r
                  INNER JOIN equip_requisitado er ON r.id = er.id_req
                  WHERE r.datautil   = STR_TO_DATE('$dataAtualEsc','%Y-%m-%d')
                  AND r.horainicio   < '$horafim'
                  AND r.horafim      > '$horainicio'
                  AND er.id_equip    IN ($eqIdsStr)
                  AND r.dataentrega  IS NULL";
        $resEq    = mysqli_query($db, $sqlEq);
        $rowEq    = mysqli_fetch_row($resEq);
        $contaEq  = (int)$rowEq[0];

        if ($contaEq > 0) {
            // Algum equipamento já está requisitado nesta data/hora
            $conflitos++;
            $datasConflito[] = date('d/m/Y', strtotime($dataAtual));
            continue;
        }

        // 3) Inserir a requisição
        $sqlIns = "INSERT INTO requisicao (email_util, datarequi, datautil, horainicio, horafim, id_sala)
                   VALUES (
                       '$emailUtil',
                       STR_TO_DATE('$datareq','%Y-%m-%d'),
                       STR_TO_DATE('$dataAtualEsc','%Y-%m-%d'),
                       '$horainicio',
                       '$horafim',
                       $idSala
                   )";
        mysqli_query($db, $sqlIns);
        $idReq = mysqli_insert_id($db);

        // 4) Inserir equipamentos requisitados
        foreach ($eqIds as $idEq) {
            mysqli_query($db, "INSERT INTO equip_requisitado (id_req, id_equip)
                               VALUES ($idReq, $idEq)");
        }

        $inseridas++;
    }

    // ── Fechar ligação ─────────────────────────────────────────────────────
    mysqli_close($db);

    // ── Feedback ao utilizador ─────────────────────────────────────────────
    $totalDatas = count($datas);

    if ($inseridas === 0 && $conflitos > 0) {
        $urlRedir = SVRURL.'reqequip?x='.base64_encode(1).'&&rei='.base64_encode($idesc).'&&dr='.base64_encode($dr);
?>
<script>
swal({
    title: 'A requisição não foi efetuada!',
    text: 'Equipamentos ou sala já requisitados para as datas e horas indicadas. Consulte a tabela das requisições para o dia.',
    icon: 'error'
})
.then(function() {
    window.location = "<?php echo $urlRedir; ?>";
});
</script>
<?php

    } elseif ($inseridas > 0 && $conflitos === 0) {
        $msgExtra = ($totalDatas > 1)
            ? 'Foram criadas ' . $inseridas . ' requisição(ões) recorrente(s).'
            : 'Requisição inserida com sucesso.';
?>
<script>
swal({
    title: 'Requisição efetuada com sucesso!',
    text: '<?php echo addslashes($msgExtra); ?>',
    icon: 'success'
})
.then(function() {
    window.location = "<?php echo SVRURL ?>equip";
});
</script>
<?php

    } elseif ($inseridas > 0 && $conflitos > 0) {
        $listaConflitos = implode(', ', $datasConflito);
        $msgParcial = 'Criadas ' . $inseridas . ' de ' . $totalDatas . ' requisição(ões). Datas com conflito ignoradas: ' . $listaConflitos;
?>
<script>
swal({
    title: 'Requisição parcialmente efetuada!',
    text: '<?php echo addslashes($msgParcial); ?>',
    icon: 'warning'
})
.then(function() {
    window.location = "<?php echo SVRURL ?>equip";
});
</script>
<?php

    } else {
        $urlRedir = SVRURL.'reqequip?x='.base64_encode(1).'&&rei='.base64_encode($idesc).'&&dr='.base64_encode($dr);
?>
<script>
swal({
    title: 'Erro inesperado!',
    text: 'Nenhuma data foi processada. Tente novamente.',
    icon: 'error'
})
.then(function() {
    window.location = "<?php echo $urlRedir; ?>";
});
</script>
<?php
    }

} // fim REQUEST_METHOD == POST

} // fim validação básica
?>

<br><br><br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
