<?php

    // obtém o id do traveljournal associado a uma viagem
    function getTravelJournalId($db, $viagem_id) {
        $stmt = $db->prepare('SELECT id FROM TravelJournals WHERE viagem_id = ?');
        $stmt->execute(array($viagem_id));
        return $stmt->fetchColumn();
    }

    // obtém os detalhes de um traveljournal
    function getTravelJournal($db, $traveljournal_id) {
        $stmt = $db->prepare('SELECT descricao, avaliacao FROM TravelJournals WHERE id = ?');
        $stmt->execute(array($traveljournal_id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // insere um novo traveljournal
    function insertTravelJournal($db, $viagem_id,  $descricao, $avaliacao) {
        $stmt = $db->prepare('INSERT INTO TravelJournals (viagem_id, descricao, avaliacao) VALUES (?, ?, ?)');
        $stmt->execute(array($viagem_id, $descricao, $avaliacao));
        return $db->lastInsertId();
    }

    // edita um traveljournal existente
    function editarTravelJournal($db, $viagem_id,  $descricao, $avaliacao) {
        $stmt = $db->prepare('UPDATE TravelJournals SET descricao = ?, avaliacao = ? WHERE viagem_id = ?');
        $stmt->execute(array($descricao, $avaliacao, $viagem_id));
        return $db->lastInsertId();
    }

?>