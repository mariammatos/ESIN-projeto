<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/posts.php'; // função removerViagem

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST['viagem_id'])) {
    die('Viagem inválida.');
}

$viagem_id = (int)$_POST['viagem_id'];
$db = getDatabaseConnection();

if (removerViagem($db, $viagem_id)) {
    $_SESSION['msg_sucesso'] = "Viagem apagada com sucesso!";
} else {
    $_SESSION['msg_erro'] = "Ocorreu um erro ao tentar apagar a viagem.";
}

// Certifica-te de que perfil.php está no caminho correto
header("Location: ../perfil.php");
exit();
?>
