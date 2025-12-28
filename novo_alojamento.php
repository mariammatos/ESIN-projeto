<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/destinos.php';
require_once 'database/alojamentos.php';

$db = getDatabaseConnection();

$viagem_id = (int)($_GET['viagem_id'] ?? $_POST['viagem_id'] ?? 0);

// estado
$alojamento = isset($_POST['alojamento']);
$atividade  = isset($_POST['atividade']);

// pesquisa
$termo = $_POST['termo'] ?? '';

// buscar viagem
$stmt = $db->prepare('
    SELECT V.id, V.data_ida, V.data_volta, D.id AS destino_id, D.cidade_local, D.pais
    FROM Viagens V
    JOIN Destino D ON V.destino = D.id
    WHERE V.id = ?
');
$stmt->execute([$viagem_id]);
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

// resultados
$matches = [];

if ($alojamento && $termo !== '') {
    $matches = procurarAlojamentosPorDestino($db, $viagem['destino_id'], $termo);
}

if ($atividade && $termo !== '') {
    $matches = procurarAtividadesPorDestino($db, $viagem['destino_id'], $termo);
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
    <h2>Adicionar Alojamento/Atividade à viagem: <?= htmlspecialchars($viagem['cidade_local'] . ', ' . $viagem['pais']) ?></h2>

<!-- Formulário de pesquisa -->
<form method="post">
    <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">

    <button type="submit" name="alojamento">Adicionar Alojamento</button>
    <button type="submit" name="atividade">Adicionar Atividade</button>
</form>

<?php if ($alojamento && !$atividade): ?>
    <h3>Procurar alojamento existente</h3>
    <form method="post">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
        <input type="hidden" name="alojamento" value="1">
        <input type="text" name="termo" placeholder="Nome ou localização">
        <button type="submit">Procurar</button>
    </form>

    <?php if ($termo !== ''): ?>
        <?php if (!empty($matches)): ?>
            <h4>Escolha existente:</h4>
            <form action="actions/action_adicionaralojamento.php" method="post">
                <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
                <input type="hidden" name="alojamento" value="1"> <?php foreach ($matches as $a): ?>
                    <input type="radio" name="detalhe_id" value="<?= $a['id'] ?>" required>
                    <?= htmlspecialchars($a['nome'].' ('.$a['localizacao'].')') ?><br>
                <?php endforeach; ?>

                <label>Data início</label>
                <input type="date" name="data_inicio" required>
                <label>Data fim</label>
                <input type="date" name="data_fim">
                <button type="submit">Adicionar</button>
            </form>
        <?php else: ?>
            <h4>Nenhum encontrado – criar novo alojamento</h4>
            <form action="actions/action_adicionaralojamento.php" method="post">
                <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
                <input type="hidden" name="alojamento" value="1">

                <input name="nome" placeholder="Nome" required>
                <input name="localizacao" placeholder="Localização" required>
                <select name="tipo" required> <option value="Hotel">Hotel</option>
                    <option value="Hostel">Hostel</option>
                    <option value="Alojamento Local">Alojamento Local</option>
                </select>
                <input type="date" name="data_inicio" required>
                <input type="date" name="data_fim">
                <button type="submit">Criar e adicionar</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($atividade && !$alojamento): ?>
    <h3>Procurar atividade existente</h3>
    <form method="post">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
        <input type="hidden" name="atividade" value="1">
        <input type="text" name="termo" placeholder="Nome ou localização">
        <button type="submit">Procurar</button>
    </form>

    <?php if ($termo !== ''): ?>
        <?php if (!empty($matches)): ?>
            <h4>Escolha atividade existente:</h4>
            <form action="actions/action_adicionaralojamento.php" method="post">
                <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
                <input type="hidden" name="atividade" value="1"> <?php foreach ($matches as $a): ?>
                    <input type="radio" name="detalhe_id" value="<?= $a['id'] ?>" required>
                    <?= htmlspecialchars($a['nome'].' ('.$a['localizacao'].')') ?><br>
                <?php endforeach; ?>

                <label>Data</label>
                <input type="date" name="data_inicio" required> <button type="submit">Adicionar Atividade</button>
            </form>
        <?php else: ?>
            <h4>Nenhuma encontrada – criar nova atividade</h4>
            <form action="actions/action_adicionaralojamento.php" method="post">
                <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
                <input type="hidden" name="atividade" value="1">

                <label>Nome</label>
                <input type="text" name="nome" required>
                <label>Localização</label>
                <input type="text" name="localizacao" required>
                <label>Tipo</label>
                <select name="tipo_atividade" required>
                    <option value="Restauração">Restauração</option>
                    <option value="Atração">Atração</option>
                    <option value="Experiência">Experiência</option>
                    <option value="Outro">Outro</option>
                </select>
                <label>Data</label>
                <input type="date" name="data_inicio" required>
                <button type="submit">Criar e adicionar</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>




</body>
</html>

