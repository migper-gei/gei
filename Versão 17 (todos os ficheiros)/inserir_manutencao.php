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

 include ("head.php");
?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php");?>
     

     <?php
//session_start();

include ("css_inserir.php");

include("sessao_timeout.php");

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);

$maxesc = $rows2a[0];

if (base64_decode($_GET["esm"])>$maxesc
|| !is_numeric(base64_decode($_GET["esm"]))
|| empty(base64_decode($_GET["esm"]))
|| !isset($_POST["sala"]) || empty($_POST["sala"])
)
{

?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>manut';
          },10);
          </script>

<?php
}

$sa=$_POST["sala"];
$idescola = (int)base64_decode($_GET["esm"]);

$stmt10 = $db->prepare("SELECT nome FROM salas WHERE id = ?");
$stmt10->bind_param("i", $sa);
$stmt10->execute();
$result10 = $stmt10->get_result();
$rows10   = $result10->fetch_row();
$num_ns   = $result10->num_rows;
$stmt10->close();

 $ns = $rows10[0] ?? '';

$stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt11->bind_param("i", $idescola);
$stmt11->execute();
$result11 = $stmt11->get_result();
$rows11   = $result11->fetch_row();
$num_ne   = $result11->num_rows;
$stmt11->close();

$ne = $rows11[0] ?? '';
  ?>
      

      <?php
     if ($num_ns==0 || $num_ne==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>

<?php

}

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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <a href="<?php echo SVRURL ?>manut" style="color:#4b6cb7;text-decoration:none;">Manutenções
                        
                        </a>

                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
               <div class="titlepage">
                     

                  </div>
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
                    CABECALHO: sala + escola (abaixo do utilizador)
                    ======================================================== -->
               <!-- ========================================================
                    CABEÇALHO: sala + escola na mesma linha, por baixo do utilizador
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">

                  <!-- Nome da sala em destaque -->
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.1rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9"/>
                     </svg>
                     <?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?>
                  </span>

                  <!-- Separador -->
                  <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>

                  <!-- Nome da escola -->
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>

               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
<script>
// Validação unificada: recebe o event explicitamente para evitar
// dependência da variável global (falha em modo estrito).
function validate(e) {
  var checkbox  = document.querySelector('input[name="eq[]"]:checked');
  var checkbox2 = document.querySelector('input[name="m[]"]:checked');

  if (!checkbox) {
    e.preventDefault();
    e.stopPropagation();
    swal({
      title: "Escolha pelo menos um equipamento!",
      type: "warning",
      confirmButtonText: "OK",
      closeOnConfirm: false,
      closeOnCancel: false
    });
    return false;
  }

  if (!checkbox2) {
    e.preventDefault();
    e.stopPropagation();
    swal({
      title: "Escolha pelo menos um tipo de manutenção!",
      type: "warning",
      confirmButtonText: "OK",
      closeOnConfirm: false,
      closeOnCancel: false
    });
    return false;
  }

  return true;
}
</script>

<script>
function Check(){
chk=document.getElementsByName("my_check")[0]
chk2=document.getElementsByName('eq[]')

if(chk.checked==true){
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=true
}else{
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=false
}

}

</script>

<script>
function Check2(){
chk=document.getElementsByName("my_check2")[0]
chk2=document.getElementsByName('m[]')

if(chk.checked==true){
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=true
}else{
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=false
}

}

</script>

<style>
@media (max-width: 768px) {
    .gei-check-grid { grid-template-columns: 1fr !important; }
}
</style>

<div class="form-container">
                      
<div class="step-indicator">
                  

<i class="fas fa-info-circle mr-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>

<form  name="myform" method="post" 
action = "<?php echo SVRURL ?>grava_manutencao_sala.php?si=<?php echo base64_encode($sa);?>&&esm=<?php echo base64_encode($idescola);?>" class="needs-validation" novalidate>

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

<?php 

//

// $sa=$_POST["sala"];
//$em=$_SESSION['email'];

$stmt3 = $db->prepare(
    "SELECT e.nomeequi AS noeq, e.id, e.tipo
     FROM equipamento e
     JOIN salas s ON e.id_sala = s.id
     WHERE s.id_escola = ?
       AND s.id = ?
       AND e.id NOT IN (
           SELECT id_equi FROM avarias_reparacoes WHERE datareparacao IS NULL
       )
     ORDER BY e.tipo, e.nomeequi"
);
$stmt3->bind_param("ii", $idescola, $sa);
$stmt3->execute();
$result3 = $stmt3->get_result();
$stmt3->close();

