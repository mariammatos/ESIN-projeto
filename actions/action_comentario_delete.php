<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/posts.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}


if (isset($_POST['comentario_id'])) {
    $db = getDatabaseConnection();
    removerComentario($db, $_POST['comentario_id']);
}

header("Location: ../viagem.php?id=" . $_POST['viagem_id']);
exit();
?>