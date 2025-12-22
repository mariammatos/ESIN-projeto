<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';


// --- 1. LÓGICA DE AUTENTICAÇÃO E BUSCA DE DADOS ---

// SIMULAÇÃO: Aqui, o seu código real iria verificar a sessão para obter o nome de utilizador logado.
// Usamos 'sara' como um utilizador de teste por agora.
// $current_user = 'mariasouza'; 
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Se falhar, volta para a página de login
    exit();
}
$current_user = $_SESSION['username'];

// Consulta SQL para obter as publicações das pessoas que o utilizador segue.
// Esta consulta junta Viagens (V) com Utilizador (U) e Seguir (S).
$db = getDatabaseConnection();
$posts = getFeed($db, $current_user);

// --- 2. APRESENTAÇÃO HTML/CSS ---
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
                <li><a href="explore.php">Explorar</a></li>
                <li><a href="perfil.php?user=<?php echo htmlspecialchars($current_user); ?>">Perfil</a></li>
                <li><a href="logout.php" class="btn-logout">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main class="feed-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($current_user); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($posts)): ?>
            <div class="sem-posts">
                <p>Ninguém que segue publicou uma viagem recentemente.</p>
                <p>Que tal começar a <a href="explore.php">Explorar</a> novos viajantes?</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-viagem">
                    <div class="post-header">
                        <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                        <span class="autor">por <a href="profile.php?user=<?php echo htmlspecialchars($post['nome_de_utilizador']); ?>">@<?php echo htmlspecialchars($post['nome_de_utilizador']); ?> (<?php echo htmlspecialchars($post['nome']); ?>)</a></span>
                    </div>
                    
                    <div class="post-detalhes">
                        <p><strong>Destino:</strong> <?php echo htmlspecialchars($post['cidade_local']); ?>, <?php echo htmlspecialchars($post['pais']); ?></p>
                        <p><a href="viagem.php?id=<?php echo $post['id']; ?>">Ver todos os detalhes da viagem...</a></p>
                    </div>

                    <div class="post-interacoes">
                        <span>15 Likes</span> | <span>3 Comentários</span>
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