?>

<label>Data: </label>  
     <input required  style="Width:100%"    class="form-control required-field"        
     size="10" type = "date" name = "data" >
     <br>   <br>
     <label>Pessoa que realizou: </label>  <br>
    <input class="form-control required-field"  required style="Width:100%"  type = "text" name="pessoa"  />
    <br> 
    <br>

<label style="font-weight:600;color:#1e2a45;font-size:.9rem;">
   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="vertical-align:middle;margin-right:4px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
   Escolha os equipamentos em que realizou a manutenção:
</label>

<div style="background:#fff;border:1.5px solid #e3e8f4;border-radius:10px;padding:14px 18px;margin:8px 0 16px;">
   <div style="text-align:center;padding-bottom:10px;border-bottom:1px solid #eef1f8;margin-bottom:10px;">
      <label style="display:inline-flex;align-items:center;gap:8px;font-size:.85rem;font-weight:600;color:#4b6cb7;cursor:pointer;">
         <input type="checkbox" name="my_check" value="yes" onClick="Check()" style="width:16px;height:16px;accent-color:#4b6cb7;">
         Selecionar/Desselecionar tudo
      </label>
   </div>
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;" class="gei-check-grid">
<?php while($row3=mysqli_fetch_array($result3)): ?>
   <label style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:6px;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='#f0f4fb'" onmouseout="this.style.background='transparent'">
      <input type="checkbox" name="eq[]" value="<?php echo $row3['id']; ?>" style="width:16px;height:16px;accent-color:#4b6cb7;flex-shrink:0;">
      <span style="font-size:.88rem;color:#1e2a45;font-weight:500;"><?php echo htmlspecialchars($row3['noeq'], ENT_QUOTES, 'UTF-8'); ?></span>
      <span style="font-size:.75rem;color:#7b88a0;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:4px;padding:1px 7px;white-space:nowrap;"><?php echo htmlspecialchars($row3['tipo'], ENT_QUOTES, 'UTF-8'); ?></span>
   </label>
<?php endwhile; ?>
   </div>
</div> 

<br>

<label style="font-weight:600;color:#1e2a45;font-size:.9rem;">
   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" style="vertical-align:middle;margin-right:4px;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
   Escolha o tipo de manutenção:
</label>

<?php
$stmt4 = $db->prepare("SELECT nome FROM tipos_manutencao ORDER BY nome");
$stmt4->execute();
$result4 = $stmt4->get_result();
$stmt4->close();
?>

<div style="background:#fff;border:1.5px solid #e3e8f4;border-radius:10px;padding:14px 18px;margin:8px 0 16px;">
   <div style="text-align:center;padding-bottom:10px;border-bottom:1px solid #eef1f8;margin-bottom:10px;">
      <label style="display:inline-flex;align-items:center;gap:8px;font-size:.85rem;font-weight:600;color:#4b6cb7;cursor:pointer;">
         <input type="checkbox" name="my_check2" value="yes" onClick="Check2()" style="width:16px;height:16px;accent-color:#4b6cb7;">
         Selecionar/Desselecionar tudo
      </label>
   </div>
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;" class="gei-check-grid">
<?php while($row4=mysqli_fetch_array($result4)): ?>
   <label style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:6px;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='#f0f4fb'" onmouseout="this.style.background='transparent'">
      <input type="checkbox" name="m[]" value="<?php echo htmlspecialchars($row4['nome'], ENT_QUOTES, 'UTF-8'); ?>" style="width:16px;height:16px;accent-color:#4b6cb7;flex-shrink:0;">
      <span style="font-size:.88rem;color:#1e2a45;font-weight:500;"><?php echo htmlspecialchars($row4['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
   </label>
<?php endwhile; ?>
   </div>
</div> 

<br>
                   <label>Observações: </label>  <br>  
                   <textarea    style="width:100%" rows="5"   name="obs"></textarea>
                  
<br><br>
      

                 
    <div  style=" text-align:center;width:100%"> 
    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir manutenção
                                    </button>
    </div>

              

 </form>
    

</div>



   <a href="<?php echo SVRURL ?>manut"  title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
    <br><br>



<?php include ("jquery_bootstrap.php");?>

                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
  <!-- Script para validação do formulário -->
<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                // 1. Validação personalizada (checkboxes)
                var ok = validate(event);

                // 2. Validação nativa HTML5
                if (!ok || form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

      <?php include ("footer.php");?>

   </body>
</html>