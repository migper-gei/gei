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

// Token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
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
        require_once("gei_audit.php");

        // ── Funções auxiliares ────────────────────────────────────────────────
        function cpy_redirect_equip() { ?>
            <script>window.setTimeout(function(){ window.location.href='<?php echo SVRURL ?>equip'; },10);</script>
        <?php exit; }

        function cpy_erro($msg) {
            // $msg: texto simples (linhas separadas por \n)
            echo '<script>
                swal({ title: "Não foi possível copiar", text: ' . json_encode($msg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . ', icon: "error" })
                .then(function(){ window.history.back(); });
            </script></body></html>';
            exit;
        }

        // Apenas administradores (tipo 1)
        if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) { cpy_redirect_equip(); }

        // Parâmetros: sala de origem e escola (GET no formulário, POST no processamento)
        $said     = isset($_GET['si'])  ? (int)base64_decode($_GET['si'])  : 0;
        $idescola = isset($_GET['ies']) ? (int)base64_decode($_GET['ies']) : 0;

        if ($said <= 0 || $idescola <= 0) { cpy_redirect_equip(); }

        // Sala e escola de origem
        $st = $db->prepare("SELECT nome FROM salas WHERE id = ? AND id_escola = ?");
        $st->bind_param("ii", $said, $idescola);
        $st->execute();
        $row_sala = $st->get_result()->fetch_row();
        $st->close();
        if (!$row_sala) { cpy_redirect_equip(); }
        $nome_sala = $row_sala[0];

        $st = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
        $st->bind_param("i", $idescola);
        $st->execute();
        $row_esc = $st->get_result()->fetch_row();
        $st->close();
        $nome_esc = $row_esc[0] ?? '';

        $url_form = SVRURL . 'copiar_equipamentos_sala.php?si=' . urlencode(base64_encode((string)$said))
                  . '&ies=' . urlencode(base64_encode((string)$idescola));

        // ══════════════════════════════════════════════════════════════════════
        //  PROCESSAMENTO (POST)
        // ══════════════════════════════════════════════════════════════════════
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'copiar') {

            if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
                cpy_erro("Token de segurança inválido. Recarregue a página e tente novamente.");
            }

            $sala_destino   = (int)($_POST['sala_destino'] ?? 0);
            $equips         = isset($_POST['equip']) && is_array($_POST['equip']) ? $_POST['equip'] : [];
            $nomes          = isset($_POST['nome'])  && is_array($_POST['nome'])  ? $_POST['nome']  : [];
            $outros         = isset($_POST['outro']) && is_array($_POST['outro']) ? $_POST['outro'] : [];
            $copiar_sw      = !empty($_POST['copiar_software']) ? 1 : 0;
            $copiar_marca   = !empty($_POST['copiar_marca'])    ? 1 : 0;
            $copiar_cpu     = !empty($_POST['copiar_cpu'])      ? 1 : 0;
            $copiar_ram     = !empty($_POST['copiar_ram'])      ? 1 : 0;
            $copiar_disco   = !empty($_POST['copiar_disco'])    ? 1 : 0;
            $copiar_monitor = !empty($_POST['copiar_monitor'])  ? 1 : 0;

            if ($sala_destino <= 0)            cpy_erro("Selecione a sala de destino.");
            if ($sala_destino === $said)       cpy_erro("A sala de destino tem de ser diferente da sala de origem.");
            if (empty($equips) && empty($outros)) cpy_erro("Selecione pelo menos um equipamento.");

            // Sala de destino (e respetiva escola)
            $st = $db->prepare("SELECT nome, id_escola FROM salas WHERE id = ?");
            $st->bind_param("i", $sala_destino);
            $st->execute();
            $row_dest = $st->get_result()->fetch_row();
            $st->close();
            if (!$row_dest) cpy_erro("A sala de destino não existe.");
            $nome_sala_dest = $row_dest[0];
            $escola_dest    = (int)$row_dest[1];

            // Nomes já existentes na sala de destino
            $existentes = [];
            $st = $db->prepare("SELECT nomeequi FROM equipamento WHERE id_sala = ?");
            $st->bind_param("i", $sala_destino);
            $st->execute();
            $rs = $st->get_result();
            while ($r = $rs->fetch_row()) { $existentes[mb_strtolower(trim($r[0]))] = true; }
            $st->close();

            // Validar cada equipamento selecionado
            $copias = [];          // [ ['id'=>, 'nome'=>, 'origem'=>] ]
            $vistos = [];
            $erros  = [];
            $st = $db->prepare("SELECT nomeequi FROM equipamento WHERE id = ? AND id_sala = ?");
            foreach ($equips as $eid_raw) {
                $eid = (int)$eid_raw;
                if ($eid <= 0) continue;

                $st->bind_param("ii", $eid, $said);
                $st->execute();
                $r = $st->get_result()->fetch_row();
                if (!$r) { $erros[] = "Equipamento #$eid não pertence à sala de origem."; continue; }
                $nome_origem = $r[0];

                $novo = trim((string)($nomes[$eid] ?? ''));
                $k    = mb_strtolower($novo);

                if ($novo === '')                { $erros[] = "\"$nome_origem\": o novo nome está em branco."; continue; }
                if (mb_strlen($novo) > 50)       { $erros[] = "\"$novo\": o nome excede 50 caracteres."; continue; }
                if (isset($vistos[$k]))          { $erros[] = "\"$novo\": nome repetido no formulário."; continue; }
                if (isset($existentes[$k]))      { $erros[] = "\"$novo\": já existe um equipamento com este nome na sala $nome_sala_dest."; continue; }

                $vistos[$k] = true;
                $copias[] = ['id' => $eid, 'nome' => $novo, 'origem' => $nome_origem];
            }
            $st->close();

            if (!empty($erros)) cpy_erro(implode("\n", $erros));
            if (empty($copias) && empty($outros)) cpy_erro("Nenhum equipamento válido para copiar.");

            // ── Inserção (tudo ou nada) ───────────────────────────────────────
            $sql_copy = "INSERT INTO equipamento
                (nomeequi, numserie, id_sala, marca_modelo, tipo, processador, memoria, disco,
                 placagrafica, placasom, placarede, monitor, teclado, tecladointerface, rato, ratointerface,
                 colunas, cd_dvd, dominio, ip, mascara_rede, gateway, dns_principal, dns_alternativo,
                 sala_temp, data_compra, observacoes, escola_digital, num_inv_dgest, fornecedor,
                 email_fornecedor, nif_pessoa, num_rma, data_fim_garantia)
              SELECT ?, NULL, ?, IF(?=1, marca_modelo, NULL), tipo,
                 IF(?=1, processador, NULL), IF(?=1, memoria, NULL), IF(?=1, disco, NULL),
                 placagrafica, placasom, placarede, IF(?=1, monitor, NULL), teclado, tecladointerface, rato, ratointerface,
                 colunas, cd_dvd, dominio, NULL, mascara_rede, gateway, dns_principal, dns_alternativo,
                 NULL, IF(CAST(data_compra AS CHAR)='0000-00-00', NULL, data_compra), observacoes, escola_digital,
                 NULL, fornecedor,
                 email_fornecedor, nif_pessoa, num_rma,
                 IF(CAST(data_fim_garantia AS CHAR)='0000-00-00', NULL, data_fim_garantia)
              FROM equipamento WHERE id = ? AND id_sala = ?";

            $sql_sw = "INSERT INTO software_instalado
                (id_equipamento, nome, versao, fabricante, licenca, data_instalacao, observacoes)
              SELECT ?, nome, versao, fabricante, licenca,
                 IF(CAST(data_instalacao AS CHAR)='0000-00-00', NULL, data_instalacao), observacoes
              FROM software_instalado WHERE id_equipamento = ?";

            $sql_outro = "INSERT INTO outro_equipamento (id_sala, nomeoutro, qta, observacoes)
              SELECT ?, nomeoutro, qta, observacoes FROM outro_equipamento WHERE id = ? AND id_sala = ?";

            $n_equip = 0; $n_sw = 0; $n_outro = 0;

            try {
                $db->begin_transaction();

                $st_copy = $db->prepare($sql_copy);
                $st_sw   = $db->prepare($sql_sw);

                foreach ($copias as $c) {
                    $nome = $c['nome']; $eid = $c['id'];
                    $st_copy->bind_param("siiiiiiii", $nome, $sala_destino,
                        $copiar_marca, $copiar_cpu, $copiar_ram, $copiar_disco, $copiar_monitor,
                        $eid, $said);
                    $st_copy->execute();
                    if ($st_copy->affected_rows < 1) {
                        throw new Exception("Falha ao copiar \"" . $c['origem'] . "\".");
                    }
                    $novo_id = (int)$db->insert_id;
                    $n_equip++;

                    if ($copiar_sw) {
                        $st_sw->bind_param("ii", $novo_id, $eid);
                        $st_sw->execute();
                        $n_sw += max(0, $st_sw->affected_rows);
                    }

                    gei_audit($db, 'criar', 'equipamento', $novo_id,
                        "Copiado de \"{$c['origem']}\" (sala $nome_sala) para \"$nome\" (sala $nome_sala_dest)");
                }
                $st_copy->close();
                $st_sw->close();

                if (!empty($outros)) {
                    $st_o = $db->prepare($sql_outro);
                    foreach ($outros as $oid_raw) {
                        $oid = (int)$oid_raw;
                        if ($oid <= 0) continue;
                        $st_o->bind_param("iii", $sala_destino, $oid, $said);
                        $st_o->execute();
                        $n_outro += max(0, $st_o->affected_rows);
                    }
                    $st_o->close();
                }

                $db->commit();
            } catch (Exception $ex) {
                $db->rollback();
                error_log('GEI copiar_equipamentos_sala: ' . $ex->getMessage());
                cpy_erro("Erro ao guardar os dados. Nada foi copiado.\n" . $ex->getMessage());
            }

            // Regenerar token CSRF após sucesso
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $url_dest = SVRURL . 'verequipsala?x=' . base64_encode(1)
                      . '&&si='  . base64_encode((string)$sala_destino)
                      . '&&ies=' . base64_encode((string)$escola_dest);

            $partes = [];
            $partes[] = $n_equip . ' equipamento' . ($n_equip !== 1 ? 's' : '');
            if ($copiar_sw && $n_sw > 0) $partes[] = $n_sw . ' programa' . ($n_sw !== 1 ? 's' : '') . ' instalado' . ($n_sw !== 1 ? 's' : '');
            if ($n_outro > 0)            $partes[] = $n_outro . ' outro' . ($n_outro !== 1 ? 's' : '') . ' equipamento' . ($n_outro !== 1 ? 's' : '');
            $texto = 'Copiado(s) para a sala ' . $nome_sala_dest . ': ' . implode(', ', $partes) . '.';
            ?>
            <script>
            swal({
                title: 'Cópia concluída!',
                text: <?php echo json_encode($texto, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>,
                icon: 'success'
            }).then(function(){ window.location = <?php echo json_encode($url_dest); ?>; });
            </script>
            <?php
            include("footer.php");
            echo '</body></html>';
            exit;
        }

        // ══════════════════════════════════════════════════════════════════════
        //  FORMULÁRIO (GET)
        // ══════════════════════════════════════════════════════════════════════

        // Escola destino (default = mesma escola)
        $esc_destino = isset($_GET['ed']) ? (int)$_GET['ed'] : $idescola;
        if ($esc_destino <= 0) $esc_destino = $idescola;
      ?>

