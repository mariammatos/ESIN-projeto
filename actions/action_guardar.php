<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/posts.php';
$db = getDatabaseConnection();

$current_user = $_SESSION['username'];
$post = $_POST['post_id'];


$guardado =  publicacaoGuardada($db, $current_user, $post);
if ($guardado) {
    removerPublicacaoGuardada($db, $current_user, $post);
} else {
    guardarPublicacao($db, $current_user, $post);
}


// Redireciona de volta para a página da viagem
header("Location: ../viagem.php?id=" . $post);
exit;
?>