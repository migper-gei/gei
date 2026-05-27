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

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>
      <?php include ("header.php"); ?>

      <?php
include("sessao_timeout.php");

$stmt_maxesc = $db->prepare("SELECT max(id) FROM escolas");
$stmt_maxesc->execute();
$maxesc = (int)$stmt_maxesc->get_result()->fetch_row()[0];
$stmt_maxesc->close();

if (
    !is_numeric(base64_decode($_GET["ia"] ?? '')) ||
    empty(base64_decode($_GET["ia"] ?? ''))        ||
    empty(base64_decode($_GET["em"] ?? ''))
) {
?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>avaria';
}, 10);
</script>
<?php
    exit;
}

$ia = (int)base64_decode($_GET["ia"]);
$em = base64_decode($_GET["em"]); // email — não fazer cast para int
$sa = (int)base64_decode($_GET["sa"] ?? '');
?>

      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2>AVARIA - Enviar email reparador</h2>
                  </div>
               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">

<?php include("msg_bemvindo.php"); ?>

<script>
function validate() {
    var checkbox = document.querySelector('input[name="rep[]"]:checked');
    if (!checkbox) {
        event.preventDefault();
        swal({
            title: "Escolha pelo menos um reparador!",
            type: "warning",
            confirmButtonText: "OK",
            closeOnConfirm: false
        });
        return false;
    }
    return true;
}

function Check() {
    var chk  = document.getElementsByName("my_check")[0];
    var chk2 = document.getElementsByName('rep[]');
    for (var i = 0; i < chk2.length; i++) {
        chk2[i].checked = chk.checked;
    }
}

function enlargeImg(img) {
    img.style.transform  = "scale(2.5)";
    img.style.transition = "transform 0.25s ease";
}

function resetImg(img) {
    img.style.transform = "scale(1)";
}
</script>

<?php
$stmt_av2 = $db->prepare(
    "SELECT ar.*, e.nomeequi, s.nome, esc.nome_escola, esc.id as ide
     FROM avarias_reparacoes ar, equipamento e, salas s, escolas esc
     WHERE ar.id_equi=e.id AND ar.id_sala=s.id AND ar.id_escola=esc.id
       AND ar.id=? AND ar.datareparacao IS NULL"
);
$stmt_av2->bind_param("i", $ia);
$stmt_av2->execute();
$row2 = $stmt_av2->get_result()->fetch_assoc();
$stmt_av2->close();

$stmt_em = $db->prepare("SELECT nome FROM utilizadores WHERE email=?");
$stmt_em->bind_param("s", $em);
$stmt_em->execute();
$rows2a = $stmt_em->get_result()->fetch_row();
$stmt_em->close();
?>

<form name="myform" onsubmit="return validate();" method="post"
      action="<?php echo SVRURL ?>enviar_email_avaria.php?r=<?php echo base64_encode(1) ?>&amp;ia=<?php echo base64_encode($row2['id']); ?>">

<?php
$stmt_rep3 = $db->prepare("SELECT id, nome, email FROM utilizadores WHERE tipo=3");
$stmt_rep3->execute();
$result3 = $stmt_rep3->get_result();
?>

<br>
Dados da avaria:
<br>
<li class="list-group-item">
    <b>Instituição / Sala / Equipamento:</b>
    <?php
    echo htmlspecialchars($row2['nome_escola']);
    echo ' / ';
    echo htmlspecialchars($row2['nome']);
    echo ' / ';
    echo htmlspecialchars($row2['nomeequi']);
    ?>
    <br>
    <label><b>Autor / Email:</b></label>
    <?php
    echo htmlspecialchars($rows2a[0] ?? '');
    echo ' / ';
    echo htmlspecialchars($em);
    echo '<br><b>Data avaria: </b>';
    echo htmlspecialchars($row2['dataavaria']);
    echo '<br><b>Descrição: </b>';
    echo htmlspecialchars($row2['avaria']);
    echo '<br>';
    ?>

    <br>
    <?php if (!empty($row2["imgavaria"])): ?>
        <img onmouseover="enlargeImg(this)" onmouseout="resetImg(this)"
             height="150" width="250"
             src="data:image/jpeg;base64,<?php echo base64_encode($row2['imgavaria']); ?>">
    <?php endif; ?>

    <?php if (!empty($row2["video"])): ?>
        <video onmouseover="enlargeImg(this)" onmouseout="resetImg(this)"
               width="250" height="200" controls>
            <source src="data:video/mp4;base64,<?php echo base64_encode($row2['video']); ?>">
        </video>
    <?php endif; ?>
</li>

<br><br>
Escolha o(s) reparador(es) para envio de email:
<br>

<li class="list-group-item">
    <div style="text-align:center;color:blue;">
        <input type="checkbox" name="my_check" value="yes" onclick="Check()">
        Selecionar/Desselecionar tudo
    </div>
    <br>
    <?php while ($row3 = mysqli_fetch_assoc($result3)): ?>
        <ul style="display:block;">
            <input type="checkbox" name="rep[]" value="<?php echo (int)$row3['id']; ?>">
            <?php echo htmlspecialchars($row3['nome']) . ' - ' . htmlspecialchars($row3['email']); ?>
        </ul>
    <?php endwhile; ?>
</li>

<br>
<div style="text-align:center;width:90%;">
    <input type="submit" value="Enviar email">
</div>

</form>

<a href="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=<?php echo base64_encode(1) ?>&amp;op=t&amp;ies=<?php echo base64_encode($row2['ide']) ?>&amp;sai=<?php echo base64_encode($sa) ?>">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>
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
