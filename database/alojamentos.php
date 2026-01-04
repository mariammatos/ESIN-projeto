<?php
include_once 'destinos.php';

// retorna todos os alojamentos da viagem e suas informações
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
// retorna os alojamentos de acordo com a pesquisa 
function procurarAlojamentosGlobais(PDO $db, string $termo): array {
    $termo = '%' . $termo . '%';
    $termo_normalizado = normalize_string($termo);
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);
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
        WHERE LOWER(removeacentos(D.nome)) LIKE :termo
           OR LOWER(removeacentos(D.localizacao)) LIKE :termo
        GROUP BY D.id
        ORDER BY media_avaliacao DESC
    ");

    $stmt->execute(['termo' => $termo_normalizado]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// adiciona um novo alojamento (novo hotel/airbnb/etc)
function insertDetalheAlojamento($db, $nome, $localizacao, $tipo_alojamento) {
    $stmtCheck = $db->prepare('SELECT tipo_alojamento FROM Tipo_alojamento WHERE tipo_alojamento = ?');
    $stmtCheck->execute([$tipo_alojamento]);
    if (!$stmtCheck->fetch()) {
        throw new Exception("O tipo de alojamento '$tipo_alojamento' não existe na tabela Tipo_alojamento");
    }

    $stmt = $db->prepare('INSERT INTO Detalhes (nome, localizacao) VALUES (?, ?)');
    $stmt->execute([$nome, $localizacao]);
    $detalhe_id = $db->lastInsertId();
    $stmt2 = $db->prepare('INSERT INTO Detalhes_alojamento (id, tipo) VALUES (?, ?)');
    $stmt2->execute([$detalhe_id, $tipo_alojamento]);

    return $detalhe_id;
}


//adiciona um novo alojamento associado a uma viagem (não precisa de ser um hotel/ect novo)
function insertAlojamento($db, $viagem_id, $detalhe_id, $data_inicio, $data_fim = null) {
    $stmt = $db->prepare('INSERT INTO Alojamento (data_inicio, data_fim, viagem, detalhes) VALUES (?, ?, ?, ?)');
    $stmt->execute([$data_inicio, $data_fim, $viagem_id, $detalhe_id]);
    return $db->lastInsertId();
}

//adiciona feedback a um alojamento
function adicionarFeedbackAlojamento($db, $alojamento_id, $rating, $comentario = null, $precos = null) {
    $stmt = $db->prepare('INSERT INTO Feedback (rating, comentario, precos) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $comentario, $precos]);
    $feedback_id = $db->lastInsertId();

    $stmt2 = $db->prepare('INSERT INTO Feedback_alojamento (id, alojamento) VALUES (?, ?)');
    $stmt2->execute([$feedback_id, $alojamento_id]);

    return $feedback_id;
}

// retorna as informações sobre um alojamento
function getDetalhesAlojamentoCompleto($db, $alojamento_id) {
    $stmt = $db->prepare('
        SELECT 
            A.id AS alojamento_id,
            A.viagem AS viagem_id,
            D.id AS detalhe_id, 
            D.nome AS nome_alojamento,
            D.localizacao,
            DA.tipo AS tipo_alojamento,
            AVG(F.rating) AS media_avaliacao
        FROM Detalhes D
        JOIN Alojamento A ON A.detalhes = D.id
        JOIN Detalhes_alojamento DA ON A.detalhes = DA.id
        LEFT JOIN Feedback_alojamento FA ON FA.alojamento = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE D.id = :alojamento_id
        GROUP BY A.id
    ');
    $stmt->bindParam(':alojamento_id', $alojamento_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// retorna todos os feedbacks de um alojamento
function getFeedbacksAlojamento($db, $detalhe_id) {
    $stmt = $db->prepare('
        SELECT F.rating, F.comentario, F.precos, A.data_inicio
        FROM Feedback F
        JOIN Feedback_alojamento FA ON F.id = FA.id
        JOIN Alojamento A ON FA.alojamento = A.id
        WHERE A.detalhes = :detalhe_id
        ORDER BY F.id DESC
    ');
    $stmt->bindParam(':detalhe_id', $detalhe_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retorna os alojamentos de um destino específico com base na pesquisa
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


#ATIVIDADES

// retorna todas as atividades associadas a uma viagem específica
function getAtividadesViagem($db, $viagem_id) {
    $stmt = $db->prepare(
        'SELECT 
            A.id AS atividade_id,
            A.data AS data_atividade, 
            D.id AS detalhe_id,
            D.nome AS nome_atividade,
            D.localizacao,
            DA.tipo AS tipo_atividade,
            AVG(F.rating) AS media_avaliacao
        FROM Atividade A
        JOIN Detalhes_atividade DA ON A.detalhes = DA.id
        JOIN Detalhes D ON DA.id = D.id
        LEFT JOIN Feedback_atividade FA ON FA.atividade = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE A.viagem = :viagem_id
        GROUP BY A.id
        ORDER BY A.data ASC'
    );
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retorna as atividades de acordo com a pesquisa
function procurarAtividadesGlobais(PDO $db, string $termo): array {
    $termo = '%' . $termo . '%';
    $termo_normalizado = normalize_string($termo);
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);

    $stmt = $db->prepare("
        SELECT 
            D.id AS atividade_id,
            D.nome AS nome_atividade,
            DA.tipo AS tipo_atividade,
            D.localizacao,
            AVG(F.rating) AS media_avaliacao
        FROM Detalhes D
        JOIN Detalhes_atividade DA ON D.id = DA.id
        LEFT JOIN Atividade A ON A.detalhes = D.id
        LEFT JOIN Feedback_atividade FA ON FA.atividade = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE LOWER(removeacentos(D.nome)) LIKE :termo
           OR LOWER(removeacentos(D.localizacao)) LIKE :termo
        GROUP BY D.id

    ");
    $stmt->execute(['termo' => $termo_normalizado]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

# adiciona uma nova atividade (novo local de atividade)
function insertDetalheAtividade($db, $nome, $localizacao, $tipo_atividade) {
    $stmtCheck = $db->prepare('SELECT tipo_atividade FROM Tipo_atividade WHERE tipo_atividade = ?');
    $stmtCheck->execute([$tipo_atividade]);
    if (!$stmtCheck->fetch()) {
        throw new Exception("O tipo de atividade '$tipo_atividade' não existe na tabela Tipo_atividade");
    }

    $stmt = $db->prepare('INSERT INTO Detalhes (nome, localizacao) VALUES (?, ?)');
    $stmt->execute([$nome, $localizacao]);
    $detalhe_id = $db->lastInsertId();

    $stmt2 = $db->prepare('INSERT INTO Detalhes_atividade (id, tipo) VALUES (?, ?)');
    $stmt2->execute([$detalhe_id, $tipo_atividade]);

    return $detalhe_id;
}

//adiciona uma nova atividade associado a uma viagem (não precisa de ser um hotel/ect novo)
function insertAtividade($db, $viagem_id, $detalhe_id, $data) {
    $stmt = $db->prepare('INSERT INTO Atividade (data, viagem, detalhes) VALUES (?, ?, ?)');
    $stmt->execute([$data, $viagem_id, $detalhe_id]);
    return $db->lastInsertId();
}

// adiciona feedback a uma atividade
function adicionarFeedbackAtividade($db, $atividade_id, $rating, $comentario = null, $precos = null) {
    $stmt = $db->prepare('INSERT INTO Feedback (rating, comentario, precos) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $comentario, $precos]);
    $feedback_id = $db->lastInsertId();

    $stmt2 = $db->prepare('INSERT INTO Feedback_atividade (id, atividade) VALUES (?, ?)');
    $stmt2->execute([$feedback_id, $atividade_id]);

    return $feedback_id;
}

// retorna as informações completas sobre uma atividade
function getDetalhesAtividadeCompleto(PDO $db, int $atividadeId): ?array {
    $stmt = $db->prepare("
        SELECT 
            A.id AS atividade_id,
            A.viagem AS viagem_id,
            D.id AS detalhe_id, 
            D.nome AS nome_atividade,
            D.localizacao,
            DA.tipo AS tipo_atividade,
            AVG(F.rating) AS media_avaliacao
        FROM Detalhes D
        JOIN Atividade A ON A.detalhes = D.id
        JOIN Detalhes_atividade DA ON A.detalhes = DA.id
        LEFT JOIN Feedback_atividade FA ON FA.atividade = A.id
        LEFT JOIN Feedback F ON F.id = FA.id
        WHERE D.id = :id
        GROUP BY D.id
    ");

    $stmt->execute(['id' => $atividadeId]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado ?: null;
}

// retorna todos os feedbacks de uma atividade
function getFeedbacksAtividade($db, $atividade_id) {
    $stmt = $db->prepare('
        SELECT F.rating, F.comentario, F.precos
        FROM Feedback_atividade FA
        JOIN Feedback F ON F.id = FA.id
        WHERE FA.atividade = :atividade_id
        ORDER BY F.id DESC
    ');
    $stmt->bindParam(':atividade_id', $atividade_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retorna as atividades de um destino específico com base na pesquisa
function procurarAtividadesPorDestino($db, $destino_id, $termo) {
    $termo = '%' . mb_strtolower($termo, 'UTF-8') . '%';
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);
    $stmt = $db->prepare('
        SELECT D.id, D.nome, D.localizacao, DA.tipo
        FROM Detalhes D
        JOIN Detalhes_atividade DA ON D.id = DA.id
        JOIN Atividade A ON A.detalhes = D.id
        WHERE A.viagem IN (SELECT id FROM Viagens WHERE destino = ?)
        AND LOWER(removeacentos(D.nome)) LIKE ?
        GROUP BY D.id
    ');
    $stmt->execute([$destino_id, $termo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retorna todos os feedbacks de uma atividade (ex todos os feedbacks do louvre, etc)
function getFeedbacksAtividadePorDetalhe($db, $detalhe_id) {
    $stmt = $db->prepare('
        SELECT F.rating, F.comentario, F.precos, A.data
        FROM Feedback F
        JOIN Feedback_atividade FA ON F.id = FA.id
        JOIN Atividade A ON FA.atividade = A.id
        WHERE A.detalhes = :detalhe_id
        ORDER BY F.id DESC
    ');
    $stmt->bindParam(':detalhe_id', $detalhe_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//remove atividade e feedbacks associados
function removerAtividade($db, $atividade_id) {
    $db->exec("DELETE FROM Feedback_atividade WHERE atividade = $atividade_id");
    $db->exec("DELETE FROM Atividade WHERE id = $atividade_id");
}
//remove atlojamento e feedbacks associados
function removerAlojamento($db, $alojamento_id) {
    $db->exec("DELETE FROM Feedback_alojamento WHERE alojamento = $alojamento_id");
    $db->exec("DELETE FROM Alojamento WHERE id = $alojamento_id");
}

//verifica se a pessoa já deu feedback a um alojamento ou atividade
function verificarFeedback($db, $id, $tipo) {
    if ($tipo === 'alojamento') {
        $stmt = $db->prepare('SELECT COUNT(*) FROM Feedback_alojamento WHERE alojamento = ?');
    } elseif ($tipo === 'atividade') {
        $stmt = $db->prepare('SELECT COUNT(*) FROM Feedback_atividade WHERE atividade = ?');
    } else {
        return false;
    }

    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}

?>
