<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/alojamentos.php';

$db = getDatabaseConnection();
$atividade = $_POST['atividade'] ?? '';
$alojamento = $_POST['alojamento'] ?? '';   
$viagem_id = $_POST['viagem_id'] ?? 0;


// ... código anterior (verificação de sessão, db connection, etc) ...

if (isset($_POST['atividade'])) {

    if (isset($_POST['detalhe_id'])) {
        $detalhe_id = $_POST['detalhe_id'];
        $data = $_POST['data'];

        if (!$data) {
            $_SESSION['error'] = "A data é obrigatória.";
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }

        insertAtividade($db, $viagem_id, $detalhe_id, $data);
    } 
    
    else {
        $nome = trim($_POST['nome']);
        $localizacao = trim($_POST['localizacao']);
        $tipo_atividade = $_POST['tipo_atividade'];
        $data = $_POST['data'];

        // Validação
        if (empty($nome) || empty($localizacao) || empty($tipo_atividade) || empty($data)) {
            $_SESSION['error'] = "Todos os campos da atividade são obrigatórios.";
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }

        try {
            // 1️⃣ Criar Detalhe + Tipo (usamos a função que faz as duas coisas)
            // Esta função retorna o ID do detalhe criado
            $detalhe_id = insertDetalheAtividade($db, $nome, $localizacao, $tipo_atividade);

            // 2️⃣ Criar a Atividade ligada à viagem
            insertAtividade($db, $viagem_id, $detalhe_id, $data);

        } catch (Exception $e) {
            $_SESSION['error'] = "Erro ao criar atividade: " . $e->getMessage();
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }
    }

    $_SESSION['success'] = "Atividade adicionada com sucesso!";
    header("Location: ../viagem.php?id=" . $viagem_id);
    exit;

} elseif (isset($_POST['alojamento'])) {

    if (isset($_POST['detalhe_id'])) {
        $detalhe_id = $_POST['detalhe_id'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'] ?? null; // Data fim pode vir vazia

        if (!$data_inicio) {
            $_SESSION['error'] = "A data de início é obrigatória.";
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }

        // Inserir a ligação com o alojamento existente
        insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim);
    } 
    
    else {
        $nome = trim($_POST['nome']);
        $localizacao = trim($_POST['localizacao']);
        // Nota: No teu HTML de alojamento o select chamava-se "tipo" e não "tipo_alojamento"
        $tipo_alojamento = $_POST['tipo']; 
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'] ?? null;

        // Validação (Data fim não é obrigatória para validação, mas as outras são)
        if (empty($nome) || empty($localizacao) || empty($tipo_alojamento) || empty($data_inicio)) {
            $_SESSION['error'] = "Nome, localização, tipo e data de início são obrigatórios.";
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }

        try {
            // 1️⃣ Criar Detalhe + Tipo de Alojamento
            // (Esta função cria na tabela Detalhes e na Detalhes_alojamento)
            $detalhe_id = insertDetalheAlojamento($db, $nome, $localizacao, $tipo_alojamento);

            // 2️⃣ Criar o Alojamento ligado à viagem
            insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim);

        } catch (Exception $e) {
            $_SESSION['error'] = "Erro ao criar alojamento: " . $e->getMessage();
            header("Location: ../novo_alojamento.php?viagem_id=$viagem_id");
            exit;
        }
    }

    $_SESSION['success'] = "Alojamento adicionado com sucesso!";
    header("Location: ../viagem.php?id=" . $viagem_id);
    exit;
}

?>
