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
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
   </head>

   <script src="<?php echo SVRURL ?>js/jquery1102.js"></script>
   <script src="<?php echo SVRURL ?>js/jqueryselectlistactions.js"></script>
   <link rel="stylesheet" href="<?php echo SVRURL ?>css/listboxs.css">

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>
     <?php include ("css_inserir.php"); ?>
     <?php include("sessao_timeout.php"); ?>

<?php
$x = (int)base64_decode($_GET["x"]);

if ($x == 0) {
    $dr = $_POST['datareq'];
} elseif ($x == 1) {
    $dr = base64_decode($_GET["dr"]);
}

// Equipamentos pré-seleccionados (vindo do calendário de reservas)
$eq_presel = [];
if (!empty($_GET['eq'])) {
    $eq_decoded = base64_decode($_GET['eq']);
    foreach (explode(',', $eq_decoded) as $eid) {
        $eid = (int)trim($eid);
        if ($eid > 0) $eq_presel[] = $eid;
    }
}

$stmt2a = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt2a->execute();
$result2a = $stmt2a->get_result();
$rows2a   = $result2a->fetch_row();
$stmt2a->close();
$maxesc   = $rows2a[0];

if (base64_decode($_GET["rei"]) > $maxesc) {
?>
<script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>equip'; }, 40);</script>
<?php
}

$idescola = (int)base64_decode($_GET["rei"]);

$stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt11->bind_param("i", $idescola);
$stmt11->execute();
$result11 = $stmt11->get_result();
$rows11   = $result11->fetch_row();
$stmt11->close();
$ne       = $rows11[0];
?>

<script>
function horasColidem(hi1, hf1, hi2, hf2) {
    return !(hf1 <= hi2 || hf2 <= hi1);
}

