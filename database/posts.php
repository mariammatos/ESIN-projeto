<?php
require_once 'destinos.php';

// retorna o feed de viagens para o utilizador atual
  function getFeed($db, $current_user) {
    $stmt = $db->prepare(
      'SELECT 
        V.id, V.titulo, U.nome_de_utilizador, U.nome, D.cidade_local, D.pais
    FROM 
        Viagens V
    JOIN 
        Utilizador U ON V.utilizador = U.nome_de_utilizador
    JOIN 
        Seguir S ON U.nome_de_utilizador = S.utilizador2 
    JOIN
        Destino D ON V.destino = D.id
    WHERE 
        S.utilizador1 = :current_user
    ORDER BY 
        V.data_ida DESC;'
    );

    $stmt->bindParam(':current_user', $current_user);

    $stmt->execute();
    return $stmt->fetchAll();
  }


// retorna os detalhes de uma viagem específica
function getViagemDetalhes($db, $id) {
    $stmt = $db->prepare(
        'SELECT 
            V.titulo, 
            U.nome_de_utilizador, 
            U.nome, 
            D.id AS destino_id,
            D.cidade_local, 
            D.pais, 
            V.data_ida, 
            V.data_volta,
            TJ.descricao AS journal_descricao, 
            TJ.avaliacao AS journal_avaliacao
        FROM 
            Viagens V
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        LEFT JOIN
            TravelJournals TJ ON TJ.viagem_id = V.id
        WHERE 
            V.id = :id'
    );

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // fetch associativo
}

