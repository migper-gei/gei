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
    // Regenerar ID periodicamente (previne session fixation)
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
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        
    <!-- Welcome Section -->
           <div class="welcome-section"> 
<?php
include("msg_bemvindo.php");
?>
   </div>


            <?php
                $sql2a = "select max(id) as me  from escolas ";
                $result2a = mysqli_query($db,$sql2a); 
                $rows2a =mysqli_fetch_row($result2a);
                
                
                $maxesc = $rows2a[0];

                 
               $id = (int)base64_decode($_GET['ide']);
               $sa = (int)base64_decode($_GET['sa']);
               $ies = (int)base64_decode($_GET['ies']);

               if ($ies>$maxesc || $ies<0 
                || !isset($id)   || !is_numeric($id)    ||  empty($id) 
               || !isset($sa)   || !is_numeric($sa)    ||  empty($sa) 
               || !isset($ies)   || !is_numeric($ies)    ||  empty($ies) 
               )
               
               {
               ?>
               
               <script>
               window.setTimeout(function() {
                  window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
               
               
               <?php
               
               }
               








                $sql = "select e.*,e.nomeequi as neq,s.nome as nos,e.tipo as ti 
                from equipamento e, salas s
                where s.id=e.id_sala
                and e.id=".$id."";
                $result = mysqli_query($db,$sql); 
                $row=mysqli_fetch_array($result);
               ?>

               <!-- Cabeçalho com info do equipamento -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Tipo</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['ti'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Equipamento</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['neq'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['nos'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>
         
                          
    
<style>
    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 10px 18px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
    }
    .nav-tabs .nav-link.active {
        background-color: #3498db !important;
        color: #fff !important;
        border: none !important;
        border-bottom: 3px solid #1a6aad !important;
    }
    .nav-tabs .nav-link.active i { color: #fff !important; }
    .nav-tabs .nav-link:not(.active) i { color: #8a99b0; }
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: #e4eef8;
        color: #3498db;
    }
    .tab-content {
        padding: 25px;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        background-color: white;
    }
    .tec-form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    .tec-form-col {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0 10px;
        margin-bottom: 14px;
    }
    @media (max-width: 768px) {
        .tec-form-col { flex: 0 0 100%; max-width: 100%; }
    }
    .tec-form-col label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        display: block;
        font-size: .88rem;
    }
</style>

<div class="equipment-tabs" style="margin-top:10px;">
    <ul class="nav nav-tabs" id="tecRedesTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#tab-tec" onclick="switchTab(event,'tab-tec')">
                <i class="fas fa-microchip"></i> Dados Técnicos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#tab-rede" onclick="switchTab(event,'tab-rede')">
                <i class="fas fa-network-wired"></i> Dados de Rede
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB: Dados Técnicos -->
        <div id="tab-tec" style="display:block;">
            <form name="equipamento_tec" action="<?php echo SVRURL ?>atualiza_dadostecredes_OK.php?id=<?php echo base64_encode($id)?>&parte=tec" method="post">

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-microchip"></i> Processador</label>
                        <input class="form-control" type="text" name="cpu" placeholder="Processador"
                               value="<?php echo htmlspecialchars($row['processador'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-memory"></i> Memória (GB)</label>
                        <input class="form-control" type="text" name="ram" placeholder="Memória (GB)" maxlength="2"
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                               value="<?php echo htmlspecialchars($row['memoria'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-hdd"></i> Disco</label>
                        <input class="form-control" type="text" name="disco" placeholder="Disco"
                               value="<?php echo htmlspecialchars($row['disco'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-photo-video"></i> Placa gráfica</label>
                        <input class="form-control" type="text" name="grafica" placeholder="Placa gráfica"
                               value="<?php echo htmlspecialchars($row['placagrafica'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-network-wired"></i> Placa rede</label>
                        <input class="form-control" type="text" name="rede" placeholder="Placa rede"
                               value="<?php echo htmlspecialchars($row['placarede'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-volume-up"></i> Placa som</label>
                        <input class="form-control" type="text" name="som" placeholder="Placa som"
                               value="<?php echo htmlspecialchars($row['placasom'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-desktop"></i> Monitor</label>
                        <input class="form-control" type="text" name="monitor" placeholder="Monitor"
                               value="<?php echo htmlspecialchars($row['monitor'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-keyboard"></i> Teclado</label>
                        <div style="display:flex;gap:6px;">
                            <input class="form-control" type="text" name="teclado" placeholder="Teclado"
                                   value="<?php echo htmlspecialchars($row['teclado'], ENT_QUOTES, 'UTF-8')?>">
                            <select class="form-control" name="tecladointerface" style="max-width:120px;"
                                    data-val="<?php echo htmlspecialchars($row['tecladointerface'], ENT_QUOTES, 'UTF-8')?>">
                                <option value=""></option>
                                <option value="USB">USB</option>
                                <option value="PS/2">PS/2</option>
                                <option value="Sem fios">Sem fios</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-mouse"></i> Rato</label>
                        <div style="display:flex;gap:6px;">
                            <input class="form-control" type="text" name="rato" placeholder="Rato"
                                   value="<?php echo htmlspecialchars($row['rato'], ENT_QUOTES, 'UTF-8')?>">
                            <select class="form-control" name="ratointerface" style="max-width:120px;"
                                    data-val="<?php echo htmlspecialchars($row['ratointerface'], ENT_QUOTES, 'UTF-8')?>">
                                <option value=""></option>
                                <option value="USB">USB</option>
                                <option value="PS/2">PS/2</option>
                                <option value="Sem fios">Sem fios</option>
                            </select>
                        </div>
                    </div>
                    <div class="tec-form-col">
                        <div style="display:flex;gap:20px;">
                            <div style="flex:1;">
                                <label><i class="fas fa-volume-up"></i> Colunas</label>
                                <select class="form-control" name="colunas"
                                        data-val="<?php echo htmlspecialchars($row['colunas'], ENT_QUOTES, 'UTF-8')?>">
                                    <option value=""></option>
                                    <option value="Sim">Sim</option>
                                    <option value="Não">Não</option>
                                </select>
                            </div>
                            <div style="flex:1;">
                                <label><i class="fas fa-compact-disc"></i> CD/DVD</label>
                                <select class="form-control" name="cddvd"
                                        data-val="<?php echo htmlspecialchars($row['cd_dvd'], ENT_QUOTES, 'UTF-8')?>">
                                    <option value=""></option>
                                    <option value="Sim">Sim</option>
                                    <option value="Não">Não</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> &nbsp;Guardar Dados Técnicos
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB: Dados de Rede -->
        <div id="tab-rede" style="display:none;">
            <form name="equipamento_rede" action="<?php echo SVRURL ?>atualiza_dadostecredes_OK.php?id=<?php echo base64_encode($id)?>&parte=rede" method="post">

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-sitemap"></i> Domínio</label>
                        <input class="form-control" type="text" name="dominio" placeholder="Domínio"
                               value="<?php echo htmlspecialchars($row['dominio'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-network-wired"></i> Endereço IP</label>
                        <input class="form-control" type="text" name="ip" maxlength="15" placeholder="Endereço IP"
                               value="<?php echo htmlspecialchars($row['ip'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-mask"></i> Máscara de rede</label>
                        <input class="form-control" type="text" name="mascara" maxlength="15" placeholder="Máscara de rede"
                               value="<?php echo htmlspecialchars($row['mascara_rede'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-door-open"></i> Gateway</label>
                        <input class="form-control" type="text" name="gateway" maxlength="15" placeholder="Gateway"
                               value="<?php echo htmlspecialchars($row['gateway'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tec-form-row">
                    <div class="tec-form-col">
                        <label><i class="fas fa-search"></i> DNS preferido</label>
                        <input class="form-control" type="text" name="dnsp" maxlength="15" placeholder="DNS preferido"
                               value="<?php echo htmlspecialchars($row['dns_principal'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="tec-form-col">
                        <label><i class="fas fa-search-plus"></i> DNS alternativo</label>
                        <input class="form-control" type="text" name="dnsa" maxlength="15" placeholder="DNS alternativo"
                               value="<?php echo htmlspecialchars($row['dns_alternativo'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> &nbsp;Guardar Dados de Rede
                    </button>
                </div>
            </form>
        </div>

    </div><!-- /tab-content -->
</div><!-- /equipment-tabs -->

<script>
function switchTab(e, tabId) {
    e.preventDefault();
    // Hide all tab panels
    document.querySelectorAll('#tecRedesTabs + .tab-content > div').forEach(function(p) {
        p.style.display = 'none';
    });
    // Remove active from all nav links
    document.querySelectorAll('#tecRedesTabs .nav-link').forEach(function(l) {
        l.classList.remove('active');
    });
    // Show selected panel and mark link active
    document.getElementById(tabId).style.display = 'block';
    e.currentTarget.classList.add('active');
}
</script>

<script>
document.querySelectorAll('select[data-val]').forEach(function(sel) {
    var val = sel.getAttribute('data-val');
    if (!val) return;
    for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === val) { sel.selectedIndex = i; break; }
    }
});
</script>


<a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    



      <?php include ("jquery_bootstrap.php");?>
      <?php include ("footer.php");?>


   </body>
</html>