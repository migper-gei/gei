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
<?php include("head.php"); ?>
</head>

<body class="main-layout">
<?php include("loader.php"); ?>
<?php include("header.php"); ?>
<?php include("sessao_timeout.php"); ?>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav style="margin-bottom:10px;">
                    <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                        <li style="display:flex;align-items:center;gap:4px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                            <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamento</a>
                        </li>
                        <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                        <li style="color:#1e2a45;">Inserção em Massa</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                <br>

<?php

// ── 1. Validações de entrada ──────────────────────────────────────────────────

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_equip(); exit;
}

// CSRF
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    fatal('Token de segurança inválido. Recarregue a página e tente novamente.');
}

// Escola
$sql_maxesc = $db->prepare("SELECT MAX(id) FROM escolas");
$sql_maxesc->execute();
$maxesc = $sql_maxesc->get_result()->fetch_row()[0];

$idescola = (int)base64_decode($_GET['ies'] ?? '');
if ($idescola < 1 || $idescola > $maxesc) {
    redirect_equip(); exit;
}

// Campos comuns obrigatórios
$id_sala      = (int)($_POST['sala']          ?? 0);
$tipo         = trim($_POST['tipoeq']          ?? '');
$marca_global = trim($_POST['marcamod_global'] ?? '');

// Campos técnicos globais (opcionais)
$_cpu    = trim($_POST['cpu']            ?? '');
$_ram    = trim($_POST['ram']            ?? '');
$_disco  = trim($_POST['disco']          ?? '');
$_mon    = trim($_POST['monitor']        ?? '');
$_tec    = trim($_POST['teclado']        ?? '');
$_ti     = trim($_POST['tecladointerface'] ?? '');
$_rato   = trim($_POST['rato']           ?? '');
$_ri     = trim($_POST['ratointerface']  ?? '');

if ($id_sala < 1 || $tipo === '') {
    fatal('Sala ou tipo de equipamento em falta.');
}

// Verificar que a sala pertence à escola
$sql_sala = $db->prepare("SELECT COUNT(*) FROM salas WHERE id=? AND id_escola=?");
$sql_sala->bind_param("ii", $id_sala, $idescola);
$sql_sala->execute();
if ($sql_sala->get_result()->fetch_row()[0] === 0) {
    fatal('Sala inválida para esta escola.');
}

// Arrays dos equipamentos
$nomes      = $_POST['nomeq']       ?? [];
$series     = $_POST['nserie']      ?? [];
$datas      = $_POST['datacompra']  ?? [];
$obs_arr    = $_POST['obs']         ?? [];

if (empty($nomes)) {
    fatal('Nenhum equipamento para inserir.');
}

// ── 2. Validar e sanitizar cada linha ────────────────────────────────────────

$equipamentos = [];
$nomes_vistos = [];
$erros        = [];

foreach ($nomes as $i => $nome_raw) {
    $nome = trim($nome_raw);
    $idx  = $i + 1;

    if ($nome === '') {
        $erros[] = "Linha $idx: nome em branco.";
        continue;
    }
    $nome_lower = mb_strtolower($nome);
    if (isset($nomes_vistos[$nome_lower])) {
        $erros[] = "Linha $idx: nome \"$nome\" duplicado no formulário.";
        continue;
    }
    $nomes_vistos[$nome_lower] = true;

    $equipamentos[] = [
        'nome'  => $nome,
        'serie' => trim($series[$i]    ?? ''),
        'data'  => trim($datas[$i]     ?? ''),
        'obs'   => trim($obs_arr[$i]   ?? ''),
    ];
}

if (!empty($erros)) {
    fatal(implode('<br>', $erros));
}

// ── 3. Verificar duplicados na base de dados (em bloco) ───────────────────────

$placeholders = implode(',', array_fill(0, count($equipamentos), '?'));
$tipos_str    = str_repeat('s', count($equipamentos));
$nomes_param  = array_column($equipamentos, 'nome');

