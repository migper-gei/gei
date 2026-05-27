<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
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
<head><?php include("head.php"); ?></head>
<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php include("sessao_timeout.php"); ?>
<?php

// Função auxiliar — mostra swal e redireciona
function resposta(string $titulo, string $icone, string $url): void {
    echo "<script>swal({ title: '" . addslashes($titulo) . "', icon: '{$icone}' })"
       . ".then(function(){ window.location = '" . addslashes($url) . "'; });</script>";
    exit;
}

$utiliz_url = SVRURL . 'utiliz';

// Apenas administradores
if (!isset($_SESSION['tipo']) || (int)$_SESSION['tipo'] !== 1) {
    resposta('Sem permissão!', 'error', $utiliz_url);
}

// Validar parâmetros da URL
if (!isset($_GET['url'])) {
    header('Location: ' . $utiliz_url);
    exit;
}

$partes     = explode('/', $_GET['url']);
$id         = (int)($partes[0] ?? 0);
$novoEstado = isset($partes[1]) ? (int)$partes[1] : -1;

if ($id <= 0 || !in_array($novoEstado, [0, 1], true)) {
    header('Location: ' . $utiliz_url);
    exit;
}

// Verificar utilizador na BD
$stmt_chk = $db->prepare("SELECT nome, tipo FROM utilizadores WHERE id=?");
$stmt_chk->bind_param("i", $id);
$stmt_chk->execute();
$u = $stmt_chk->get_result()->fetch_assoc();
$stmt_chk->close();

if (!$u) {
    header('Location: ' . $utiliz_url);
    exit;
}

// Não desativar administradores (tipo 1)
if ((int)$u['tipo'] === 1) {
    resposta('Não é possível desativar um administrador!', 'error', $utiliz_url);
}

// Não desativar a própria conta
if ($u['nome'] === ($_SESSION['login_user'] ?? '')) {
    resposta('Não pode desativar a sua própria conta!', 'error', $utiliz_url);
}

// Aplicar toggle
$stmt_upd = $db->prepare("UPDATE utilizadores SET ativo=? WHERE id=? AND tipo!=1");
$stmt_upd->bind_param("ii", $novoEstado, $id);
$stmt_upd->execute();
$stmt_upd->close();
mysqli_close($db);

$titulo = $novoEstado === 1 ? 'Conta ativada!' : 'Conta desativada!';
$icone  = $novoEstado === 1 ? 'success' : 'warning';
resposta($titulo, $icone, $utiliz_url);
?>
</body>
</html>
