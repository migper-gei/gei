

<?php
/**
 * Detecta automaticamente HTTP/HTTPS e host.
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost', ENT_QUOTES, 'UTF-8');
defined('SVRURL') || define('SVRURL', $protocol . '://' . $host . '/gei/');
?>
 