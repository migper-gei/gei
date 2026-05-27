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

<?php include ("head.php"); ?>

   </head>

   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include ("header.php"); ?>

      <?php
      include("sessao_timeout.php");
      ?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span style="color:#4b6cb7;">Avarias</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
               <div class="titlepage"></div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

<?php include("msg_bemvindo.php"); ?>

<br>

<?php

$said  = (int)base64_decode($_GET["ai"]);
$idesc = (int)base64_decode($_GET["esi"]);

// equip chega como array de checkboxes: equip[]
$equipArray = [];
if (isset($_POST['equip']) && is_array($_POST['equip'])) {
    foreach ($_POST['equip'] as $v) {
        $iv = (int)$v;
        if ($iv > 0) $equipArray[] = $iv;
    }
}

if ( empty($_POST['data']) || empty($_POST['avaria'])
|| empty($equipArray)
|| !isset($idesc) || !isset($said) || !is_numeric($said) || !is_numeric($idesc)
|| empty($idesc) || empty($said)
) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>insereavaria?aves=<?php echo base64_encode($idesc) ?>';
}, 10);
</script>
<?php
} else {

    // --- Validação CSRF ---
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        ?>
        <script>
        swal({ title: 'Erro de segurança!', text: 'Token inválido. Por favor recarregue a página.', icon: 'error' })
        .then(function() { window.location = "<?php echo SVRURL ?>insereavaria?aves=<?php echo base64_encode($idesc); ?>"; });
        </script>
        <?php
        exit;
    }
    // Invalidar token após uso (one-time use)
    unset($_SESSION['csrf_token']);

    // =========================================================
    // LIMITES DE TAMANHO
    // =========================================================
    define('MAX_IMG_SIZE',   5 * 1024 * 1024);  // 5 MB
    define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50 MB

    // =========================================================
    // TIPOS PERMITIDOS (MIME real via finfo + extensão)
    // =========================================================
    $allowedImageTypes = [
        'image/png'  => ['png'],
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/bmp'  => ['bmp'],
        'image/gif'  => ['gif'],
    ];

    $allowedVideoTypes = [
        'video/mp4'       => ['mp4'],
        'video/mpeg'      => ['mpeg', 'mpg'],
        'video/quicktime' => ['mov'],
        'video/x-msvideo' => ['avi'],
        'video/webm'      => ['webm'],
    ];

    // =========================================================
    // Função de validação segura — usa finfo (servidor), nunca $_FILES["type"]
    // =========================================================
    function validarFicheiro(string $tmpPath, string $nomeOriginal, array $allowedTypes, int $maxSize): array
    {
        // 1. Verificar se o ficheiro foi realmente enviado via HTTP POST
        if (!is_uploaded_file($tmpPath)) {
            return ['ok' => false, 'erro' => 'Upload inválido.'];
        }

        // 2. Verificar tamanho
        $size = filesize($tmpPath);
        if ($size === false || $size > $maxSize) {
            $mb = round($maxSize / 1024 / 1024);
            return ['ok' => false, 'erro' => "O ficheiro excede o tamanho máximo permitido ({$mb} MB)."];
        }

        // 3. Verificar MIME real via finfo (ignora o que o browser diz)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!array_key_exists($mimeReal, $allowedTypes)) {
            return ['ok' => false, 'erro' => 'Tipo de ficheiro não permitido (conteúdo inválido).'];
        }

        // 4. Verificar extensão do nome original contra o MIME real
        $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes[$mimeReal], true)) {
            return ['ok' => false, 'erro' => 'A extensão do ficheiro não corresponde ao seu conteúdo.'];
        }

        // 5. Para imagens: tentar abrir com getimagesize como verificação extra
        if (str_starts_with($mimeReal, 'image/')) {
            if (@getimagesize($tmpPath) === false) {
                return ['ok' => false, 'erro' => 'O ficheiro não é uma imagem válida.'];
            }
        }

        return ['ok' => true];
    }

    // =========================================================
    // PROCESSAR IMAGEM
    // =========================================================
    $tmp  = '';
    $x    = 0;
    $filename = $_FILES["imgavaria"]["name"] ?? '';

    if ($filename !== '') {
        $valImg = validarFicheiro(
            $_FILES["imgavaria"]["tmp_name"],
            $filename,
            $allowedImageTypes,
            MAX_IMG_SIZE
        );

        if (!$valImg['ok']) {
            $x = 1;
            ?>
            <script>
            swal({
                title: 'ERRO',
                text: '<?php echo addslashes($valImg['erro']); ?>',
                icon: 'error',
            }).then(function() {
                window.location = "<?php echo SVRURL ?>insereavaria";
            });
            </script>
            <?php
        } else {
            $tmp = file_get_contents($_FILES["imgavaria"]["tmp_name"]); // BLOB — sem addslashes
        }
    }

    // =========================================================
    // PROCESSAR VÍDEO
    // =========================================================
    $tmpv      = '';
    $filenamev = $_FILES["v"]["name"] ?? '';

    if ($filenamev !== '' && $x === 0) {
        $valVid = validarFicheiro(
            $_FILES["v"]["tmp_name"],
            $filenamev,
            $allowedVideoTypes,
            MAX_VIDEO_SIZE
        );

        if (!$valVid['ok']) {
            $x = 1;
            ?>
            <script>
            swal({
                title: 'ERRO',
                text: '<?php echo addslashes($valVid['erro']); ?>',
                icon: 'error',
            }).then(function() {
                window.location = "<?php echo SVRURL ?>insereavaria";
            });
            </script>
            <?php
        } else {
            $tmpv = file_get_contents($_FILES["v"]["tmp_name"]); // BLOB — sem addslashes
        }
    }

    // =========================================================
    // GRAVAR NA BASE DE DADOS
    // =========================================================
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $x == 0) {

        $dataatual = date('Y-m-d');

        // Ano letivo
        $sql2    = "SELECT MAX(ano_lectivo) FROM periodos";
        $result2 = mysqli_query($db, $sql2);
        $rows2   = mysqli_fetch_row($result2);
        $conta   = $rows2[0];

        // Período atual
        $stmtPer = $db->prepare("
            SELECT MAX(num_periodo) FROM periodos
            WHERE STR_TO_DATE(?, '%Y-%m-%d') >= STR_TO_DATE(data_inicio, '%Y-%m-%d')
              AND STR_TO_DATE(?, '%Y-%m-%d') <= STR_TO_DATE(data_fim, '%Y-%m-%d')
              AND ano_lectivo = ?
        ");
        $stmtPer->bind_param("sss", $dataatual, $dataatual, $conta);
        $stmtPer->execute();
        $per = $stmtPer->get_result()->fetch_row()[0];
        $stmtPer->close();

        $em = $_SESSION['email'];

        $stmt_av = $db->prepare("INSERT INTO avarias_reparacoes (id_equi, id_sala, id_escola, autoravaria, dataavaria, avaria, imgavaria, video, ano_letivo, periodo) VALUES (?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, ?, ?, ?, ?)");
        $_av_data  = $_POST["data"] ?? '';
        $_av_av    = $_POST["avaria"] ?? '';

        // Posições dos BLOBs no prepared statement (0-based): imgavaria=6, video=7
        // bind_param("b") com send_long_data envia dados binários sem qualquer escaping
        $null      = null;
        $_av_equip = 0;
        $stmt_av->bind_param("iiisssbbss",
            $_av_equip, $said, $idesc, $em, $_av_data, $_av_av, $null, $null, $conta, $per
        );

        $ida = null;
        foreach ($equipArray as $_av_equip) {
            // send_long_data tem de ser reenviado antes de cada execute() em ciclos
            if ($tmp  !== '') $stmt_av->send_long_data(6, $tmp);
            if ($tmpv !== '') $stmt_av->send_long_data(7, $tmpv);
            $stmt_av->execute();
        }
        $stmt_av->close();

        // ID do último registo inserido (para email)
        $sql2    = "SELECT MAX(id) FROM avarias_reparacoes";
        $result2 = mysqli_query($db, $sql2);
        $rows    = mysqli_fetch_row($result2);
        $ida     = $rows[0];

        $num_inseridos = count($equipArray);
        $txt_equip_pl  = $num_inseridos > 1 ? "{$num_inseridos} avarias registadas" : "avaria registada";

        mysqli_close($db);

        unset($_SESSION['idesc']);

        $notif_admin = (isset($_POST['notif_admin']) && $_POST['notif_admin'] === 'yes') ? 1 : 0;
        $tipo        = $_SESSION['tipo'] ?? 0;
        $is_adm_rep  = ($tipo == 1 || $tipo == 3);

        if ($is_adm_rep) {
            if ($notif_admin == 1) {
                $redirect  = SVRURL . "enviar_email_avaria.php?r=" . base64_encode(1) . "&&ia=" . base64_encode($ida);
                $txt_email = 'Um email foi enviado ao autor e ao administrador/reparador com os dados da avaria!';
            } else {
                $redirect  = SVRURL . "avaria";
                $txt_email = 'Os dados foram guardados com sucesso!';
            }
        } else {
            $redirect  = SVRURL . "enviar_email_avaria.php?r=" . base64_encode(0) . "&&ia=" . base64_encode($ida);
            $txt_email = 'Um email foi enviado com os dados da avaria!';
        }
        ?>
        <script>
        swal({
            title: 'Os dados foram guardados!',
            text: '<?php echo addslashes($txt_equip_pl . " — " . $txt_email); ?>',
            icon: 'success',
        }).then(function() {
            window.location = "<?php echo $redirect; ?>";
        });
        </script>
        <?php
    }
}
?>

<br><br><br><br><br><br><br><br>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->

      <?php include ("footer.php"); ?>

   </body>
</html>
