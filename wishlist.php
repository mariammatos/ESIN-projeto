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

$css_especifico = 'stylefeed.css';

include_once 'templates/header_tpl.php';


?>

<!DOCTYPE html>


    <main class="feed-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($current_user); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($destinos)): ?>
            <div class="sem-posts">
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

<?php include_once 'templates/footer_tpl.php'; ?>