<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#182848; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; font-size:.83rem; }
.gei-table input.novo-nome { width:100%; min-width:140px; padding:5px 10px; border-radius:7px; border:1.5px solid #c7d4f0; font-size:.85rem; font-weight:600; color:#182848; background:#fff; }
.gei-table input.novo-nome:focus { outline:none; border-color:#4b6cb7; box-shadow:0 0 0 3px rgba(75,108,183,.15); }
.gei-table input.novo-nome:disabled { background:#f1f3f8; color:#9aa5bd; }
.sel-all-wrap { display:flex; align-items:center; gap:8px; font-size:.82rem; font-weight:600; color:#4b6cb7; cursor:pointer; margin:0; }
.card-destino { background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px; padding:16px 20px; margin-bottom:18px; }
.card-destino label.lbl { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; display:block; margin-bottom:4px; }
.card-destino select { padding:6px 12px; border-radius:7px; border:1.5px solid #c7d4f0; font-size:.85rem; font-weight:600; color:#1e2a45; background:#fff; width:100%; max-width:360px; }
.opcoes-copia { display:flex; flex-wrap:wrap; gap:18px; margin-top:14px; padding-top:12px; border-top:1px dashed #d5dcec; }
.opcoes-copia label { font-size:.82rem; font-weight:600; color:#1e2a45; display:flex; align-items:center; gap:6px; cursor:pointer; margin:0; }
.gei-section-label { font-size:.78rem; font-weight:700; text-transform:uppercase; color:#7b88a0; letter-spacing:.4px; margin:18px 0 8px; }
.dica { font-size:.78rem; color:#7b88a0; margin:0 0 10px; }
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
                        <li style="color:#1e2a45;">Copiar para outra sala</li>
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
                        <span style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:#7b88a0;">Origem:</span>
                        <span style="font-size:1.05rem;font-weight:700;color:#182848;"><?php echo htmlspecialchars($nome_sala, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span style="color:#c5cde0;">|</span>
                        <span style="font-size:.9rem;font-weight:500;color:#5a6a85;"><?php echo htmlspecialchars($nome_esc, ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>

                     <?php
                     // Equipamentos informáticos da sala de origem
                     $st = $db->prepare("SELECT id, tipo, nomeequi, marca_modelo, numserie FROM equipamento WHERE id_sala = ? ORDER BY tipo, nomeequi");
                     $st->bind_param("i", $said);
                     $st->execute();
                     $res_equip = $st->get_result();
                     $total_equip = $res_equip->num_rows;
                     $st->close();

                     // Outros equipamentos da sala de origem
                     $st = $db->prepare("SELECT id, nomeoutro, qta, observacoes FROM outro_equipamento WHERE id_sala = ? ORDER BY nomeoutro");
                     $st->bind_param("i", $said);
                     $st->execute();
                     $res_outro = $st->get_result();
                     $total_outro = $res_outro->num_rows;
                     $st->close();
                     ?>

                     <?php if ($total_equip + $total_outro > 0): ?>

                     <form action="<?php echo htmlspecialchars($url_form, ENT_QUOTES, 'UTF-8'); ?>" method="post" id="frm_copiar" autocomplete="off">
                        <input type="hidden" name="acao" value="copiar">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <!-- Destino -->
                        <div class="card-destino">
                           <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:flex-end;">
                              <div>
                                 <label class="lbl" for="escola_destino">Instituição destino</label>
                                 <select id="escola_destino" onchange="mudarEscola(this.value)">
                                    <?php
                                    $res_escs = $db->query("SELECT id, nome_escola FROM escolas ORDER BY nome_escola");
                                    while ($re = $res_escs->fetch_assoc()) {
                                       $sel = ((int)$re['id'] === $esc_destino) ? 'selected' : '';
                                       echo '<option value="'.(int)$re['id'].'" '.$sel.'>'.htmlspecialchars($re['nome_escola'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                    ?>
                                 </select>
                              </div>
                              <div>
                                 <label class="lbl" for="sala_destino">Sala destino</label>
                                 <select name="sala_destino" id="sala_destino" required onchange="sugerirNomes()">
                                    <?php
                                    $st = $db->prepare("SELECT id, nome FROM salas WHERE id_escola = ? AND id <> ? ORDER BY nome");
                                    $st->bind_param("ii", $esc_destino, $said);
                                    $st->execute();
                                    $res_salas = $st->get_result();
                                    $tem_salas = false;
                                    while ($rs = $res_salas->fetch_assoc()) {
                                       $tem_salas = true;
                                       echo '<option value="'.(int)$rs['id'].'">'.htmlspecialchars($rs['nome'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                    $st->close();
                                    if (!$tem_salas) echo '<option value="">-- Sem salas disponíveis --</option>';
                                    ?>
                                 </select>
                              </div>
                           </div>

                           <div class="opcoes-copia">
                              <label><input type="checkbox" name="copiar_software" value="1" checked> Copiar programas instalados</label>
                              <label><input type="checkbox" name="copiar_marca" value="1" checked> Copiar Marca / Modelo</label>
                              <label><input type="checkbox" name="copiar_cpu" value="1" checked> Copiar CPU</label>
                              <label><input type="checkbox" name="copiar_ram" value="1" checked> Copiar RAM (GB)</label>
                              <label><input type="checkbox" name="copiar_disco" value="1" checked> Copiar Disco (GB)</label>
                              <label><input type="checkbox" name="copiar_monitor" value="1" checked> Copiar Monitor</label>
                           </div>
                        </div>

                        <?php if ($total_equip > 0): ?>
                        <div class="gei-section-label">Equipamentos informáticos</div>
                        <p class="dica">Todos os dados são copiados; só mudam a sala e o nome. Se o nome original contém o nome da sala de origem, o novo nome é sugerido automaticamente — pode editá-lo. As avarias, manutenções e requisições não são copiadas.</p>
                        <div class="gei-table-wrap">
                           <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:12px 16px;background:#f4f6fb;border-bottom:1px solid #e3e8f4;">
                              <label class="sel-all-wrap">
                                 <input type="checkbox" id="sel_todos" onchange="toggleTodos(this)" checked style="width:16px;height:16px;cursor:pointer;">
                                 Selecionar todos (<?php echo $total_equip; ?>)
                              </label>
                              <span id="contador_sel" style="font-size:.8rem;color:#7b88a0;font-weight:600;"></span>
                           </div>
                           <table class="gei-table">
                              <thead>
                                 <tr>
                                    <th style="width:40px;text-align:center;"></th>
                                    <th>Tipo</th>
                                    <th>Nome atual</th>
                                    <th>Marca / Modelo</th>
                                    <th>Novo nome</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php while ($eq = $res_equip->fetch_assoc()): ?>
                              <tr>
                                 <td style="text-align:center;">
                                    <input type="checkbox" name="equip[]" value="<?php echo (int)$eq['id']; ?>" class="chk_equip" checked onchange="atualizarContador()" style="width:16px;height:16px;cursor:pointer;">
                                 </td>
                                 <td style="font-size:.75rem;text-transform:uppercase;color:#7b88a0;font-weight:700;"><?php echo htmlspecialchars($eq['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td style="font-weight:600;color:#182848;"><?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td><?php echo htmlspecialchars($eq['marca_modelo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td>
                                    <input type="text" class="novo-nome" name="nome[<?php echo (int)$eq['id']; ?>]" maxlength="50"
                                           data-orig="<?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>"
                                           value="<?php echo htmlspecialchars($eq['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>">
                                 </td>
                              </tr>
                              <?php endwhile; ?>
                              </tbody>
                           </table>
                        </div>
                        <?php endif; ?>

                        <?php if ($total_outro > 0): ?>
                        <div class="gei-section-label">Outro equipamento (opcional)</div>
                        <div class="gei-table-wrap">
                           <table class="gei-table">
                              <thead>
                                 <tr>
                                    <th style="width:40px;text-align:center;"></th>
                                    <th>Nome</th>
                                    <th>Quantidade</th>
                                    <th>Observações</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php while ($ou = $res_outro->fetch_assoc()): ?>
                              <tr>
                                 <td style="text-align:center;">
                                    <input type="checkbox" name="outro[]" value="<?php echo (int)$ou['id']; ?>" style="width:16px;height:16px;cursor:pointer;">
                                 </td>
                                 <td><?php echo htmlspecialchars($ou['nomeoutro'], ENT_QUOTES, 'UTF-8'); ?></td>
                                 <td><?php echo (int)$ou['qta']; ?></td>
                                 <td><?php echo htmlspecialchars($ou['observacoes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                              </tr>
                              <?php endwhile; ?>
                              </tbody>
                           </table>
                        </div>
                        <?php endif; ?>

                        <div style="text-align:center;margin-top:10px;">
                           <button type="submit" id="btn_copiar" class="btn-submit">
                              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:5px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                              Copiar para a sala destino
                           </button>
                        </div>
                     </form>

                     <?php else: ?>
                     <div style="text-align:center;padding:40px;color:#7b88a0;font-size:.9rem;">
                        Não existem equipamentos nesta sala para copiar.
                     </div>
                     <?php endif; ?>

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
var NOME_SALA_ORIGEM = <?php echo json_encode($nome_sala, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
var URL_BASE_FORM    = <?php echo json_encode($url_form); ?>;

// Mudar de instituição recarrega a página (para listar as salas dessa instituição)
function mudarEscola(idEscola) {
    window.location.href = URL_BASE_FORM + '&ed=' + encodeURIComponent(idEscola);
}

function escapeRegExp(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

// Sugere novos nomes: substitui o nome da sala de origem pelo da sala destino
// (só nas linhas que o utilizador ainda não editou à mão)
function sugerirNomes() {
    var sel = document.getElementById('sala_destino');
    if (!sel || sel.selectedIndex < 0 || !sel.value) return;
    var nomeDestino = sel.options[sel.selectedIndex].text;
    var re = NOME_SALA_ORIGEM ? new RegExp(escapeRegExp(NOME_SALA_ORIGEM), 'gi') : null;
    document.querySelectorAll('.novo-nome').forEach(function (inp) {
        if (inp.dataset.manual === '1') return;
        var orig = inp.dataset.orig;
        inp.value = re ? orig.replace(re, function () { return nomeDestino; }) : orig;
    });
}

document.querySelectorAll('.novo-nome').forEach(function (inp) {
    inp.addEventListener('input', function () { inp.dataset.manual = '1'; });
});

function toggleTodos(cb) {
    document.querySelectorAll('.chk_equip').forEach(function (c) { c.checked = cb.checked; });
    atualizarContador();
}

function atualizarContador() {
    var chks = document.querySelectorAll('.chk_equip');
    var sel  = document.querySelectorAll('.chk_equip:checked').length;
    var cont = document.getElementById('contador_sel');
    if (cont) cont.textContent = sel + ' selecionado' + (sel !== 1 ? 's' : '');
    chks.forEach(function (c) {
        var inp = c.closest('tr').querySelector('.novo-nome');
        if (inp) inp.disabled = !c.checked;
    });
    var todos = document.getElementById('sel_todos');
    if (todos) {
        todos.indeterminate = (sel > 0 && sel < chks.length);
        todos.checked = (sel === chks.length && chks.length > 0);
    }
}

(function () {
    var form = document.getElementById('frm_copiar');
    if (!form) return;

    atualizarContador();
    sugerirNomes();

    form.addEventListener('submit', function (e) {
        if (form.dataset.confirmado === '1') return;
        e.preventDefault();

        var salaSel = document.getElementById('sala_destino');
        if (!salaSel.value) { swal('Atenção', 'Selecione uma sala de destino.', 'warning'); return; }

        var nEquip  = document.querySelectorAll('.chk_equip:checked').length;
        var nOutro  = document.querySelectorAll('input[name="outro[]"]:checked').length;
        if (nEquip + nOutro === 0) { swal('Atenção', 'Selecione pelo menos um equipamento.', 'warning'); return; }

        // Nomes não vazios e sem repetições (as repetições na sala destino são verificadas no servidor)
        var vistos = {}, problema = '';
        document.querySelectorAll('.chk_equip:checked').forEach(function (c) {
            var inp = c.closest('tr').querySelector('.novo-nome');
            var v = inp.value.trim();
            if (!v) { problema = 'Existem equipamentos selecionados com o novo nome em branco.'; return; }
            var k = v.toLowerCase();
            if (vistos[k]) { problema = 'O nome "' + v + '" está repetido no formulário.'; return; }
            vistos[k] = true;
        });
        if (problema) { swal('Atenção', problema, 'warning'); return; }

        var salaTxt = salaSel.options[salaSel.selectedIndex].text;
        // SweetAlert 1.x (o jquery_bootstrap.php carrega a v1.1.3 e sobrepõe-se à do head.php)
        swal({
            title: 'Copiar para a sala ' + salaTxt + '?',
            text: nEquip + ' equipamento(s) informático(s)' + (nOutro ? ' e ' + nOutro + ' outro(s) equipamento(s)' : '') + ' serão criados na sala de destino.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Copiar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
        }, function (ok) {
            if (ok) {
                form.dataset.confirmado = '1';
                document.getElementById('btn_copiar').disabled = true;
                form.submit();
            }
        });
    });
})();
</script>

      <?php include("jquery_bootstrap.php"); ?>
      <?php include("footer.php"); ?>
   </body>
</html>
