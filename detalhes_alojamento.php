<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/alojamentos.php';


$db = getDatabaseConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'alojamento';

if (!$id || !in_array($tipo, ['alojamento', 'atividade'])) {
    die('Item inválido.');
}


if ($tipo === 'alojamento') {
    $detalhes = getDetalhesAlojamentoCompleto($db, $id);
    $feedbacks = getFeedbacksAlojamento($db, $detalhes['detalhe_id']);
    $label_tipo = 'Alojamento';
} else {
    $detalhes = getDetalhesAtividadeCompleto($db, $id);
    $feedbacks = getFeedbacksAtividade($db, $detalhes['detalhe_id']);
    $label_tipo = 'Atividade';
}


if (!$detalhes) {
    die("$label_tipo não encontrado.");
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes - <?= $label_tipo ?>  | TripTales</title>
    <link rel="stylesheet" href="css/styledetalhesaloj.css">
</head>
<body>
 <main class="detalhes-card">
        
        <h2>
        <?= htmlspecialchars(
            $tipo === 'atividade'
                ? $detalhes['nome_atividade']
                : $detalhes['nome_alojamento']
        ) ?>
        </h2>

        <p class="tipo-tag">
        <?= htmlspecialchars(
            $tipo === 'atividade'
                ? $detalhes['tipo_atividade']
                : $detalhes['tipo_alojamento']
        ) ?>
        </p>

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
            <?php if (!empty($feedbacks)): ?>
                <?php foreach ($feedbacks as $fb): ?>
                    <div class="feedback-item">
                        <div class="fb-header">
                            <span class="stars"><?= str_repeat('★', (int)$fb['rating']) ?></span>
                            <span class="fb-rating"><?= (int)$fb['rating'] ?>/5</span>
                        </div>
                        
                        <?php if (!empty($fb['comentario'])): ?>
                            <p class="fb-comentario">"<?= htmlspecialchars($fb['comentario']) ?>"</p>
                        <?php endif; ?>
                        
                        <?php if (!empty($fb['precos'])): ?>
                            <p class="fb-precos">💰 Preço: <span><?= htmlspecialchars($fb['precos']) ?>€</span></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-feedback">Ainda não existem comentários.</p>
            <?php endif; ?>
        </div>

        <a href="explorar.php?id=<?= (int)($detalhes['viagem_id'] ?? 0) ?>" class="btn-voltar-viagem">← Voltar</a>
    </main>
</body>
</html>
