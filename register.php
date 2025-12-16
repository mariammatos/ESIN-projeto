<?php
session_start();
require_once 'database/db_connect.php';
$dbh = getDatabaseConnection();

$username = $_POST['username'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm']; // Novo campo para confirmação
$email = $_POST['email'];                       // Novo campo para e-mail
$nome = $_POST['nome'];                         // Novo campo para Nome
$pais_de_origem = $_POST['pais_de_origem'];     // Novo campo para País
$preferencia_de_viagem = $_POST['preferencia_de_viagem']; // Novo campo para Preferência
$foto_de_perfil = $_POST['foto_de_perfil'];     // Novo campo para Foto

function insertUser($username, $password, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil) { // Parâmetros adicionados
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Utilizador (nome_de_utilizador, email, nome, pais_de_origem, preferencia_de_viagem, foto_de_perfil, palavra_passe) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($username, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil, hash('sha256', $password)));
}

if (strlen($username) == 0 || strlen($email) == 0 || strlen($nome) == 0 || strlen($pais_de_origem) == 0 || strlen($preferencia_de_viagem) == 0 || strlen($foto_de_perfil) == 0) { // Campos adicionados à verificação
    $_SESSION['msg'] = 'Por favor, preencha todos os campos obrigatórios.';
    header('Location: registration.php');
    die();
}

if ($password != $password_confirm) {
    $_SESSION['msg'] = 'A palavra-passe e a confirmação não correspondem.';
    header('Location: registration.php');
    die();
}

if (strlen($password) < 8) {
    $_SESSION['msg'] = 'A palavra-passe deve ter pelo menos 8 caracteres.';
    header('Location: registration.php');
    die();
}

try {

    insertUser($username, $password, $email, $nome, $pais_de_origem, $preferencia_de_viagem, $foto_de_perfil); // Parâmetros adicionados
    $_SESSION['username'] = $username;
    $_SESSION['msg'] = 'Registo bem-sucedido! Faça login para começar.';
    header('Location: feed.php');

} catch (PDOException $e) {
    $error_msg = $e->getMessage();

    if (strpos($error_msg, 'UNIQUE')) {
        $_SESSION['msg'] = 'Nome de utilizador ou E-mail já existe(m)!'; // Mensagem ajustada
    } else if (strpos($error_msg, 'FOREIGN KEY constraint failed')) { // Adicionado para capturar erros de FK (ex: País não existe)
        $_SESSION['msg'] = 'O País de origem selecionado é inválido. Tente novamente.';
    } else {
        $_SESSION['msg'] = "Falha no Registo! ($error_msg)"; // Mensagem ajustada
    }
    header('Location: registration.php');
}
?>