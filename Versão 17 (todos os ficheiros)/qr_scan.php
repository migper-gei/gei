<?php
// ============================================================
// qr_scan.php — GEI
// Ponto de entrada único para QR Codes afixados nos equipamentos.
//
// Comportamento:
//   • Administrador (tipo 1) → ficha_equipamento.php (só leitura)
//   • Reparador     (tipo 3) → ficha_equipamento.php (só leitura)
//   • Todos os outros        → qr_acesso.php (página intermédia:
//                              código de acesso ou registo de avaria)
//
// URL gerada pelo QR Code:
//   qr_scan.php?eq=ID&sala=ID_SALA&esc=ID_ESC&cod=CODIGO_BD
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

include_once('svrurl.php');

// ── Parâmetros do QR Code ─────────────────────────────────────────────────────
$id_equip = isset($_GET['eq'])   ? (int)$_GET['eq']   : 0;
$id_sala  = isset($_GET['sala']) ? (int)$_GET['sala']  : 0;
$id_esc   = isset($_GET['esc'])  ? (int)$_GET['esc']   : 0;
$_codigo  = isset($_GET['cod'])  ? (int)$_GET['cod']   : 0;

if ($id_equip <= 0 || $_codigo <= 0) {
    die('QR Code inválido — parâmetros em falta. Por favor use o QR Code afixado no equipamento.');
}

// ── Verificar sessão GEI ──────────────────────────────────────────────────────
$autenticado = !empty($_SESSION['login_user']);
$tipo_user   = $autenticado ? (int)($_SESSION['tipo'] ?? 0) : 0;

// ── Routing ───────────────────────────────────────────────────────────────────

// Administrador (1) ou Reparador (3) com sessão → ficha de dados (só leitura)
if ($autenticado && ($tipo_user === 1 || $tipo_user === 3)) {

    $url = SVRURL . 'ficha_equipamento.php'
         . '?ide=' . base64_encode($id_equip)
         . '&&sai=' . base64_encode($id_sala)
         . '&&ies=' . base64_encode($id_esc);

    header('Location: ' . $url);
    exit();
}

// Todos os outros (sem sessão ou tipo diferente) → página intermédia
$url = SVRURL . 'qr_acesso.php'
     . '?eq='   . $id_equip
     . '&sala='  . $id_sala
     . '&esc='   . $id_esc
     . '&cod='   . $_codigo;

header('Location: ' . $url);
exit();
