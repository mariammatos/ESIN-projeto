<?php
// Inclui o ficheiro que faz a ligação à base de dados.
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/alojamentos.php';
require_once 'database/users.php';
require_once 'database/destinos.php';

// --- 1. LÓGICA DE AUTENTICAÇÃO E BUSCA DE DADOS ---

// SIMULAÇÃO: Aqui, o seu código real iria verificar a sessão para obter o nome de utilizador logado.
// Usamos 'sara' como um utilizador de teste por agora.
$id_viagem = (int)$_GET['id'];


// Consulta SQL para obter as publicações das pessoas que o utilizador segue.
// Esta consulta junta Viagens (V) com Utilizador (U) e Seguir (S).
$db = getDatabaseConnection();
$viagem = getViagemDetalhes($db, $id_viagem);
$likes = getViagemLikes($db, $id_viagem);
$likes_count = getViagemLikesCount($db, $id_viagem);
$comentarios = getComentarios($db, $id_viagem);
$alojamentos = getAlojamentosViagem($db, $id_viagem);

session_start();
$current_user = $_SESSION['username'] ?? null;
$wishlist = $current_user ? getuserwishlist($db, $current_user) : null;
$user_liked = $current_user ? userLikedViagem($db, $id_viagem, $current_user) : false;
$user_guardou = $current_user ? publicacaoGuardada($db, $current_user, $id_viagem) : false;
$destino = getDestinoId($db, $viagem['pais'], $viagem['cidade_local']);
$user_wishlist = $wishlist ? destinonaWishlist($db, $destino, $wishlist) : false;

// --- 2. APRESENTAÇÃO HTML/CSS ---
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($viagem['titulo']); ?> | TripTales</title>
    <link rel="stylesheet" href="css/styleviagem.css">
