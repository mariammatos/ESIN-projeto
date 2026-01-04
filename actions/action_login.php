<?php
require_once '../database/db_connect.php';
require_once '../database/users.php';

session_start();

$username = $_POST['username'];
$password = $_POST['password'];
$next_page = $_SESSION['next_page'] ?? null;
unset($_SESSION['next_page']);


try {
    $dbh = getDatabaseConnection();


    if (loginSuccess($dbh, $username, $password)) {
        $_SESSION['username'] = $username;

        if (!empty($next_page)) {
            header('Location: ../' . $next_page);
        } else {
            header('Location: ../feed.php');
        }
        exit();
    } else {
        $_SESSION['msg'] = 'Nome de utilizador ou password inválidos!';
        header('Location: ../login.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['msg'] = 'Erro: ' . $e->getMessage();
}