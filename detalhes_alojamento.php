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

$media_real = 0;
$total_reviews = count($feedbacks);

if ($total_reviews > 0) {
    $soma_ratings = 0;
    foreach ($feedbacks as $fb) {
        $soma_ratings += (int)$fb['rating'];
    }
    $media_real = $soma_ratings / $total_reviews;
} else {
    $media_real = null;
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
                <<strong><?= $media_real !== null ? number_format($media_real, 1) . ' / 5.0' : '---' ?></strong>
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
                            <?php if ($fb['precos'] == 0): ?>
                                <p class="fb-precos"> Preço: <span>Gratuito</span></p>
                            <?php elseif ($fb['precos'] == 1): ?>
                                <p class="fb-precos"> Preço: <span>$</span></p>
                            <?php elseif ($fb['precos'] == 2): ?>
                                <p class="fb-precos"> Preço: <span>$$</span></p>
                            <?php elseif ($fb['precos'] == 3): ?>
                                <p class="fb-precos"> Preço: <span>$$$</span></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-feedback">Ainda não existem comentários.</p>
            <?php endif; ?>
        </div>

        
        <?php if ($_SESSION['last_page'] === 'explorar.php'): ?>
            <a href="explorar.php?search_user=<?= $_SESSION['search_user'] ?>&search_viagem=<?= $_SESSION['search_viagem'] ?>&search_alojamento=<?= $_SESSION['search_alojamento'] ?>&search_atividade=<?= $_SESSION['search_atividade'] ?>" class="btn-voltar-viagem">← Voltar à Pesquisa</a>
            <?php unset($_SESSION['last_page']); ?>
        <?php else: ?>
            <a href="viagem.php?id=<?= (int)($detalhes['viagem_id'] ?? 0) ?>" class="btn-voltar-viagem">← Voltar à Viagem</a>
        <?php endif; ?>

    </main>
<?php include_once 'templates/footer_tpl.php'; ?>  
