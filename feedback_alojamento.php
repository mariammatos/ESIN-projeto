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
    $comentario = trim($_POST['comentario'] ?? null);
    $precos = trim($_POST['precos'] ?? null);
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 5]
    ]);
    if ($rating === false) {
        $_SESSION['error'] = "Avaliação inválida. Deve ser um número entre 0 e 5.";
        header("Location: feedback_alojamento.php?alojamento_id=$alojamento_id");
        exit;
    }


    adicionarFeedbackAlojamento($db, $alojamento_id, $rating, $comentario, $preços);

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

  <a href="viagem.php?id=2" class="btn-voltar">← Voltar à Viagem</a>

    <div>
        <h2>Dar Feedback ao Alojamento</h2>
        <form method="post">
            <div class="form-group">
                <label for="rating">Avaliação (0 a 5):</label>
                <input type="number" id="rating" name="rating" min="0" max="5" step="1" value="5" required>
                <div class="rating-display">
                <span class="stars">
                        <?php 
                            $media = $a['media_avaliacao'] ? round($a['media_avaliacao']) : 0;
                            echo str_repeat('★', $media) . str_repeat('☆', 5 - $media);
                        ?>
                </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" name="comentario" placeholder="Partilhe a sua experiência com este alojamento..."></textarea>
            </div>
            
            <button type="submit">Enviar Feedback</button>
        </form>
    </div>

    <script>
    
</body>
</html>