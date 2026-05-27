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

<?php
// ── Verificar sessão ─────────────────────────────────────────────────────────
if (!isset($_SESSION['login_user'])) {
    echo '<script>window.location.href = "' . SVRURL . 'index.php";</script>';
    exit;
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Listagens</li>
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

<!-- ── Scripts de validação de datas ──────────────────────────────────────── -->
<script>
// Função genérica de validação de datas
function validarDatas(formName, campoI, campoF, event) {
    var datai = document.forms[formName].elements[campoI].value;
    var dataf = document.forms[formName].elements[campoF].value;

    if (!datai || !dataf) {
        event.preventDefault();
        swal({
            title: "Preencha as duas datas!",
            type: "warning",
            confirmButtonText: "OK",
            closeOnConfirm: false
        });
        return false;
    }

    var di = new Date(Date.parse(datai));
    var df = new Date(Date.parse(dataf));

    if (df <= di) {
        event.preventDefault();
        swal({
            title: "A data final deve ser superior à data inicial!",
            type: "warning",
            confirmButtonText: "OK",
            closeOnConfirm: false
        });
        return false;
    }
    return true;
}

function clickMe()  { return validarDatas('datas',   'datai',  'dataf',  event); }
function clickMe1() { return validarDatas('avarias', 'datai',  'dataf',  event); }
function clickMe2() { return validarDatas('requi',   'datai1', 'dataf1', event); }
</script>

<?php

// ── Escola seleccionada ──────────────────────────────────────────────────────
// Obter 1ª escola como fallback
$sql4    = "SELECT id FROM escolas ORDER BY id LIMIT 1";
$result4 = mysqli_query($db, $sql4);
$rows4   = mysqli_fetch_row($result4);
$nes     = (int) $rows4[0];

$esc = (!empty($_POST["escola"]) && is_numeric($_POST["escola"]))
       ? (int) $_POST["escola"]
       : $nes;

// Validar que a escola existe
$stmt_esc = mysqli_prepare($db, "SELECT id, nome_escola FROM escolas WHERE id = ?");
mysqli_stmt_bind_param($stmt_esc, 'i', $esc);
mysqli_stmt_execute($stmt_esc);
$res_esc = mysqli_fetch_row(mysqli_stmt_get_result($stmt_esc));
if (!$res_esc) {
    $esc = $nes;
    // Re-buscar com fallback
    $stmt_esc2 = mysqli_prepare($db, "SELECT id, nome_escola FROM escolas WHERE id = ?");
    mysqli_stmt_bind_param($stmt_esc2, 'i', $esc);
    mysqli_stmt_execute($stmt_esc2);
    $res_esc = mysqli_fetch_row(mysqli_stmt_get_result($stmt_esc2));
}
$ne = htmlspecialchars($res_esc[1] ?? '', ENT_QUOTES, 'UTF-8');

?>

<!-- ── Secção: Seleccionar Instituição ──────────────────────────────────── -->
<div class="action-section" style="overflow:hidden; display:flow-root;">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>

<form name="frm" id="frm" action="" method="post">
<div style="text-align:left;">
<select class="custom-select" style="width:100%;" name="escola" onChange="this.form.submit();">
<?php
$sql2    = "SELECT id, nome_escola FROM escolas ORDER BY id";
$result2 = mysqli_query($db, $sql2);
while ($row2 = mysqli_fetch_array($result2)) {
    $sel = ($row2['id'] == $esc) ? ' selected' : '';
    echo '<option value="' . (int)$row2['id'] . '"' . $sel . '>'
         . htmlspecialchars($row2['nome_escola'], ENT_QUOTES, 'UTF-8')
         . '</option>';
}
?>
</select>
</div>
</form>

<div style="text-align:left;">
<div class="text-center mt-3">
    <span class="badge badge-primary p-2" style="font-size:1rem;">
        <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
    </span>
</div>
</div>
</div><!-- /action-section -->


<!-- ── Secção: Visualizar quantidade de equipamento ─────────────────────── -->
<div class="action-section" style="overflow:hidden; display:flow-root;">
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar quantidade de equipamento</h2>

<!-- Por sala -->
<form action="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode('li') ?>&amp;&amp;x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row align-items-end">
<div class="col-md-8 mb-3">
<label class="form-label"><i class="fas fa-door-open btn-icon"></i> &nbsp;Selecione a sala:</label>
<?php
$stmt_s = mysqli_prepare($db, "SELECT DISTINCT s.id, s.nome FROM salas s INNER JOIN equipamento e ON s.id = e.id_sala WHERE s.id_escola = ? ORDER BY s.nome");
mysqli_stmt_bind_param($stmt_s, 'i', $esc);
mysqli_stmt_execute($stmt_s);
$res_s    = mysqli_stmt_get_result($stmt_s);
$rowcount = mysqli_num_rows($res_s);
echo '<select style="width:100%;" class="form-control required-field" required name="sala">';
if ($rowcount > 0) {
    while ($row = mysqli_fetch_array($res_s)) {
        echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
} else {
    echo '<option value="">Sem salas disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-4 mb-1">
<button style="width:100%" title="Quantidade de equipamento da sala" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade por sala
</button>
</div>
</div>
</form>

<br>

<?php if ($_SESSION['tipo'] != 4): ?>
<!-- Por tipo de equipamento -->
<form action="<?php echo SVRURL ?>qta_equipamentos_sala_tiposequi.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row align-items-end">
<div class="col-md-8 mb-3">
<label class="form-label"><i class="fa-solid fa-server"></i> &nbsp;Selecione o tipo de equipamento:</label><br>
<?php
$stmt_te = mysqli_prepare($db,
    "SELECT DISTINCT t.nome AS noeq
     FROM tipos_equipamento t
     INNER JOIN equipamento eq ON eq.tipo = t.nome
     INNER JOIN salas s ON eq.id_sala = s.id
     WHERE s.id_escola = ?
     ORDER BY t.nome");
mysqli_stmt_bind_param($stmt_te, 'i', $esc);
mysqli_stmt_execute($stmt_te);
$res_te    = mysqli_stmt_get_result($stmt_te);
$rowcount  = mysqli_num_rows($res_te);
echo '<select style="width:100%;" class="form-control required-field" required name="tiposequi">';
if ($rowcount > 0) {
    while ($roweq = mysqli_fetch_array($res_te)) {
        echo '<option value="' . htmlspecialchars($roweq['noeq'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($roweq['noeq'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
} else {
    echo '<option value="">Sem tipos de equipamento disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-4 mb-1">
<button style="width:100%" title="Quantidade por sala do tipo" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade por tipo
</button>
</div>
</div>
</form>
<?php endif; ?>

<?php if ($_SESSION['tipo'] == 1): ?>
<br>
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar quantidade total</h2>
<div class="row">
<div class="col-md-6 mb-3">
<form action="<?php echo SVRURL ?>qta_equipamentos_total_sala.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%" title="Quantidade de equipamento total por sala" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade total por sala
</button>
</form>
</div>
<div class="col-md-6 mb-3">
<form action="<?php echo SVRURL ?>qta_equipamentos_total.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%" title="Quantidade de equipamento total" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i>&nbsp; Ver quantidade total
</button>
</form>
</div>
</div>

<?php endif; ?>



<div style="clear:both; padding-bottom:10px;"></div>
</div><!-- /action-section -->


<?php if ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3): ?>

<!-- ── Secção: Visualizar avarias ───────────────────────────────────────── -->
<div class="action-section" style="overflow:hidden; display:flow-root;">
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar avarias</h2>

<!-- Avarias entre datas -->
<form name="avarias" action="<?php echo SVRURL ?>num_avarias_entredatas.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<label class="form-label"><i class="fas fa-door-open btn-icon"></i> &nbsp;Selecione as datas:</label><br>
<div class="row">
<div class="col-md-6 mb-3">
    <input style="width:100%;" class="form-control required-field" required type="date" name="datai">
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div class="col-md-5 mb-3">
    <input style="width:100%;" class="form-control required-field" required type="date" name="dataf">
</div>
</div>
<button title="Nº de avarias e reparações entre datas" type="submit" onclick="return clickMe1(event);" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i> &nbsp;Ver nº de avarias entre datas
</button>
</form>

<br>

<!-- Avarias por equipamento -->
<form action="<?php echo SVRURL ?>num_avarias_equipamento.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row align-items-end">
<div class="col-md-6 mb-3">
<label class="form-label"><i class="fa-solid fa-laptop-code"></i> Selecione o equipamento:</label>
<?php
$stmt_eq = mysqli_prepare($db,
    "SELECT DISTINCT e.id, e.nomeequi
     FROM equipamento e
     INNER JOIN avarias_reparacoes ar ON ar.id_equi = e.id
     WHERE ar.id_escola = ?
     ORDER BY e.nomeequi");
mysqli_stmt_bind_param($stmt_eq, 'i', $esc);
mysqli_stmt_execute($stmt_eq);
$res_eq   = mysqli_stmt_get_result($stmt_eq);
$rowcount = mysqli_num_rows($res_eq);
echo '<select style="width:100%;" class="form-control required-field" required name="equi">';
if ($rowcount > 0) {
    while ($row = mysqli_fetch_array($res_eq)) {
        echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
} else {
    echo '<option value="">Sem equipamentos disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-6 mb-3">
<button style="width:100%;" title="Nº de avarias do equipamento" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i> &nbsp;Ver nº de avarias do equipamento
</button>
</div>
</div>
</form>

<br>

<!-- Avarias por sala -->
<form action="<?php echo SVRURL ?>num_avarias_sala.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row align-items-end">
<div class="col-md-6 mb-3">
<label class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a sala:</label>
<?php
$stmt_as = mysqli_prepare($db,
    "SELECT DISTINCT ar.id_sala, s.nome
     FROM avarias_reparacoes ar
     INNER JOIN salas s ON ar.id_sala = s.id
     WHERE ar.id_escola = ?
     ORDER BY ar.id_sala");
mysqli_stmt_bind_param($stmt_as, 'i', $esc);
mysqli_stmt_execute($stmt_as);
$res_as   = mysqli_stmt_get_result($stmt_as);
$rowcount = mysqli_num_rows($res_as);
echo '<select style="width:100%;" class="form-control required-field" required name="sala2">';
if ($rowcount > 0) {
    while ($row = mysqli_fetch_array($res_as)) {
        echo '<option value="' . (int)$row['id_sala'] . '">' . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
} else {
    echo '<option value="">Sem datas disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-6 mb-3">
<button style="width:100%;" title="Nº de avarias da sala" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i> &nbsp;Ver nº de avarias da sala
</button>
</div>
</div>
</form>

<br>

<!-- Avarias por tipo de equipamento -->
<form action="<?php echo SVRURL ?>num_avarias_tipoeq.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row align-items-end">
<div class="col-md-6 mb-3">
<label class="form-label"><i class="fa-solid fa-rectangle-list"></i> Selecione o tipo de equipamento:</label>
<?php
$stmt_tip = mysqli_prepare($db,
    "SELECT DISTINCT eq.tipo
     FROM avarias_reparacoes ar
     INNER JOIN equipamento eq ON ar.id_equi = eq.id
     WHERE ar.id_escola = ?
     ORDER BY eq.tipo");
mysqli_stmt_bind_param($stmt_tip, 'i', $esc);
mysqli_stmt_execute($stmt_tip);
$res_tip  = mysqli_stmt_get_result($stmt_tip);
$rowcount = mysqli_num_rows($res_tip);
echo '<select style="width:100%;" class="form-control required-field" required name="tipoeq">';
if ($rowcount > 0) {
    while ($row = mysqli_fetch_array($res_tip)) {
        echo '<option value="' . htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
} else {
    echo '<option value="">Sem tipos de equipamento disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-6 mb-3">
<button style="width:100%;" title="Nº de avarias por tipo equipamento" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i> &nbsp;Ver nº de avarias por tipo de equipamento
</button>
</div>
</div>
</form>

<div style="text-align:center;"></div><br>

<!-- Avarias de utilizadores removidos -->
<form action="<?php echo SVRURL ?>avarias_utilizadores_removidos.php?ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%;" title="Ver avarias registadas por utilizadores removidos" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-user-slash btn-icon"></i> &nbsp;Ver avarias de utilizadores removidos
</button>
</form>

<div style="text-align:center;"></div><br>

<!-- Estatística -->
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Estatística de avarias</h2>

<form action="<?php echo SVRURL ?>estatistica_avarias.php?ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%;" title="Avarias (últimos 5 anos letivos)" type="submit" class="action-button btn-secondary-action">Avarias (últimos 5 anos)</button>
</form>

<div style="text-align:center;"></div><br>

<div class="row">
<div class="col-md-6 mb-3">
<form action="<?php echo SVRURL ?>equipamentos_mais_avarias_top.php?op=t&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%;" title="Equipamentos com mais avarias (top 10)" type="submit" class="action-button btn-secondary-action">Equipamentos com mais avarias (top 10)</button>
</form>
</div>
<div class="col-md-6 mb-3">
<form action="<?php echo SVRURL ?>salas_mais_avarias_top.php?op=t&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post">
<button style="width:100%;" title="Salas com mais avarias (top 10)" type="submit" class="action-button btn-secondary-action">Salas com mais avarias (top 10)</button>
</form>
</div>
</div>

</div><!-- /action-section avarias -->

<?php endif; // tipo == 1 || tipo == 3 ?>


<?php if ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 1): ?>

<!-- ── Secção: Visualizar requisições ───────────────────────────────────── -->
<div class="action-section" style="overflow:hidden; display:flow-root;">
<h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar requisições</h2>
<br>

<!-- Requisições por data -->
<form action="<?php echo SVRURL ?>requisicoes_terminar_dia.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label"><i class="fa-solid fa-calendar-days"></i> &nbsp;Selecione a data:</label>
<?php
$stmt_rd = mysqli_prepare($db,
    "SELECT DISTINCT r.datautil
     FROM requisicao r
     INNER JOIN salas s ON r.id_sala = s.id
     WHERE s.id_escola = ? AND r.dataentrega IS NULL
     ORDER BY r.datautil");
mysqli_stmt_bind_param($stmt_rd, 'i', $esc);
mysqli_stmt_execute($stmt_rd);
$res_rd   = mysqli_stmt_get_result($stmt_rd);
$rowcount = mysqli_num_rows($res_rd);
echo '<select title="Escolha a data" required class="form-control required-field" name="data" style="width:100%;">';
if ($rowcount > 0) {
    while ($row2 = mysqli_fetch_array($res_rd)) {
        echo '<option value="' . htmlspecialchars($row2['datautil'], ENT_QUOTES, 'UTF-8') . '">'
             . date('d/m/Y', strtotime($row2['datautil']))
             . '</option>';
    }
} else {
    echo '<option value="">Sem datas disponíveis</option>';
}
echo '</select>';
?>
</div>
<div class="col-md-6 mb-3"><br>
<button style="width:100%" title="Ver requisições a terminar no dia" type="submit" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i>&nbsp; Ver requisições a terminar na data
</button>
</div>
</div>
</form>

<!-- Requisições entre datas -->
<form name="requi" action="<?php echo SVRURL ?>requisicoes_terminar_entre_datas.php?x=<?php echo base64_encode(0) ?>&amp;&amp;ies=<?php echo base64_encode($esc) ?>" method="post" class="needs-validation" novalidate>
<label class="form-label"><i class="fa-solid fa-calendar-days"></i> &nbsp;Selecione as datas:</label>
<div class="row">
<div class="col-md-6 mb-3">
    <input style="width:100%;" class="form-control required-field" required type="date" name="datai1">
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div class="col-md-5 mb-3">
    <input style="width:100%;" class="form-control required-field" required type="date" name="dataf1">
</div>
</div>
<button title="Requisições a terminar entre datas" type="submit" onclick="return clickMe2(event);" class="action-button btn-secondary-action">
    <i class="fas fa-eye btn-icon"></i> &nbsp;Ver requisições a terminar entre datas
</button>
</form>

</div><!-- /action-section -->

<?php endif; ?>

<?php include ("jquery_bootstrap.php"); ?>

<br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

<!-- Validação Bootstrap -->
<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

      <?php include ("footer.php"); ?>

   </body>
</html>
