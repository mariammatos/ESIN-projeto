<?php

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
    // Esta query junta Viagens, Utilizador, Destino e o TravelJournal.
    $stmt = $db->prepare(
        'SELECT 
            V.titulo, U.nome_de_utilizador, U.nome, 
            D.cidade_local, D.pais, V.data_ida, V.data_volta,
            TJ.descricao AS journal_descricao, TJ.avaliacao AS journal_avaliacao
        FROM 
            Viagens V
        JOIN 
            Utilizador U ON V.utilizador = U.nome_de_utilizador
        JOIN
            Destino D ON V.destino = D.id
        JOIN
            TraveJournals TJ ON V.travel_journal = TJ.id
        WHERE 
            V.id = :id'
    );

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(); // Usamos fetch() porque só esperamos um resultado
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
?>
