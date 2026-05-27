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
<?php include("head.php"); ?>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include("header.php"); ?>

      <?php
      include("css_inserir.php");
      include("sessao_timeout.php");
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
                           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                           <span style="color:#4b6cb7;">Configurações</span>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Utilizadores &raquo; Importação</li>
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

                     // Limite máximo de linhas por importação
                     define('MAX_LINHAS_CSV', 500);

                     // Tipos de utilizador permitidos
                     $tipos_validos = [1, 2, 3];

                     /**
                      * Valida a força da password:
                      *   - mínimo 12 caracteres
                      *   - pelo menos uma letra (maiúscula ou minúscula)
                      *   - pelo menos um dígito
                      *   - pelo menos um carácter especial
                      */
                     function password_forte(string $pass): bool {
                         if (mb_strlen($pass) < 12)              return false;
                         if (!preg_match('/[A-Za-z]/',  $pass))  return false;
                         if (!preg_match('/[0-9]/',     $pass))  return false;
                         if (!preg_match('/[^A-Za-z0-9]/', $pass)) return false;
                         return true;
                     }

                     if (isset($_POST['submit'])) {

                         // ── 1. Validar extensão ──────────────────────────────
                         $filename0 = $_FILES['file']['name'] ?? '';
                         $tmpPath   = $_FILES['file']['tmp_name'] ?? '';
                         $ext = strtolower(pathinfo($filename0, PATHINFO_EXTENSION));

                         $uploadOk = false;
                         if (!is_uploaded_file($tmpPath)) {
                             $uploadErr = 'Upload inválido.';
                         } elseif ($_FILES['file']['size'] > 2097152) {
                             $uploadErr = 'O ficheiro excede o tamanho máximo permitido (2 MB)!';
                         } elseif ($ext !== 'csv') {
                             $uploadErr = 'Ficheiro inválido, deve ser .CSV!';
                         } else {
                             $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                             $mimeReal = finfo_file($finfo, $tmpPath);
                             finfo_close($finfo);
                             $allowedMimes = ['text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
                             if (!in_array($mimeReal, $allowedMimes, true)) {
                                 $uploadErr = 'O ficheiro não tem conteúdo CSV válido!';
                             } else {
                                 $uploadOk = true;
                             }
                         }

                         if (!$uploadOk) { ?>
                             <script>
                             swal({ title: '<?php echo addslashes($uploadErr); ?>', icon: 'error' })
                             .then(function() { window.location = "<?php echo SVRURL ?>importarusers"; });
                             </script>
                             <?php
                         } else {

                             // ── 2. Validar delimitador ───────────────────────
                             include("validar_delimitadorCSV.php");

                             if (!isset($d) || !in_array($d, [',', ';', "\t"], true)) {
                                 ?>
                                 <script>
                                 swal({ title: 'O ficheiro CSV não tem como delimitador a , (vírgula)!', icon: 'error' })
                                 .then(function() { window.location = "<?php echo SVRURL ?>importarusers"; });
                                 </script>
                                 <?php
                             } else {

                                 $fileName1 = $_FILES["file"]["tmp_name"];

                                 if ($_FILES["file"]["size"] > 0) {

                                     $row          = 1;
                                     $importados   = 0;
                                     $ignorados    = 0;
                                     $erros        = 0;
                                     $pass_fracas  = 0; // ← contador de passwords rejeitadas

                                     $file1 = fopen($fileName1, "r");

                                     // Prepared statements fora do loop
                                     $stmt_chk_u = $db->prepare("SELECT id FROM utilizadores WHERE email = ? LIMIT 1");
                                     $stmt_imp_u = $db->prepare("INSERT INTO utilizadores (nome, email, pass, tipo) VALUES (?, ?, ?, ?)");

                                     while (($column = fgetcsv($file1, 10000, $d)) !== FALSE) {

                                         // ── Limite de linhas ─────────────────
                                         if ($row > MAX_LINHAS_CSV + 1) {
                                             break;
                                         }

                                         // Converter encoding
                                         $column = array_map(function($v) {
                                             return mb_convert_encoding($v, 'UTF-8', 'UTF-8, ISO-8859-1');
                                         }, $column);

                                         if ($row > 1) {

                                             // ── 3. Validar campos obrigatórios ──
                                             $nome  = trim($column[0] ?? '');
                                             $email = trim($column[1] ?? '');
                                             $pass  = trim($column[2] ?? '');
                                             $tipo  = (int)($column[3] ?? 0);

                                             if (empty($nome) || empty($email) || empty($pass)) {
                                                 $erros++;
                                                 $row++;
                                                 continue;
                                             }

                                             if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                 $erros++;
                                                 $row++;
                                                 continue;
                                             }

                                             // ── 4. Validar força da password ─────
                                             if (!password_forte($pass)) {
                                                 $pass_fracas++;
                                                 $row++;
                                                 continue;
                                             }

                                             // ── 5. Validar tipo de utilizador ───
                                             if (!in_array($tipo, $tipos_validos, true)) {
                                                 $erros++;
                                                 $row++;
                                                 continue;
                                             }

                                             // ── 6. Verificar duplicado ──────────
                                             $stmt_chk_u->bind_param("s", $email);
                                             $stmt_chk_u->execute();
                                             $stmt_chk_u->store_result();
                                             $existe = $stmt_chk_u->num_rows > 0;
                                             $stmt_chk_u->free_result();

                                             if ($existe) {
                                                 $ignorados++;
                                             } else {
                                                 // ── 7. Inserir com password Argon2id ──
                                                 $_hash_imp = password_hash($pass, PASSWORD_ARGON2ID);
                                                 $stmt_imp_u->bind_param("sssi", $nome, $email, $_hash_imp, $tipo);
                                                 if ($stmt_imp_u->execute()) {
                                                     $importados++;
                                                 } else {
                                                     $erros++;
                                                 }
                                             }
                                         }

                                         $row++;
                                     }

                                     fclose($file1);
                                     $stmt_chk_u->close();
                                     $stmt_imp_u->close();

                                     // Incluir passwords fracas no resumo
                                     $msg_detalhe = "Importados: {$importados} | Ignorados (já existem): {$ignorados} | Password fraca: {$pass_fracas} | Erros: {$erros}";
                                     ?>
                                     <script>
                                     swal({
                                         title: 'Importação concluída!',
                                         text: '<?php echo addslashes($msg_detalhe); ?>',
                                         icon: 'success'
                                     }).then(function() {
                                         window.location = "<?php echo SVRURL ?>utiliz";
                                     });
                                     </script>
                                     <?php
                                 }
                             }
                         }
                     }
                     ?>

                     <br>
                     <form class="needs-validation" novalidate enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importarusers">
                        <div class="form-group">
                           <div class="action-section">
                              <label for="file">
                                 Escolha o ficheiro .CSV para importar
                                 <small class="text-muted">(máx. <?php echo MAX_LINHAS_CSV; ?> utilizadores — se já existir, não é importado)</small>
                              </label>
                              <input required name="file" type="file" accept=".csv" class="form-control required-field">
                              <small style="display:block;margin-top:6px;color:#7b88a0;font-size:.75rem;">
                                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                  Formato: <strong>.CSV</strong> com delimitador <strong>vírgula ( , )</strong> ou <strong>ponto e vírgula ( ; )</strong> &nbsp;&middot;&nbsp; Tamanho máximo: <strong>2 MB</strong>
                              </small>
                              <!-- Requisitos da password -->
                              <small style="display:block;margin-top:8px;padding:8px 12px;border-radius:6px;background:#f0f4fb;border:1px solid #d0d9f0;color:#4b6cb7;font-size:.75rem;line-height:1.6;">
                                  <strong>Requisitos da password (coluna 3):</strong><br>
                                  &#10003; Mínimo 12 caracteres &nbsp;&middot;&nbsp;
                                  &#10003; Pelo menos uma letra &nbsp;&middot;&nbsp;
                                  &#10003; Pelo menos um número &nbsp;&middot;&nbsp;
                                  &#10003; Pelo menos um carácter especial (ex: <code>!@#$%*-</code>)<br>
                                  Registos com password que não cumpra estes requisitos serão ignorados e contabilizados em <em>Password fraca</em>.
                              </small>
                           </div>
                        </div>
                        <br>
                        <div class="form-group">
                           <div style="text-align:center;width:100%">
                              <button type="submit" name="submit" class="btn-submit">
                                 <i class="fas fa-file-import"></i>&nbsp;Importar utilizadores
                              </button>
                           </div>
                        </div>
                     </form>

                     <a href="<?php echo SVRURL ?>configura">
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

      <?php include("footer.php"); ?>

   </body>
</html>
