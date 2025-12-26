<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';



if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}
$current_user = $_SESSION['username'];

$destino_id = $_GET['destino'] ?? '';

$db = getDatabaseConnection();
$destino = getDestino($db, $destino_id);
$posts = getPostsporDestino($db, $destino_id);

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed | TripTales</title>
    <link rel="stylesheet" href="css/stylefeed.css">
    </head>
<body>

    <header>
        <nav>
            <div class="logo">
                <a href="index.html">
                    <img src="logo TripTales.png" alt="TripTales Logo">
                    <span>TripTales</span>
                </a>
            </div>
            <ul>
                <li><a href="feed.php">Feed</a></li>
                <li><a href="explorar.php">Explorar</a></li>
                <li><a href="perfil.php?user=<?php echo htmlspecialchars($current_user); ?>">Perfil</a></li>
                <li><a href="logout.php" class="btn-logout">Sair</a></li>
                <li><a href="nova_viagem.php" class="btn-novaviagem">Nova Viagem</a></li>
            </ul>
        </nav>
    </header>

    <main class="feed-container">
        <h1>Tudo o que os nossos utilizadores pensam sobre <?php echo htmlspecialchars($destino['cidade_local']); ?>, <?php echo htmlspecialchars($destino['pais']); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($posts)): ?>
            <div class="sem-posts">
                <p>Ninguém que segue publicou uma viagem recentemente.</p>
                <p>Que tal começar a <a href="explorar.php">Explorar</a> novos viajantes?</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-viagem">
                    <div class="post-header">
                        <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                        <span class="autor">
                            por 
                            <a href="perfil.php?user=<?= urlencode($post['nome_de_utilizador']) ?>">
                                @<?= htmlspecialchars($post['nome_de_utilizador']) ?> (<?= htmlspecialchars($post['nome']) ?>)
                            </a>
                        </span>
                    </div>
                    
                    <div class="post-detalhes">
                        <p><strong>Destino:</strong> <?php echo htmlspecialchars($post['cidade_local']); ?>, <?php echo htmlspecialchars($post['pais']); ?></p>
                        <p><a href="viagem.php?id=<?php echo $post['id']; ?>">Ver todos os detalhes da viagem...</a></p>
                    </div>

                    <div class="post-interacoes">
                        <?php $likes_count = getViagemLikesCount($db, $post['id']); ?>
                        <?php $comentarios_count = getViagemComentariosCount($db, $post['id']); ?>
                        <span><?php echo $likes_count; ?> Likes</span> | <span><?php echo $comentarios_count; ?> Comentários</span>
                    </div>
                    
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>

</body>
</html>