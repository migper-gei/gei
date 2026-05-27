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

include("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>


<?php
               
                 
               $id = (int)base64_decode($_GET['ide']);
               $sa = (int)base64_decode($_GET['sai']);
               $idescola = (int)base64_decode($_GET['ies']);

               $sql2a = "select max(id) as me  from escolas ";
               $result2a = mysqli_query($db,$sql2a); 
               $rows2a =mysqli_fetch_row($result2a);
               
               
               $maxesc = $rows2a[0];



               if ($idescola>$maxesc || $idescola<0 
               ||  !isset($id)   || !is_numeric($id) 
               || !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
               || !isset($sa)   ||  empty($sa)
               )
               {
               ?>
               
               <script>
               window.setTimeout(function() {
                  window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>';
               }, 10);
               </script>
               
               
               <?php
               
               }



                $sql3 ="select oe.*,s.*, es.nome_escola
                from outro_equipamento oe, salas s, escolas es
                where oe.id_sala=s.id and s.id_escola=es.id
                and oe.id=$id and s.id=$sa and es.id=$idescola";
                $result3 = mysqli_query($db,$sql3); 
                $row3=mysqli_fetch_array($result3);
               
                $nr = mysqli_num_rows($result3);

             
               ?>
      

      <?php

     if ($nr==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>';
}, 10);
</script>


<?php

}

?>




      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Atualizar outro equipamento</li>
                  </ol>
               </nav>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">

                  <!-- Welcome Section -->
                  <div class="welcome-section">
                     <div>
                        <?php include("msg_bemvindo.php"); ?>
                     </div>
                  </div>

                  <!-- Cabeçalho com info do equipamento -->
                  <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                     <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                     <div style="display:flex; flex-direction:column;">
                        <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Equipamento</span>
                        <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nomeoutro'], ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex; flex-direction:column;">
                        <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                        <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                     <span style="color:#c5cde0;">|</span>
                     <div style="display:flex; flex-direction:column;">
                        <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                        <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></span>
                     </div>
                  </div>

                  <div class="form-container">
                  <form class="needs-validation" novalidate action="<?php echo SVRURL ?>atualiza_outro_equipamento_OK.php?ide=<?php echo base64_encode($id)?>&&sai=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>" method="post">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                  <label>Nome do equipamento:</label>
                  <input required type="text" name="nomeq" value="<?php echo htmlspecialchars($row3['nomeoutro'], ENT_QUOTES, 'UTF-8')?>" class="form-control required-field" style="width:100%" placeholder="Nome do equipamento">
                  <br>

                  <label>Quantidade:</label>
                  <input value="<?php echo htmlspecialchars($row3['qta'], ENT_QUOTES, 'UTF-8')?>" required min="1" type="number" name="qta" class="form-control required-field" style="width:100%" placeholder="Quantidade">
                  <br>

                  <label>Observações:</label>
                  <textarea rows="5" name="obs" class="form-control" style="width:100%" placeholder="Observações"><?php echo htmlspecialchars($row3['observacoes'], ENT_QUOTES, 'UTF-8')?></textarea>
                  <br>

                  <div style="text-align:center;width:100%">
                     <button type="submit" class="btn-submit">
                         <i class="fa-solid fa-pen"></i>
                        &nbsp;Atualizar
                     </button>
                  </div>

                  </form>
                  </div>
<!--
                  <div class="text-center mt-3">
                     <a class="btn btn-secondary" title="Voltar"
                        href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($sa) ?>&&ies=<?php echo base64_encode($idescola) ?>">
                        <i class="bi bi-arrow-left"></i> Voltar
                     </a>
                  </div>
-->
                 <a href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($sa) ?>&&ies=<?php echo base64_encode($idescola) ?>"  title="Voltar">
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
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
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