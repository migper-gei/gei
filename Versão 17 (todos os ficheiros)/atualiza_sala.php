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

include ("css_inserir.php");

include("sessao_timeout.php");

 
  ?>
      

      <?php

if (isset($_GET['url'])  &&  is_numeric(base64_decode($_GET['url'])) )
{
$url = explode('/',$_GET['url']);
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=0';
}, 10);
</script>
<?php
}
?>


<?php
$sql2 = "select count(*) from salas where id=".(int)base64_decode($url[0])."";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];


if ($conta==0)
{

?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=0';
}, 10);
</script>




<?php
}

$sql11 = "select e.nome_escola from escolas e, salas s
where s.id_escola=e.id and
s.id=".base64_decode($url[0])."";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);




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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                      <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>

                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">
                         <a href="<?php echo SVRURL ?>sala" style="color:#4b6cb7;text-decoration:none;">Salas</a>
  
                     >> Atualizar</li>
                  </ol>
               </nav>

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
                    CABEÇALHO: escola na mesma linha, por baixo do utilizador
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">

                  <!-- Nome da escola -->
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.05rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo htmlspecialchars($rows11[0], ENT_QUOTES, 'UTF-8'); ?>
                  </span>

               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
 




<?php

 // Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$sql = "select * from salas where id=".(int)base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);
?>

<br>

<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1);?>&&escola=<?php echo htmlspecialchars($row['id_escola'], ENT_QUOTES, 'UTF-8')?>';
}, 10);
</script>

<?php
}
else
{ 
?>

<div class="form-container">
<form 
action="<?php echo SVRURL ?>atualiza_ok_sala.php?sai=<?php echo base64_encode($row['id']); ?>" method="post" class="needs-validation" novalidate>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <label>Nome da sala: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="nome"  required value="<?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?>"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="localizacao"  required value="<?php echo htmlspecialchars($row['localizacao'], ENT_QUOTES, 'UTF-8'); ?>"/><br /><br />
                
                    <label>Departamento/Grupo/Serviço: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="departamento"   value="<?php echo htmlspecialchars($row['departamento'], ENT_QUOTES, 'UTF-8'); ?>"/><br /><br />
                    <label>Sala com equipamento requisitável: </label>                

                    <select name="eqreq" required class="form-control required-field">

<?php
if ($row['equip_requisitavel']=="Sim")
{

      echo('<option selected value="Sim">Sim</option>');
      echo('<option value="Não">Não</option>');
}
else
{
      echo('<option selected value="Não">Não</option>');
      echo('<option value="Sim">Sim</option>');
} 
?>     
</select>
                                 
                         <!--           (Ao atualizar, também será atualizado a sala nos respetivos equipamentos)  
                          -->
                         <br><br>

                                    <div  style=" text-align:center;width:100%"> 
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar sala
                                    </button>
                                 
                                 </div>
                 </form>
</div>
<!--
                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($row['id_escola'])?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                        </div>
-->


<a href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($row['id_escola'])?>"  title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br>



<?php
}
?>


<br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


<!-- Script para validação do formulário -->
<script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
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



      <?php include ("footer.php");?>


   </body>
</html>
