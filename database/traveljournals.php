<?php

    function getTravelJournalId($db, $viagem_id) {
        $stmt = $db->prepare('SELECT id FROM TravelJournals WHERE viagem_id = ?');
        $stmt->execute(array($viagem_id));
        return $stmt->fetchColumn();
    }

    function getTravelJournal($db, $traveljournal_id) {
        $stmt = $db->prepare('SELECT descricao, avaliacao FROM TravelJournals WHERE id = ?');
        $stmt->execute(array($traveljournal_id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getTravelJournalmedia($db, $traveljournal_id) {
        $stmt = $db->prepare('SELECT path FROM TMedia WHERE TravelJournal = ?');
        $stmt->execute(array($traveljournal_id));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function insertTravelJournal($db, $viagem_id,  $descricao, $avaliacao) {
        $stmt = $db->prepare('INSERT INTO TravelJournals (viagem_id, descricao, avaliacao) VALUES (?, ?, ?)');
        $stmt->execute(array($viagem_id, $descricao, $avaliacao));
        return $db->lastInsertId();
    }

    function editarTravelJournal($db, $viagem_id,  $descricao, $avaliacao) {
        $stmt = $db->prepare('UPDATE TravelJournals SET descricao = ?, avaliacao = ? WHERE viagem_id = ?');
        $stmt->execute(array($descricao, $avaliacao, $viagem_id));
        return $db->lastInsertId();
    }
?>