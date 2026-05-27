<?php

// ─────────────────────────────────────────────
//  SESSÃO SEGURA
// ─────────────────────────────────────────────
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

// [FIX-1] Gerar CSRF token se ainda não existir na sessão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ─────────────────────────────────────────────
//  CONFIGURAÇÃO  (lida do ficheiro .env)
// ─────────────────────────────────────────────
$_env_file = __DIR__ . '/.env';
if (file_exists($_env_file)) {
    foreach (file($_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if ((strpos(trim($_line), '#') === 0) || (strpos($_line, '=') === false)) continue;
        [$_k, $_v] = explode('=', $_line, 2);
        $_ENV[trim($_k)] = trim($_v);
    }
}

// Variáveis obrigatórias do .env — sem fallback para evitar credenciais expostas no código
foreach (['DB_HOST', 'DB_USER', 'DB_PASS'] as $_required) {
    if (empty($_ENV[$_required])) {
        http_response_code(500);
        exit('Erro de configuração: variável ' . $_required . ' não definida no .env');
    }
}

define('DB_HOST',    $_ENV['DB_HOST']);
define('DB_USER',    $_ENV['DB_USER']);
define('DB_PASS',    $_ENV['DB_PASS']);
define('DB_PORT',    (int)($_ENV['DB_PORT']   ?? 3306));
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// DB_NAME vem da sessão do utilizador autenticado (BD da escola/instituição)
// Definido em validauser.php: $_SESSION['nobd'] = $nomebd
if (empty($_SESSION['nobd'])) {
    http_response_code(403);
    exit('Sessão inválida ou expirada. <a href="l">Faça login novamente.</a>');
}
define('DB_NAME', $_SESSION['nobd']);

// Tamanho máximo do ficheiro SQL aceite (ex: 100 MB)
define('MAX_FILE_MB', 100);

// ─────────────────────────────────────────────
//  VARIÁVEIS DE ESTADO
// ─────────────────────────────────────────────
$restore_log     = [];
$restore_success = false;
$restore_error   = null;

// ─────────────────────────────────────────────
//  PROCESSAMENTO DO UPLOAD
// ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {

    // [FIX-1] Validar CSRF token antes de qualquer outra operação
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        $restore_error = 'Token CSRF inválido ou ausente. Recarregue a página e tente novamente.';
    }

    // 1. Autorização
    elseif (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
        $restore_error = 'Acesso negado. Apenas administradores podem efectuar o restauro.';
    } else {

        $ts_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $file      = $_FILES['sql_file'];
        $file_name = basename($file['name']);
        $file_tmp  = $file['tmp_name'];
        $file_size = $file['size'];
        $file_err  = $file['error'];

        // 2. Validação do upload
        if ($file_err !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => 'O ficheiro excede o limite definido no php.ini (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE  => 'O ficheiro excede o limite definido no formulário.',
                UPLOAD_ERR_PARTIAL    => 'O ficheiro foi enviado apenas parcialmente.',
                UPLOAD_ERR_NO_FILE    => 'Nenhum ficheiro foi enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária inexistente.',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o ficheiro no disco.',
                UPLOAD_ERR_EXTENSION  => 'Upload bloqueado por extensão PHP.',
            ];
            $restore_error = $upload_errors[$file_err] ?? "Erro de upload desconhecido (código {$file_err}).";

        } elseif ($file_size > MAX_FILE_MB * 1024 * 1024) {
            $restore_error = "O ficheiro excede o limite de " . MAX_FILE_MB . " MB.";

        } elseif (strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) !== 'sql') {
            $restore_error = "Apenas ficheiros .sql são aceites.";

        } else {

            // [FIX-2] Verificar magic bytes / bytes de controlo nos primeiros 512 bytes
            // Ficheiros SQL legítimos são texto ASCII/UTF-8 puro.
            // Binários (PE, ELF, ZIP, PHP compilado…) têm bytes de controlo no cabeçalho.
            $header = file_get_contents($file_tmp, false, null, 0, 512);
            if ($header === false) {
                $restore_error = "Não foi possível ler o ficheiro para validação.";

            } elseif (
                !mb_check_encoding($header, 'UTF-8') ||
                preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $header)
            ) {
                // Bytes de controlo fora do ASCII imprimível (excluindo TAB \x09, LF \x0A, CR \x0D)
                $restore_error = "O ficheiro contém bytes inválidos para SQL (possível ficheiro binário). Upload rejeitado.";

            } else {

                // 3. Ler o conteúdo completo do ficheiro SQL
                $sql_content = file_get_contents($file_tmp);
                if ($sql_content === false) {
                    $restore_error = "Não foi possível ler o ficheiro SQL.";
                } else {

                    // [FIX-3] Blocklist de construções SQL perigosas — verificação antes de executar
                    // Estas instruções não aparecem em backups normais gerados pelo mysqldump/SGEI
                    // e podem ser usadas para leitura/escrita de ficheiros do servidor ou escalada de privilégios.
                    $dangerous_patterns = [
                        '/\bLOAD\s+DATA\b/i',      // leitura de ficheiros do servidor
                        '/\bINTO\s+OUTFILE\b/i',    // escrita de ficheiros arbitrários
                        '/\bINTO\s+DUMPFILE\b/i',   // escrita binária de ficheiros
                        '/\bGRANT\b/i',             // alteração de privilégios MySQL
                        '/\bREVOKE\b/i',            // remoção de privilégios MySQL
                        '/\bCREATE\s+USER\b/i',     // criação de utilizadores MySQL
                        '/\bDROP\s+USER\b/i',       // remoção de utilizadores MySQL
                        '/\bRENAME\s+USER\b/i',
                        '/\bSET\s+GLOBAL\b/i',      // alteração de variáveis globais do servidor
                        '/\bFLUSH\b/i',             // flush de logs, privilégios, cache…
                        '/\bSHUTDOWN\b/i',          // encerramento do servidor MySQL
                        '/\bSYSTEM\b/i',            // execução de comandos do SO (MySQL 8+)
                    ];

                    $dangerous_found = false;
                    foreach ($dangerous_patterns as $pattern) {
                        if (preg_match($pattern, $sql_content)) {
                            $restore_error   = "O ficheiro SQL contém instruções não permitidas. Upload rejeitado.";
                            $dangerous_found = true;
                            break;
                        }
                    }

                    if (!$dangerous_found) {

                        // [FIX-5] Log de auditoria — hash do ficheiro submetido
                        $file_sha256 = hash_file('sha256', $file_tmp);

                        // 4. Ligação à base de dados
                        // Desativar exceções automáticas do mysqli (XAMPP tem MYSQLI_REPORT_STRICT por defeito)
                        mysqli_report(MYSQLI_REPORT_OFF);
                        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
                        if (!$conn) {
                            $restore_error = "Erro de ligação à BD: " . mysqli_connect_error();
                        } else {
                            mysqli_set_charset($conn, DB_CHARSET);
                            $restore_log[] = ['ok',   "Ligação estabelecida a " . DB_HOST . "/" . DB_NAME];
                            $restore_log[] = ['info', "Ficheiro: {$file_name} (" . round($file_size / 1024, 1) . " KB)"];
                            // [FIX-5] Registar hash SHA-256 para auditoria
                            $restore_log[] = ['info', "SHA-256: {$file_sha256}"];
                            $restore_log[] = ['info', "Operador: " . htmlspecialchars($_SESSION['user'] ?? 'desconhecido', ENT_QUOTES, 'UTF-8') . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-')];

                            // 5. Dividir o SQL em statements individuais
                            //    Estratégia: dividir por ";" respeitando strings e comentários
                            $statements = split_sql_statements($sql_content);
                            $restore_log[] = ['info', count($statements) . " statement(s) encontrado(s)."];

                            // 6. Executar cada statement
                            $ok_count      = 0;
                            $err_count     = 0;
                            $blocked_count = 0;
                            $table_stats   = [];   // [tabela => nº de INSERTs]

                            // [FIX-4] Padrão de whitelist: apenas tipos de statement gerados por mysqldump / SGEI
                            $allowed_pattern = '/^\s*(' .
                                'CREATE\s+(TABLE|DATABASE|INDEX|UNIQUE\s+INDEX)|' .
                                'ALTER\s+TABLE|' .
                                'INSERT\s+INTO|' .
                                'DROP\s+TABLE\s+IF\s+EXISTS|' .
                                'DROP\s+DATABASE\s+IF\s+EXISTS|' .
                                'SET\s+(?!GLOBAL\b)|' .        // SET session vars (ex: SET NAMES), mas não SET GLOBAL
                                'USE\s+`|' .
                                'LOCK\s+TABLES|' .
                                'UNLOCK\s+TABLES|' .
                                'START\s+TRANSACTION|' .
                                'BEGIN|' .
                                'COMMIT|' .
                                '\/\*![0-9]+' .                // directivas condicionais mysqldump (/*!40101 … */)
                            ')/ix';

                            foreach ($statements as $stmt) {
                                $stmt = trim($stmt);
                                if ($stmt === '' || (strpos($stmt, '--') === 0)) continue;

                                // [FIX-4] Rejeitar statements fora da whitelist
                                if (!preg_match($allowed_pattern, $stmt)) {
                                    $blocked_count++;
                                    $restore_log[] = ['warn',
                                        "Bloqueado (tipo não permitido): " . mb_substr($stmt, 0, 100) . "…"
                                    ];
                                    continue;
                                }

                                if (!mysqli_query($conn, $stmt)) {
                                    $err_count++;
                                    $restore_log[] = ['error',
                                        "Erro: " . mysqli_error($conn) . " → " . mb_substr($stmt, 0, 120) . "…"
                                    ];
                                } else {
                                    $ok_count++;
                                    // Contabilizar INSERTs por tabela
                                    if (preg_match('/^INSERT\s+INTO\s+`([^`]+)`/i', $stmt, $m)) {
                                        $table_stats[$m[1]] = ($table_stats[$m[1]] ?? 0) + 1;
                                    }
                                }
                            }

                            mysqli_close($conn);

                            // 7. Resumo por tabela
                            foreach ($table_stats as $tbl => $cnt) {
                                $restore_log[] = ['ok', "→ `{$tbl}`: {$cnt} linha(s) inserida(s)"];
                            }

                            if ($blocked_count > 0) {
                                $restore_log[] = ['warn', "{$blocked_count} statement(s) bloqueado(s) por não serem permitidos."];
                            }

                            $elapsed = round(microtime(true) - $ts_start, 2);

                            if ($err_count === 0) {
                                $restore_log[]   = ['ok', "Restauro concluído em {$elapsed}s — {$ok_count} statement(s) executado(s) sem erros."];
                                $restore_success = true;
                            } else {
                                $restore_log[] = ['warn',
                                    "Restauro terminado em {$elapsed}s com {$err_count} erro(s). " .
                                    "{$ok_count} statement(s) executado(s) com sucesso."
                                ];
                                $restore_success = true; // parcialmente ok
                            }

                            // ── Auditoria do restauro ─────────────────────────────────────────────
                            require_once __DIR__ . '/gei_audit.php';
                            $detalhe_restore = implode(' | ', array_filter([
                                'ficheiro='  . $file_name,
                                'tamanho='   . round($file_size / 1024, 1) . ' KB',
                                'sha256='    . $file_sha256,
                                'statements='. $ok_count . ' ok / ' . $err_count . ' erros / ' . $blocked_count . ' bloqueados',
                                'elapsed='   . $elapsed . 's',
                                'tabelas='   . implode(',', array_keys($table_stats)),
                            ], fn($p) => !str_ends_with($p, '=')));

                            $db_audit = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
                            if (!$db_audit->connect_error) {
                                $db_audit->set_charset(DB_CHARSET);
                                $audit_action = ($err_count === 0) ? 'restore_ok' : 'restore_parcial';
                                gei_audit($db_audit, $audit_action, 'restore', 0, $detalhe_restore);
                                $db_audit->close();
                            }

                            // [FIX-1] Regenerar CSRF token após operação bem-sucedida
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        }
                    }
                }
            }
        }
    }
}

