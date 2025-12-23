<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/destinos.php';
require_once '../database/posts.php';

$dbh = getDatabaseConnection();

if ($_SESSION['em_andamento']) {
    $data_volta = null;
} else {
    $data_volta = $_POST['data_volta'] ?? null;
}

$titulo = trim($_POST['titulo'] ?? '');
$data_ida = $_POST['data_ida'] ?? '';
$pais = trim($_POST['pais_selecionado'] ?? '');
$local_input = trim($_POST['local'] ?? '');
$utilizador = $_SESSION['username'] ?? '';

if (!$titulo || !$data_ida || !$pais || !$local_input) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header("Location: ../nova_viagem.php");
    exit;
}

if ($data_volta && strtotime($data_volta) <= strtotime($data_ida)) {
    $_SESSION['error'] = "A data de fim deve ser posterior à data de início.";
    header("Location: ../nova_viagem.php");
    exit;
}



$destino_id = getDestinoId($dbh, $pais, $local_input);

if (!$destino_id) {
    $destino_id = insertdestino($dbh, $pais, $local_input);
}


$viagem_id = insertviagem($dbh, $titulo, $data_ida, $data_volta, $utilizador, $destino_id);

$_SESSION['success'] = "Viagem criada com sucesso!";
header("Location: /viagem.php?id=" . $viagem_id);
exit;