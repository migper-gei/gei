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

// --- Verificar autenticação e permissão antes de qualquer output ---
if (empty($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header('Location: ' . SVRURL . 'dadosescola');
    exit;
}

// Gerar token CSRF — sempre regenerar a cada carregamento do formulário
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="pt">
   <head>

<?php
 include ("head.php");
 include ("css_inserir.php");
?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>

     <?php include("sessao_timeout.php"); ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Configurações gerais >
Dados da(s) Instituição(ões) > Atualizar</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                  <div class="welcome-section">
<?php include("msg_bemvindo.php"); ?>
                  </div>

<?php

if (isset($_GET['url'])) {
    $url = explode('/', $_GET['url']);
} else { ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>
<?php
    exit;
}

$sql = "SELECT * FROM escolas WHERE id=" . (int)base64_decode($url[0]);
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_array($result);
?>
<br>

<?php
if (mysqli_num_rows($result) == 0) { ?>
    <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>
<?php
} else { ?>

<form class="needs-validation" novalidate action="<?php echo SVRURL ?>atualizaescok/<?php echo base64_encode($row['id']); ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <label>Nome da instituição: </label><br>
    <input style="width:100%;" placeholder="Nome da instituição"
        class="form-control required-field"
        size=50 type="text" name="nome" required
        value="<?php echo htmlspecialchars($row['nome_escola'], ENT_QUOTES, 'UTF-8'); ?>"/><br /><br />

    <div class="text-center mt-4">
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-pen"></i>
            &nbsp;Atualizar instituição
        </button>
    </div>
</form>

<a href="<?php echo SVRURL ?>dadosescola" title="Voltar">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br>

<?php } ?>

<br>
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

      <?php include ("footer.php"); ?>

   </body>
</html>
