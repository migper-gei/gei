<?php

if (isset($_SESSION['nobd']) && isset($_SESSION['serverbd'])
    && !empty($_SESSION['nobd']) && !empty($_SESSION['serverbd']))
{
    $nobd     = $_SESSION['nobd'];
    $serverbd = $_SESSION['serverbd'];
}
else
{
    // Sessão sem dados de BD — redirecionar para login
    ?>
    <script>
        window.setTimeout(function() {
            window.location.href = '<?php echo SVRURL ?>i';
        }, 10);
    </script>
    <?php
    // CORRECÇÃO: terminar execução para evitar erros fatais com variáveis indefinidas
    exit();
}

include ("config_serverbd.php");

defined('DB_SERVER')   || define('DB_SERVER',   $serverbd);
defined('DB_DATABASE') || define('DB_DATABASE', $nobd);

// Configurar reporte de erros mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli($serverbd, DB_USERNAME, DB_PASSWORD, $nobd);
    $db->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log('Erro BD: ' . $e->getMessage());
    die('Erro ao ligar à base de dados. Contacte o administrador.');
}

$db->select_db($nobd);

if (mysqli_connect_errno()) {
    printf("Falha na ligação à base de dados!: %s\n", mysqli_connect_error());
    exit();
}
?>