function verificadados(e) {
    var hi1   = document.forms.req.elements.horainicio.value;
    var hf1   = document.forms.req.elements.horafim.value;
    var lbox2 = document.getElementById("lstBox2");

    if (hi1 > hf1) {
        e.preventDefault();
        swal({ title: "A hora de fim deve ser superior à hora de inicio!", type: "warning", confirmButtonText: "OK" });
        return;
    }
    if (lbox2.options.length == 0) {
        e.preventDefault();
        swal({
            title: "Nenhum equipamento selecionado!",
            text: "Deve adicionar pelo menos um equipamento à lista 'Equipamentos a requisitar'.",
            type: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    // Validação de requisição recorrente
    var chkRec = document.getElementById("chkRecorrente");
    if (chkRec && chkRec.checked) {
        var dataFim = document.getElementById("recorrencia_fim").value;
        var dataUtil = document.forms.req.elements.datautil.value;
        if (!dataFim) {
            e.preventDefault();
            swal({ title: "Indique a data de fim da recorrência!", type: "warning", confirmButtonText: "OK" });
            return;
        }
        if (dataFim <= dataUtil) {
            e.preventDefault();
            swal({ title: "A data de fim da recorrência deve ser posterior à data de utilização!", type: "warning", confirmButtonText: "OK" });
            return;
        }
    }

    // Verificar colisão de horário para equipamentos a laranja
    if (typeof requisicoesDia !== 'undefined' && requisicoesDia.length > 0 && hi1 && hf1) {
        var conflitos = [];
        for (var i = 0; i < lbox2.options.length; i++) {
            var opt = lbox2.options[i];
            if (opt.style.color === 'orange') {
                var idEquip = parseInt(opt.value);
                var nomeEquip = opt.text;
                for (var j = 0; j < requisicoesDia.length; j++) {
                    var req = requisicoesDia[j];
                    if (req.id_equip === idEquip && horasColidem(hi1, hf1, req.horainicio, req.horafim)) {
                        conflitos.push('\u2022 ' + nomeEquip + '  (' + req.horainicio.substring(0,5) + ' \u2014 ' + req.horafim.substring(0,5) + ')');
                        break;
                    }
                }
            }
        }
        if (conflitos.length > 0) {
            e.preventDefault();
            swal({
                title: 'Conflito de hor\u00e1rio!',
                text: 'N\u00e3o \u00e9 poss\u00edvel requisitar o(s) seguinte(s) equipamento(s) porque j\u00e1 est\u00e3o requisitados nas horas indicadas:\n\n'
                      + conflitos.join('\n')
                      + '\n\nVerifique as horas ou remova esses equipamentos da lista.',
                type: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
    }

    // Selecionar todos os itens do lstBox2 para envio via POST
    for (var i = 0; i < lbox2.options.length; i++) {
        lbox2.options[i].selected = true;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("formReq").addEventListener("submit", verificadados);

    // Pré-mover equipamentos seleccionados no calendário
    if (eqPresel.length > 0) {
        var lbox1 = document.getElementById("lstBox1");
        var lbox2 = document.getElementById("lstBox2");
        for (var i = lbox1.options.length - 1; i >= 0; i--) {
            if (eqPresel.includes(parseInt(lbox1.options[i].value))) {
                var opt = lbox1.options[i];
                var newOpt = new Option(opt.text, opt.value);
                if (opt.style.color === 'orange') newOpt.style.color = 'orange';
                lbox2.add(newOpt);
                lbox1.remove(i);
            }
        }
    }

    // Mostrar/esconder painel de recorrência
    var chk = document.getElementById("chkRecorrente");
    if (chk) {
        chk.addEventListener("change", function() {
            document.getElementById("divRecorrente").style.display = this.checked ? "block" : "none";
        });
    }

    // Pré-seleccionar dia da semana com base na data de utilização
    var dataUtil = document.forms.req ? document.forms.req.elements.datautil.value : null;
    if (dataUtil) {
        var d = new Date(dataUtil);
        // getDay(): 0=Dom,1=Seg,...,6=Sab → mesmo mapeamento do campo recorrencia_dia
        var diaSemana = d.getDay();
        var sel = document.getElementById("recorrencia_dia");
        if (sel) sel.value = diaSemana;
    }
});

function selectAll(selectBox, selectAll) {
    var lbox2 = document.getElementById("lstBox2");
    if (lbox2.options.length == 0) {
        swal({ title: "A lista deve ter pelo menos um equipamento a requisitar!", type: "warning", confirmButtonText: "OK" });
        return false;
    } else {
        if (typeof selectBox == "string") { selectBox = document.getElementById(selectBox); }
        if (selectBox.type == "select-multiple") {
            for (var i = 0; i < selectBox.options.length; i++) { selectBox.options[i].selected = selectAll; }
        }
    }
}
</script>

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
                        <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamentos</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Requisição</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

               <!-- Card instituição + data -->
               <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin:14px 0 10px;padding:12px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <div style="display:flex;flex-direction:column;">
                     <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <div style="margin-left:auto;display:flex;flex-direction:column;align-items:flex-end;">
                     <span style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#7b88a0;">Data de utilização</span>
                     <span style="font-size:.95rem;font-weight:700;color:#182848;"><?php echo date('d/m/Y', strtotime($dr)); ?></span>
                  </div>
               </div>

<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:20px; }
.gei-table-section-header { padding:10px 16px; background:#182848; color:#fff; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#253d6e; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:top; color:#1e2a45; }
.gei-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:3px; }
.gei-rep-input { width:100%; padding:5px 8px; border:1.5px solid #c7d4f0; border-radius:6px; font-size:.83rem; color:#1e2a45; background:#f8faff; margin-bottom:6px; }
.gei-rep-input:focus { outline:none; border-color:#4b6cb7; background:#fff; }
.listbox-wrap { background:#fff; border:1.5px solid #c7d4f0; border-radius:8px; overflow:hidden; }
.listbox-wrap select { width:100%; border:none; padding:6px 4px; font-size:.83rem; color:#1e2a45; background:transparent; }
.listbox-wrap select option[style*="orange"] { background:#fff8e1; }
.arrows-col { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; padding:0 8px; }
.arrows-col input[type=button] { width:36px; padding:4px 0; border-radius:6px; border:1.5px solid #c7d4f0; background:#f4f6fb; color:#253d6e; font-weight:700; cursor:pointer; transition:background .15s; }
.arrows-col input[type=button]:hover { background:#e8f0fe; border-color:#4b6cb7; }

/* Painel recorrência */
.recorrencia-panel {
    margin: 14px 0;
    padding: 14px 16px;
    background: #f0f4ff;
    border: 1.5px solid #c7d4f0;
    border-radius: 10px;
}
.recorrencia-panel .rec-toggle-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: .88rem;
    font-weight: 700;
    color: #182848;
    user-select: none;
}
.recorrencia-panel .rec-toggle-label input[type=checkbox] {
    width: 17px;
    height: 17px;
    accent-color: #4b6cb7;
    cursor: pointer;
}
.recorrencia-panel .rec-body {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #dce5f7;
}
.recorrencia-panel .rec-info {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 9px 12px;
    background: #e8f0fe;
    border-radius: 7px;
    font-size: .79rem;
    color: #253d6e;
    font-weight: 600;
    margin-top: 10px;
}
</style>

               <!-- Formulário -->
               <div class="form-container">
                  <div class="step-indicator">
                  <i class="fas fa-info-circle me-2"></i>
                     Complete todos os campos obrigatórios (indicados com fundo azul claro)
                  </div>

                  <form name="req" id="formReq"
                        action="<?php echo SVRURL ?>grava_requisicao.php?rei=<?php echo base64_encode($idescola) ?>&&dr=<?php echo base64_encode($dr); ?>"
                        method="post">

                  <!-- Nota informativa -->
                  <div style="display:flex;align-items:center;gap:8px;margin:10px 0 18px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                     <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                     Ver tabela em baixo com requisições já efetuadas para o dia pretendido.
                  </div>

                  <!-- Datas e horas -->
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <span class="gei-label">Data da requisição</span>
                        <input style="width:100%;" readonly class="form-select gei-rep-input"
                               value="<?php echo date('Y-m-d'); ?>" type="date" name="datareq">
                     </div>
                     <div class="col-md-6 mb-3">
                        <span class="gei-label">Data da utilização</span>
                        <input style="width:100%;" readonly class="form-select gei-rep-input"
                               value="<?php echo htmlspecialchars($dr, ENT_QUOTES, 'UTF-8'); ?>" type="date" name="datautil" required>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <span class="gei-label">Hora de início</span>
                        <input class="form-control required-field gei-rep-input" required type="time" name="horainicio">
                     </div>
                     <div class="col-md-6 mb-3">
                        <span class="gei-label">Hora de fim</span>
                        <input class="form-control required-field gei-rep-input" required type="time" name="horafim">
                     </div>
                  </div>

                  <!-- Sala -->
                  <div class="mb-3">
                     <span class="gei-label">Sala</span>
                     <?php
                     $stmt_salas = $db->prepare("SELECT DISTINCT nome AS no, id FROM salas WHERE id_escola = ? AND equip_requisitavel = 'Não' ORDER BY nome");
                     $stmt_salas->bind_param("i", $idescola);
                     $stmt_salas->execute();
                     $result = $stmt_salas->get_result();
                     $stmt_salas->close();
                     ?>
                     <select class="form-control required-field gei-rep-input" required name="sala" style="height:auto;">
                        <?php while ($row = mysqli_fetch_array($result)): ?>
                           <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['no'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                     </select>
                  </div>

                  <!-- Listboxes equipamentos -->
                  <?php
                  $stmt_eq = $db->prepare("SELECT e.id, e.nomeequi FROM equipamento e, salas s WHERE e.id_sala = s.id AND s.id_escola = ? AND s.equip_requisitavel = 'Sim' ORDER BY e.tipo, e.nomeequi");
                  $stmt_eq->bind_param("i", $idescola);
                  $stmt_eq->execute();
                  $result   = $stmt_eq->get_result();
                  $stmt_eq->close();
                  $rowcount = $result->num_rows;

                  $stmt1a = $db->prepare("SELECT DISTINCT er.id_equip FROM requisicao r, salas s, equip_requisitado er WHERE s.id = r.id_sala AND r.id = er.id_req AND s.id_escola = ? AND r.datautil = STR_TO_DATE(?, '%Y-%m-%d') AND r.dataentrega IS NULL ORDER BY er.id_equip");
                  $stmt1a->bind_param("is", $idescola, $dr);
                  $stmt1a->execute();
                  $result1a = $stmt1a->get_result();
                  $stmt1a->close();
                  $i = 0; $arrid = [];
                  while ($row1a = $result1a->fetch_array()) { $arrid[$i++] = $row1a['id_equip']; }
                  ?>

                  <?php
                  $stmtReqJS = $db->prepare("SELECT er.id_equip, r.horainicio, r.horafim FROM requisicao r INNER JOIN salas s ON s.id = r.id_sala INNER JOIN equip_requisitado er ON er.id_req = r.id WHERE s.id_escola = ? AND r.datautil = STR_TO_DATE(?, '%Y-%m-%d') AND r.dataentrega IS NULL");
                  $stmtReqJS->bind_param("is", $idescola, $dr);
                  $stmtReqJS->execute();
                  $resReqJS = $stmtReqJS->get_result();
                  $stmtReqJS->close();
                  $reqJS    = [];
                  while ($rjs = $resReqJS->fetch_assoc()) {
                      $reqJS[] = [
                          'id_equip'   => (int)$rjs['id_equip'],
                          'horainicio' => $rjs['horainicio'],
                          'horafim'    => $rjs['horafim'],
                      ];
                  }
                  ?>
                  <script>
                  var requisicoesDia = <?php echo json_encode($reqJS, JSON_UNESCAPED_UNICODE); ?>;
                  var eqPresel = <?php echo json_encode($eq_presel); ?>;
                  </script>

                  <div style="display:flex;gap:8px;align-items:stretch;margin-top:10px;">

                           <!-- Disponíveis -->
                           <div style="flex:1;min-width:0;">
                              <span class="gei-label">Equipamentos disponíveis <span style="color:#b07d00;">(laranja = já em requisição do dia)</span></span>
                              <div class="listbox-wrap" style="height:220px;">
                                 <select style="height:100%;width:100%;border:none;padding:4px;font-size:.83rem;" multiple id="lstBox1" name="eqdisp[]">
                                    <?php while ($row3 = $result->fetch_array()):
                                        $stmt_av = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes ar, equipamento eq, salas s WHERE ar.id_equi = eq.id AND s.id = ar.id_sala AND s.id_escola = ? AND eq.id = ? AND datareparacao IS NULL");
                                        $stmt_av->bind_param("ii", $idescola, $row3['id']);
                                        $stmt_av->execute();
                                        $result1 = $stmt_av->get_result();
                                        $rows    = $result1->fetch_row();
                                        $stmt_av->close();
                                        if ($rows[0] == 0):
                                            $e = 0;
                                            foreach ($arrid as $var_listar) {
                                                if ($row3['id'] == $var_listar) {
                                                    echo '<option style="color:orange;" value="'.$row3['id'].'">'.$row3['nomeequi'].'</option>';
                                                    $e = 1;
                                                }
                                            }
                                            if ($e == 0) echo '<option value="'.$row3['id'].'">'.$row3['nomeequi'].'</option>';
                                        endif;
                                    endwhile; ?>
                                 </select>
                              </div>
                           </div>

                           <!-- Setas -->
                           <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding-top:20px;min-width:48px;">
                              <input id="btnAllRight" type="button" value=">>" style="width:44px;padding:6px 0;border-radius:6px;border:1.5px solid #c7d4f0;background:#f4f6fb;color:#253d6e;font-weight:700;cursor:pointer;font-size:.82rem;">
                              <input id="btnRight"    type="button" value=">"  style="width:44px;padding:6px 0;border-radius:6px;border:1.5px solid #c7d4f0;background:#f4f6fb;color:#253d6e;font-weight:700;cursor:pointer;font-size:.82rem;">
                              <input id="btnLeft"     type="button" value="<"  style="width:44px;padding:6px 0;border-radius:6px;border:1.5px solid #c7d4f0;background:#f4f6fb;color:#253d6e;font-weight:700;cursor:pointer;font-size:.82rem;">
                              <input id="btnAllLeft"  type="button" value="<<" style="width:44px;padding:6px 0;border-radius:6px;border:1.5px solid #c7d4f0;background:#f4f6fb;color:#253d6e;font-weight:700;cursor:pointer;font-size:.82rem;">
                           </div>

                           <!-- A requisitar -->
                           <div style="flex:1;min-width:0;">
                              <span class="gei-label">Equipamentos a requisitar</span>
                              <div class="listbox-wrap" style="height:220px;">
                                 <select style="height:100%;width:100%;border:none;padding:4px;font-size:.83rem;" multiple id="lstBox2" name="eqrequi[]"></select>
                              </div>
                              <div style="margin-top:8px;display:flex;justify-content:flex-end;">
                                 <button type="button" class="btn btn-outline-primary btn-sm"
                                         onclick="selectAll('lstBox2', true)">Selecionar todos</button>
                              </div>
                           </div>

                  </div>

                  <!-- ══════════════════════════════════════════
                       PAINEL DE REQUISIÇÃO RECORRENTE (NOVO)
                       ══════════════════════════════════════════ -->
                  <div class="recorrencia-panel" style="margin-top:18px;">
                     <label class="rec-toggle-label">
                        <input type="checkbox" id="chkRecorrente" name="recorrente" value="1">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                        Requisição recorrente &nbsp;<span style="font-weight:400;color:#7b88a0;font-size:.8rem;">(repetir semanalmente no mesmo dia da semana)</span>
                     </label>

                     <div id="divRecorrente" style="display:none;" class="rec-body">
                        <div class="row">
                           <div class="col-md-5 mb-2">
                              <span class="gei-label">Dia da semana a repetir</span>
                              <select id="recorrencia_dia" name="recorrencia_dia" class="gei-rep-input">
                                 <option value="1">Segunda-feira</option>
                                 <option value="2">Terça-feira</option>
                                 <option value="3">Quarta-feira</option>
                                 <option value="4">Quinta-feira</option>
                                 <option value="5">Sexta-feira</option>
                                 <option value="6">Sábado</option>
                                 <option value="0">Domingo</option>
                              </select>
                           </div>
                           <div class="col-md-4 mb-2">
                              <span class="gei-label">Repetir até (data fim)</span>
                              <input type="date" id="recorrencia_fim" name="recorrencia_fim" class="gei-rep-input"
                                     min="<?php echo date('Y-m-d', strtotime($dr . ' +7 days')); ?>">
                           </div>
                           <div class="col-md-3 mb-2" style="display:flex;align-items:flex-end;padding-bottom:6px;">
                              <span style="font-size:.75rem;color:#5a6a8a;font-weight:600;">
                                 A 1ª ocorrência é sempre a data de utilização acima.
                              </span>
                           </div>
                        </div>

                        <div class="rec-info">
                           <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                           <span>
                              Serão criadas requisições para <strong>todas as ocorrências</strong> do dia selecionado até à data de fim.
                              Datas com conflito de horário são <strong>automaticamente ignoradas</strong> — as restantes são inseridas.
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- ══ fim painel recorrência ══ -->

                  <!-- Nota final -->
                  <div style="display:flex;align-items:center;gap:8px;margin:16px 0 18px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                     <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                     Após clicar em "Requisitar equipamento" é verificado se os equipamentos ainda estão disponíveis para a data, horas e sala. Caso um não esteja disponível, a requisição não é feita.
                  </div>

                  <div style="text-align:center;">
                     <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-cart-flatbed"></i>&nbsp;Requisitar equipamento
                     </button>
                  </div>

                  </form>
               </div>

               <!-- Tabela requisições já efetuadas -->
               <div class="gei-table-wrap" style="margin-top:24px;">
                  <div class="gei-table-section-header">
                     <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                     Requisições já efetuadas para <?php echo date('d/m/Y', strtotime($dr)); ?>
                  </div>
                  <table class="gei-table">
                     <thead>
                        <tr>
                           <th style="width:8%">Nº</th>
                           <th style="width:18%">Data requisição</th>
                           <th style="width:30%">Sala / Horas</th>
                           <th>Equipamentos</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php
                     $stmt2 = $db->prepare("SELECT r.id AS rid, r.*, s.* FROM requisicao r, salas s WHERE s.id = r.id_sala AND s.id_escola = ? AND r.datautil = STR_TO_DATE(?, '%Y-%m-%d') AND r.dataentrega IS NULL ORDER BY r.datarequi, s.nome, r.horainicio");
                     $stmt2->bind_param("is", $idescola, $dr);
                     $stmt2->execute();
                     $result2 = $stmt2->get_result();
                     $stmt2->close();
                     while ($row2 = $result2->fetch_array()):
                         $idr  = $row2['rid'];
                         $stmt3 = $db->prepare("SELECT e.nomeequi FROM equip_requisitado er, equipamento e WHERE er.id_equip = e.id AND er.id_req = ?");
                         $stmt3->bind_param("i", $idr);
                         $stmt3->execute();
                         $result3 = $stmt3->get_result();
                         $stmt3->close();
                         $equips  = [];
                         while ($row3 = $result3->fetch_array()) { $equips[] = htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8'); }
                     ?>
                     <tr>
                        <td data-label="Nº"><span style="font-weight:700;"><?php echo htmlspecialchars($idr, ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td data-label="Data requisição"><span style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row2['datarequi'])); ?></span></td>
                        <td data-label="Sala / Horas">
                           <span style="font-weight:700;"><?php echo htmlspecialchars($row2['nome'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                           <span style="font-family:monospace;font-size:.82rem;color:#7b88a0;">
                              <?php echo htmlspecialchars($row2['horainicio'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($row2['horafim'], ENT_QUOTES, 'UTF-8'); ?>
                           </span>
                        </td>
                        <td data-label="Equipamentos"><?php echo implode(' &nbsp;|&nbsp; ', $equips); ?></td>
                     </tr>
                     <?php endwhile; ?>
                     </tbody>
                  </table>
               </div>

               <a href="<?php echo SVRURL ?>calendario_reservas.php">
                  <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
               </a>
               <br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

      <?php include ("footer.php"); ?>

<script>

$('#btnRight').click(function(e) {
    e.preventDefault();
    var lbox1 = document.getElementById('lstBox1');
    var lbox2 = document.getElementById('lstBox2');
    var normalItems = [];
    var orangeItems = [];

    for (var i = lbox1.options.length - 1; i >= 0; i--) {
        var opt = lbox1.options[i];
        if (opt.selected) {
            if (opt.style.color === 'orange') {
                orangeItems.push(i);
            } else {
                normalItems.push(i);
            }
        }
    }

    for (var j = 0; j < normalItems.length; j++) {
        var idx = normalItems[j];
        var o = lbox1.options[idx];
        lbox2.add(new Option(o.text, o.value));
        lbox1.remove(idx);
    }

    if (orangeItems.length > 0) {
        var names = orangeItems.map(function(idx) { return '• ' + lbox1.options[idx].text; }).join('\n');
        swal({
            title: 'Atenção: equipamento(s) já requisitado(s)!',
            text: 'Os seguintes equipamentos já estão em requisição neste dia:\n\n' + names + '\n\nConfirme que as horas de utilização não coincidem com as requisições existentes (consulte a tabela em baixo).\n\nDeseja adicioná-los mesmo assim?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, adicionar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: true
        }, function(confirmed) {
            if (confirmed) {
                var lbox1b = document.getElementById('lstBox1');
                var lbox2b = document.getElementById('lstBox2');
                for (var k = lbox1b.options.length - 1; k >= 0; k--) {
                    var ob = lbox1b.options[k];
                    if (ob.style.color === 'orange' && ob.selected) {
                        var newOpt = new Option(ob.text, ob.value);
                        newOpt.style.color = 'orange';
                        lbox2b.add(newOpt);
                        lbox1b.remove(k);
                    }
                }
            }
        });
    }
});

$('#btnAllRight').click(function(e) {
    e.preventDefault();
    var lbox1 = document.getElementById('lstBox1');
    var lbox2 = document.getElementById('lstBox2');
    var orangeItems = [];

    for (var i = lbox1.options.length - 1; i >= 0; i--) {
        var opt = lbox1.options[i];
        if (opt.style.color === 'orange') {
            orangeItems.push(opt.text);
        } else {
            lbox2.add(new Option(opt.text, opt.value));
            lbox1.remove(i);
        }
    }

    if (orangeItems.length > 0) {
        var names = orangeItems.map(function(n) { return '• ' + n; }).join('\n');
        swal({
            title: 'Atenção: equipamento(s) já requisitado(s)!',
            text: 'Os seguintes equipamentos já estão em requisição neste dia:\n\n' + names + '\n\nConfirme que as horas de utilização não coincidem com as requisições existentes (consulte a tabela em baixo).\n\nDeseja adicioná-los mesmo assim?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, adicionar todos',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: true
        }, function(confirmed) {
            if (confirmed) {
                var lbox1b = document.getElementById('lstBox1');
                var lbox2b = document.getElementById('lstBox2');
                for (var k = lbox1b.options.length - 1; k >= 0; k--) {
                    var ob = lbox1b.options[k];
                    var newOpt = new Option(ob.text, ob.value);
                    newOpt.style.color = 'orange';
                    lbox2b.add(newOpt);
                    lbox1b.remove(k);
                }
            }
        });
    }
});

$('#btnLeft').click(function(e)     { $('select').moveToListAndDelete('#lstBox2','#lstBox1'); e.preventDefault(); });
$('#btnAllLeft').click(function(e)  { $('select').moveAllToListAndDelete('#lstBox2','#lstBox1'); e.preventDefault(); });
</script>

   </body>
</html>
