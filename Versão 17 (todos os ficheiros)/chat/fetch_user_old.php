<?php
include('database_connection.php');
header("Content-type: text/html; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href="index.php";</script>';
    exit;
}

$_uid = (int)($_SESSION['user_id'] ?? 0);
$statement = $connect->prepare(
    "SELECT * FROM utilizadores WHERE id != ? ORDER BY sessao_ativa DESC, nome"
);
$statement->bind_param("i", $_uid);
$statement->execute();
$result = $statement->get_result();

$output  = '<table class="table table-bordered table-striped">';
$output .= '<thead><tr>';
$output .= '<th width="40%">Utilizador</th>';
$output .= '<th width="25%">Estado</th>';
$output .= '<th width="15%">Acção</th>';
$output .= '</tr></thead><tbody>';

foreach ($result as $row) {
    $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');

    $status = ($row['sessao_ativa'] == 1)
        ? '<span class="label label-success">Online</span>'
        : '<span class="label label-danger">Offline</span>';

    $unseen = count_unseen_message($row['id'], $_SESSION['user_id'], $connect);

    $output .= '<tr>';
    $output .= '<td>' . $nome . ' ' . $unseen . '</td>';
    $output .= '<td>' . $status . '</td>';
    $output .= '<td>
        <button type="button"
                class="btn btn-info btn-xs start_chat"
                data-touserid="'  . (int)$row['id'] . '"
                data-tousername="' . $nome . '">
            Iniciar Chat
        </button>
    </td>';
    $output .= '</tr>';
}

$output .= '</tbody></table>';
echo $output;
?>
