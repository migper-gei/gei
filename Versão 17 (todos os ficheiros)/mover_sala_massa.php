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
        include("css_inserir.php");
        include("sessao_timeout.php");

        // Apenas administradores (tipo 1)
        if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) { ?>
            <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>equip'; },10);</script>
        <?php exit; }

        // Parâmetros: sala de origem e escola
        $said     = isset($_GET['si'])  ? (int)base64_decode($_GET['si'])  : 0;
        $idescola = isset($_GET['ies']) ? (int)base64_decode($_GET['ies']) : 0;

        if ($said <= 0 || $idescola <= 0) { ?>
            <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>equip'; },10);</script>
        <?php exit; }

        // Info da sala e escola de origem
        $sql_sala   = "SELECT nome FROM salas WHERE id=$said AND id_escola=$idescola";
        $res_sala   = mysqli_query($db, $sql_sala);
        $row_sala   = mysqli_fetch_row($res_sala);
        if (!$row_sala) { ?>
            <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>equip'; },10);</script>
        <?php exit; }
        $nome_sala = $row_sala[0];

        $sql_esc  = "SELECT nome_escola FROM escolas WHERE id=$idescola";
        $res_esc  = mysqli_query($db, $sql_esc);
        $row_esc  = mysqli_fetch_row($res_esc);
        $nome_esc = $row_esc[0];

        // Escola destino selecionada (default = mesma escola)
        $esc_destino = isset($_POST['escola_destino']) ? (int)$_POST['escola_destino'] : $idescola;
      ?>

