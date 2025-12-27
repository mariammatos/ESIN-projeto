<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/alojamentos.php';
require_once 'database/users.php';
require_once 'database/media.php';


if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}
$current_user = $_SESSION['username'];
$pesquisa_user = $_GET['search_user'] ?? '';
$pesquisa_viagem = $_GET['search_viagem'] ?? '';
$pesquisa_alojamento = $_GET['search_alojamento'] ?? '';



$db = getDatabaseConnection();
if (!empty($pesquisa_user)) {
    $posts = [];
    $user_ids = procurarusers($db, $pesquisa_user);
    $user_matches = [];
    foreach ($user_ids as $uid) {
        $user_matches[] = getUserDetails($db, $uid);
    }
    $alojamentos_matches = [];
} elseif (!empty($pesquisa_viagem)) {
    $posts = procurarviagens($db, $pesquisa_viagem);
    $alojamentos_matches = [];
} elseif (!empty($pesquisa_alojamento)) {
    // Pesquisa alojamentos por nome ou localização
    require_once 'database/destinos.php';
    $alojamentos_matches = procurarAlojamentosGlobais($db, $pesquisa_alojamento);
    $posts = [];
} else {
    $posts = getexplorar($db);
    $alojamentos_matches = [];
}


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed | TripTales</title>
    <link rel="stylesheet" href="css/styleexplorar.css">
    </head>
<body>

    <header>
        <nav>
            <div class="logo">
                <a href="index.php">
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
            <form action="explorar.php" method="get">
                <input type="text" name="search_alojamento" placeholder="Procurar alojamentos..." required>
                <button type="submit">Pesquisar Alojamentos</button>
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
            <?php elseif (!empty($alojamentos_matches)): ?>
                <!-- Mostrar alojamentos encontrados -->
                <?php foreach ($alojamentos_matches as $a): ?>
                    <article class="post-alojamento">
                        <div class="post-header">
                            <h2>
                                <a href="detalhes_alojamento.php?id=<?= $a['detalhe_id'] ?>">
                                    <?= htmlspecialchars($a['nome_alojamento']) ?>
                                </a>
                            </h2>

                            <span class="tipo">Tipo: <?php echo htmlspecialchars($a['tipo_alojamento']); ?></span>
                        </div>
                        <div class="post-detalhes">
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($a['localizacao']); ?></p>
                            <p><strong>Rating Global:</strong> <?php echo $a['media_avaliacao'] !== null ? number_format($a['media_avaliacao'], 1) . ' / 5' : 'Sem avaliações'; ?></p>
                            <a href="detalhes_alojamento.php?id=<?= $a['detalhe_id'] ?>">Ver detalhes do alojamento...</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php elseif (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-viagem">
                        <div class="post-header">
                            <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                            <span class="autor">por <a href="perfil.php?user=<?php echo htmlspecialchars($post['nome_de_utilizador']); ?>">@<?php echo htmlspecialchars($post['nome_de_utilizador']); ?> (<?php echo htmlspecialchars($post['nome']); ?>)</a></span>
                        </div>
                        
                        <div class="post-detalhes">
                                    <?php 
                                        $fotos_post = getFotos($db, $post['id']); // todas as fotos da viagem
                                        if (!empty($fotos_post)):
                                            $foto_principal = $fotos_post[0]; // a de menor id
                                    ?>
                                        <div class="post-foto">
                                            <img src="<?= htmlspecialchars($foto_principal['path']); ?>" 
                                                alt="Foto da viagem <?= htmlspecialchars($post['titulo']); ?>" 
                                                width="200" height="200">
                                        </div>
                                    <?php endif; ?>
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