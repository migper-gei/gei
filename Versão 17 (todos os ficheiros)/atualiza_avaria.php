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

// Validar CSRF se for POST (formulário submetido directamente para esta página)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_post = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf_post)) {
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

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php"); ?>

     <?php
include ("css_inserir.php");
include("sessao_timeout.php");
?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                           <a href="<?php echo SVRURL ?>avaria" style="color:#4b6cb7;text-decoration:none;">Avarias</a>
                    
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Atualizar</li>
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

<script>
function fot(n) {
    var n1 = n;
    event.preventDefault();
    swal({
        title: "Deseja eliminar a foto?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            window.setTimeout(function() {
                window.location.href = '<?php echo SVRURL ?>eliminafovi/' + n1 + '/f';
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<script>
function vid(n) {
    var n1 = n;
    event.preventDefault();
    swal({
        title: "Deseja eliminar o vídeo?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            window.setTimeout(function() {
                window.location.href = '<?php echo SVRURL ?>eliminafovi/' + n1 + '/v';
            }, 10);
        } else {
            swal("Cancelado.");
        }
    });
}
</script>

<script type="text/javascript">
function Filevalida() {
    const fi = document.getElementById('file').files[0];
    const fsize = fi.size;
    const file = Math.round((fsize / 1024));
    var fileIsMp4 = (fi.type === "video/mp4");
    if (file >= 3000 || !fileIsMp4) {
        swal({
            title: 'Tamanho máximo de 3Mb!',
            text: 'Tipo MP4',
            icon: 'error',
        });
        document.getElementById("file").value = '';
        return false;
    }
    return true;
}
</script>

<script>
function enImg(img) {
    img.style.transform = "scale(2.5)";
    img.style.transition = "transform 0.25s ease";
}
function rImg(img) {
    img.style.transform = "scale(1)";
}
</script>

<script type="text/javascript">
function validaImg() {
    var formData = new FormData();
    var file = document.getElementById("img").files[0];
    formData.append("Filedata", file);
    var t = file.type.split('/').pop().toLowerCase();
    if (t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
        swal({
            title: 'Inserir um tipo de ficheiro válido!',
            text: 'tipo: JPEG, JPG, PNG, BMP ou GIF',
            icon: 'error',
        });
        document.getElementById("img").value = '';
        return false;
    }
    return true;
}
</script>

<?php

$sql = "SELECT DISTINCT(nome) as no FROM salas order by nome";
$result = mysqli_query($db, $sql);

$idav = (int)base64_decode($_GET['url']);

if (isset($_GET['url'])) {
    $url = explode('/', $_GET['url']);
} else {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>
<?php
}
?>

<?php
if (!isset($idav) || empty($idav) || !is_numeric($idav)) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>
<?php
}
?>

<?php
$em = $_SESSION['email'];

// Verificar autoria com prepared statement (previne SQL injection)
$stmt0 = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes WHERE id = ? AND autoravaria = ?");
$stmt0->bind_param("is", $idav, $em);
$stmt0->execute();
$aut = (int)$stmt0->get_result()->fetch_row()[0];
$stmt0->close();