$sql_dup = $db->prepare(
    "SELECT nomeequi FROM equipamento
     WHERE id_sala = ? AND nomeequi IN ($placeholders)"
);
$params = array_merge([$id_sala], $nomes_param);
$tipos_bind = 'i' . $tipos_str;
$sql_dup->bind_param($tipos_bind, ...$params);
$sql_dup->execute();
$res_dup = $sql_dup->get_result();

$duplicados = [];
while ($row = $res_dup->fetch_row()) {
    $duplicados[] = $row[0];
}

if (!empty($duplicados)) {
    $lista = implode(', ', array_map('htmlspecialchars', $duplicados));
    fatal("Os seguintes equipamentos já existem nesta sala:<br><strong>$lista</strong><br>Corrija os nomes e tente novamente.");
}

// ── 4. Inserção em bloco (prepared statement reutilizado) ─────────────────────

// Prepared statements reutilizáveis — com e sem data
$stmt_sem_data = $db->prepare(
    "INSERT INTO equipamento
        (nomeequi, id_sala, tipo, marca_modelo, numserie, observacoes, escola_digital,
         processador, memoria, disco, monitor, teclado, tecladointerface, rato, ratointerface)
     VALUES (?, ?, ?, ?, ?, ?, 'Não', ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt_com_data = $db->prepare(
    "INSERT INTO equipamento
        (nomeequi, id_sala, tipo, marca_modelo, numserie, data_compra, observacoes, escola_digital,
         processador, memoria, disco, monitor, teclado, tecladointerface, rato, ratointerface)
     VALUES (?, ?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, 'Não', ?, ?, ?, ?, ?, ?, ?, ?)"
);

$db->begin_transaction();
$inseridos = 0;

try {
    foreach ($equipamentos as $eq) {
        $data_val = ($eq['data'] !== '') ? $eq['data'] : null;

        if ($data_val !== null) {
            $stmt_com_data->bind_param("sisssssssssssss",
                $eq['nome'], $id_sala, $tipo, $marca_global,
                $eq['serie'], $data_val, $eq['obs'],
                $_cpu, $_ram, $_disco, $_mon, $_tec, $_ti, $_rato, $_ri
            );
            $stmt_com_data->execute();
        } else {
            $stmt_sem_data->bind_param("sissssssssssss",
                $eq['nome'], $id_sala, $tipo, $marca_global,
                $eq['serie'], $eq['obs'],
                $_cpu, $_ram, $_disco, $_mon, $_tec, $_ti, $_rato, $_ri
            );
            $stmt_sem_data->execute();
        }
        $inseridos++;
    }
    $db->commit();
} catch (Exception $ex) {
    $db->rollback();
    fatal('Erro ao guardar os dados: ' . htmlspecialchars($ex->getMessage()));
}

$stmt_sem_data->close();
$stmt_com_data->close();
mysqli_close($db);

// Regenerar token CSRF após submissão bem-sucedida
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>

<script>
swal({
    title: '<?php echo $inseridos; ?> equipamento<?php echo $inseridos !== 1 ? "s" : ""; ?> inserido<?php echo $inseridos !== 1 ? "s" : ""; ?>!',
    text: 'Todos os dados foram guardados com sucesso.',
    icon: 'success',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>equip";
});
</script>

<?php

// ── Funções auxiliares ────────────────────────────────────────────────────────

function fatal($msg) {
    echo '<script>
        swal({
            title: "Erro",
            content: (function(){ var d=document.createElement("div"); d.innerHTML="' . addslashes($msg) . '"; return d; })(),
            icon: "error"
        }).then(function(){ window.history.back(); });
    </script>';
    exit;
}

function redirect_equip() {
    echo '<script>window.location.href="' . SVRURL . 'equip";</script>';
    exit;
}

?>

<br><br><br><br><br><br>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
