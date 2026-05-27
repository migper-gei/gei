<?php
/**
 * GEI Chat — Ligação à base de dados e funções auxiliares
 * Prepared statements em todas as queries. Sem credenciais hardcoded.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name('gei_session');
    session_start();
}

if (!isset($_SESSION['nobd']) || !isset($_SESSION['serverbd'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão inválida']);
    exit;
}

if (!isset($_SESSION['login_user'])) {
    http_response_code(401);
    exit;
}

$nobd     = $_SESSION['nobd'];
$serverbd = $_SESSION['serverbd'];

// Carregar .env se existir (não obrigatório em produção com env vars do sistema)
$_gei_envFile = dirname(__DIR__) . '/.env';
if (file_exists($_gei_envFile)) {
    foreach (file($_gei_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_gei_line) {
        if (str_starts_with(trim($_gei_line), '#') || !str_contains($_gei_line, '=')) continue;
        [$_gei_k, $_gei_v] = explode('=', $_gei_line, 2);
        // Só definir se ainda não estiver definida no ambiente do sistema
        if (!isset($_ENV[trim($_gei_k)]) && getenv(trim($_gei_k)) === false) {
            $_ENV[trim($_gei_k)] = trim($_gei_v, " '\"");
        }
    }
    unset($_gei_line, $_gei_k, $_gei_v);
}
unset($_gei_envFile);

// Função auxiliar para ler variável de ambiente (sem fallback hardcoded)
if (!function_exists('_gei_env')) {
    function _gei_env(string $key): string {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            throw new \RuntimeException("Variável de ambiente obrigatória não definida: {$key}");
        }
        return (string) $value;
    }
}

try {
    $connect = new mysqli($serverbd, _gei_env('DB_USER'), _gei_env('DB_PASS'), $nobd);
    if ($connect->connect_error) {
        throw new Exception($connect->connect_error);
    }
    mysqli_set_charset($connect, "utf8mb4");
} catch (Exception $e) {
    error_log('[GEI Chat] DB error: ' . $e->getMessage());
    http_response_code(500);
    exit;
}

date_default_timezone_set('Europe/Lisbon');

/* ─────────────────────────────────────────────────────────────────
   fetch_user_chat_history()
   Devolve o HTML do histórico de mensagens entre dois utilizadores,
   com estilo GEI aplicado inline (compatível com jQuery UI dialog).
   ───────────────────────────────────────────────────────────────── */
function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
    $from_user_id = (int)$from_user_id;
    $to_user_id   = (int)$to_user_id;

    $stmt = $connect->prepare(
        "SELECT * FROM chat_message
         WHERE (from_user_id=? AND to_user_id=?)
            OR (from_user_id=? AND to_user_id=?)
         ORDER BY timestamp DESC"
    );
    $stmt->bind_param("iiii", $from_user_id, $to_user_id, $to_user_id, $from_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cores GEI aplicadas inline para funcionar dentro do dialog jQuery UI
    $style_mine   = 'background-color:#eaf3fb;border-left:3px solid #00509e;padding:9px 13px;border-bottom:1px solid #cfd6de;';
    $style_theirs = 'background-color:#ffffff;border-left:3px solid #e87722;padding:9px 13px;border-bottom:1px solid #cfd6de;';
    $style_ts     = 'color:#5a6370;font-size:10.5px;';

    $output = '<ul class="list-unstyled" style="margin:0;padding:0;">';

    foreach ($result as $row) {
        $msg = htmlspecialchars($row['chat_message'] ?? '', ENT_QUOTES, 'UTF-8');
        $ts  = htmlspecialchars($row['timestamp']    ?? '', ENT_QUOTES, 'UTF-8');
        $mid = (int)$row['chat_message_id'];

        if ($row['from_user_id'] == $from_user_id) {
            // Mensagem própria
            if ($row['status'] == '2') {
                $label = '<b style="color:#00509e;font-weight:700;">Tu</b>';
                $msg   = '<em>A mensagem foi apagada</em>';
            } else {
                $label  = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="' . $mid . '" '
                        . 'style="background:transparent;border:1px solid #fca5a5;color:#ef4444;border-radius:4px;font-size:10px;padding:1px 6px;font-weight:700;margin-right:4px;">'
                        . 'x</button>';
                $label .= '<b style="color:#00509e;font-weight:700;">Tu</b>';
            }
            $style = $style_mine;
        } else {
            // Mensagem do outro utilizador
            $uname = htmlspecialchars(get_user_name((int)$row['from_user_id'], $connect), ENT_QUOTES, 'UTF-8');
            $label = '<b style="color:#e87722;font-weight:700;">' . $uname . '</b>';
            if ($row['status'] == '2') $msg = '<em>A mensagem foi apagada</em>';
            $style = $style_theirs;
        }

        $output .= '<li style="' . $style . '">';
        $output .= '<p style="margin:0;font-size:13px;line-height:1.5;">' . $label . ' — ' . $msg;
        $output .= '<div style="text-align:right;"><small><em style="' . $style_ts . '">' . $ts . '</em></small></div>';
        $output .= '</p></li>';
    }

    $output .= '</ul>';

    // Marcar como lidas
    $upd = $connect->prepare(
        "UPDATE chat_message SET status='0'
         WHERE from_user_id=? AND to_user_id=? AND status='1'"
    );
    $upd->bind_param("ii", $to_user_id, $from_user_id);
    $upd->execute();

    return $output;
}