if ($aut <> 0) {
    // Prepared statement — previne SQL injection
    $stmt1 = $db->prepare(
        "SELECT ar.*, s.nome, eq.nomeequi FROM avarias_reparacoes ar
         JOIN salas s ON ar.id_sala = s.id
         JOIN equipamento eq ON ar.id_equi = eq.id
         WHERE ar.id = ? LIMIT 1"
    );
    $stmt1->bind_param("i", $idav);
    $stmt1->execute();
    $row1 = $stmt1->get_result()->fetch_assoc();
    $stmt1->close();

    $idesc = (int)$row1['id_escola'];

    $stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ? LIMIT 1");
    $stmt11->bind_param("i", $idesc);
    $stmt11->execute();
    $idescola = $stmt11->get_result()->fetch_row()[0] ?? '';
    $stmt11->close();
?>

<a href="<?php echo SVRURL ?>sair">Sair</a>

<div class="form-container">

<form name="avaria" action="<?php echo SVRURL ?>atualokavaria/<?php echo base64_encode($idav); ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onSubmit="return enviardados1();">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <label>Instituição: </label><br>
    <input style="width:100%" class="form-control" value="<?php echo htmlspecialchars($idescola); ?>" readonly name="escola" type="text" /><br /><br />

    <label>Sala: </label><br>
    <input style="width:100%" class="form-control" value="<?php echo htmlspecialchars($row1['nome']); ?>" readonly name="sala" type="text" /><br /><br />

    <label>Equipamento: </label><br>
    <input style="width:100%" class="form-control" value="<?php echo htmlspecialchars($row1['nomeequi']); ?>" readonly type="text" name="nomeequi" /><br /><br />

    <?php mysqli_close($db); ?>

    <label>Data: </label><br>
    <input required style="width:100%" class="form-control required-field" value='<?php echo htmlspecialchars($row1['dataavaria'], ENT_QUOTES, 'UTF-8'); ?>' type="date" name="data" /><br /><br />

    <label>Avaria (descrição): </label><br>
    <textarea required class="form-control required-field" style="width:100%;text-align:justify;" rows="4" cols="80" name="avaria"><?php echo htmlspecialchars($row1['avaria']); ?></textarea><br /><br />

    <table style="width:100%">
        <tr>
            <td>
            <?php
            if ($row1["imgavaria"] != null) {
                // FIX: detectar MIME real da imagem (evita falha com PNG/GIF/BMP)
                $finfo   = finfo_open(FILEINFO_MIME_TYPE);
                $imgMime = finfo_buffer($finfo, $row1['imgavaria']) ?: 'image/jpeg';
                finfo_close($finfo);
                echo '<img name="i1" id="img1" onmouseover="enImg(this)" onmouseout="rImg(this)"
                     height="150" width="250"
                     src="data:' . htmlspecialchars($imgMime) . ';base64,' . base64_encode($row1['imgavaria']) . '"
                     style="border-radius:6px;border:1px solid #e3e8f4;">';
            ?>
            <br>
            <a onclick="fot(<?php echo $url[0]; ?>);"
               href="<?php echo SVRURL ?>eliminafovi/<?php echo $url[0] ?>/<?php echo base64_encode('f') ?>"
               title="Remover foto"
               style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;padding:5px 13px;border-radius:7px;font-size:.78rem;font-weight:700;text-decoration:none;border:1.5px solid #c0392b;background:#fdecea;color:#c0392b;transition:background .15s;"
               onmouseover="this.style.background='#c0392b';this.style.color='#fff';"
               onmouseout="this.style.background='#fdecea';this.style.color='#c0392b';">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Remover foto
            </a>
            <?php } ?>
            </td>
            <td style="padding-left:20px;">
            <?php if ($row1["video"] != null): ?>
            <video width="250" height="200" controls style="border-radius:6px;border:1px solid #e3e8f4;">
                <source src="<?php echo SVRURL ?>streamvideo.php?id=<?php echo base64_encode($idav); ?>" type="video/mp4">
                O seu browser não suporta a reprodução de vídeo.
            </video>
            <br>
            <a onclick="vid(<?php echo $url[0]; ?>);"
               href="<?php echo SVRURL ?>eliminafovi/<?php echo $url[0] ?>/<?php echo base64_encode('v') ?>"
               title="Remover vídeo"
               style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;padding:5px 13px;border-radius:7px;font-size:.78rem;font-weight:700;text-decoration:none;border:1.5px solid #c0392b;background:#fdecea;color:#c0392b;transition:background .15s;"
               onmouseover="this.style.background='#c0392b';this.style.color='#fff';"
               onmouseout="this.style.background='#fdecea';this.style.color='#c0392b';">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Remover vídeo
            </a>
            <?php endif; ?>
            </td>
        </tr>
    </table>

    <br>
    <label>Avaria (imagem: JPEG, JPG, PNG, GIF, BMP): </label><br>
    <input accept="image/png, image/gif, image/jpeg, image/jpg, image/bmp" class="form-control" size="50" type="file" name="imgavaria" id="img" onChange="validaImg()" /><br /><br />

    <label>Avaria (vídeo tamanho máximo 3Mb, tipo MP4): </label><br>
    <input accept="video/mp4" class="form-control" size="50" type="file" name="v" id="file" onChange="return Filevalida();" /><br /><br />

    <div class="text-center mt-4">
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-pen"></i>
            &nbsp;Atualizar avaria
        </button>
    </div>

</form>

</div><!-- /.form-container -->

<?php
} else {
?>
<script>
swal({
    title: 'Não é o autor da avaria ou avaria não existe!',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>myavarias?op=t";
});
</script>
<?php
}
?>



    <a href="<?php echo SVRURL ?>myavarias?op=t"  title="Voltar">
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
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
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

      <?php include ("jquery_bootstrap.php"); ?>
      <?php include ("footer.php"); ?>

   </body>
</html>
