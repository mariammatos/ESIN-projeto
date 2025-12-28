<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/media.php';
require_once 'database/users.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); 
    exit();
}
$current_user = $_SESSION['username'];

$db = getDatabaseConnection();
$posts = getFeed($db, $current_user);

$_SESSION['last_page'] = 'feed.php';
$css_especifico = 'stylefeed.css';

include_once 'templates/header_tpl.php';


?>

<!DOCTYPE html>

    <main class="feed-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($current_user); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($posts)): ?>
            <div class="sem-posts">
                <p>Ninguém que segue publicou uma viagem recentemente.</p>
                <p>Que tal começar a <a href="explorar.php">Explorar</a> novos viajantes?</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $viagem): ?>
                <?php include 'templates/feed_tpl.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

<?php include_once 'templates/footer_tpl.php'; ?>