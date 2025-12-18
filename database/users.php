<?php

function insertUser($username, $password, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Utilizador (nome_de_utilizador, email, nome, pais_de_origem, preferencia_de_viagem, foto_de_perfil, palavra_passe) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($username, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil, hash('sha256', $password)));
}

?>