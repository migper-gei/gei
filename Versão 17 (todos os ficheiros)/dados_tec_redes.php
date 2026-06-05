
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
      
   <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: #f5f8fa;
        }
        
        .card {
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            padding: 25px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link {
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 12px 20px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link {
            position: relative;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            border: none !important;
            border-bottom: 3px solid #1a6aad !important;
        }

        .nav-tabs .nav-link.active i {
            color: #fff !important;
        }

        .nav-tabs .nav-link:not(.active) i {
            color: #8a99b0;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: #e4eef8;
            color: var(--primary-color);
        }
        
        .tab-content {
            padding: 25px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            background-color: white;
        }
        
        .section-header {
            color: var(--secondary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .form-control {
            padding: 12px;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .equipment-info {
            background-color: #e9f7fe;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            transition: all 0.3s;
            border: 1px solid var(--primary-color);
            margin-top: 20px;
        }
        
        .back-button:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .back-button img {
            margin-right: 8px;
            height: 20px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .form-group {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .form-group {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: var(--secondary-color);
        }
        
        .select-custom {
            height: 45px;
            padding: 0 12px;
        }
        
        .equipment-tabs {
            margin-bottom: 30px;
        }

        /* ── Software tab styles ── */
        .sw-table-wrap { overflow-x:auto; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); margin-top:18px; }
        .sw-table { width:100%; border-collapse:collapse; font-size:.85rem; background:#fff; }
        .sw-table thead tr { background:linear-gradient(90deg,#4b6cb7 0%,#182848 100%); color:#fff; }
        .sw-table thead th { padding:10px 13px; font-weight:600; white-space:nowrap; border:none; }
        .sw-table tbody tr { border-bottom:1px solid #eef0f7; transition:background .15s; }
        .sw-table tbody tr:hover { background:#f4f6fb; }
        .sw-table tbody td { padding:9px 13px; vertical-align:middle; color:#2d3748; }
        .sw-table tbody td.td-nome { font-weight:600; color:#182848; }
        .sw-table .badge-lic { display:inline-block; background:#e8eefa; color:#4b6cb7; border-radius:5px; padding:2px 8px; font-size:.78rem; font-weight:600; }
        .sw-table .btn-del { background:none; border:none; color:#dc3545; cursor:pointer; font-size:.9rem; padding:2px 6px; border-radius:5px; transition:background .15s; }
        .sw-table .btn-del:hover { background:#fde8ea; }
        .sw-empty { text-align:center; padding:30px; color:#8a93a8; font-size:.9rem; }
        .sw-form-card { background:#fff; border:1px solid #e3e8f4; border-radius:12px; padding:22px 26px; margin-top:10px; box-shadow:0 2px 10px rgba(75,108,183,.08); }
        .sw-form-card .form-group { margin-bottom:12px; }
        .sw-form-title { font-size:.95rem; font-weight:700; color:#182848; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .sw-counter { display:inline-flex; align-items:center; gap:6px; background:#e8eefa; color:#4b6cb7; font-weight:700; font-size:.8rem; padding:3px 10px; border-radius:20px; margin-left:8px; }
        .sw-alert-ok { background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:8px; padding:10px 16px; font-size:.88rem; font-weight:600; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
    </style>







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

$idescola = (int)base64_decode($_GET["ies"]);

$id = (int)base64_decode($_GET["qi"]);


$z=$_GET["z"];



if ( !isset($_GET["ies"]) || !isset($_GET["qi"]) || !isset($_GET["z"]) 
|| empty($_GET["ies"])  || empty($_GET["qi"])  || empty($_GET["z"]) || ($_GET["z"])>3 || ($_GET["z"])<1
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





$sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
               <div class="titlepage">
                     <h2>Dados técnicos e de rede
                        <br> <?php echo $ne ?>

                     </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        



<?php

$sql2 = "select count(*) from equipamento where id=".$id."";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];

if ($conta==0) 
{
?>
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(1)?>&escola=<?php echo $idescola ?>';
   }, 10);
   </script>
<?php
}
else
{
   
$sql3 = "select e.nomeequi as neq,s.nome as nos,e.tipo as ti from equipamento e, salas s
where s.id=e.id_sala
and e.id=".$id."";
$result3 = mysqli_query($db,$sql3); 
$row=mysqli_fetch_array($result3);

// Carregar dados técnicos e de rede existentes
$sql_dados = "SELECT processador, memoria, disco, placagrafica, placasom, placarede,
                     monitor, teclado, tecladointerface, rato, ratointerface, colunas, cd_dvd,
                     dominio, ip, mascara_rede, gateway, dns_principal, dns_alternativo
              FROM equipamento WHERE id=" . $id;
$result_dados = mysqli_query($db, $sql_dados);
$dados = mysqli_fetch_assoc($result_dados);
$dados = $dados ?: [];
?>

<div  style=" text-align:center;width:90%">
<?php
echo('<h4>');
echo htmlspecialchars($row['ti'], ENT_QUOTES, 'UTF-8');
echo(" | ");
echo htmlspecialchars($row['neq'], ENT_QUOTES, 'UTF-8');
echo(" | ");
echo htmlspecialchars($row['nos'], ENT_QUOTES, 'UTF-8');
echo('</h4>');
   ?>
</div>




<br>

                        <!-- Tabs for Technical and Network Data -->
                        <div class="equipment-tabs">
                            <ul class="nav nav-tabs" id="equipmentTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($z == 1) ? 'active' : ''; ?>" 
                                       href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id); ?>&z=1&ies=<?php echo base64_encode($idescola) ?>" 
                                       role="tab">
                                        <i class="fas fa-microchip"></i> Dados Técnicos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($z == 2) ? 'active' : ''; ?>" 
                                       href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id); ?>&z=2&ies=<?php echo base64_encode($idescola) ?>" 
                                       role="tab">
                                        <i class="fas fa-network-wired"></i> Dados de Rede
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($z == 3) ? 'active' : ''; ?>"
                                       href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id); ?>&z=3&ies=<?php echo base64_encode($idescola) ?>"
                                       id="tab-sw-link"
                                       role="tab">
                                        <i class="fas fa-box-open"></i> Software
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content">
                                <?php if ($z == 1) { ?>
                                    <!-- Technical Data Form -->
                                    <div id="tab-z-content" class="tab-pane fade show active">
                                        <form name="equipamento" action="<?php echo SVRURL ?>gravaequipdadostec.php?qi=<?php echo base64_encode($id)?>&ies=<?php echo base64_encode($idescola) ?>" method="post">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="cpu">Processador</label>
                                                    <input class="form-control" id="cpu" type="text" name="cpu" placeholder="" value="<?php echo htmlspecialchars($dados['processador'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ram">Memória RAM (GB)</label>
                                                    <input class="form-control" id="ram" type="text" name="ram" 
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                           maxlength="2" placeholder="" value="<?php echo htmlspecialchars($dados['memoria'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="disco">Disco (GB)</label>
                                                    <input class="form-control" id="disco" type="text" name="disco" maxlength="10" placeholder="" value="<?php echo htmlspecialchars($dados['disco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="grafica">Placa Gráfica</label>
                                                    <input class="form-control" id="grafica" type="text" name="grafica" placeholder="" value="<?php echo htmlspecialchars($dados['placagrafica'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="rede">Placa de Rede</label>
                                                    <input class="form-control" id="rede" type="text" name="rede" placeholder="" value="<?php echo htmlspecialchars($dados['placarede'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="som">Placa de Som</label>
                                                    <input class="form-control" id="som" type="text" name="som" placeholder="" value="<?php echo htmlspecialchars($dados['placasom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="monitor">Monitor</label>
                                                    <input class="form-control" id="monitor" type="text" name="monitor" placeholder="" value="<?php echo htmlspecialchars($dados['monitor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="teclado">Teclado</label>
                                                    <input class="form-control" id="teclado" type="text" name="teclado" placeholder="" value="<?php echo htmlspecialchars($dados['teclado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="tecladointerface">Interface do Teclado</label>
                                                    <select class="form-control select-custom" id="tecladointerface" name="tecladointerface" data-val="<?php echo htmlspecialchars($dados['tecladointerface'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                        <option value=""></option>
                                                        <option value="USB">USB</option>
                                                        <option value="PS/2">PS/2</option>
                                                        <option value="Sem fios">Sem fios</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="rato">Rato</label>
                                                    <input class="form-control" id="rato" type="text" name="rato" placeholder="" value="<?php echo htmlspecialchars($dados['rato'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="ratointerface">Interface do Rato</label>
                                                    <select class="form-control select-custom" id="ratointerface" name="ratointerface" data-val="<?php echo htmlspecialchars($dados['ratointerface'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                        <option value=""></option>
                                                        <option value="USB">USB</option>
                                                        <option value="PS/2">PS/2</option>
                                                        <option value="Sem fios">Sem fios</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Colunas</label>
                                                            <select class="form-control select-custom" name="colunas" data-val="<?php echo htmlspecialchars($dados['colunas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                <option value=""></option>
                                                                <option value="Sim">Sim</option>
                                                                <option value="Não">Não</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">CD/DVD</label>
                                                            <select class="form-control select-custom" name="cddvd" data-val="<?php echo htmlspecialchars($dados['cd_dvd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                <option value=""></option>
                                                                <option value="Sim">Sim</option>
                                                                <option value="Não">Não</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-custom">
                                                    <i class="fas fa-save"></i> Guardar Dados Técnicos
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php } elseif ($z == 2) { ?>
                                    <!-- Network Data Form -->
                                    <div id="tab-z-content" class="tab-pane fade show active">
                                        <form name="form2" action="<?php echo SVRURL ?>gravaequipdadosrede.php?qi=<?php echo base64_encode($id)?>&esi=<?php echo base64_encode($idescola) ?>" method="post">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="dominio">Domínio</label>
                                                    <input class="form-control" id="dominio" type="text" name="dominio" placeholder="" value="<?php echo htmlspecialchars($dados['dominio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ip">Endereço IP</label>
                                                    <input class="form-control" id="ip" type="text" name="ip" maxlength="15" placeholder="" value="<?php echo htmlspecialchars($dados['ip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="mascara">Máscara de Rede</label>
                                                    <input class="form-control" id="mascara" type="text" name="mascara" maxlength="15" placeholder="" value="<?php echo htmlspecialchars($dados['mascara_rede'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="gateway">Gateway</label>
                                                    <input class="form-control" id="gateway" type="text" name="gateway" maxlength="15" placeholder="" value="<?php echo htmlspecialchars($dados['gateway'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsp">DNS Preferido</label>
                                                    <input class="form-control" id="dnsp" type="text" name="dnsp" maxlength="15" placeholder="" value="<?php echo htmlspecialchars($dados['dns_principal'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsa">DNS Alternativo</label>
                                                    <input class="form-control" id="dnsa" type="text" name="dnsa" maxlength="15" placeholder="" value="<?php echo htmlspecialchars($dados['dns_alternativo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-custom">
                                                    <i class="fas fa-save"></i> Guardar Dados de Rede
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php } elseif ($z == 3) { ?>
                                    <!-- Software -->
                                    <div id="tab-z-content" class="tab-pane fade show active">
                                        <?php include("software_equipamento_ajax.php"); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

<?php
}



?>


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
    


      <?php include ("footer.php");?>




   </body>
</html>