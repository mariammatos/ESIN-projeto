<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();

$item_id = (int)($_GET['id'] ?? 0); 
$tipo = $_GET['tipo'] ?? 'alojamento'; 

$viagem_id = null;

if ($tipo === 'alojamento') {
    $stmt = $db->prepare('SELECT viagem FROM Alojamento WHERE id = ?');
} else {
    $stmt = $db->prepare('SELECT viagem FROM Atividade WHERE id = ?');
}
$stmt->execute([$item_id]);
$viagem_id = $stmt->fetchColumn();

if (!$item_id || !$viagem_id) {
    die('Item inválido.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    $precos = $precos = isset($_POST['precos']) ? (int)$_POST['precos'] : null;
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 5]
    ]);

    if ($rating === false) {
        $_SESSION['error'] = "Avaliação inválida. Deve ser um número entre 0 e 5.";
        header("Location: feedback.php?id=$item_id&tipo=$tipo");
        exit;
    }

    if ($tipo === 'alojamento') {
        adicionarFeedbackAlojamento($db, $item_id, $rating, $comentario, $precos);
    } else {
        adicionarFeedbackAtividade($db, $item_id, $rating, $comentario, $precos);
    }

    $_SESSION['success'] = "Feedback adicionado com sucesso!";
    header("Location: viagem.php?id=$viagem_id");
    exit;
}
?>



<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dar Feedback | TripTales</title>
    <link rel="stylesheet" href="css/stylefeedback.css">
</head>
<body>

  <a href="viagem.php?id=<?= htmlspecialchars($viagem_id) ?>" class="btn-voltar">← Voltar</a>

    <div class="feedback-container">
        <h2>Dar Feedback - <?= $tipo === 'alojamento' ? 'Alojamento' : 'Atividade' ?></h2>
        
        <form method="post">
            <div class="form-group">
                <label for="rating">Avaliação (0 a 5):</label>
                <input type="number" id="rating" name="rating" min="0" max="5" step="1" value="5" required>
            </div>
            
            <div class="form-group">
                <label>Preço:</label>
                <div class="price-selector">
                    <label class="price-option">
                        <input type="radio" name="precos" value="0" required>
                        <span>Gratuito</span>
                    </label>
                    <label class="price-option">
                        <input type="radio" name="precos" value="1">
                        <span>$</span>
                    </label>
                    <label class="price-option">
                        <input type="radio" name="precos" value="2">
                        <span>$$</span>
                    </label>
                    <label class="price-option">
                        <input type="radio" name="precos" value="3">
                        <span>$$$</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" name="comentario" placeholder="Partilhe a sua experiência..."></textarea>
            </div>
            
            <button type="submit">Enviar Feedback</button>
        </form>
    </div>

<?php include_once 'templates/footer_tpl.php'; ?>
