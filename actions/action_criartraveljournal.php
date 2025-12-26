<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/destinos.php';
require_once '../database/traveljournals.php';

$dbh = getDatabaseConnection();
$viagem_id = $_POST['viagem_id'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');
$avaliacao = floatval($_POST['avaliacao'] ?? 0);
$editar = isset($_POST['editar']) ? true : false;

if (!$viagem_id || !$descricao || $avaliacao < 0 || $avaliacao > 5) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header("Location: ../adicionartraveljournal.php");
    exit;
}

if ($editar) {
    $traveljournal_id = getTravelJournalId($dbh, $viagem_id);
    if (!$traveljournal_id) {
        $_SESSION['error'] = "Travel Journal não encontrado para edição.";
        header("Location: ../adicionartraveljournal.php");
        exit;
    };

    editarTravelJournal($dbh, $viagem_id, $descricao, $avaliacao);

    unset($_SESSION['form_data']);
    $_SESSION['msg'] = "Travel Journal atualizado com sucesso!";
    header("Location: ../viagem.php?id=$viagem_id");
    exit;
}
else {
    $traveljournal_id = insertTravelJournal($dbh, $viagem_id, $descricao, $avaliacao);

    unset($_SESSION['form_data']);
    $_SESSION['msg'] = "Travel Journal '$titulo' criado com sucesso!";
    header("Location: ../viagem.php?id=$viagem_id");
    exit;
}





?>