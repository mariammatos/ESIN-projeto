<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/media.php';
require_once 'database/posts.php';
require_once 'database/users.php';

if (isset($_GET['user'])) {
    $user = $_GET['user'];     
} else {
    $user = $_SESSION['username']; 
}

if (!isset($_SESSION['username'])) {
    $_SESSION['next_page'] = 'perfil.php?user=' . urlencode($user);
    header("Location: login.php");
    exit();
}

$dbh = getDatabaseConnection();


$utilizador = getuserdetails($dbh, $user);

if (!$utilizador) {
    echo "<h2>Utilizador não encontrado.</h2>";
    exit();
}

$perfil_user = $utilizador['nome_de_utilizador'];
$current_user = $_SESSION['username'];

$segue = false;

if ($perfil_user !== $current_user) {
    $segue = usersegue($dbh, $current_user, $perfil_user);
}

$viagens = getViagensUtilizador($dbh, $user);
$_SESSION['last_page'] = 'perfil.php?user=' . urlencode($perfil_user);

$css_especifico = 'styleperfil.css';

include_once 'templates/header_tpl.php';

?>
<!DOCTYPE html>

<body>
<main class="perfil-container">
 <?php if ($perfil_user === $current_user): ?>
            <section class="perfil-menu-barra">
                <div class="menu-spacer"></div>

                <div class="menu-links-centrais">
                    <a href="guardados.php">Viagens guardadas</a>
                    <a href="wishlist.php">Wishlist 🔖</a>
                </div>

                <div class="menu-botao-editar">
                    <form action="registration.php" method="post">
                        <input type="hidden" name="username" value="<?= $perfil_user ?>">
                        <input type="hidden" name="nome" value="<?= htmlspecialchars($utilizador['nome'] ?? '') ?>">
                        <input type="hidden" name="pais" value="<?= htmlspecialchars($utilizador['pais_de_origem'] ?? '') ?>">
                        <input type="hidden" name="pref" value="<?= htmlspecialchars($utilizador['preferencia_de_viagem'] ?? '') ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($utilizador['email'] ?? '') ?>">
                        <input type="hidden" name="foto_perfil" value="<?= htmlspecialchars($utilizador['foto_de_perfil'] ?? '') ?>">
                        <input type="hidden" name="editar" value="1">
                        <button type="submit">Editar Perfil</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <section class="perfil-info">
            <div class="foto-wrapper">
                <img src="media/profile_pictures/<?= htmlspecialchars($utilizador['foto_de_perfil']) ?>" 
                     alt="Foto de Perfil" 
                     class="foto-perfil">
            </div>
            
            <h1>@<?= htmlspecialchars($utilizador['nome_de_utilizador']) ?></h1>
            <h2><?= htmlspecialchars($utilizador['nome']) ?></h2>

            <div class="detalhes-texto">
                <p><strong>País de origem:</strong> <?= htmlspecialchars($utilizador['pais_de_origem']) ?></p>
                <p><strong>Preferências de viagem:</strong> <?= htmlspecialchars($utilizador['preferencia_de_viagem']) ?></p>
            </div>

            <?php if ($perfil_user !== $current_user): ?>
                <form action="actions/action_seguir.php" method="post">
                    <input type="hidden" name="seguido" value="<?= htmlspecialchars($perfil_user) ?>">
                    <button type="submit" class="btn-follow <?= $segue ? 'following' : '' ?>">
                        <?= $segue ? 'Deixar de seguir' : 'Seguir' ?>
                    </button>
                </form>
            <?php endif; ?>
        </section>

        <section class="perfil-viagens">
            <h2 class="titulo-seccao">Viagens Publicadas</h2>

            <?php if (empty($viagens)): ?>
                <div class="sem-viagens">
                    <p>Este utilizador ainda não publicou viagens.</p>
                </div>
            <?php else: ?>
                <div class="lista-viagens">
                    <?php foreach ($viagens as $v): ?>
                        <article class="cartao">
                            <?php 
                                $fotos_post = getFotos($dbh, $v['id']);
                                if (!empty($fotos_post)):
                                    $foto_principal = $fotos_post[0];
                            ?>
                                <div class="post-foto">
                                    <img src="<?= htmlspecialchars($foto_principal['path']); ?>" 
                                         alt="Foto da viagem <?= htmlspecialchars($v['titulo']); ?>">
                                </div>
                            <?php endif; ?>

                            <div class="cartao-conteudo">
                                <h3><?= htmlspecialchars($v['titulo']) ?></h3>
                                <p class="localizacao">
                                    📍 <?= htmlspecialchars($v['cidade_local']) ?>, <?= htmlspecialchars($v['pais']) ?>
                                </p>
                                <a href="viagem.php?id=<?= $v['id'] ?>" class="btn-ver-viagem">Ver Viagem</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <?php include_once 'templates/footer_tpl.php'; ?>

</body>
</html>

<?php include_once 'templates/footer_tpl.php'; ?>
