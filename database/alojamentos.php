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
?>
