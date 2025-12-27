<?php
// --- Funções de Alojamentos ---

// Obter todos os alojamentos de uma viagem, incluindo média de avaliação
function getAlojamentosViagem($db, $viagem_id) {
    $stmt = $db->prepare(
        'SELECT 
            A.id AS alojamento_id,
            A.data_inicio,
            A.data_fim,
            D.id AS detalhe_id,
            D.nome AS nome_alojamento,
            D.localizacao,
            DA.tipo AS tipo_alojamento,
            AVG(F.rating) AS media_avaliacao
        FROM Alojamento A
        JOIN Detalhes_alojamento DA ON A.detalhes = DA.id
        JOIN Detalhes D ON DA.id = D.id
        LEFT JOIN Feedback_alojamento FA ON FA.alojamento = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE A.viagem = :viagem_id
        GROUP BY A.id'
    );
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Pesquisa global de alojamentos por nome ou localização
function procurarAlojamentosGlobais(PDO $db, string $termo): array {
    $termo = '%' . $termo . '%';

    $stmt = $db->prepare("
        SELECT 
            D.id AS detalhe_id,
            D.nome AS nome_alojamento,
            DA.tipo AS tipo_alojamento,
            D.localizacao,
            AVG(F.rating) AS media_avaliacao
        FROM Detalhes D
        JOIN Detalhes_alojamento DA ON DA.id = D.id
        LEFT JOIN Alojamento A ON A.detalhes = D.id
        LEFT JOIN Feedback_alojamento FA ON FA.alojamento = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE D.nome LIKE :termo
           OR D.localizacao LIKE :termo
        GROUP BY D.id
        ORDER BY media_avaliacao DESC
    ");

    $stmt->execute(['termo' => $termo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// Inserir um novo detalhe de alojamento
function insertDetalheAlojamento($db, $nome, $localizacao, $tipo) {
    $stmt = $db->prepare('INSERT INTO Detalhes (nome, localizacao) VALUES (?, ?)');
    $stmt->execute([$nome, $localizacao]);
    $detalhe_id = $db->lastInsertId();

    $stmt2 = $db->prepare('INSERT INTO Detalhes_alojamento (id, tipo) VALUES (?, ?)');
    $stmt2->execute([$detalhe_id, $tipo]);

    return $detalhe_id;
}

// Inserir um alojamento associado a uma viagem
function insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim = null) {
    $stmt = $db->prepare('INSERT INTO Alojamento (data_inicio, data_fim, viagem, detalhes) VALUES (?, ?, ?, ?)');
    $stmt->execute([$data_inicio, $data_fim, $viagem_id, $detalhe_id]);
    return $db->lastInsertId();
}

// Adicionar feedback a um alojamento
function adicionarFeedbackAlojamento($db, $alojamento_id, $rating, $comentario = null, $precos = null) {
    // Primeiro inserir o feedback
    $stmt = $db->prepare('INSERT INTO Feedback (rating, comentario, precos) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $comentario, $precos]);
    $feedback_id = $db->lastInsertId();

    // Depois associar ao alojamento
    $stmt2 = $db->prepare('INSERT INTO Feedback_alojamento (id, alojamento) VALUES (?, ?)');
    $stmt2->execute([$feedback_id, $alojamento_id]);

    return $feedback_id;
}

// Buscar detalhes completos do alojamento, rating global e viagem associada
function getDetalhesAlojamentoCompleto($db, $alojamento_id) {
    $stmt = $db->prepare('
        SELECT 
            A.id AS alojamento_id,
            A.viagem AS viagem_id,
            D.nome AS nome_alojamento,
            D.localizacao,
            DA.tipo AS tipo_alojamento,
            AVG(F.rating) AS media_avaliacao
        FROM Alojamento A
        JOIN Detalhes_alojamento DA ON A.detalhes = DA.id
        JOIN Detalhes D ON DA.id = D.id
        LEFT JOIN Feedback_alojamento FA ON FA.alojamento = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE A.id = :alojamento_id
        GROUP BY A.id
    ');
    $stmt->bindParam(':alojamento_id', $alojamento_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar todos os feedbacks de um alojamento
function getFeedbacksAlojamento($db, $alojamento_id) {
    $stmt = $db->prepare('
        SELECT F.rating, F.comentario, F.precos
        FROM Feedback_alojamento FA
        JOIN Feedback F ON F.id = FA.id
        WHERE FA.alojamento = :alojamento_id
        ORDER BY F.id DESC
    ');
    $stmt->bindParam(':alojamento_id', $alojamento_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function procurarAlojamentosPorDestino($db, $destino_id, $termo) {
    $termo = '%' . mb_strtolower($termo, 'UTF-8') . '%';
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);

    $stmt = $db->prepare('
        SELECT D.id, D.nome, D.localizacao, DA.tipo
        FROM Detalhes D
        JOIN Detalhes_alojamento DA ON D.id = DA.id
        JOIN Alojamento A ON A.detalhes = D.id
        WHERE A.viagem IN (SELECT id FROM Viagens WHERE destino = ?)
        AND LOWER(removeacentos(D.nome)) LIKE ?
        GROUP BY D.id
    ');
    $stmt->execute([$destino_id, $termo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
