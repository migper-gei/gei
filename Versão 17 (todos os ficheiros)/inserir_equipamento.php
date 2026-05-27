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

<?php
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
   

<?php
 include ("css_inserir.php");
 include ("head.php");
?>

<script>
var equipExiste = false;
var _checkEquipTimer = null;

function verificaEquipDuplicado() {
    var nome   = document.getElementById('nomeq').value.trim();
    var sala   = document.getElementById('sala').value;
    var errDiv = document.getElementById('equip_err');

    if (!nome || !sala) {
        errDiv.innerHTML = '';
        equipExiste = false;
        clearTimeout(_checkEquipTimer);
        return;
    }

    clearTimeout(_checkEquipTimer);
    _checkEquipTimer = setTimeout(function() {
        errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar...</small>';
        equipExiste = false;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_equipamento.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.existe) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; Já existe um equipamento com este nome nesta sala.</small>';
                        equipExiste = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Disponível</small>';
                        equipExiste = false;
                    }
                } catch(e) { errDiv.innerHTML = ''; }
            } else { errDiv.innerHTML = ''; }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; equipExiste = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; equipExiste = false; };
        xhr.send('nome=' + encodeURIComponent(nome) + '&sala=' + encodeURIComponent(sala));
    }, 400);
}
</script>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php");?>
     

     <?php
//session_start();

include("sessao_timeout.php");

//include("verifica_sessao.php");

$sql2a =  $db->prepare("select max(id) as me  from escolas ");
//$result2a = mysqli_query($db,$sql2a); 
//$rows2a =mysqli_fetch_row($result2a);

$sql2a->execute();
$rows2a = $sql2a->get_result()->fetch_row();

$maxesc = $rows2a[0];
$x = (int)base64_decode($_GET["x"]);

if ($x>1 || $x<0 || base64_decode($_GET["ies"])>$maxesc)
{

?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>

<?php
}

if ($x==1)
{
$idescola = (int)base64_decode($_GET["ies"]);
}
elseif ($x==0)
{
$idescola = (int)base64_decode($_GET["ies"]);

}
 

$sql11 = $db->prepare("select nome_escola from escolas where id=?");
$sql11->bind_param("i", $idescola);
$sql11->execute();

$rows11= $sql11->get_result()->fetch_row();
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
                             <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamento</a>
               
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
              <!--
               <div class="titlepage">
              
                  </div>
              -->

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
           

           <!-- Welcome Section -->
 <div class="welcome-section">              
<?php
include("msg_bemvindo.php");
?>
</div>

       
                        

               <!-- ========================================================
                    CABECALHO: escola (abaixo do utilizador)
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.05rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
               </div>
               <!-- ===== FIM CABECALHO ===== -->
