<?php
require_once 'destinos.php';

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


/**
 * Obtém todos os detalhes de uma única viagem, incluindo o seu TravelJournal.
 * @param PDO $db A ligação à base de dados.
 * @param int $id O ID da viagem a procurar.
 * @return array Os detalhes da viagem.
 */

function getViagemDetalhes($db, $id) {
    // Esta query junta Viagens, Utilizador, Destino e, opcionalmente, o TravelJournal.
    $stmt = $db->prepare(
        'SELECT 
            V.titulo, 
            U.nome_de_utilizador, 
            U.nome, 
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


function getViagemLikes($db, $id) {
    // Esta query junta Viagens, Utilizador, Destino e o TravelJournal.
    $stmt = $db->prepare(
        'SELECT * FROM Like_Viagem WHERE viagem = :viagem_id'
    );

    $stmt->bindParam(':viagem_id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(); // Usamos fetch() porque só esperamos um resultado
}
function userLikedViagem($db, $viagem_id, $username) {
    $stmt = $db->prepare(
        'SELECT 1 FROM Like_Viagem WHERE viagem = :viagem_id AND utilizador = :username'
    );
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch() !== false;
}

function getViagemLikesCount($db, $viagem_id) {
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM Like_Viagem WHERE viagem = :viagem_id');
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['total'] : 0;
}

function getComentarios($db, $viagem_id) {
    $stmt = $db->prepare('SELECT * FROM Comentario WHERE viagem = ? ORDER BY data DESC');
    $stmt->execute([$viagem_id]);
    return $stmt->fetchAll();
}

function getViagemComentariosCount($db, $viagem_id) {
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM Comentario WHERE viagem = :viagem_id');
    $stmt->bindParam(':viagem_id', $viagem_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['total'] : 0;
}

function adicionarComentario($db, $viagem_id, $username, $texto) {
    $data = date('Y-m-d');
    $hora = date('H:i:s');
    $stmt = $db->prepare('INSERT INTO Comentario (viagem, utilizador, texto, data, hora) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$viagem_id, $username, $texto, $data, $hora]);
}

function removerComentario($db, $comentario_id) {
    $stmt = $db->prepare('DELETE FROM Comentario WHERE id = ?');
    $stmt->execute([$comentario_id]);
}


function insertviagem($db, $titulo, $data_ida, $data_volta, $utilizador, $destino) {
    $stmt = $db->prepare('INSERT INTO Viagens (titulo, data_ida, data_volta, utilizador, destino) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$titulo, $data_ida, $data_volta, $utilizador, $destino]);
    return $db->lastInsertId();
}


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

function publicacaoGuardada($db, $utilizador, $viagem_id) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM Guardar_publicacao WHERE utilizador = :utilizador AND viagem = :viagem_id");
    $stmt->bindParam(':utilizador', $utilizador);
    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();

    $guardado = $stmt->fetchColumn();
    return $guardado > 0;
}

function guardarPublicacao($db, $utilizador, $viagem_id) {
    $data_atual = date('Y-m-d H:i:s');    
    $stmt = $db->prepare('INSERT INTO Guardar_publicacao (utilizador, viagem, data) VALUES (?, ?, ?)');
    $stmt->execute(array($utilizador, $viagem_id, $data_atual));
}

function removerPublicacaoGuardada($db, $utilizador, $viagem_id) {
    $stmt = $db->prepare('DELETE FROM Guardar_publicacao WHERE utilizador = ? AND viagem = ?');
    $stmt->execute(array($utilizador, $viagem_id));}

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

  function getFotos($db, $viagem_id) {
    $stmt = $db->prepare(
        'SELECT 
            Path, data
        FROM 
            Media
        WHERE 
            Viagem = :viagem_id
        ORDER BY 
            data DESC;'
    );

    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();
    return $stmt->fetchAll();
  }

?>