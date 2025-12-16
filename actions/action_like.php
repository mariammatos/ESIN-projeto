<?php
session_start();
require_once 'db_connect.php';
$db = getDatabaseConnection();

$current_user = $_SESSION['username'];
$liked_post = $_POST['post_id'];

// 1. Verifica se já existe like
$stmt = $db->prepare("SELECT COUNT(*) FROM Like_Viagem WHERE utilizador = :utilizador AND viagem = :viagem_id");
$stmt->bindParam(':utilizador', $current_user);
$stmt->bindParam(':viagem_id', $liked_post);
$stmt->execute();

$like_exists = $stmt->fetchColumn(); // retorna 0 ou 1

if ($like_exists) {
    // Já tem like → remove
    $stmt = $db->prepare("DELETE FROM Like_Viagem WHERE utilizador = :utilizador AND viagem = :viagem_id");
    $stmt->bindParam(':utilizador', $current_user);
    $stmt->bindParam(':viagem_id', $liked_post);
    $stmt->execute();
} else {
    // Não tem like → adiciona
    $stmt = $db->prepare(
        'INSERT INTO Like_Viagem (utilizador, viagem, data)
        VALUES (:utilizador, :viagem_id, datetime("now"))'
    );
    $stmt->bindParam(':utilizador', $current_user);
    $stmt->bindParam(':viagem_id', $liked_post);
    $stmt->execute();
}

// Redireciona de volta para a página da viagem
header("Location: ../viagem.php?id=" . $liked_post);
exit;
?>

// ob_iconv_handler