<div class="form-container">
                        
                         <div class="step-indicator">

                        

                             <i class="fas fa-info-circle mr-2"></i>
                                Complete todos os campos obrigatórios (indicados com fundo azul claro)
                            </div>

                            <script language="javascript" type="text/javascript">
                                function showescdig(escola) {
                                    document.frme.submit();
                                }
                            </script>

                            <form name="frme" id="frme" action="" method="post" class="mb-4">
                                <div class="form-group row">
                                    <label for="escdig" class="col-sm-4 col-form-label">Equipamento Escola Digital:</label>
                                    
                                    <div class="col-sm-8">
                                        <select onChange="showescdig(this.value);" required name="escdig" class="form-control required-field" id="escdig">
                                            <?php
                                            if ($_REQUEST["escdig"] == "Sim") {
                                                echo('<option selected value="Sim">Sim</option>');
                                                echo('<option value="Não">Não</option>');
                                            } else {
                                                echo('<option selected value="Não">Não</option>');
                                                echo('<option value="Sim">Sim</option>');
                                            }
                                            ?>     
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <?php
                            if (!empty($_POST["escdig"])) {
                                $escdig = $_POST["escdig"];
                            } else {
                                $escdig = "Não";
                            }
                            ?>

                            <form name="equipamento" action="<?php echo SVRURL ?>gravaequip?ies=<?php echo base64_encode($idescola);?>&&ed=<?php echo base64_encode($escdig);?>" method="post" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if ($escdig == "Sim") { ?>
                                    <div class="form-group row">
                                        <label for="numinv" class="col-sm-4 col-form-label">Nº inventário Dgest:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="numinv" id="numinv" class="form-control required-field" placeholder="Nº inventário Dgest">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="fornecedor" class="col-sm-4 col-form-label">Fornecedor:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="fornecedor" id="fornecedor" class="form-control required-field" placeholder="Fornecedor">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="emailfornecedor" class="col-sm-4 col-form-label">Email do fornecedor:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="email" id="emailfornecedor" class="form-control required-field" placeholder="Email do fornecedor"
                                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$">
                                        </div>
                                       
                                    </div>

                                    <div class="form-group row">
                                        <label for="nifpessoa" class="col-sm-4 col-form-label">NIF da pessoa:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required maxlength="9" type="text" 
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                            name="nifpessoa" id="nifpessoa" class="form-control required-field" placeholder="NIF da pessoa">
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group row">
                                    <label for="tipoeq" class="col-sm-4 col-form-label">Tipo de equipamento:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select name="tipoeq" id="tipoeq" required  class="form-control required-field">
                                                <?php
                                                $sql = $db->prepare("SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome");
                                                $sql->execute();
                                                $result = $sql->get_result();

                                                echo('<option value=""> -- Selecione -- </option>');  

                                                while($row = mysqli_fetch_array($result)) {
                                                    echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');
                                                }
                                                ?>
                                            </select>

                                            <!--
                                            <a    style="color:Gainsboro;" class="btn btn-outline-secondary" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">
                                                <i class="bi bi-plus-circle"></i> Novo
                                            </a>
-->
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sala" class="col-sm-4 col-form-label">Sala:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?php
                                            $sql = $db->prepare("select * FROM salas where id_escola=? order by nome");
                                            $sql->bind_param("i", $idescola);
                                            $sql->execute();
                                            $result = $sql->get_result();
                                            $rowcount = mysqli_num_rows($result);
                                            ?>

                                            <select name="sala" id="sala" class="form-control required-field" required onchange="verificaEquipDuplicado()">
                                                <?php
                                                if ($rowcount > 0) {
                                                    echo('<option value=""> -- Selecione -- </option>');  
                                                    
                                                    while($row2 = mysqli_fetch_array($result)) {
                                                        echo('<option value="'.$row2['id'].'">'.$row2['nome'].'</option>');
                                                    }
                                                } else {
                                                    echo('<option value="">Sem salas disponíveis</option>');
                                                }
                                                ?>
                                            </select>
                                            <!--
                                            <a    style="color:Gainsboro;" class="btn btn-outline-secondary" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" title="Gerir salas">
                                                <i class="bi bi-door-open"></i> Salas
                                            </a>
-->
                                        </div>
                                        <?php if ($rowcount == 0) { ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nomeq" class="col-sm-4 col-form-label">Nome:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" required type="text" name="nomeq" id="nomeq" class="form-control required-field" placeholder="Nome do equipamento"
                                               onBlur="verificaEquipDuplicado()" onInput="verificaEquipDuplicado()">
                                        <div id="equip_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nserie" class="col-sm-4 col-form-label">Nº de série:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" type="text" name="nserie" id="nserie" class="form-control" placeholder="Nº de série">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="marcamod" class="col-sm-4 col-form-label">Marca/Modelo:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" type="text" name="marcamod" id="marcamod" class="form-control" placeholder="Marca/Modelo">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="datacompra" class="col-sm-4 col-form-label">Data da compra:</label>
                                    <div class="col-sm-8">
                                        <input  style="width: 100%;" type="date" name="datacompra" id="datacompra" class="form-control" placeholder="Data da compra">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="obs" class="col-sm-4 col-form-label">Observações:</label>
                                    <div class="col-sm-8">
                                        <textarea style="width: 100%;" rows="4" name="obs" id="obs" class="form-control" placeholder="Observações adicionais"></textarea>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir Equipamento
                                    </button>
                                </div>
                            </form>
                        </div>

                        

<a href="<?php echo SVRURL ?>equip">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>



<br><br>


                    </div>
                </div>
            </div>
        </div>
    </div>

  
<?php
      // Clear the session
		unset($_SESSION['escola']);
?>

      <?php include ("footer.php");?>

    <!-- Script para validação do formulário -->
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (equipExiste) {
                            event.preventDefault();
                            event.stopPropagation();
                            document.getElementById('equip_err').innerHTML =
                                '<small style="color:#dc3545;font-weight:600;">&#10007; Já existe um equipamento com este nome nesta sala.</small>';
                            document.getElementById('nomeq').focus();
                            return;
                        }
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
   </body>
</html>