/* ─────────────────────────────────────────────────────────────────
   get_user_name()
   ───────────────────────────────────────────────────────────────── */
function get_user_name($user_id, $connect)
{
    $user_id = (int)$user_id;
    $stmt = $connect->prepare("SELECT nome FROM utilizadores WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    return $row ? $row[0] : 'Desconhecido';
}

/* ─────────────────────────────────────────────────────────────────
   count_unseen_message()
   Devolve badge HTML laranja (GEI) se houver mensagens não lidas.
   ───────────────────────────────────────────────────────────────── */
function count_unseen_message($from_user_id, $to_user_id, $connect)
{
    $from_user_id = (int)$from_user_id;
    $to_user_id   = (int)$to_user_id;
    $stmt = $connect->prepare(
        "SELECT COUNT(*) FROM chat_message
         WHERE from_user_id=? AND to_user_id=? AND status='1'"
    );
    $stmt->bind_param("ii", $from_user_id, $to_user_id);
    $stmt->execute();
    $count = (int)$stmt->get_result()->fetch_row()[0];

    if ($count > 0) {
        return '<span class="label label-success" '
             . 'style="background:#e87722;border-radius:10px;padding:2px 7px;font-size:10.5px;font-weight:700;margin-left:6px;">'
             . $count . '</span>';
    }
    return '';
}

/* ─────────────────────────────────────────────────────────────────
   fetch_group_chat_history()
   ───────────────────────────────────────────────────────────────── */
function fetch_group_chat_history($connect)
{
    $stmt = $connect->prepare(
        "SELECT * FROM chat_message WHERE to_user_id=0 ORDER BY timestamp DESC"
    );
    $stmt->execute();
    $result = $stmt->get_result();

    $myId = (int)($_SESSION['user_id'] ?? 0);

    $style_mine   = 'background-color:#eaf3fb;border-left:3px solid #00509e;padding:9px 13px;border-bottom:1px solid #cfd6de;';
    $style_theirs = 'background-color:#ffffff;border-left:3px solid #e87722;padding:9px 13px;border-bottom:1px solid #cfd6de;';
    $style_ts     = 'color:#5a6370;font-size:10.5px;';

    $output = '<ul class="list-unstyled" style="margin:0;padding:0;">';

    foreach ($result as $row) {
        $msg = htmlspecialchars($row['chat_message'] ?? '', ENT_QUOTES, 'UTF-8');
        $ts  = htmlspecialchars($row['timestamp']    ?? '', ENT_QUOTES, 'UTF-8');
        $mid = (int)$row['chat_message_id'];

        if ($row['from_user_id'] == $myId) {
            if ($row['status'] == '2') {
                $label = '<b style="color:#00509e;font-weight:700;">Tu</b>';
                $msg   = '<em>A mensagem foi apagada</em>';
            } else {
                $label  = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="' . $mid . '" '
                        . 'style="background:transparent;border:1px solid #fca5a5;color:#ef4444;border-radius:4px;font-size:10px;padding:1px 6px;font-weight:700;margin-right:4px;">'
                        . 'x</button>';
                $label .= '<b style="color:#00509e;font-weight:700;">Tu</b>';
            }
            $style = $style_mine;
        } else {
            $uname = htmlspecialchars(get_user_name((int)$row['from_user_id'], $connect), ENT_QUOTES, 'UTF-8');
            $label = '<b style="color:#e87722;font-weight:700;">' . $uname . '</b>';
            if ($row['status'] == '2') $msg = '<em>A mensagem foi apagada</em>';
            $style = $style_theirs;
        }

        $output .= '<li style="' . $style . '">';
        $output .= '<p style="margin:0;font-size:13px;line-height:1.5;">' . $label . ' — ' . $msg;
        $output .= '<div style="text-align:right;"><small><em style="' . $style_ts . '">' . $ts . '</em></small></div>';
        $output .= '</p></li>';
    }

    $output .= '</ul>';
    return $output;
}
?>
