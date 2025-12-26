<?php
  session_start();
  $current_user = $_SESSION['username'] ?? null;

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripTales | A Sua Comunidade Global de Viagens</title>

    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="index.php">Início</a></li>
                <li><a href="#descobrir">Descobrir</a></li>
                <li><a href="#sobre">Sobre Nós</a></li>
                <?php if (!$current_user): ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                    <li><a href="registration.php" class="btn-signup">Registar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Partilhe as Suas Aventuras. Planeie a Sua Próxima Viagem.</h1>
            <p>Ligue-se a viajantes de diversos países, partilhe experiências e descubra novos destinos de forma interativa e personalizada[cite: 139].</p>
            <a href="explorar.php" class="btn-cta">Comece a Viajar Hoje!</a>
        </section>

        <section id="descobrir" class="destaques">
            <h2>As Viagens mais Inspiradoras</h2>
            <div class="cartoes-viagem">
                <article class="cartao">
                    <h3>"Um Verão no Porto"</h3>
                    <p>por Maria Matos</p>
                    <p class="destino">Porto, Portugal</p>
                    <a href="#">Ver Viagem</a>
                </article>
                </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 TripTales. Todos os direitos reservados.</p>
        <p>Projeto ESIN</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>