// ─────────────────────────────────────────────
//  FUNÇÃO: dividir SQL em statements
//  Suporta: BLOBs hex (0x...), strings com ';'
//  dentro, comentários --
// ─────────────────────────────────────────────
function split_sql_statements(string $sql): array {
    $statements = [];
    $current    = '';
    $length     = strlen($sql);
    $i          = 0;

    while ($i < $length) {
        $char = $sql[$i];

        // Comentário de linha --  → ignorar completamente (não acumular no statement)
        if ($char === '-' && isset($sql[$i + 1]) && $sql[$i + 1] === '-') {
            $end = strpos($sql, "\n", $i);
            if ($end === false) { $i = $length; continue; }
            $i = $end + 1;
            continue;
        }

        // Comentário bloco /* */  → ignorar completamente
        if ($char === '/' && isset($sql[$i + 1]) && $sql[$i + 1] === '*') {
            $end = strpos($sql, '*/', $i + 2);
            if ($end === false) { $i = $length; continue; }
            $i = $end + 2;
            continue;
        }

        // String entre aspas simples — avançar até fechar, respeitando escapes
        if ($char === "'") {
            $current .= $char;
            $i++;
            while ($i < $length) {
                $c = $sql[$i];
                $current .= $c;
                if ($c === '\\') {
                    // caracter de escape — incluir o próximo sem verificar
                    $i++;
                    if ($i < $length) { $current .= $sql[$i]; $i++; }
                    continue;
                }
                if ($c === "'") { $i++; break; }
                $i++;
            }
            continue;
        }

        // Fim de statement
        if ($char === ';') {
            $current = trim($current);
            if ($current !== '') $statements[] = $current;
            $current = '';
            $i++;
            continue;
        }

        $current .= $char;
        $i++;
    }

    // Último statement sem ';'
    $current = trim($current);
    if ($current !== '') $statements[] = $current;

    return $statements;
}
?>
<!DOCTYPE html>
<html lang="pt">
   <head>

