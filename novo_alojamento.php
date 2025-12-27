<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/destinos.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();

$viagem_id = (int)($_GET['viagem_id'] ?? 0);

// Buscar info da viagem e destino
$stmt = $db->prepare('SELECT V.id, V.data_ida, V.data_volta, D.id AS destino_id, D.cidade_local, D.pais 
                      FROM Viagens V 
                      JOIN Destino D ON V.destino = D.id 
                      WHERE V.id = ?');
$stmt->execute([$viagem_id]);
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializa array de alojamentos encontrados
$alojamentos_matches = [];

if (!empty($_POST['alojamento'])) {
    $busca = trim($_POST['alojamento']);
    $alojamentos_matches = procurarAlojamentosPorDestino($db, $viagem['destino_id'], $busca);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Alojamento | TripTales</title>
    <link rel="stylesheet" href="css/stylenova_viagem.css">
</head>
<body>
    <h2>Adicionar Alojamento à viagem: <?= htmlspecialchars($viagem['cidade_local'] . ', ' . $viagem['pais']) ?></h2>

<!-- Formulário de pesquisa -->
<form action="" method="post">
    <label for="alojamento">Procurar alojamento existente:</label>
    <input type="text" id="alojamento" name="alojamento">
    <button type="submit">Procurar</button>
</form>

<?php if (!empty($alojamentos_matches)): ?>
    <!-- Formulário para escolher alojamento existente -->
    <h3>Escolha um alojamento existente:</h3>
    <form action="actions/action_adicionaralojamento.php" method="post">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
        <?php foreach ($alojamentos_matches as $a): ?>
            <input type="radio" name="detalhe_id" value="<?= $a['id'] ?>" required>
            <?= htmlspecialchars($a['nome'] . ' (' . $a['localizacao'] . ')') ?><br>
        <?php endforeach; ?>
        <label for="data_inicio">Data Início:</label>
        <input type="date" name="data_inicio" required
               min="<?= $viagem['data_ida'] ?>" 
               max="<?= $viagem['data_volta'] ?? '' ?>">
        <label for="data_fim">Data Fim:</label>
        <input type="date" name="data_fim"
               min="<?= $viagem['data_ida'] ?>" 
               max="<?= $viagem['data_volta'] ?? '' ?>">
        <button type="submit">Adicionar Alojamento</button>
    </form>

<?php elseif (isset($_POST['alojamento']) && empty($alojamentos_matches)): ?>
    <!-- Formulário para adicionar novo alojamento -->
    <h3>Nenhum alojamento encontrado. Adicione um novo:</h3>
    <form action="actions/action_adicionaralojamento.php" method="post">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
        <label for="nome">Nome do Alojamento:</label>
        <input type="text" name="nome" required>
        <label for="localizacao">Localização:</label>
        <input type="text" name="localizacao" required>
        <label for="tipo">Tipo:</label>
        <select name="tipo" required>
            <option value="Hostel">Hostel</option>
            <option value="Hotel">Hotel</option>
            <option value="Alojamento Local">Alojamento Local</option>
            <option value="Outro">Outro</option>
        </select>
        <label for="data_inicio">Data Início:</label>
        <input type="date" name="data_inicio" required
               min="<?= $viagem['data_ida'] ?>" 
               max="<?= $viagem['data_volta'] ?? '' ?>">
        <label for="data_fim">Data Fim:</label>
        <input type="date" name="data_fim"
               min="<?= $viagem['data_ida'] ?>" 
               max="<?= $viagem['data_volta'] ?? '' ?>">
        <button type="submit">Adicionar Novo Alojamento</button>
    </form>
<?php endif; ?>

</body>
</html>

