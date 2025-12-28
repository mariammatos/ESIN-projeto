<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/destinos.php';
require_once 'database/alojamentos.php';~
require_once 'database/posts.php';

$db = getDatabaseConnection();

$viagem_id = (int)($_GET['viagem_id'] ?? $_POST['viagem_id'] ?? 0);

// estado
$alojamento = isset($_POST['alojamento']);
$atividade  = isset($_POST['atividade']);

// pesquisa
$termo = $_POST['termo'] ?? '';

$viagem = getViagemDetalhes($db, $viagem_id);

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
    <link rel="stylesheet" href="css/stylenovo_aloj.css">
</head>
<body>

  <a href="perfil.php" class="btn-voltar">← Voltar ao Perfil</a>


    <h4><?php echo $_SESSION['error'] ?? ''; unset($_SESSION['error']); ?></h4>
    <h4><?php echo $_SESSION['success'] ?? ''; unset($_SESSION['success']); ?></h4>  
    <?php if ($atividade): ?>
        <h2>Adicionar Atividade à sua viagem a <?= htmlspecialchars($viagem['cidade_local'] . ', ' . $viagem['pais']) ?></h2>
    <?php else: ?>
        <h2>Adicionar Alojamento à sua viagem a <?= htmlspecialchars($viagem['cidade_local'] . ', ' . $viagem['pais']) ?></h2>
    <?php endif; ?>



<?php if ($alojamento && !$atividade): ?>
    <h3>Procure um alojamento existente</h4>
    <form method="post">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
        <input type="hidden" name="alojamento" value="1">
        <input type="text" name="termo" placeholder="Nome ou localização">
        <button type="submit">Procurar</button>
    </form>

    <?php if ($termo !== ''): ?>
        <?php if (!empty($matches)): ?>
            <form action="actions/action_adicionaralojamento.php" method="post">
                <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
                <input type="hidden" name="alojamento" value="1"> <?php foreach ($matches as $a): ?>
                    <input type="radio" name="detalhe_id" value="<?= $a['id'] ?>" required>
                    <?= htmlspecialchars($a['nome'].' ('.$a['localizacao'].')') ?><br>
                <?php endforeach; ?>

                <label>Data início</label>
                <input type="date" name="data_inicio" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>" required>
                <label>Data fim</label>
                <input type="date" name="data_fim" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>">
                <button type="submit">Adicionar</button>
            </form>
        <?php else: ?>
            <h5>Nenhum encontrado – crie um novo alojamento</h5>
        <?php endif; ?>
        <h4>Adicione um novo:</h4>
        <form action="actions/action_adicionaralojamento.php" method="post">
            <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
            <input type="hidden" name="alojamento" value="1">

            <input name="nome" placeholder="Nome" required>
            <input name="localizacao" placeholder="Localização" required>
            <select name="tipo" required> <option value="Hotel">Hotel</option>
                <option value="Hostel">Hostel</option>
                <option value="Alojamento Local">Alojamento Local</option>
            </select>
            <input type="date" name="data_inicio" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>" required>
            <input type="date" name="data_fim" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>">
            <button type="submit">Adicionar</button>
        </form>
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
                <input type="hidden" name="atividade" value="1"> 
                <?php foreach ($matches as $a): ?>
                    <label class="radio-option">
                        <input type="radio" name="detalhe_id" value="<?= $a['id'] ?>" required>
                        <span><?= htmlspecialchars($a['nome'].' ('.$a['localizacao'].')') ?></span>
                    </label>
                <?php endforeach; ?>

                <label>Data</label>
                <input type="date" name="data" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>" required> 
                <button type="submit">Adicionar Atividade</button>
            </form>
        <?php else: ?>
            <h5>Nenhuma encontrada – criar nova atividade</h5>
        <?php endif; ?>

        <h4>Ou adicione uma nova:</h4>
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
            <input type="date" name="data" min="<?= $viagem['data_ida'] ?>" max="<?= $viagem['data_volta'] ?>" required>
            <button type="submit">Criar e adicionar</button>
        </form>
    <?php endif; ?>
<?php endif; ?>




</body>
</html>

