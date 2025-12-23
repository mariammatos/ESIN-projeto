<?php
session_start();
require_once 'db_connect.php';
require_once 'database/destinos.php';

$pdo = getDatabaseConnection();

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



$destino_id = getDestinoId($pais, $local_input);

if (!$destino_id) {
    $destino_id = insertdestino($pais, $local_input);
}


// -------------------
// 4. ISTO AINDA NÃO ESTA ACABADO EU PAREI AQUI MAS O POST AINDA NÃO FUNCIONA
// -------------------
$stmt = $pdo->prepare("INSERT INTO TraveJournals DEFAULT VALUES");
$stmt->execute();
$travel_journal_id = $pdo->lastInsertId();

// -------------------
// 5. Criar Viagem
// -------------------
$stmt = $pdo->prepare("INSERT INTO Viagens (titulo, data_ida, data_volta, utilizador, destino, travel_journal) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$titulo, $data_ida, $data_volta, $utilizador, $destino_id, $travel_journal_id]);

$_SESSION['success'] = "Viagem criada com sucesso!";
header("Location: ../nova_viagem.php");
exit;
