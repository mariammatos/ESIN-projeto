<?php
require_once 'destinos.php';

    function insertUser($db, $username, $password, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil) {
        $stmt = $db->prepare('INSERT INTO Utilizador (nome_de_utilizador, email, nome, pais_de_origem, preferencia_de_viagem, foto_de_perfil, palavra_passe) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array($username, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil, hash('sha256', $password)));
    }

function saveProfilePic($username) {
    $file = $_FILES['profile_pic'];

    $uploadPath = "../media/profile_pictures/$username.jpg";
    move_uploaded_file($file['tmp_name'], $uploadPath);
}

function procurarusers($db, $username_input) {
    $username_normalizado = normalize_string($username_input);
    $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);

    $stmt = $db->prepare('SELECT nome_de_utilizador FROM Utilizador WHERE removeacentos(nome_de_utilizador) LIKE ? OR removeacentos(nome) LIKE ?');
    $stmt->execute(array("%$username_normalizado%", "%$username_normalizado%"));
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getuserdetails($db, $username) {
    $stmt = $db->prepare('SELECT * FROM Utilizador WHERE nome_de_utilizador = ?');
    $stmt->execute(array($username));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getuserwishlist($db, $username) {
    $stmt = $db->prepare('SELECT id FROM WishList WHERE utilizador = ?');
    $stmt->execute(array($username));
    return $stmt->fetch(PDO::FETCH_COLUMN);
}
?>