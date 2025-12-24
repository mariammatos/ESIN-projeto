<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

$db = getDatabaseConnection();

$utilizador1 = $_SESSION['username']; // seguidor
$utilizador2 = $_POST['seguido'];     // seguido

// Verifica se existe
$stmt = $db->prepare("SELECT COUNT(*) FROM Utilizador WHERE nome_de_utilizador = ?");
$stmt->execute([$utilizador2]);
if ($stmt->fetchColumn() == 0) {
    die("O utilizador que estás a tentar seguir não existe.");
}

// Não permitir seguir a si próprio
if ($utilizador1 === $utilizador2) {
    die("Não podes seguir-te a ti próprio.");
}

// Verificar se já segue
$stmt = $db->prepare("SELECT COUNT(*) FROM Seguir WHERE utilizador1 = ? AND utilizador2 = ?");
$stmt->execute([$utilizador1, $utilizador2]);
$jaSegue = $stmt->fetchColumn();

if ($jaSegue) {
    // Deixar de seguir
    $stmt = $db->prepare("DELETE FROM Seguir WHERE utilizador1 = ? AND utilizador2 = ?");
    $stmt->execute([$utilizador1, $utilizador2]);
} else {
    // Seguir
    $stmt = $db->prepare("INSERT INTO Seguir (utilizador1, utilizador2, data) VALUES (?, ?, datetime('now'))");
    $stmt->execute([$utilizador1, $utilizador2]);
}

// Voltar ao perfil
header("Location: ../perfil.php?user=" . $utilizador2);
exit();

