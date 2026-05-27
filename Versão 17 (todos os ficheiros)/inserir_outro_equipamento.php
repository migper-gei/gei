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
// ── CSRF: verificar token recebido de equipamento.php ─────────────
// Só validar se vier via POST (navegação pelo botão "Inserir Outro Equipamento")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['csrf_token_outequip']) ||
        empty($_SESSION['csrf_token_outequip']) ||
        !hash_equals($_SESSION['csrf_token_outequip'], $_POST['csrf_token_outequip'])
    ) {
        http_response_code(403);
        die('Pedido inválido (token CSRF em falta ou inválido).');
    }
    // Regenerar token após uso
    $_SESSION['csrf_token_outequip'] = bin2hex(random_bytes(32));
}
// ─────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
      

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

<style>
     .form-container {
         background-color: #f8f9fa;
         border-radius: 10px;
         padding: 25px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         margin-bottom: 30px;
     }
     
     .form-title {
         color: #0d6efd;
         margin-bottom: 20px;
         font-weight: 600;
     }
     
     .form-group {
         margin-bottom: 20px;
     }
     
     .form-control:focus {
         border-color: #0d6efd;
         box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
     }
     
     .required-field {
         background-color: #e8f4f8 !important;
     }
     
     .btn-submit {
         background-color: #0d6efd;
         color: white;
         padding: 10px 30px;
         border-radius: 5px;
         border: none;
         font-weight: 500;
         transition: all 0.3s;
     }
     
     .btn-submit:hover {
         background-color: #0b5ed7;
         transform: translateY(-2px);
     }
     
     .step-indicator {
         margin-bottom: 20px;
         padding: 10px;
         background-color: #f1f8ff;
         border-radius: 5px;
         border-left: 4px solid #0d6efd;
     }
     
     .help-icon {
         color: #6c757d;
         font-size: 16px;
         cursor: pointer;
     }
     
     .help-icon:hover {
         color: #0d6efd;
     }
     
     .loader_bg {
         background-color: rgba(255, 255, 255, 0.9);
     }
 </style>

<?php
 include ("head.php");
?>

<script>
var equipOutExiste = false;
var _checkEquipOutTimer = null;

function verificaEquipOutDuplicado() {
    var nome   = document.getElementById('nomeq').value.trim();
    var sala   = document.getElementById('sala').value;
    var errDiv = document.getElementById('equip_out_err');

    if (!nome || !sala) {
        errDiv.innerHTML = '';
        equipOutExiste = false;
        clearTimeout(_checkEquipOutTimer);
        return;
    }

    clearTimeout(_checkEquipOutTimer);
    _checkEquipOutTimer = setTimeout(function() {
        errDiv.innerHTML = '<small style="color:#555;background:none;">A verificar...</small>';
        equipOutExiste = false;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_outro_equipamento.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.timeout = 5000;
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.existe) {
                        errDiv.innerHTML = '<small style="color:#dc3545;font-weight:600;">&#10007; Já existe um equipamento com este nome nesta sala.</small>';
                        equipOutExiste = true;
                    } else {
                        errDiv.innerHTML = '<small style="color:#00AF33;font-weight:600;">&#10003; Disponível</small>';
                        equipOutExiste = false;
                    }
                } catch(e) { errDiv.innerHTML = ''; }
            } else { errDiv.innerHTML = ''; }
        };
        xhr.onerror   = function() { errDiv.innerHTML = ''; equipOutExiste = false; };
        xhr.ontimeout = function() { errDiv.innerHTML = ''; equipOutExiste = false; };
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
             // window.location.href = '<?php echo SVRURL ?>equip';
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
                           <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Outro equipamento</a>
               
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
               <!--
               <div class="titlepage">
                     <h2>Outro equipamento<br>
                     <?php echo $ne ?>
                  </h2>
                  </div>
-->

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
<div class="welcome-section">                   
<?php
include("msg_bemvindo.php");
?>
 </div>

 
               <!-- ========================================================
                    CABEÇALHO: sala + escola na mesma linha, por baixo do utilizador
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
               <!-- ===== FIM CABEÇALHO ===== -->
<?php
$token = bin2hex(random_bytes(32));
$_SESSION['token'] = $token;
?>
<form name="equipamentoout" action = "<?php echo SVRURL ?>gravaoutequip?ies=<?php echo base64_encode($idescola);?>"
 method = "post" class="needs-validation" novalidate>
<input type="hidden" name="token" value="<?php echo $token; ?>">

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

<div class="form-group">
                                    <label for="sala">Sala:</label>
                                    <?php
                                    $sql = $db->prepare("select * FROM salas where id_escola=? order by nome");
                                    $sql->bind_param("i", $idescola);
                                    $sql->execute();
                                    $result = $sql->get_result();
                                    $rowcount = mysqli_num_rows($result);
                                    ?>
                                    <select name="sala" id="sala" required class="form-control required-field" style="width:100%;" onchange="verificaEquipOutDuplicado()">
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
                                    <?php if ($rowcount == 0) { ?>
                                        <div class="alert alert-warning mt-2">
                                            <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                                            <a href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" class="alert-link">Gerir salas</a>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <label for="nomeq">Nome:</label>
                                    <input required type="text" name="nomeq" id="nomeq" class="form-control required-field" style="width:100%;" placeholder="Nome do equipamento"
                                           onBlur="verificaEquipOutDuplicado()" onInput="verificaEquipOutDuplicado()">
                                    <div id="equip_out_err" style="margin-top:4px;min-height:18px;background:none;padding:0;border:none;"></div>
                                </div>

                                <div class="form-group">
                                    <label for="quantidade">Quantidade:</label>
                                    <input required type="number" name="qta" id="quantidade" class="form-control required-field" style="width:100%;" placeholder="Quantidade">
                                </div>

                                <div class="form-group">
                                    <label for="obs">Observações:</label>
                                    <textarea rows="4" name="obs" id="obs" class="form-control" style="width:100%;" placeholder="Observações adicionais"></textarea>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir outro equipamento
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
      <!-- end about -->
    
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
                    if (equipOutExiste) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.getElementById('equip_out_err').innerHTML =
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

         <!-- ═══ TEMA ESCURO ═══ -->
      <script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
      <!-- ═══════════════════════ -->
</body>
</html>