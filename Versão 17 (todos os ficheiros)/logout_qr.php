<?php
// logout_qr.php — GEI
// Termina a sessão temporária criada pelo acesso via QR Code (PIN).
// Chamado ao clicar "Voltar ao QR" na ficha_equipamento.php.

if (session_status() === PHP_SESSION_NONE) {
    session_name('gei_session');
    session_start();
}

// Só destruir se for mesmo uma sessão QR temporária
if (!empty($_SESSION['qr_temp'])) {
    session_unset();
    session_destroy();
}

// Resposta vazia (é chamado via fetch)
http_response_code(204);