// retorna todas as viagens de um utilizador específico
function getViagensUtilizador($db, $username) {
    $stmt = $db->prepare(
        'SELECT 
            V.id, V.titulo, D.cidade_local, D.pais
        FROM 
            Viagens V
        JOIN 
            Destino D ON V.destino = D.id
        WHERE 
            V.utilizador = :username
        ORDER BY 
            V.data_ida DESC;'
    );

    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// remove o like de uma viagem
function removerlike($db, $viagem_id, $username) {
    $stmt = $db->prepare("DELETE FROM Like_Viagem WHERE utilizador = :utilizador AND viagem = :viagem_id");
    $stmt->bindParam(':utilizador', $username);
    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();
}

// adiciona um like a uma viagem
function like($db, $viagem_id, $username) {
    $stmt = $db->prepare(
        'INSERT INTO Like_Viagem (utilizador, viagem, data)
        VALUES (:utilizador, :viagem_id, datetime("now"))'
    );
    $stmt->bindParam(':utilizador', $username);
    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();
}

// obtém os likes de uma viagem
function getViagemLikes($db, $id) {
    $stmt = $db->prepare(
        'SELECT * FROM Like_Viagem WHERE viagem = :viagem_id'
    );

    $stmt->bindParam(':viagem_id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(); // Usamos fetch() porque só esperamos um resultado
}

// verifica se o utilizador já gostou de uma viagem
function userLikedViagem($db, $viagem_id, $username) {
    $stmt = $db->prepare(
        'SELECT 1 FROM Like_Viagem WHERE viagem = :viagem_id AND utilizador = :username'
    );
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch() !== false;
}

// obtém o número total de likes de uma viagem
function getViagemLikesCount($db, $viagem_id) {
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM Like_Viagem WHERE viagem = :viagem_id');
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['total'] : 0;
}

// obtém os comentários de uma viagem
function getComentarios($db, $viagem_id) {
    $stmt = $db->prepare('SELECT * FROM Comentario WHERE viagem = ? ORDER BY data DESC');
    $stmt->execute([$viagem_id]);
    return $stmt->fetchAll();
}

// obtém o número total de comentários de uma viagem
function getViagemComentariosCount($db, $viagem_id) {
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM Comentario WHERE viagem = :viagem_id');
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['total'] : 0;
}

// adiciona um comentário a uma viagem
function adicionarComentario($db, $viagem_id, $username, $texto) {
    $data = date('Y-m-d');
    $hora = date('H:i:s');
    $stmt = $db->prepare('INSERT INTO Comentario (viagem, utilizador, texto, data, hora) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$viagem_id, $username, $texto, $data, $hora]);
}

// remove um comentário de uma viagem
function removerComentario($db, $comentario_id) {
    $stmt = $db->prepare('DELETE FROM Comentario WHERE id = ?');
    $stmt->execute([$comentario_id]);
}

// insere uma nova viagem
function insertviagem($db, $titulo, $data_ida, $data_volta, $utilizador, $destino) {
    $stmt = $db->prepare('INSERT INTO Viagens (titulo, data_ida, data_volta, utilizador, destino) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$titulo, $data_ida, $data_volta, $utilizador, $destino]);
    return $db->lastInsertId();
}

// edita uma viagem existente
function editarviagem($db, $titulo, $data_ida, $data_volta, $viagem_id) {
    $stmt = $db->prepare('UPDATE Viagens SET titulo = ?, data_ida = ?, data_volta = ? WHERE id = (SELECT id FROM Viagens WHERE id = ?)');
    $stmt->execute([$titulo, $data_ida, $data_volta, $viagem_id]);
    return $db->lastInsertId();
}

// obtém viagens para a página explorar
function getexplorar($db, $limite = 10) {
    $stmt = $db->prepare(
        'SELECT 
            V.id, 
            V.titulo, 
            U.nome_de_utilizador, 
            U.nome, 
            D.cidade_local, 
            D.pais
        FROM 
            Viagens V
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        ORDER BY RANDOM()
        LIMIT :limite;'
    );

    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// procura viagens por título ou destino
function procurarviagens($db, $viagem_input) {
    $viagem_normalizado = normalize_string($viagem_input);
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);
    
    $stmt = $db->prepare(       
        'SELECT 
            V.id, 
            V.titulo, 
            U.nome_de_utilizador, 
            U.nome, 
            D.cidade_local, 
            D.pais
        FROM 
            Viagens V
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        WHERE 
            LOWER(removeacentos(V.titulo)) LIKE ? OR LOWER(removeacentos(D.cidade_local)) LIKE ?'
    );
    $stmt->execute(array("%$viagem_normalizado%", "%$viagem_normalizado%"));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// verifica se uma publicação está guardada por um utilizador
function publicacaoGuardada($db, $utilizador, $viagem_id) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM Guardar_publicacao WHERE utilizador = :utilizador AND viagem = :viagem_id");
    $stmt->bindParam(':utilizador', $utilizador);
    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();

    $guardado = $stmt->fetchColumn();
    return $guardado > 0;
}

// guarda uma publicação
function guardarPublicacao($db, $utilizador, $viagem_id) {
    $data_atual = date('Y-m-d H:i:s');    
    $stmt = $db->prepare('INSERT INTO Guardar_publicacao (utilizador, viagem, data) VALUES (?, ?, ?)');
    $stmt->execute(array($utilizador, $viagem_id, $data_atual));
}

// remove uma publicação guardada
function removerPublicacaoGuardada($db, $utilizador, $viagem_id) {
    $stmt = $db->prepare('DELETE FROM Guardar_publicacao WHERE utilizador = ? AND viagem = ?');
    $stmt->execute(array($utilizador, $viagem_id));}

    // obtém as publicações guardadas por um utilizador
  function getguardados($db, $current_user) {
    $stmt = $db->prepare(
      'SELECT 
            V.id, 
            V.titulo, 
            U.nome_de_utilizador, -- O criador da viagem
            U.nome,               -- O nome do criador
            D.cidade_local, 
            D.pais
        FROM 
            Viagens V
        JOIN 
            Guardar_publicacao GP ON V.id = GP.viagem
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        WHERE 
            GP.utilizador = :current_user
        ORDER BY 
            V.data_ida DESC;'
    );

    $stmt->bindParam(':current_user', $current_user);

    $stmt->execute();
    return $stmt->fetchAll();
  }

  // obtém as publicações por destino
  function getPostsporDestino($db, $destino) {
    $stmt = $db->prepare(
        'SELECT 
            V.id, V.titulo, V.data_ida, U.nome_de_utilizador, U.nome, D.cidade_local, D.pais
        FROM 
            Viagens V
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        WHERE 
            V.destino = :destino
        ORDER BY 
            V.data_ida DESC;'
    );
    
    $stmt->bindParam(':destino', $destino);
    $stmt->execute();
    return $stmt->fetchAll();
  }
  
// apaga permanentemente uma viagem
function removerViagem($db, $viagem_id) {
    $db->exec("PRAGMA foreign_keys = ON");

    try {
        $db->beginTransaction();

        $db->prepare("
            DELETE FROM Media WHERE Feedback IN (
                SELECT id FROM Feedback_atividade WHERE atividade IN (SELECT id FROM Atividade WHERE viagem = ?)
                UNION
                SELECT id FROM Feedback_alojamento WHERE alojamento IN (SELECT id FROM Alojamento WHERE viagem = ?)
            )
        ")->execute([$viagem_id, $viagem_id]);

        $db->prepare("DELETE FROM Media WHERE Viagem = ?")->execute([$viagem_id]);


        $stmtF = $db->prepare("
            SELECT id FROM Feedback_atividade WHERE atividade IN (SELECT id FROM Atividade WHERE viagem = ?)
            UNION
            SELECT id FROM Feedback_alojamento WHERE alojamento IN (SELECT id FROM Alojamento WHERE viagem = ?)
        ");
        $stmtF->execute([$viagem_id, $viagem_id]);
        $feedbackIds = $stmtF->fetchAll(PDO::FETCH_COLUMN);


        $db->prepare("DELETE FROM Feedback_atividade WHERE atividade IN (SELECT id FROM Atividade WHERE viagem = ?)")->execute([$viagem_id]);
        $db->prepare("DELETE FROM Feedback_alojamento WHERE alojamento IN (SELECT id FROM Alojamento WHERE viagem = ?)")->execute([$viagem_id]);


        if (!empty($feedbackIds)) {
            $placeholders = implode(',', array_fill(0, count($feedbackIds), '?'));
            $db->prepare("DELETE FROM Feedback WHERE id IN ($placeholders)")->execute($feedbackIds);
        }

        $db->prepare("DELETE FROM Atividade WHERE viagem = ?")->execute([$viagem_id]);
        $db->prepare("DELETE FROM Alojamento WHERE viagem = ?")->execute([$viagem_id]);


        $db->prepare("DELETE FROM Comentario WHERE viagem = ?")->execute([$viagem_id]);
        $db->prepare("DELETE FROM Like_Viagem WHERE viagem = ?")->execute([$viagem_id]);
        $db->prepare("DELETE FROM Guardar_publicacao WHERE viagem = ?")->execute([$viagem_id]);
        $db->prepare("DELETE FROM TravelJournals WHERE viagem_id = ?")->execute([$viagem_id]);


        $result = $db->prepare("DELETE FROM Viagens WHERE id = ?")->execute([$viagem_id]);
        
        $db->commit();
        return $result;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}



?>