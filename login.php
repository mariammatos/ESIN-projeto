<?php
session_start();

// get username and password from HTTP parameters
$username = $_POST['username'];
$password = $_POST['password'];

// check if username and password are correct
function loginSuccess($username, $password) {
    global $dbh;
    // Tabela alterada de 'Users' para 'Utilizador'
    // Coluna alterada de 'username' para 'nome_de_utilizador'
    $stmt = $dbh->prepare('SELECT * FROM Utilizador WHERE nome_de_utilizador = ? AND password = ?');
    $stmt->execute(array($username, hash('sha256', $password)));
    return $stmt->fetch();
}

// if login successful:
// - create a new session attribute for the user
// - redirect user to main page
// else:
// - set error msg "Login failed!"
// - redirect user to main page

try {
    // Nome da base de dados alterado para 'triptales.db'
    $dbh = new PDO('sqlite:triptales.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Linha adicionada para ativar as Foreign Keys no SQLite (Boa prática)
    $dbh->exec('PRAGMA foreign_keys = ON;');

    if (loginSuccess($username, $password)) {
        $_SESSION['username'] = $username;
        // Redirecionamento alterado para 'feed.php'
        header('Location: feed.php');
        exit();
    } else {
        $_SESSION['msg'] = 'Nome de utilizador ou password inválidos!';
        // Redirecionamento alterado para a página inicial (se falhar)
        header('Location: index.html');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['msg'] = 'Erro interno do servidor: ' . $e->getMessage();
}

// Esta linha de redirecionamento foi removida e substituída pelos headers dentro do if/else
// header('Location: list_categories.php');
?>