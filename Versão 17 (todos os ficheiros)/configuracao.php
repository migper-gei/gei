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
<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
      <?php include ("head.php"); ?>
      <style>
         .import-links { display:flex; align-items:center; gap:6px; font-size:0.82rem; padding:4px 2px; }
         .import-link { color:#0d6efd !important; text-decoration:none !important; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; transition:color 0.15s; font-weight:500; }
         .import-link:hover { color:#0a58ca !important; text-decoration:underline !important; }
         .import-link i { font-size:0.75rem; color:#0d6efd !important; }
         .import-sep { color:#6c757d !important; font-size:0.75rem; }
      </style>
   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>

      <?php include ("header.php"); ?>
      <?php include ("sessao_timeout.php"); ?>

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
                     <li style="color:#1e2a45;">Configurações</li>
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

                     <div class="action-section">
                        <div class="row">

                           <!-- Períodos -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>peri" method="get">
                                 <button title="Períodos/Semestres" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-calendar-days"></i>&nbsp; Períodos
                                 </button>
                              </form>
                           </div>

                           <!-- Dados da instituição (só admin) -->
                           <div class="col-md-4 mb-3">
                              <?php if ($_SESSION['tipo'] == 1): ?>
                              <form action="<?php echo SVRURL ?>dadosesc" method="get">
                                 <button type="submit" class="action-button btn-primary-action" title="Definições Gerais (Logotipo, nome, site, ...)">
                                    <i class="fa-solid fa-gear"></i>&nbsp; Dados da(s) instituição(ões)
                                 </button>
                              </form>
                              <?php endif; ?>
                           </div>

                           <!-- Email / Tempo de sessão (só admin) -->
                           <div class="col-md-4 mb-3">
                              <?php if ($_SESSION['tipo'] == 1): ?>
                              <form action="<?php echo SVRURL ?>emsess" method="get">
                                 <button style="width:100%" title="Email / Tempo sessão" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-envelopes-bulk"></i>&nbsp; Email / Tempo de sessão
                                 </button>
                              </form>
                              <?php endif; ?>
                           </div>

                           <!-- Utilizadores -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>utiliz" method="get">
                                 <button title="Utilizadores" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-users"></i>&nbsp; Utilizadores
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de utilizadores" href="<?php echo SVRURL ?>importarusers"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/utilizadores.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Tipos de equipamento -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>tiposequip" method="get">
                                 <button title="Tipos de equipamento" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-rectangle-list"></i>&nbsp; Tipos de equipamento
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de tipos equipamentos" href="<?php echo SVRURL ?>importar_tiposequip.php"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/tiposequipamento.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Tipos de manutenção -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>tiposmanuten" method="get">
                                 <button title="Tipos de manutenção" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>&nbsp; Tipos de manutenção
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de tipos de manutenção" href="<?php echo SVRURL ?>importar_tiposmanuten.php"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/tiposmanutencao.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Salas -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(0) ?>" method="get">
                                 <button title="Salas" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-door-open"></i>&nbsp; Salas
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de salas" href="<?php echo SVRURL ?>importarsalas"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/salas.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Equipamentos Informáticos -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>equip" method="get">
                                 <button title="Equipamentos" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-laptop"></i>&nbsp; Equipamentos Informáticos
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de equipamentos" href="<?php echo SVRURL ?>importar_equip.php"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/equipamento.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Outros Equipamentos -->
                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>equip" method="get">
                                 <button title="Outros Equipamentos" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-list-ul"></i>&nbsp; Outros Equipamentos
                                 </button>
                              </form>
                              <?php if ($_SESSION['tipo'] == 1): ?>
                                 <div class="import-links mt-1">
                                    <a class="import-link" title="Importação de outros equipamentos" href="<?php echo SVRURL ?>importar_outro_equip.php"><i class="fa-solid fa-file-import"></i> Importação</a>
                                    <span class="import-sep">|</span>
                                    <a class="import-link" title="Ficheiro exemplo" href="<?php echo SVRURL ?>importar_files/outro_equipamento.csv"><i class="fa-solid fa-file-csv"></i> Exemplo CSV</a>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Tarefas + Backup + Restore (só admin) -->
                           <?php if ($_SESSION['tipo'] == 1): ?>

                           <div class="col-md-4 mb-3">
                              <a href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>&amp;z=<?php echo base64_encode(1) ?>" class="action-button btn-primary-action" title="Tarefas a realizar" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
                                 <i class="fa-solid fa-list-check"></i>&nbsp; Tarefas a realizar
                              </a>
                           </div>

                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>backup.php" method="get">
                                 <button style="width:100%" title="Cópia de segurança da base de dados" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-database"></i>&nbsp; Cópia de segurança da base de dados
                                 </button>
                              </form>
                           </div>

                           <div class="col-md-4 mb-3">
                              <form action="<?php echo SVRURL ?>restore.php" method="get">
                                 <button style="width:100%" title="Restauração da base de dados" type="submit" class="action-button btn-primary-action">
                                    <i class="fa-solid fa-rotate-left"></i>&nbsp; Restauração da base de dados
                                 </button>
                              </form>
                           </div>

                           <?php endif; ?>

                        </div><!-- /.row -->
                     </div><!-- /.action-section -->

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
