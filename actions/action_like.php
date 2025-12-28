<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/posts.php';

$db = getDatabaseConnection();

$current_user = $_SESSION['username'];
$liked_post = $_POST['post_id'];


$like_exists = userLikedViagem($db, $liked_post, $current_user); 

if ($like_exists) {
    removerlike($db, $liked_post, $current_user);
} else {
    like($db, $liked_post, $current_user);
}

header("Location: ../viagem.php?id=" . $liked_post);
exit;
?>


