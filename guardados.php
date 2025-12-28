<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/posts.php';



if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$current_user = $_SESSION['username'];


$db = getDatabaseConnection();
$posts = getguardados($db, $current_user);
$_SESSION['last_page'] = 'guardados.php';

$css_especifico = 'styleguardados.css';

include_once 'templates/header_tpl.php';

?>

<!DOCTYPE html>


    <main class="feed-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($current_user); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($posts)): ?>
            <div class="sem-posts">
                <p>Sem viagens guardadas.</p>
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

<?php include_once 'templates/footer_tpl.php'; ?>