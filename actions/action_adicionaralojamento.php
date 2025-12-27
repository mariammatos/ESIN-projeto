<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/alojamentos.php';

$db = getDatabaseConnection();

$viagem_id = $_POST['viagem_id'] ?? 0;

// Se o usuário escolheu um alojamento existente
if (!empty($_POST['detalhe_id'])) {
    $detalhe_id = $_POST['detalhe_id'];
} else {
    // Novo alojamento
    $nome = trim($_POST['nome']);
    $localizacao = trim($_POST['localizacao']);
    $tipo = $_POST['tipo'];

    if (!$nome || !$localizacao || !$tipo) {
        $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
        header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
        exit;
    }

    // Criar novo detalhe de alojamento
    $detalhe_id = insertDetalheAlojamento($db, $nome, $localizacao, $tipo);
}

// Datas do alojamento
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'] ?? null;

// Inserir o alojamento associado à viagem
insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim);

$_SESSION['success'] = "Alojamento adicionado com sucesso!";

if (empty($viagem_id)) {
    die('ERRO: viagem_id não recebido');
}
// REDIRECIONAR para a página da viagem
header("Location: ../viagem.php?id=$viagem_id");
exit;
?>
