<?php
/**
 * GEI - Verificação de timeout de sessão
 * Incluído no topo de páginas protegidas após header.php
 */

// Função de redirect que usa JS como fallback quando headers já foram enviados
if (!function_exists('gei_redirect')) {
    function gei_redirect($url) {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        } else {
            echo '<script>window.location.href=' . json_encode($url) . ';</script>';
            exit;
        }
    }
}

// Timeout configurável via BD, default 90 min
$timeout = 5400;

if (isset($db)) {
    $r = mysqli_query($db, "SELECT sessao_timeout FROM settings LIMIT 1");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $timeout = (int)($row['sessao_timeout'] ?? 5400);
    }
}

// Verificar se sessão existe
if (!isset($_SESSION['login_user'])) {
    gei_redirect(SVRURL . 'l');
}

// Verificar timeout
if (isset($_SESSION['lastaccess'])) {
    $duration = time() - (int)$_SESSION['lastaccess'];
    if ($duration > $timeout) {
        $id = (int)($_SESSION['user_id'] ?? 0);
        if ($id > 0 && isset($db)) {
            $stmt = $db->prepare("UPDATE utilizadores SET sessao_ativa=0 WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        session_unset();
        session_destroy();
        gei_redirect(SVRURL . 'i?timeout=1');
    }
}
$_SESSION['lastaccess'] = time();

// ── Aviso de timeout no frontend ─────────────────────────────────────────────
if (!defined('GEI_SESSION_WARNING_LOADED')) {
    define('GEI_SESSION_WARNING_LOADED', true);
    $svrurl_js = defined('SVRURL') ? SVRURL : '/';
    echo '<script>'
        . 'var GEI_SESSION_TIMEOUT=' . (int)$timeout . ';'
        . 'var GEI_SVRURL=' . json_encode($svrurl_js) . ';'
        . '</script>';
    echo '<script src="' . htmlspecialchars($svrurl_js) . 'js/session-warning.js" defer></script>';
}

// Verificar User-Agent para prevenir session hijacking
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
} elseif ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
    session_unset();
    session_destroy();
    gei_redirect(SVRURL . 'l');
}

// ── 2FA Setup Guard ──────────────────────────────────────────────────────────
// Reservado para implementação futura de 2FA obrigatório para administradores.
// Quando o módulo 2FA for criado (ativar_2fa.php, verificar_2fa.php),
// descomentar e adaptar o bloco abaixo.
//
// if (
//     (int)($_SESSION['tipo'] ?? 0) === 1 &&
//     !empty($_SESSION['2fa_setup_obrigatorio'])
// ) {
//     $paginaAtual = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
//     if (defined('SVRURL')) {
//         $base = trim(parse_url(SVRURL, PHP_URL_PATH), '/');
//         if ($base !== '' && strpos($paginaAtual, $base) === 0) {
//             $paginaAtual = trim(substr($paginaAtual, strlen($base)), '/');
//         }
//     }
//     $permitidas = ['ativar_2fa.php', 'logout', 'l'];
//     $permitido  = false;
//     foreach ($permitidas as $p) {
//         if ($paginaAtual === $p || strpos($paginaAtual, $p) === 0) {
//             $permitido = true;
//             break;
//         }
//     }
//     if (!$permitido) {
//         gei_redirect(SVRURL . 'ativar_2fa.php');
//     }
// }
// ── fim 2FA Setup Guard ──────────────────────────────────────────────────────
?>
