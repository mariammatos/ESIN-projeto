<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/destinos.php';
require_once '../database/users.php';
$db = getDatabaseConnection();

$current_user = $_SESSION['username'];
$destino = $_POST['destino_id'];
$post = $_POST['post_id'];
$wishlist = getuserwishlist($db, $current_user);

$guardado =  destinonaWishlist($db, $destino, $wishlist);
if ($guardado) {
    removerwishlist($db, $destino, $wishlist);
} else {
    adicionarwishlist($db, $destino, $wishlist);
}


header("Location: ../viagem.php?id=" . $post);
exit;
?>