<?php include ("head.php"); ?>

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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Restauro da base de dados</li>
                  </ol>
               </nav>
                  <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

                     <div class="welcome-section">
                        <?php include("msg_bemvindo.php"); ?>
                     </div>

                     <br>

                     <?php if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1): ?>
                        <!-- Acesso negado -->
                        <div class="alert alert-danger" role="alert">
                           <strong>Acesso negado.</strong> Apenas administradores podem efectuar o restauro.
                        </div>

                     <?php else: ?>

                        <!-- Aviso -->
                        <div class="alert alert-warning" role="alert">
                           <strong>⚠ Atenção:</strong> O restauro irá <strong>apagar e recriar todas as tabelas</strong>
                           da base de dados <code><?php echo htmlspecialchars(DB_NAME); ?></code>.
                           Esta operação é <strong>irreversível</strong>. Efectue um backup antes de continuar.
                        </div>

                        <?php if ($restore_error): ?>
                           <div class="alert alert-danger" role="alert">
                              <strong>Erro:</strong> <?php echo htmlspecialchars($restore_error, ENT_QUOTES, 'UTF-8'); ?>
                           </div>
                        <?php endif; ?>

                        <?php if (!empty($restore_log)): ?>
                           <!-- Log do restauro -->
                           <div style="background:#1e1e1e; border-radius:6px; padding:1.2em;
                                       font-family:'Courier New',monospace; font-size:.88em;
                                       line-height:1.8; color:#d4d4d4; margin-bottom:1rem;">
                              <?php
                              $level_colors = [
                                  'ok'    => '#4ec9b0',
                                  'info'  => '#9cdcfe',
                                  'warn'  => '#ce9178',
                                  'error' => '#f44747',
                              ];
                              foreach ($restore_log as [$lvl, $msg]):
                                  $color = $level_colors[$lvl] ?? '#d4d4d4';
                              ?>
                                 <span style="color:<?php echo $color; ?>">
                                    <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
                                 </span><br>
                              <?php endforeach; ?>
                           </div>

                           <?php if ($restore_success): ?>
                              <div class="alert alert-success" role="alert">
                                 ✅ Restauro concluído com sucesso.
                              </div>
                           <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!$restore_success): ?>
                           <!-- Formulário de upload -->
                           <div class="card" style="border:1px solid #dee2e6; border-radius:8px; padding:1.5rem;">
                              <h5 style="margin-bottom:1rem;">📂 Selecionar ficheiro de backup</h5>
                              <form method="POST" enctype="multipart/form-data"
                                    onsubmit="return confirmarRestaurar()">

                                 <input type="hidden" name="MAX_FILE_SIZE"
                                        value="<?php echo MAX_FILE_MB * 1024 * 1024; ?>">

                                 <!-- [FIX-1] CSRF token -->
                                 <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                 <div class="mb-3">
                                    <label for="sql_file" class="form-label">
                                       Ficheiro SQL <small class="text-muted">(máx. <?php echo MAX_FILE_MB; ?> MB)</small>
                                    </label>
                                    <input type="file"
                                           class="form-control"
                                           id="sql_file"
                                           name="sql_file"
                                           accept=".sql"
                                           required>
                                    <div class="form-text text-muted">
                                       Apenas ficheiros <code>.sql</code> gerados pelo sistema de backup SGEI.
                                    </div>
                                 </div>

                                 <button type="submit" class="btn btn-danger">
                                    🔄 Iniciar Restauro
                                 </button>

                              </form>
                           </div>
                        <?php else: ?>
                           <!-- Botão para efectuar novo restauro -->
                           <a href="restore.php" class="btn btn-secondary">🔄 Efectuar outro restauro</a>
                        <?php endif; ?>

                     <?php endif; ?>

                     <br><br>

                     <a href="<?php echo SVRURL ?>configura">
                        <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
                     </a>

                     <br> <br>

                     <?php include ("jquery_bootstrap.php"); ?>

                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>

<script>
function confirmarRestaurar() {
    return confirm(
        '⚠ ATENÇÃO!\n\n' +
        'Esta operação irá APAGAR e RECRIAR todas as tabelas da base de dados.\n\n' +
        'Tem a certeza que pretende continuar?'
    );
}
</script>
