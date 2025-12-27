<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/users.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

$db = getDatabaseConnection();

$utilizador1 = $_SESSION['username'];
$utilizador2 = $_POST['seguido'];     


if ($utilizador1 === $utilizador2) {
    die("Não podes seguir-te a ti próprio.");
}

$jaSegue = usersegue($db, $utilizador1, $utilizador2);

if ($jaSegue) {
    deixarDeSeguir($db, $utilizador1, $utilizador2);
} else {
    seguir($db, $utilizador1, $utilizador2);
}

// Voltar ao perfil
header("Location: ../perfil.php?user=" . $utilizador2);
exit();

