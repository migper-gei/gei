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
                       <a href="<?php echo SVRURL ?>utiliz" style="color:#4b6cb7;text-decoration:none;">Utilizadores</a>
     
                     >> Atualizar</li>
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




 <?php

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>
<?php
}


    





 $uid = (int)base64_decode($url[0]);
 $stmt = $db->prepare("SELECT * FROM utilizadores WHERE id=?");
 $stmt->bind_param("i", $uid);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = mysqli_fetch_array($result);
?>
            <a href="<?php echo SVRURL ?>sair">Sair</a>
              </h3>   


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>utiliz';
}, 10);
</script>

<?php
}
else
{ 
?>



<div class="form-container">


<form action = "<?php echo SVRURL ?>atualiza_ok_utiliz.php?ui=<?php echo base64_encode($row['id']); ?>" method = "post" class="needs-validation" novalidate >
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <label>Utilizador: </label>  <br>  
                    <input style="width:100%" required class="form-control required-field" type="text" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>"/><br /><br />

                     <label>Email: </label>  <br>  
                    <input style="width:100%" required class="form-control required-field" type="text" name="email" value="<?php echo htmlspecialchars($row['email']); ?>"/><br /><br />

                    <label>Tipo:    </label>

                    &nbsp;

                    <select style="width:100%" class="form-control required-field" name="tipo">
                    <?php foreach ([1,2,3,4] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo ($row['tipo']==$t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                    <?php endforeach; ?>
                    </select>
                 
                   

                     <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#ede8fc;color:#6f42c1;border:1.5px solid #6f42c1;">
                            1 – Administrador
                        </span>
                        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0eeff;color:#00509e;border:1.5px solid #00509e;">
                            2 – Utilizador
                        </span>
                        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0f5fb;color:#0891b2;border:1.5px solid #0891b2;">
                            3 – Reparador
                        </span>
                        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:6px;font-size:.78rem;font-weight:700;background:#e0f7f0;color:#059669;border:1.5px solid #059669;">
                            4 – Funcionário
                        </span>
                     </div>
              
                                 
                                 
                     <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar utilizador
                                    </button>
                                </div>

                 </form>



              


                           </div>
<?php
}


?>




<a href="<?php echo SVRURL ?>utiliz" title="Voltar">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>



               
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