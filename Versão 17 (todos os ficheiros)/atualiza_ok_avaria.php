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

// Validar token CSRF em todos os pedidos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_post = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $csrf_post)) {
        http_response_code(403);
        exit('Pedido inválido.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include ("head.php"); ?>
</head>
<body class="main-layout">
<?php include ("header.php"); ?>
<?php include("sessao_timeout.php"); ?>

<div class="about">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span style="color:#4b6cb7;">Avarias</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Atualizar</li>
                  </ol>
               </nav>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-3">
<?php include("msg_bemvindo.php"); ?>
<?php
if (!isset($_POST['data']) || !isset($_POST['avaria'])
    || empty($_POST['data']) || empty($_POST['avaria'])) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>
<?php
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_av_data = $_POST["data"]   ?? '';
    $_av_av   = $_POST["avaria"] ?? '';

    $idav = (int)base64_decode($_GET['url']);

    if (!$idav || $idav <= 0) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>
<?php
    } else {

        // --- Imagem ---
        $img = 0; $x = 0; $imgData = null;
        if (!empty($_FILES["imgavaria"]["name"])) {
            $filepath = $_FILES['imgavaria']['tmp_name'];
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $ftype    = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            $allowed  = ['image/png','image/jpeg','image/jpg','image/bmp','image/gif'];
            if (!in_array($ftype, $allowed)) {
                $x = 1;
?>
<script>
swal({ title: 'ERRO', text: 'Ficheiro de imagem inválido!', icon: 'error' })
.then(function() { window.location = "<?php echo SVRURL ?>myavarias?op=t"; });
</script>
<?php
            } else {
                $imgData = file_get_contents($filepath);
                $img = 1;
            }
        }

        // --- Video ---
        $vid = 0; $vidData = null;
        if (!empty($_FILES["v"]["name"])) {
            $tmpv  = $_FILES["v"]["tmp_name"];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, $tmpv);
            finfo_close($finfo);
            if ($ftype === 'video/mp4' && filesize($tmpv) <= 3145728) {
                $vidData = file_get_contents($tmpv);
                $vid = 1;
            } else {
?>
<script>
swal({ title: 'ERRO', text: 'O vídeo deve ser MP4 com menos de 3Mb!', icon: 'error' })
.then(function() { window.location = "<?php echo SVRURL ?>myavarias?op=t"; });
</script>
<?php
            }
        }

        if ($x == 0) {
            if ($img == 0 && $vid == 0) {
                $stmt = $db->prepare("UPDATE avarias_reparacoes SET dataavaria=STR_TO_DATE(?,'%Y-%m-%d'), avaria=? WHERE id=?");
                $stmt->bind_param("ssi", $_av_data, $_av_av, $idav);
                $stmt->execute(); $stmt->close();

            } elseif ($img == 1 && $vid == 0) {
                $stmt = $db->prepare("UPDATE avarias_reparacoes SET dataavaria=STR_TO_DATE(?,'%Y-%m-%d'), avaria=?, imgavaria=? WHERE id=?");
                $null = null;
                $stmt->bind_param("ssbi", $_av_data, $_av_av, $null, $idav);
                $stmt->send_long_data(2, $imgData);
                $stmt->execute(); $stmt->close();

            } elseif ($img == 0 && $vid == 1) {
                $stmt = $db->prepare("UPDATE avarias_reparacoes SET dataavaria=STR_TO_DATE(?,'%Y-%m-%d'), avaria=?, video=? WHERE id=?");
                $null = null;
                $stmt->bind_param("ssbi", $_av_data, $_av_av, $null, $idav);
                $stmt->send_long_data(2, $vidData);
                $stmt->execute(); $stmt->close();

            } elseif ($img == 1 && $vid == 1) {
                $stmt = $db->prepare("UPDATE avarias_reparacoes SET dataavaria=STR_TO_DATE(?,'%Y-%m-%d'), avaria=?, imgavaria=?, video=? WHERE id=?");
                $null1 = null; $null2 = null;
                $stmt->bind_param("ssbbi", $_av_data, $_av_av, $null1, $null2, $idav);
                $stmt->send_long_data(2, $imgData);
                $stmt->send_long_data(3, $vidData);
                $stmt->execute(); $stmt->close();
            }
?>
<script>
swal({ title: 'Os dados foram atualizados!', icon: 'success' })
.then(function() { window.location = "<?php echo SVRURL ?>myavarias?op=t"; });
</script>
<?php
        }

        mysqli_close($db);
    }
}
?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include ("footer.php"); ?>
</body>
</html>
