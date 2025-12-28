<?php
  session_start();
  $current_user = $_SESSION['username'] ?? null;
  require_once 'database/db_connect.php';
  require_once 'database/posts.php';
  $db = getDatabaseConnection();
  $posts = getexplorar($db, 4);
  $_SESSION['last_page'] = 'index.php';
  $pagina_atual = 'index';
  include_once 'templates/header_tpl.php';


?>


<!DOCTYPE html>
    <main>
        <section class="hero">
            <h1>Partilhe as Suas Aventuras. Planeie a Sua Próxima Viagem.</h1>
            <p>Ligue-se a viajantes de diversos países, partilhe experiências e descubra novos destinos de forma interativa e personalizada.</p>
            <a href="explorar.php" class="btn-cta">Comece a Viajar Hoje!</a>
        </section>

        <section id="descobrir" class="destaques">
            <h2>As Viagens mais Inspiradoras</h2>
            <div class="cartoes-viagem">
                <?php foreach ($posts as $post): ?>
                    <article class="cartao">
                        <h3>"<?= htmlspecialchars($post['titulo']); ?>"</h3>
                        <p>por <?= htmlspecialchars($post['nome']); ?></p>
                        <p class="destino"><?= htmlspecialchars($post['cidade_local']); ?>, <?= htmlspecialchars($post['pais']); ?></p>
                        <a href="viagem.php?id=<?= $post['id']; ?>">Ver Viagem</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="sobre" class="sobre-nos">
            <div class="sobre-conteudo">
                    <h2>Sobre o TripTales</h2>
                    <p>O TripTales nasceu da paixão por viagens de três raparigas, unidas pelo desejo de descobrir o mundo, colecionar histórias e partilhar experiências inesquecíveis. Cada lugar, cada sabor, cada experiência e cada pôr-do-sol é uma pequena aventura que queremos transformar em memórias para partilhar.</p>
                    
                    <p>No TripTales, acreditamos que viajar é muito mais do que o destino: são momentos, emoções e conexões. Aqui vais encontrar dicas, relatos e inspirações para tornar cada viagem única.</p>

                    <p class="destaque-texto">Porque viajar é contar histórias, e cada história merece ser vivida e lembrada. Junta-te a nós, e atreve-te a escrever a tua próxima aventura!</p>

                    <div class="equipa-links">
                        <p>Queres saber mais sobre as nossas próprias aventuras?</p>
                        <div class="botoes-perfil">
                            <a href="perfil.php?user=maria.m.matos" class="btn-perfil">@maria.m.matos</a> 
                            <a href="perfil.php?user=marta.filipee" class="btn-perfil">@marta.filipee</a> 
                            <a href="perfil.php?user=saravgouveia" class="btn-perfil">@saravgouveia</a>
                        </div>
                    </div>
            </div>
        </section>

    </main>

<?php include_once 'templates/footer_tpl.php'; ?>