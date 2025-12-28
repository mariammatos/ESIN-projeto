<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/alojamentos.php';

$db = getDatabaseConnection();
$atividade = $_POST['atividade'] ?? '';
$alojamento = $_POST['alojamento'] ?? '';   
$viagem_id = $_POST['viagem_id'] ?? 0;


if (!empty($atividade)) {

    $nome = trim($_POST['nome']);
    $localizacao = trim($_POST['localizacao']);
    $tipo_atividade = $_POST['tipo_atividade'];
    $data = $_POST['data_inicio'];

    if (!$nome || !$localizacao || !$tipo_atividade || !$data) {
        $_SESSION['error'] = "Todos os campos da atividade são obrigatórios.";
        header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
        exit;
    }

    // 1️⃣ criar detalhes base
    $detalhes_id = insertDetalhes($db, $nome, $localizacao);

    // 2️⃣ ligar como atividade
    insertDetalhesAtividade($db, $detalhes_id, $tipo_atividade);

    // 3️⃣ criar atividade
    insertAtividade($db, $viagem_id, $detalhes_id, $data);

    $_SESSION['success'] = "Atividade adicionada com sucesso!";
    // REDIRECIONAR para a página da viagem
    header("Location: ../viagem.php?id=" . $viagem_id);

}else {


// Se o usuário escolheu um alojamento existente
if (!empty($_POST['detalhe_id'])) {
    $detalhe_id = $_POST['detalhe_id'];
} else {
    // Novo alojamento
    $nome = trim($_POST['nome']);
    $localizacao = trim($_POST['localizacao']);
    $tipo_alojamento = $_POST['tipo'];

    if (!$nome || !$localizacao || !$tipo_alojamento) {
        $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
        header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
        exit;
    }

    // Criar novo detalhe de alojamento
    $detalhe_id = insertDetalheAlojamento($db, $nome, $localizacao, $tipo_alojamento);
}

// Datas do alojamento
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'] ?? null;

// Inserir o alojamento associado à viagem
insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim);

$_SESSION['success'] = "Alojamento adicionado com sucesso!";
header("Location: ../viagem.php?id=" . $viagem_id);
exit;

if (empty($viagem_id)) {
    die('ERRO: viagem_id não recebido');
}
}
// REDIRECIONAR para a página da viagem

?>