</head>
<body>

    <header>
        </header>

    <main class="viagem-detalhe-container">
        <a href="feed.php" class="btn-voltar">← Voltar ao Feed</a>

        <h1><?php echo htmlspecialchars($viagem['titulo']); ?></h1>
        
        <div class="autor-info">
            Publicado por:
            <a href="perfil.php?user=<?= urlencode($viagem['nome_de_utilizador']) ?>">
                @<?= htmlspecialchars($viagem['nome_de_utilizador']) ?>
            </a>
            (<?= htmlspecialchars($viagem['nome']) ?>)
        </div>
        
        <section class="informacao-base">
            <h2>Destino e Datas</h2>
            <p><strong>Local:</strong> <?php echo htmlspecialchars($viagem['cidade_local']); ?>, <?php echo htmlspecialchars($viagem['pais']); ?>
                <form action="actions/action_adicionarwishlist.php" method="post">
                    <input type="hidden" name="post_id" value="<?php echo $id_viagem; ?>">
                    <input type="hidden" name="destino_id" value="<?php echo $destino; ?>">
                    <button type="submit" ><?php echo $user_wishlist ? 'Remover da Wishlist' : 'Adicionar à Wishlist'; ?>
                    </button>
                </form></p>
            <p><strong>De:</strong> <?php echo htmlspecialchars($viagem['data_ida']); ?> <strong>A:</strong> <?php echo htmlspecialchars($viagem['data_volta'] ?? 'Em andamento'); ?></p>
        </section>

        <section class="travel-journal">
            <h2>Travel Journal</h2>
            <p class="journal-texto"><?php echo nl2br(htmlspecialchars($viagem['journal_descricao'])); ?></p>
            <p>Avaliação Final: <?php echo htmlspecialchars($viagem['journal_avaliacao'] ?? 'N/A'); ?>/5</p>
        </section>

        <section class="atividades-alojamentos">
            <h2>Atividades e Alojamentos</h2>

            <?php if (count($alojamentos) === 0): ?>
                <p>Sem alojamentos registados nesta viagem.</p>
                    <?php if ($current_user == $viagem['nome_de_utilizador']): ?>
                    <!-- FORMULÁRIO PARA ADICIONAR ALOJAMENTO -->
                    <section class="adicionar-alojamento">
                        <h3>Adicionar Alojamento</h3>
                        <form action="actions/action_add_alojamento.php" method="post">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">

                            <label for="nome">Nome do Alojamento:</label>
                            <input type="text" name="nome" id="nome" required>

                            <label for="localizacao">Localização:</label>
                            <input type="text" name="localizacao" id="localizacao" required>

                            <label for="tipo">Tipo:</label>
                            <select name="tipo" id="tipo" required>
                                <option value="Hostel">Hostel</option>
                                <option value="Hotel">Hotel</option>
                                <option value="Alojamento Local">Alojamento Local</option>
                                <option value="Outro">Outro</option>
                            </select>

                            <label for="data_inicio">Data Início:</label>
                            <input type="date" name="data_inicio" id="data_inicio" required>

                            <label for="data_fim">Data Fim:</label>
                            <input type="date" name="data_fim" id="data_fim">

                            <label for="rating">Avaliação:</label>
                            <input type="number" name="rating" id="rating" min="0" max="5" step="0.5">

                            <label for="comentario">Comentário:</label>
                            <textarea name="comentario" id="comentario"></textarea>

                            <button type="submit">Adicionar Alojamento</button>
                        </form>
                    </section>
                    <?php endif; ?>
            <?php else: ?>
                <ul>
                <?php foreach ($alojamentos as $a): ?>
                    <li>
                        <strong><?= htmlspecialchars($a['nome_alojamento']) ?></strong> (<?= htmlspecialchars($a['tipo_alojamento']) ?>)<br>
                        Local: <?= htmlspecialchars($a['localizacao']) ?><br>
                        De: <?= htmlspecialchars($a['data_inicio']) ?> 
                        <?php if ($a['data_fim']): ?>
                            Até: <?= htmlspecialchars($a['data_fim']) ?>
                        <?php else: ?>
                            Em andamento
                        <?php endif; ?><br>
                        Avaliação média: <?= $a['media_avaliacao'] ? round($a['media_avaliacao'], 1) : 'N/A' ?>/5
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>


        </section>

    
        <section class="like">
            <?php if ($current_user): ?>
                <form action="actions/action_like.php" method="post">
                    <input type="hidden" name="post_id" value="<?php echo $id_viagem; ?>">
                    <button type="submit" ><?php echo $user_liked ? 'Liked' : 'Like'; ?>
                    </button>
                </form>
                <span><?php echo $likes_count; ?> likes</span>
            <?php else: ?>
                <p>Faça login para dar like.</p>
                <span><?php echo $likes_count; ?> likes</span>
            <?php endif; ?>
        </section>

        <section class="guardar">
            <?php if ($current_user): ?>
                <form action="actions/action_guardar.php" method="post">
                    <input type="hidden" name="post_id" value="<?php echo $id_viagem; ?>">
                    <button type="submit" ><?php echo $user_guardou ? 'Guardado' : 'Guardar'; ?>
                    </button>
                </form>
            <?php else: ?>
                <p>Faça login para dar guardar.</p>
            <?php endif; ?>
        </section>


        <section class="comentarios">
            <h2>Comentários</h2>
            <?php if ($current_user): ?>
                <form action="actions/action_comentar.php" method="post">
                    <input type="hidden" name="viagem_id" value="<?php echo $id_viagem; ?>">
                    <textarea name="comentario" required placeholder="Escreve um comentário..."></textarea>
                    <button type="submit">Comentar</button>
        
                </form>
            <?php else: ?>
                <p>Faça login para comentar.</p>
            <?php endif; ?>

            <?php if (count($comentarios) === 0): ?>
                <p>Sem comentários ainda.</p>
            <?php else: ?>
                <ul class="lista-comentarios">
                    <?php foreach ($comentarios as $c): ?>
                        <li>
                            <strong>@<?= htmlspecialchars($c['utilizador']) ?>:</strong> 
                            <?= htmlspecialchars($c['texto']) ?>
                            <em>(<?= htmlspecialchars($c['data']) ?>)</em>
                            <?php if ($c['utilizador'] === $current_user): ?>

                                <form action="actions/action_comentario_delete.php" method="post">
                                    <input type="hidden" name="comentario_id" value="<?= $c['id'] ?>">
                                    <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                    <button type="submit" onclick="return confirm('Tem a certeza que quer eliminar este comentário?');">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

    </main>

    <footer>
        </footer>

</body>
</html>