<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#182848; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td, .gei-table th[scope="row"] { padding:10px 14px; vertical-align:middle; color:#1e2a45; font-size:.83rem; }
.gei-badge-op { background:#e6f9f2; color:#1a7a52; border:1.5px solid #a8e6cf; border-radius:5px; padding:2px 8px; font-size:.75rem; font-weight:700; display:inline-block; }
.gei-badge-av { background:#fde8e6; color:#c0392b; border:1.5px solid #f5c0bb; border-radius:5px; padding:2px 8px; font-size:.75rem; font-weight:700; display:inline-block; }
.sel-all-wrap { display:flex; align-items:center; gap:8px; font-size:.82rem; font-weight:600; color:#4b6cb7; cursor:pointer; }
.card-destino { background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px; padding:16px 20px; margin-bottom:18px; }
.card-destino label { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:4px; }
.card-destino select { padding:6px 12px; border-radius:7px; border:1.5px solid #c7d4f0; font-size:.85rem; font-weight:600; color:#1e2a45; background:#fff; width:100%; max-width:360px; }
</style>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <nav style="margin-bottom:10px;">
                     <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                           <span style="color:#4b6cb7;">Equipamentos</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">›</li>
                        <li><a href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&amp;&amp;si=<?php echo base64_encode($said) ?>&amp;&amp;ies=<?php echo base64_encode($idescola) ?>" style="color:#4b6cb7;text-decoration:none;">Ver equipamentos da sala</a></li>
                        <li style="color:#c5cde0;font-size:.9rem;">›</li>
                        <li style="color:#1e2a45;">Mover em massa</li>
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

                     <!-- Cabeçalho: sala origem -->
                     <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin:14px 0 18px;padding:10px 16px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:10px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        <span style="font-size:1.05rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($nome_sala, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span style="color:#c5cde0;">|</span>
                        <span style="font-size:.9rem;font-weight:500;color:#5a6a85;"><?php echo htmlspecialchars($nome_esc, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>

                     <!-- FORM principal -->
                     <form name="frme_escola" id="frme_escola" action="" method="post" style="margin-bottom:0;">
                        <input type="hidden" name="escola_destino_hidden" value="<?php echo $esc_destino; ?>">
                        <!-- Seleção da escola destino -->
                        <div class="card-destino">
                           <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:flex-end;">
                              <div>
                                 <label>Instituição destino</label>
                                 <select name="escola_destino" id="escola_destino" onchange="this.form.submit()">
                                    <?php
                                    $res_escs = mysqli_query($db, "SELECT id, nome_escola FROM escolas ORDER BY nome_escola");
                                    while ($re = mysqli_fetch_assoc($res_escs)) {
                                       $sel = ($re['id'] == $esc_destino) ? 'selected' : '';
                                       echo '<option value="'.htmlspecialchars($re['id']).'" '.$sel.'>'.htmlspecialchars($re['nome_escola'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                    ?>
                                 </select>
                              </div>
                              <div>
                                 <label>Sala destino</label>
                                 <select name="sala_destino" id="sala_destino" required style="padding:6px 12px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.85rem;font-weight:600;color:#1e2a45;background:#fff;width:100%;max-width:360px;">
                                    <?php
                                    // Salas da escola destino, excluindo a sala de origem se for a mesma escola
                                    $exc = ($esc_destino == $idescola) ? "AND s.id <> $said" : "";
                                    $res_salas = mysqli_query($db, "SELECT s.id, s.nome FROM salas s WHERE s.id_escola=$esc_destino $exc ORDER BY s.nome");
                                    $tem_salas = false;
                                    while ($rs = mysqli_fetch_assoc($res_salas)) {
                                       $tem_salas = true;
                                       echo '<option value="'.htmlspecialchars($rs['id']).'">'.htmlspecialchars($rs['nome'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                    if (!$tem_salas) echo '<option value="">-- Sem salas disponíveis --</option>';
                                    ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </form>

                     <?php
                     // Lista de equipamentos da sala origem
                     $sql_equip = "SELECT e.id, e.tipo, e.nomeequi, e.marca_modelo, e.numserie FROM equipamento e WHERE e.id_sala = $said ORDER BY e.tipo, e.nomeequi";
                     $res_equip = mysqli_query($db, $sql_equip);
                     $total_equip = mysqli_num_rows($res_equip);
                     ?>

                     <form action="<?php echo SVRURL ?>mover_sala_massa_ok.php" method="post" id="frm_mover" onsubmit="return confirmarMover()">
                        <input type="hidden" name="sala_origem" value="<?php echo $said; ?>">
                        <input type="hidden" name="idescola"   value="<?php echo $idescola; ?>">
                        <input type="hidden" name="sala_destino_val" id="sala_destino_val" value="">

                        <?php if ($total_equip > 0): ?>
                        <div class="gei-table-wrap">
                           <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:12px 16px;background:#f4f6fb;border-bottom:1px solid #e3e8f4;">
                              <label class="sel-all-wrap">
                                 <input type="checkbox" id="sel_todos" onchange="toggleTodos(this)" style="width:16px;height:16px;cursor:pointer;">
                                 Selecionar todos (<?php echo $total_equip; ?>)
                              </label>
                              <span id="contador_sel" style="font-size:.8rem;color:#7b88a0;font-weight:600;">0 selecionados</span>
                           </div>
                           <table class="gei-table">
                              <thead>
                                 <tr>
                                    <th style="width:40px;text-align:center;"></th>
                                    <th>Tipo / Nome</th>
                                    <th>Marca / Modelo</th>
                                    <th>Nº Série</th>
                                    <th>Estado</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php while ($eq = mysqli_fetch_assoc($res_equip)):
                                 // Estado: avarias por reparar
                                 $res_av = mysqli_query($db, "SELECT COUNT(*) FROM avarias_reparacoes WHERE id_equi=".$eq['id']." AND datareparacao IS NULL");
                                 $row_av = mysqli_fetch_row($res_av);
                                 $avariado = ($row_av[0] > 0);
                              ?>
                              <tr>
                                 <td style="text-align:center;">
                                    <input type="checkbox" name="equip[]" value="<?php echo $eq['id']; ?>" class="chk_equip" onchange="atualizarContador()" style="width:16px;height:16px;cursor:pointer;">
                                 </td>
                                 <td>
                                    <span style="font-size:.75rem;text-transform:uppercase;color:#7b88a0;font-weight:700;"><?php echo htmlspecialchars($eq['tipo'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                                    <span style="font-weight:600;color:#182848;"><?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></span>
                                 </td>
                                 <td><?php echo htmlspecialchars($eq['marca_modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td style="font-size:.78rem;color:#5a6a85;"><?php echo htmlspecialchars($eq['numserie'], ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td>
                                    <?php if ($avariado): ?>
                                       <span class="gei-badge-av">Avariado</span>
                                    <?php else: ?>
                                       <span class="gei-badge-op">Operacional</span>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                              <?php endwhile; ?>
                              </tbody>
                           </table>
                        </div>

                        <div style="text-align:center;margin-top:10px;">
                           <button type="submit" id="btn_mover" class="btn-submit" disabled style="opacity:.5;cursor:not-allowed;">
                              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:5px;"><polyline points="5 9 2 12 5 15"/><polyline points="9 5 12 2 15 5"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/></svg>
                              Mover equipamentos selecionados
                           </button>
                        </div>

                        <?php else: ?>
                        <div style="text-align:center;padding:40px;color:#7b88a0;font-size:.9rem;">
                           <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c5cde0" stroke-width="1.5" style="display:block;margin:0 auto 12px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                           Não existem equipamentos informáticos nesta sala.
                        </div>
                        <?php endif; ?>

                     </form>

                     <br>
                     <a href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&amp;&amp;si=<?php echo base64_encode($said) ?>&amp;&amp;ies=<?php echo base64_encode($idescola) ?>">
                        <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
                     </a>
                     <br><br>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

<script>
function toggleTodos(cb) {
    document.querySelectorAll('.chk_equip').forEach(c => c.checked = cb.checked);
    atualizarContador();
}

function atualizarContador() {
    const selecionados = document.querySelectorAll('.chk_equip:checked').length;
    const total        = document.querySelectorAll('.chk_equip').length;
    document.getElementById('contador_sel').textContent = selecionados + ' selecionado' + (selecionados !== 1 ? 's' : '');
    const btn = document.getElementById('btn_mover');
    if (selecionados > 0) {
        btn.disabled = false;
        btn.style.opacity  = '1';
        btn.style.cursor   = 'pointer';
    } else {
        btn.disabled = true;
        btn.style.opacity  = '.5';
        btn.style.cursor   = 'not-allowed';
    }
    // Atualiza estado do "Selecionar todos"
    const cbTodos = document.getElementById('sel_todos');
    cbTodos.indeterminate = (selecionados > 0 && selecionados < total);
    cbTodos.checked = (selecionados === total && total > 0);
}

function confirmarMover() {
    const selecionados = document.querySelectorAll('.chk_equip:checked').length;
    const salaDestino  = document.getElementById('sala_destino').value;
    if (!salaDestino || salaDestino === '') {
        swal('Atenção', 'Selecione uma sala de destino.', 'warning');
        return false;
    }
    if (selecionados === 0) {
        swal('Atenção', 'Selecione pelo menos um equipamento.', 'warning');
        return false;
    }
    // Copia o valor da sala destino para o campo hidden do form principal
    document.getElementById('sala_destino_val').value = salaDestino;
    return true;
}
</script>

      <?php include("jquery_bootstrap.php"); ?>
      <?php include("footer.php"); ?>
   </body>
</html>
