<?php
  session_start();
  session_regenerate_id();
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







<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));

$idescola=base64_decode($_GET["ies"]);

$id=base64_decode($_GET["qi"]);


$z=$_GET["z"];



if ( !isset($_GET["ies"]) || !isset($_GET["qi"]) || !isset($_GET["z"]) 
|| empty($_GET["ies"])  || empty($_GET["qi"])  || empty($_GET["z"]) || ($_GET["z"])>2 || ($_GET["z"])<1
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
               <a href="#" class="btn btn-secondary disabled">Equipamentos >> Inserir</a>
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



//echo ($id);

$sql2 = "select count(*) from equipamento where id=".$id."";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];

if ($conta==0) 
{
?>
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(1)?>&&escola=<?php echo $idescola ?>';
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
?>

<div  style=" text-align:center;width:90%">
<?php
echo('<h4>');
echo $row['ti'];
echo(" | ");
echo $row['neq'];
echo(" | ");
echo $row['nos'];
echo('</h4>');
   ?>
</div>




<br>

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
                                <?php if ($z == 1) { ?>
                                    <!-- Technical Data Form -->
                                    <div class="tab-pane fade show active">
                                        <form name="equipamento" action="<?php echo SVRURL ?>gravaequipdadostec.php?qi=<?php echo base64_encode($id)?>&&ies=<?php echo base64_encode($idescola) ?>" method="post">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="cpu">Processador</label>
                                                    <input class="form-control" id="cpu" type="text" name="cpu" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ram">Memória RAM (GB)</label>
                                                    <input class="form-control" id="ram" type="text" name="ram" 
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                           maxlength="2" placeholder="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="disco">Disco (GB)</label>
                                                    <input class="form-control" id="disco" type="text" name="disco" maxlength="10" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="grafica">Placa Gráfica</label>
                                                    <input class="form-control" id="grafica" type="text" name="grafica" placeholder="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="rede">Placa de Rede</label>
                                                    <input class="form-control" id="rede" type="text" name="rede" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="som">Placa de Som</label>
                                                    <input class="form-control" id="som" type="text" name="som" placeholder="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="monitor">Monitor</label>
                                                    <input class="form-control" id="monitor" type="text" name="monitor" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="teclado">Teclado</label>
                                                    <input class="form-control" id="teclado" type="text" name="teclado" placeholder="">
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
                                                    <input class="form-control" id="rato" type="text" name="rato" placeholder="">
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
                                                    <input class="form-control" id="dominio" type="text" name="dominio" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="ip">Endereço IP</label>
                                                    <input class="form-control" id="ip" type="text" name="ip" maxlength="15" placeholder="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="mascara">Máscara de Rede</label>
                                                    <input class="form-control" id="mascara" type="text" name="mascara" maxlength="15" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="gateway">Gateway</label>
                                                    <input class="form-control" id="gateway" type="text" name="gateway" maxlength="15" placeholder="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsp">DNS Preferido</label>
                                                    <input class="form-control" id="dnsp" type="text" name="dnsp" maxlength="15" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="dnsa">DNS Alternativo</label>
                                                    <input class="form-control" id="dnsa" type="text" name="dnsa" maxlength="15" placeholder="">
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

<?php
}



?>


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