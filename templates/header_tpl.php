<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripTales | A Sua Comunidade Global de Viagens</title>

    <link rel="stylesheet" href="css/style.css">
    <?php if (isset($css_especifico)): ?>
        <link rel="stylesheet" href="css/<?php echo $css_especifico; ?>">
    <?php endif; ?>
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
                <?php if ($pagina_atual == 'index'): ?>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="#descobrir">Descobrir</a></li>
                    <li><a href="#sobre">Sobre Nós</a></li>
                <?php endif; ?>
                <?php if (!$current_user): ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                    <li><a href="registration.php" class="btn-signup">Registar</a></li>
                <?php endif; ?>
                <?php if ($current_user): ?>
                    <li><a href="feed.php">Feed</a></li>
                    <li><a href="explorar.php">Explorar</a></li>
                    <li><a href="perfil.php?user=<?php echo htmlspecialchars($current_user); ?>">Perfil</a></li>
                    <li><a href="logout.php" class="btn-logout">Sair</a></li>
                    <li><a href="nova_viagem.php" class="btn-novaviagem">Nova Viagem</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>