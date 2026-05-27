<?php





/**
 * GEI - Logout seguro
 */
// Iniciar sessão com o mesmo nome usado na app
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

// Atualizar sessão_ativa na BD (se possível)
if (isset($_SESSION['user_id'])) {
    include __DIR__ . '/config.php';


//Auditoria-logs
require_once('gei_audit.php');
gei_audit($db, 'logout', 'sessao');




    if (isset($db)) {
        $stmt = $db->prepare("UPDATE utilizadores SET sessao_ativa='0' WHERE id=?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        mysqli_close($db);
    }
}






// Destruir sessão de forma segura
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Limpar cookie de token secundário (gei_sec)
setcookie('gei_sec', '', time() - 42000, '/', '', $isHttps, true);

//header('Location: ' . (defined('SVRURL') ? SVRURL : '/gei/') . 'l');

header("location: i");

exit;
?>
