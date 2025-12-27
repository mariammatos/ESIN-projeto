<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();

$alojamento_id = (int)($_GET['alojamento_id'] ?? 0);
$viagem_id = null;

// Pegar a viagem do alojamento
$stmt = $db->prepare('SELECT viagem FROM Alojamento WHERE id = ?');
$stmt->execute([$alojamento_id]);
$viagem_id = $stmt->fetchColumn();

if (!$alojamento_id || !$viagem_id) {
    die('Alojamento inválido.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)$_POST['rating'];
    $comentario = trim($_POST['comentario'] ?? null);

    if ($rating < 0 || $rating > 5) {
        $_SESSION['error'] = "Avaliação deve ser entre 0 e 5.";
        header("Location: feedback_alojamento.php?alojamento_id=$alojamento_id");
        exit;
    }

    adicionarFeedbackAlojamento($db, $alojamento_id, $rating, $comentario);

    $_SESSION['success'] = "Feedback adicionado com sucesso!";
    header("Location: viagem.php?id=$viagem_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dar Feedback | TripTales</title>
</head>
<body>
    <h2>Dar Feedback ao Alojamento</h2>
    <form method="post">
        <label for="rating">Avaliação (0 a 5):</label>
        <input type="number" name="rating" min="0" max="5" required>
        <label for="comentario">Comentário:</label>
        <textarea name="comentario"></textarea>
        <button type="submit">Enviar Feedback</button>
    </form>
</body>
</html>
