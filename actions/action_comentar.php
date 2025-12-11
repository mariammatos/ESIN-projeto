<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/posts.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['comentario'], $_POST['viagem_id'])) {
    $db = getDatabaseConnection();
    adicionarComentario($db, $_POST['viagem_id'], $_SESSION['username'], $_POST['comentario']);
}

header("Location: ../viagem.php?id=" . $_POST['viagem_id']);
exit();
?>