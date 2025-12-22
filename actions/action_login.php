<?php
require_once '../database/db_connect.php';

session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// check if username and password are correct
function loginSuccess($dbh, $username, $password) {
    $stmt = $dbh->prepare('SELECT * FROM Utilizador WHERE nome_de_utilizador = ? AND palavra_passe = ?');
    $stmt->execute(array($username, hash('sha256', $password)));
    return $stmt->fetch();
}

// if login successful:
// - redirect user to feed page
// else:
// - set error msg "Login failed!"
// - redirect user back to index page

try {
    $dbh = getDatabaseConnection();


    if (loginSuccess($dbh, $username, $password)) {
        $_SESSION['username'] = $username;
        // Redirecionamento alterado para 'feed.php'
        header('Location: ../feed.php');
        exit();
    } else {
        $_SESSION['msg'] = 'Nome de utilizador ou password inválidos!';
        // Redirecionamento alterado para a página inicial (se falhar)
        header('Location: ../login.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['msg'] = 'Erro: ' . $e->getMessage();
}