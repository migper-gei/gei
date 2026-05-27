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
    <?php include ("head.php"); ?>
    <!-- Estilos personalizados -->
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
        
        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            background-color: #e9ecef;
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
    </style>
</head>

<body class="main-layout">
    <!-- loader -->
  
    <!-- end loader -->



    <?php


    $idescola = 1;
   
 

    if (!isset($_GET["ies"]) || !isset($_GET["qi"]) || !isset($_GET["z"]) 
        || empty($_GET["ies"]) || empty($_GET["qi"]) || empty($_GET["z"]) 
        || ($_GET["z"]) > 2 || ($_GET["z"]) < 1) {
    ?>
        
    <?php
    }

    
    ?>
    
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SVRURL ?>dashboard">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo SVRURL ?>equip">Equipamentos</a></li>
                        <li class="breadcrumb-item active">Dados Técnicos e de Rede</li>
                    </ol>
                </nav>
                
                <div class="card">
                    <h2 class="section-header">
                        <i class="fas fa-server"></i> Dados Técnicos e de Rede
                        <small class="text-muted d-block mt-2"></small>
                    </h2>

                    

                        <!-- Tabs for Technical and Network Data -->
                        <div class="equipment-tabs">
                            <ul class="nav nav-tabs" id="equipmentTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($z == 1) ? 'active' : ''; ?>" 
                                       href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id); ?>&z=1&&ies=<?php echo base64_encode($idescola) ?>" 
                                       role="tab">
                                        <i class="fas fa-microchip"></i> Dados Técnicos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($z == 2) ? 'active' : ''; ?>" 
                                       href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id); ?>&z=2&&ies=<?php echo base64_encode($idescola) ?>" 
                                       role="tab">
                                        <i class="fas fa-network-wired"></i> Dados de Rede
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content">
                                <?php if (1 == 1) { ?>
                                    <!-- Technical Data Form -->
                                    <div class="tab-pane fade show active">
                                        <form name="equipamento" action="<?php echo SVRURL ?>gravaequipdadostec.php?qi=<?php echo base64_encode($id)?>&&ies=<?php echo base64_encode($idescola) ?>" method="post">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="cpu">Processador</label>
                                                    <input class="form-control" id="cpu" type="text" name="cpu" placeholder="Intel Core i5-10400">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ram">Memória RAM (GB)</label>
                                                    <input class="form-control" id="ram" type="text" name="ram" 
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                           maxlength="2" placeholder="8">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="disco">Disco (GB)</label>
                                                    <input class="form-control" id="disco" type="text" name="disco" maxlength="10" placeholder="500">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="grafica">Placa Gráfica</label>
                                                    <input class="form-control" id="grafica" type="text" name="grafica" placeholder="NVIDIA GeForce GTX 1650">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="rede">Placa de Rede</label>
                                                    <input class="form-control" id="rede" type="text" name="rede" placeholder="Realtek PCIe GbE">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="som">Placa de Som</label>
                                                    <input class="form-control" id="som" type="text" name="som" placeholder="Realtek ALC662">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="monitor">Monitor</label>
                                                    <input class="form-control" id="monitor" type="text" name="monitor" placeholder="Samsung 24' LED">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="teclado">Teclado</label>
                                                    <input class="form-control" id="teclado" type="text" name="teclado" placeholder="Logitech K120">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="tecladointerface">Interface do Teclado</label>
                                                    <select class="form-control select-custom" id="tecladointerface" name="tecladointerface">
                                                        <option value=""></option>
                                                        <option value="USB">USB</option>
                                                        <option value="PS/2">PS/2</option>
                                                        <option value="Sem fios">Sem fios</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="rato">Rato</label>
                                                    <input class="form-control" id="rato" type="text" name="rato" placeholder="Logitech M185">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="ratointerface">Interface do Rato</label>
                                                    <select class="form-control select-custom" id="ratointerface" name="ratointerface">
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
                                                            <select class="form-control select-custom" name="colunas">
                                                                <option value=""></option>
                                                                <option value="Sim">Sim</option>
                                                                <option value="Não">Não</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">CD/DVD</label>
                                                            <select class="form-control select-custom" name="cddvd">
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
                                    <div class="tab-pane fade show active">
                                        <form name="form2" action="<?php echo SVRURL ?>gravaequipdadosrede.php?qi=<?php echo base64_encode($id)?>&&esi=<?php echo base64_encode($idescola) ?>" method="post">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="dominio">Domínio</label>
                                                    <input class="form-control" id="dominio" type="text" name="dominio" placeholder="exemplo.local">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ip">Endereço IP</label>
                                                    <input class="form-control" id="ip" type="text" name="ip" maxlength="15" placeholder="192.168.1.100">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="mascara">Máscara de Rede</label>
                                                    <input class="form-control" id="mascara" type="text" name="mascara" maxlength="15" placeholder="255.255.255.0">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="gateway">Gateway</label>
                                                    <input class="form-control" id="gateway" type="text" name="gateway" maxlength="15" placeholder="192.168.1.1">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsp">DNS Preferido</label>
                                                    <input class="form-control" id="dnsp" type="text" name="dnsp" maxlength="15" placeholder="8.8.8.8">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsa">DNS Alternativo</label>
                                                    <input class="form-control" id="dnsa" type="text" name="dnsa" maxlength="15" placeholder="8.8.4.4">
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-custom">
                                                    <i class="fas fa-save"></i> Guardar Dados de Rede
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <a href="<?php echo SVRURL ?>equip" class="back-button">
                            <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar"> Voltar à Lista de Equipamentos
                        </a>
               
                </div>
            </div>
        </div>
    </div>

    <?php include ("footer.php"); ?>

    <!-- Scripts adicionais -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight form fields on focus
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#3498db';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(52, 152, 219, 0.25)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.borderColor = '#ced4da';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>
