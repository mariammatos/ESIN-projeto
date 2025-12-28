<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();

$item_id = (int)($_GET['id'] ?? 0); // id do alojamento ou atividade
$tipo = $_GET['tipo'] ?? 'alojamento'; // 'alojamento' ou 'atividade'

$viagem_id = null;

if ($tipo === 'alojamento') {
    $stmt = $db->prepare('SELECT viagem FROM Alojamento WHERE id = ?');
} else { // atividade
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

  <a href="perfil.php?" class="btn-voltar">← Voltar ao Perfil</a>

    <div>
        <h2>Dar Feedback - <?= $tipo === 'alojamento' ? 'Alojamento' : 'Atividade' ?></h2>
        <form method="post">
            <div class="form-group">
                <label for="rating">Avaliação (0 a 5):</label>
                <input type="number" id="rating" name="rating" min="0" max="5" step="1" value="5" required>
            </div>
            
            <div class="form-group">
                <label>Preço:</label>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="precos" value="0" required> Gratuito
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="precos" value="1"> $
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="precos" value="2"> $$
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="precos" value="3"> $$$
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" name="comentario" placeholder="Partilhe a sua experiência com este <?= $tipo === 'alojamento' ? 'alojamento' : 'atividade' ?>..."></textarea>
            </div>
            
            <button type="submit">Enviar Feedback</button>
        </form>
    </div>
    
<?php include_once 'templates/footer_tpl.php'; ?>