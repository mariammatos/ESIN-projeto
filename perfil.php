<?php
session_start();
require_once 'database/db_connect.php';

// Se não estiver login, manda para login
if (!isset($_SESSION['username'])) {
    header("Location: loginlogin.php");
    exit();
}

$dbh = getDatabaseConnection();

// --- 1. Obter utilizador do perfil ---
if (isset($_GET['user'])) {
    $user = $_GET['user'];      // ver perfil de outro utilizador
} else {
    $user = $_SESSION['username']; // ver o próprio perfil
}

// Query do utilizador
$stmt = $dbh->prepare("SELECT * FROM Utilizador WHERE nome_de_utilizador = ?");
$stmt->execute([$user]);
$utilizador = $stmt->fetch();

if (!$utilizador) {
    echo "<h2>Utilizador não encontrado.</h2>";
    exit();
}

// --- 2. Obter viagens do utilizador ---
$stmt = $dbh->prepare("
    SELECT V.id, V.titulo, D.cidade_local, D.pais
    FROM Viagens V 
    JOIN Destino D ON V.destino = D.id
    WHERE V.utilizador = ?
");
$stmt->execute([$user]);
$viagens = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil - <?= htmlspecialchars($utilizador['nome_de_utilizador']) ?></title>
    <link rel="stylesheet" href="css/styleperfil.css">
</head>

<body>

<header>
    <nav>
        <div class="logo">TripTales</div>
        <ul>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
</header>

<main class="perfil-container">

    <section class="perfil-info">
        <img width="50" height= "50" src="media/<?= htmlspecialchars($utilizador['foto_de_perfil']) ?>" class="foto-perfil">
        <h1>@<?= htmlspecialchars($utilizador['nome_de_utilizador']) ?></h1>
        <h2><?= htmlspecialchars($utilizador['nome']) ?></h2>

        <p><strong>País de origem:</strong> <?= htmlspecialchars($utilizador['pais_de_origem']) ?></p>
        <p><strong>Preferências de viagem:</strong> <?= htmlspecialchars($utilizador['preferencia_de_viagem']) ?></p>
    </section>

    <section class="perfil-viagens">
        <h2>Viagens Publicadas</h2>

        <?php if (count($viagens) === 0): ?>
            <p>Este utilizador ainda não publicou viagens.</p>
        <?php else: ?>
            <div class="lista-viagens">
                <?php foreach ($viagens as $v): ?>
                    <article class="cartao">
                        <h3><?= htmlspecialchars($v['titulo']) ?></h3>
                        <p><?= htmlspecialchars($v['cidade_local']) ?>, <?= htmlspecialchars($v['pais']) ?></p>
                        <a href="viagem.php?id=<?= $v['id'] ?>">Ver Viagem</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<footer>
    <p>&copy; 2025 TripTales</p>
</footer>

</body>
</html>
