<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/users.php';


if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}
$current_user = $_SESSION['username'];
$pesquisa_user = $_GET['search_user'] ?? '';
$pesquisa_viagem = $_GET['search_viagem'] ?? '';


$db = getDatabaseConnection();
if (!empty($pesquisa_user)) {
    $posts = [];
    $user_ids = procurarusers($db, $pesquisa_user);
    $user_matches = [];
        foreach ($user_ids as $uid) {
            $user_matches[] = getUserDetails($db, $uid);
        }
} elseif (!empty($pesquisa_viagem)) {
    $posts = procurarviagens($db, $pesquisa_viagem);
} else {
$posts = getexplorar($db);
}


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
            <div class="logo">TripTales</div>
            <ul>
                <li><a href="feed.php">Feed</a></li>
                <li><a href="explorar.php">Explorar</a></li>
                <li><a href="perfil.php?user=<?php echo htmlspecialchars($current_user); ?>">Perfil</a></li>
                <li><a href="logout.php" class="btn-logout">Sair</a></li>
                <li><a href="nova_viagem.php" class="btn-novaviagem">Nova Viagem</a></li>
            </ul>
        </nav>
    </header>

    <main>

        <div class= "pesquisar">
            <form action="explorar.php" method="get">
                <input type="text" name="search_user" placeholder="Procurar pessoas..." required>
                <button type="submit">Pesquisar Utilizadores</button>
            </form>
            <form action="explorar.php" method="get">
                <input type="text" name="search_viagem" placeholder="Procurar viagens, destinos..." required>
                <button type="submit">Pesquisar Viagens</button>
            </form>
        </div>

        <div class="feed-container">
            <?php if (!empty($pesquisa_user) && !empty($user_matches)): ?>
                <!-- Mostrar utilizadores -->
                <?php foreach ($user_matches as $user): ?>
                    <article class="post-usuario">
                        <div class="post-header">
                            <img 
                                src="media/profile_pictures/<?php echo htmlspecialchars($user['foto_de_perfil']); ?>" 
                                alt="Foto de <?php echo htmlspecialchars($user['foto_de_perfil']); ?>" 
                                class="foto-perfil" 
                            >
                            <h2><?php echo htmlspecialchars($user['nome_de_utilizador']); ?></h2>
                            <span class="autor"><?php echo htmlspecialchars($user['nome']); ?></span>
                        </div>
                        <div class="post-detalhes">
                            <p><a href="perfil.php?user=<?php echo htmlspecialchars($user['nome_de_utilizador']); ?>">Ver perfil</a></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php elseif (!empty($posts)): ?>
                <!-- Mostrar posts -->
                <?php foreach ($posts as $post): ?>
                    <article class="post-viagem">
                        <div class="post-header">
                            <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                            <span class="autor">por <a href="perfil.php?user=<?php echo htmlspecialchars($post['nome_de_utilizador']); ?>">@<?php echo htmlspecialchars($post['nome_de_utilizador']); ?> (<?php echo htmlspecialchars($post['nome']); ?>)</a></span>
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
            <?php else: ?>
                <p>Nenhum resultado encontrado.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>

</body>
</html>