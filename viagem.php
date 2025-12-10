<?php
// Inclui o ficheiro que faz a ligação à base de dados.
require_once 'database/db_connect.php';
require_once 'database/posts.php';

// --- 1. LÓGICA DE AUTENTICAÇÃO E BUSCA DE DADOS ---

// SIMULAÇÃO: Aqui, o seu código real iria verificar a sessão para obter o nome de utilizador logado.
// Usamos 'sara' como um utilizador de teste por agora.
$id_viagem = (int)$_GET['id'];

// Consulta SQL para obter as publicações das pessoas que o utilizador segue.
// Esta consulta junta Viagens (V) com Utilizador (U) e Seguir (S).
$db = getDatabaseConnection();
$viagem = getViagemDetalhes($db, $id_viagem);
$likes = getViagemLikes($db, $id_viagem);
$likes_count = getViagemLikesCount($db, $id_viagem);

session_start();
$current_user = $_SESSION['username'] ?? null;
$user_liked = $current_user ? userLikedViagem($db, $id_viagem, $current_user) : false;

// --- 2. APRESENTAÇÃO HTML/CSS ---
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($viagem['titulo']); ?> | TripTales</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        </header>

    <main class="viagem-detalhe-container">
        <a href="feed.php" class="btn-voltar">← Voltar ao Feed</a>

        <h1><?php echo htmlspecialchars($viagem['titulo']); ?></h1>
        
        <div class="autor-info">
            Publicado por: <a href="profile.php?user=<?php echo htmlspecialchars($viagem['nome_de_utilizador']); ?>">@<?php echo htmlspecialchars($viagem['nome_de_utilizador']); ?></a> (<?php echo htmlspecialchars($viagem['nome']); ?>)
        </div>
        
        <section class="informacao-base">
            <h2>🌍 Destino e Datas</h2>
            <p><strong>Local:</strong> <?php echo htmlspecialchars($viagem['cidade_local']); ?>, <?php echo htmlspecialchars($viagem['pais']); ?></p>
            <p><strong>De:</strong> <?php echo htmlspecialchars($viagem['data_ida']); ?> <strong>A:</strong> <?php echo htmlspecialchars($viagem['data_volta'] ?? 'Em andamento'); ?></p>
        </section>

        <section class="travel-journal">
            <h2>📔 Travel Journal</h2>
            <p class="journal-texto"><?php echo nl2br(htmlspecialchars($viagem['journal_descricao'])); ?></p>
            <p>Avaliação Final: <?php echo htmlspecialchars($viagem['journal_avaliacao'] ?? 'N/A'); ?>/5</p>
            </section>

        <section class="atividades-alojamentos">
            <h2>🗺️ Atividades e Alojamentos</h2>
            <p>Esta secção será preenchida com as Atividades e Alojamentos registados durante a viagem.</p>
        </section>

        <section class="like">
            <?php if ($current_user): ?>
                <form action="actions/action_like.php" method="get">
                    <input type="hidden" name="post_id" value="<?php echo $id_viagem; ?>">
                    <button type="submit" ><?php echo $user_liked ? 'Liked' : 'Like'; ?>
                    </button>
                </form>
                <span><?php echo $likes_count; ?> likes</span>
            <?php else: ?>
                <p>Faça login para dar like.</p>
                <span><?php echo $likes_count; ?> likes</span>
            <?php endif; ?>
        </section>


        <section class="comentarios">
            <h2>💬 Comentários</h2>
            <p>...</p>
        </section>

    </main>

    <footer>
        </footer>

</body>
</html>