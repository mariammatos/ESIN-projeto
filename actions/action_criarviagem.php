<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/destinos.php';
require_once '../database/posts.php';
require_once '../database/alojamentos.php';

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
$editar = isset($_POST['editar']) ? true : false;
$viagem_id_editar = $_POST['viagem_id'] ?? null;


if ($editar) {
    if (!$titulo || !$data_ida) {
        $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
        header("Location: ../editar_viagem.php");
        exit;
    }
    if ($data_volta && strtotime($data_volta) <= strtotime($data_ida)) {
        $_SESSION['error'] = "A data de fim deve ser posterior à data de início.";
        header("Location: ../editar_viagem.php");
        exit;
    }
    editarviagem($dbh, $titulo, $data_ida, $data_volta, $viagem_id_editar);

    $_SESSION['success'] = "Viagem criada com sucesso!";
    header("Location: /viagem.php?id=" . $viagem_id_editar);
    exit;
}
else {

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
}


// --- DADOS DO ALOJAMENTO ---
$nome_alojamento = trim($_POST['nome_alojamento'] ?? '');
$localizacao_alojamento = trim($_POST['localizacao_alojamento'] ?? '');
$tipo_alojamento = $_POST['tipo_alojamento'] ?? '';
$data_inicio_alojamento = $_POST['data_inicio_alojamento'] ?? '';
$data_fim_alojamento = $_POST['data_fim_alojamento'] ?? null;
$rating = $_POST['rating'] ?? null;
$comentario = trim($_POST['comentario'] ?? null);

// Se o utilizador preencheu os campos do alojamento
if ($nome_alojamento && $localizacao_alojamento && $tipo_alojamento && $data_inicio_alojamento) {

    // Verifica se o alojamento já existe na plataforma
    $detalhe_id = getDetalheAlojamento($dbh, $nome_alojamento, $localizacao_alojamento);

    if (!$detalhe_id) {
        // Cria novo detalhe de alojamento
        $detalhe_id = insertDetalheAlojamento($dbh, $nome_alojamento, $localizacao_alojamento, $tipo_alojamento);
    }

    // Inserir o alojamento na viagem
    $alojamento_id = insertAlojamento($dbh, $viagem_id, $detalhe_id, $data_inicio_alojamento, $data_fim_alojamento);

    // Adicionar feedback inicial (opcional)
    if ($rating !== null && $rating !== '') {
        adicionarFeedbackAlojamento($dbh, $alojamento_id, $rating, $comentario);
    }
}

?>