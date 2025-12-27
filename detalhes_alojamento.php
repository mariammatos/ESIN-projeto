<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();
$alojamento_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$alojamento_id) {
    die('Alojamento inválido.');
}

// Buscar detalhes do alojamento, rating global e comentários
$detalhes = getDetalhesAlojamentoCompleto($db, $alojamento_id);
$feedbacks = getFeedbacksAlojamento($db, $alojamento_id);

if (!$detalhes) {
    die('Alojamento não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Alojamento | TripTales</title>
    <link rel="stylesheet" href="css/stylefeedback.css">
</head>
<body>
    <h2><?= htmlspecialchars($detalhes['nome_alojamento']) ?> (<?= htmlspecialchars($detalhes['tipo_alojamento']) ?>)</h2>
    <p><strong>Localização:</strong> <?= htmlspecialchars($detalhes['localizacao']) ?></p>
    <p><strong>Rating Global:</strong> <?= $detalhes['media_avaliacao'] !== null ? number_format($detalhes['media_avaliacao'], 1) . ' / 5' : 'Sem avaliações' ?></p>

    <h3>Comentários de Feedback</h3>
    <?php if ($feedbacks): ?>
        <ul>
        <?php foreach ($feedbacks as $fb): ?>
            <li>
                <strong>Avaliação:</strong> <?= (int)$fb['rating'] ?> / 5<br>
                <?php if ($fb['comentario']): ?>
                    <em><?= htmlspecialchars($fb['comentario']) ?></em><br>
                <?php endif; ?>
                <?php if ($fb['precos']): ?>
                    <small>Preços: <?= htmlspecialchars($fb['precos']) ?></small>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Sem comentários ainda.</p>
    <?php endif; ?>

    <a href="viagem.php?id=<?= (int)($detalhes['viagem_id'] ?? 0) ?>">← Voltar à Viagem</a>
</body>
</html>
