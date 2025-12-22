<?php
session_start();
require_once '../database/db_connect.php';
$dbh = getDatabaseConnection();

$username = $_POST['username'] ?? '';

if (strlen($username) == 0) {
    $_SESSION['msg1'] = 'Por favor, insira um nome de utilizador.';
    // $_SESSION['form_data'] = $_POST;
    header('Location: ../registration.php');
    exit;
}

$stmt = $dbh->prepare("SELECT 1 FROM Utilizador WHERE nome_de_utilizador = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    $_SESSION['msg1'] = "Nome de utilizador já existe.";
} else {
    $_SESSION['msg1'] = "Nome de utilizador disponível!";
}

$_SESSION['form_data'] = $_POST;
header('Location: ../registration.php');
exit;
