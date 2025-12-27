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
    <link rel="stylesheet" href="css/styledetalhesaloj.css">
</head>
<body>
 <main class="detalhes-card">
        
        <h2><?= htmlspecialchars($detalhes['nome_alojamento']) ?></h2>
        <p class="tipo-tag"><?= htmlspecialchars($detalhes['tipo_alojamento']) ?></p>

        <div class="info-geral">
            <p><strong>📍 Localização:</strong> <?= htmlspecialchars($detalhes['localizacao']) ?></p>
            <div class="rating-badge">
                <span>Rating Global</span>
                <strong><?= $detalhes['media_avaliacao'] !== null ? number_format($detalhes['media_avaliacao'], 1) . ' / 5.0' : '---' ?></strong>
            </div>
        </div>

        <hr>

        <h3>Comentários de Feedback</h3>
        <div class="lista-feedback">
            <?php if ($feedbacks): ?>
                <?php foreach ($feedbacks as $fb): ?>
                    <div class="feedback-item">
                        <div class="fb-header">
                            <span class="stars"><?= str_repeat('⭐', (int)$fb['rating']) ?></span>
                            <span class="fb-rating"><?= (int)$fb['rating'] ?>/5</span>
                        </div>
                        
                        <?php if ($fb['comentario']): ?>
                            <p class="fb-comentario">"<?= htmlspecialchars($fb['comentario']) ?>"</p>
                        <?php endif; ?>
                        
                        <?php if ($fb['precos']): ?>
                            <p class="fb-precos">💰 Preço: <span><?= htmlspecialchars($fb['precos']) ?>€</span></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-feedback">Ainda não existem comentários para este alojamento.</p>
            <?php endif; ?>
        </div>

        <a href="viagem.php?id=<?= (int)($detalhes['viagem_id'] ?? 0) ?>" class="btn-voltar-viagem">← Voltar à Viagem</a>
    </main>
</body>
</html>
