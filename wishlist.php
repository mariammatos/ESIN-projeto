<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/users.php';
require_once 'database/destinos.php';



if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$current_user = $_SESSION['username'];


$db = getDatabaseConnection();
$wishlist_id = getuserwishlist($db, $current_user);
$destinos = getwishlistdestinos($db, $wishlist_id);


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
        <h1>Bem-vindo, <?php echo htmlspecialchars($current_user); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($destinos)): ?>
            <div class="sem-destinos">
                <p>Sem destinos na WishList.</p>
                <p>Que tal começar a <a href="explorar.php">Explorar</a> novos locais?</p>
            </div>
        <?php else: ?>
            <?php foreach ($destinos as $destino): ?>
                <article class="post-viagem">
                    <div class="post-header">
                        <h2><?php echo htmlspecialchars($destino['cidade_local']); ?>, <?php echo htmlspecialchars($destino['pais']); ?></h2>
                    </div>
                    
                    <div class="post-detalhes">
                        <p><a href="explorar_destino.php?destino=<?php echo $destino['id']; ?>">Descobrir mais sobre <?php echo htmlspecialchars($destino['cidade_local']); ?>...</a></p>
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