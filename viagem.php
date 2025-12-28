<?php
session_start();

// Inclui o ficheiro que faz a ligação à base de dados.
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/alojamentos.php';
require_once 'database/users.php';
require_once 'database/destinos.php';
require_once 'database/traveljournals.php';
require_once 'database/media.php';


$id_viagem = (int)$_GET['id'];




if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "Faça login para ver mais!";
    $_SESSION['viagem'] = $id_viagem;
    header('Location: login.php');
    exit();
}


$db = getDatabaseConnection();
$viagem = getViagemDetalhes($db, $id_viagem);
$fotos = getFotos($db, $id_viagem);
$likes = getViagemLikes($db, $id_viagem);
$likes_count = getViagemLikesCount($db, $id_viagem);
$comentarios = getComentarios($db, $id_viagem);
$alojamentos = getAlojamentosViagem($db, $id_viagem);
$atividades = getAtividadesViagem($db, $id_viagem);


session_start();
$current_user = $_SESSION['username'] ?? null;
$is_owner = ($current_user === $viagem['nome_de_utilizador']);
$wishlist = $current_user ? getuserwishlist($db, $current_user) : null;
$user_liked = $current_user ? userLikedViagem($db, $id_viagem, $current_user) : false;
$user_guardou = $current_user ? publicacaoGuardada($db, $current_user, $id_viagem) : false;
$destino = getDestinoId($db, $viagem['pais'], $viagem['cidade_local']);
$user_wishlist = $wishlist ? destinonaWishlist($db, $destino, $wishlist) : false;

$traveljournal_id = getTravelJournalId($db, $id_viagem);

if ($traveljournal_id) {
    $traveljournal = getTravelJournal($db, $traveljournal_id);
    $viagem['journal_descricao'] = $traveljournal['descricao'];
    $viagem['journal_avaliacao'] = $traveljournal['avaliacao'];
    $has_journal = true;
} else {
    $viagem['journal_descricao'] = '';
    $viagem['journal_avaliacao'] = null;
    $has_journal = false;
}

if ($_SESSION['last_page'] == 'viagem.php?') {
    $_SESSION['last_page'] = $_SESSION['last_page_2'];
}
else {
    $_SESSION['last_page_2'] = $_SESSION['last_page'];
}
$last_page = $_SESSION['last_page'] ?? null;

$_SESSION['last_page'] = 'viagem.php?';

$css_especifico = 'styleviagem.css';
include_once 'templates/header_tpl.php';

?>


<!DOCTYPE html>

    <main class="viagem-detalhe-container">
        <?php if ($last_page === 'explorar.php'): ?>
            <a href="explorar.php?search_user=<?= $_SESSION['search_user'] ?>&search_viagem=<?= $_SESSION['search_viagem'] ?>&search_alojamento=<?= $_SESSION['search_alojamento'] ?>&search_atividade=<?= $_SESSION['search_atividade'] ?>" class="btn-voltar">← Voltar à Pesquisa</a>
        <?php elseif ($last_page === 'feed.php'): ?>
            <a href="feed.php" class="btn-voltar">← Voltar ao Feed</a>
        <?php elseif ($last_page === 'guardados.php'): ?>
            <a href="guardados.php" class="btn-voltar">← Voltar às viagens guardadas</a>
        <?php elseif (strpos($last_page, 'explorar_destino.php') !== false): ?>
            <a href="<?= $last_page ?>" class="btn-voltar">← Voltar a explorar destino</a>
        <?php elseif (strpos($last_page, 'perfil.php') !== false): ?>
            <a href="<?= $last_page ?>" class="btn-voltar">← Voltar ao perfil</a>
        <?php else: ?>
            <a href="<?="index.php" ?>" class="btn-voltar">← Voltar</a>
        <?php endif; ?>

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
            <div class="local-wishlist">
                <p>
                    <strong>Local:</strong>
                    <?= htmlspecialchars($viagem['cidade_local']); ?>,
                    <?= htmlspecialchars($viagem['pais']); ?>
                </p>

                <form action="actions/action_adicionarwishlist.php" method="post">
                    <input type="hidden" name="post_id" value="<?= $id_viagem ?>">
                    <input type="hidden" name="destino_id" value="<?= $destino ?>">
                    <button type="submit" class="<?= $user_wishlist ? 'active' : '' ?>">
                    </button>
                </form>
            </div>


            <p><strong>De:</strong> <?php echo htmlspecialchars($viagem['data_ida']); ?> <strong>A:</strong> <?php echo htmlspecialchars($viagem['data_volta'] ?? 'Em andamento'); ?></p>
                 <div class="editar-viagem">
                    <?php if ($is_owner): ?>
                        <form action="editar_viagem.php" method="post">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                            <input type="hidden" name="titulo" value="<?= htmlspecialchars($viagem['titulo'] ?? '') ?>">
                            <input type="hidden" name="data_ida" value="<?= htmlspecialchars($viagem['data_ida'] ?? '') ?>">
                            <input type="hidden" name="data_volta" value="<?= htmlspecialchars($viagem['data_volta'] ?? '') ?>">
                            <button type="submit">Editar Viagem</button>
                        </form>
                    <?php endif; ?>
                </div>       
        </section>

        <section class="galeria-fotos">
            <?php if (!empty($fotos)): ?>
                <div class="galeria-container">
                    <div class="galeria-horizontal">
                        <?php foreach ($fotos as $index => $foto): ?>
                            <a href="#foto-<?= $index ?>" class="foto-item">
                                <img src="<?= htmlspecialchars($foto['path']) ?>" alt="Foto da viagem">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php foreach ($fotos as $index => $foto): ?>
                                <div id="foto-<?= $index ?>" class="modal-sem-js">
                                    <a href="#" class="modal-overlay"></a>
                                    <div class="modal-content">
                                        <a href="#" class="modal-close-btn">&times;</a>

                                        <?php if ($index > 0): ?>
                                            <a href="#foto-<?= $index - 1 ?>" class="nav-btn prev">‹</a>
                                        <?php endif; ?>

                                        <img src="<?= htmlspecialchars($foto['path']) ?>">

                                        <?php if ($index < count($fotos) - 1): ?>
                                            <a href="#foto-<?= $index + 1 ?>" class="nav-btn next">›</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if ($is_owner && count($fotos) < 16): ?>
                                <form action="adicionarfotos.php" method="post" class="editar-fotos">
                                    <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                    <button type="submit">Adicionar Foto</button>
                                </form>
                            <?php endif; ?>

                        <?php elseif ($is_owner): ?>

                            <form action="adicionarfotos.php" method="post" class="editar-fotos">
                                <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                <button type="submit">Adicionar Foto</button>
                            </form>

                        <?php endif; ?>
                    </section>


        <section class="travel-journal">
            <h2>Travel Journal</h2>

            <?php if ($is_owner): ?>
                <?php if (!$has_journal): ?>
                    <section class="adicionar-travel-journal">
                        <form action="adicionartraveljournal.php" method="post">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                            <button type="submit">Adicionar Travel Journal</button>
                        </form>
                    </section>
                <?php else: ?>
                    <section class="editar-travel-journal">
                        <form action="adicionartraveljournal.php" method="post">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                            <input type="hidden" name="descricao" value="<?= htmlspecialchars($viagem['journal_descricao'] ?? '') ?>">
                            <input type="hidden" name="avaliacao" value="<?= htmlspecialchars($viagem['journal_avaliacao'] ?? 0) ?>">
                            <input type="hidden" name="editar" value="1">
                            <button type="submit">Editar Travel Journal</button>
                        </form>
                    </section>

                    <p class="journal-texto">
                        <?= nl2br(htmlspecialchars($viagem['journal_descricao'])) ?>
                    </p>
                    <div class="avaliacao-stars">
                        <span class="avaliacao-label">Avaliação Final:</span>
                        <span class="stars">
                            <?php 
                            $avaliacao = intval($viagem['journal_avaliacao'] ?? 0);
                            echo str_repeat('★', $avaliacao) . str_repeat('☆', 5 - $avaliacao);
                            ?>
                        </span>
                        <span class="avaliacao-numero"><?= htmlspecialchars($viagem['journal_avaliacao'] ?? 'N/A') ?>/5</span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (!$has_journal): ?>
                    <p>Sem Travel Journal registado para esta viagem.</p>
                <?php else: ?>
                    <p class="journal-texto">
                        <?= nl2br(htmlspecialchars($viagem['journal_descricao'])) ?>
                    </p>
                    <div class="avaliacao-stars">
                        <span class="avaliacao-label">Avaliação Final:</span>
                        <span class="stars">
                            <?php 
                            $avaliacao = intval($viagem['journal_avaliacao'] ?? 0);
                            echo str_repeat('★', $avaliacao) . str_repeat('☆', 5 - $avaliacao);
                            ?>
                        </span>
                        <span class="avaliacao-numero"><?= htmlspecialchars($viagem['journal_avaliacao'] ?? 'N/A') ?>/5</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>


        <section class="alojamentos">
            <h2>Alojamentos</h2>

            <?php if (count($alojamentos) === 0): ?>
                <p>Sem alojamentos registados nesta viagem.</p>
                <?php if ($current_user == $viagem['nome_de_utilizador']): ?>
                    <form action="novo_alojamento.php" method="post">
                        <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                        <input type="hidden" name="alojamento" value="1">
                        <button type="submit">Adicionar Alojamento</button>
                    </form>

                <?php endif; ?>
            <?php else: ?>
                <ul>
                <?php foreach ($alojamentos as $a): ?>
                    <li>
                        <strong>
                            <a href="detalhes_alojamento.php?id=<?= $a['alojamento_id'] ?>&tipo=alojamento" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($a['nome_alojamento']) ?>
                            </a>
                        </strong> 
                        (<?= htmlspecialchars($a['tipo_alojamento']) ?>)<br>
                        Local: <?= htmlspecialchars($a['localizacao']) ?><br>
                        De: <?= htmlspecialchars($a['data_inicio']) ?> 
                        <?php if ($a['data_fim']): ?>
                            Até: <?= htmlspecialchars($a['data_fim']) ?>
                        <?php else: ?>
                            Em andamento
                        <?php endif; ?><br>
                        
                        <div class="avaliacao-stars alojamento-stars">
                            <span class="avaliacao-label">Avaliação:</span>
                            <span class="stars">
                                <?php 
                                $media = $a['media_avaliacao'] ? round($a['media_avaliacao']) : 0;
                                echo str_repeat('★', $media) . str_repeat('☆', 5 - $media);
                                ?>
                            </span>
                            <span class="avaliacao-numero">
                                <?= $a['media_avaliacao'] ? round($a['media_avaliacao'], 1) : 'N/A' ?>/5
                            </span>
                        </div>
                        
                        <?php if ($is_owner): ?>
                            <a href="detalhes_alojamento.php?id=<?= $a['alojamento_id'] ?>&tipo=alojamento" class="btn-detalhes">Ver Detalhes</a>
                            <a href="feedback_alojamento.php?id=<?= $a['alojamento_id'] ?>&tipo=alojamento" class="btn-feedback">Dar Feedback</a>
                            <!-- Botão Apagar Alojamento -->
                            <form method="post" action="actions/action_delete_aloj_ativ.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $a['alojamento_id'] ?>">
                                <input type="hidden" name="tipo" value="alojamento">
                                <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Tem a certeza que deseja apagar este alojamento?');">Apagar</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php if ($current_user == $viagem['nome_de_utilizador']): ?>
                    <form action="novo_alojamento.php" method="post">
                        <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                        <input type="hidden" name="alojamento" value="1">
                        <button type="submit">Adicionar Alojamento</button>
                    </form>

                <?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="atividades">
            <h2>Atividades</h2>

            <?php if (count($alojamentos) === 0): ?>
                <p>Sem alojamentos registados nesta viagem.</p>
                <?php if ($current_user == $viagem['nome_de_utilizador']): ?>

                    <form action="novo_alojamento.php" method="post">
                        <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                        <input type="hidden" name="atividade" value="1">
                        <button type="submit">Adicionar Atividade</button>
                    </form>

                <?php endif; ?>
            <?php else: ?>
                <ul>
                <?php foreach ($atividades as $a): ?>
                    <li>
                        <strong>
                            <a href="detalhes_alojamento.php?id=<?= $a['atividade_id'] ?>&tipo=atividade" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($a['nome_atividade']) ?>
                            </a>
                        </strong> 
                        (<?= htmlspecialchars($a['tipo_atividade']) ?>)<br>
                        Local: <?= htmlspecialchars($a['localizacao']) ?><br>
                        A: <?= htmlspecialchars($a['data_inicio']) ?> 
                        <div class="avaliacao-stars alojamento-stars">
                            <span class="avaliacao-label">Avaliação:</span>
                            <span class="stars">
                                <?php 
                                $media = $a['media_avaliacao'] ? round($a['media_avaliacao']) : 0;
                                echo str_repeat('★', $media) . str_repeat('☆', 5 - $media);
                                ?>
                            </span>
                            <span class="avaliacao-numero">
                                <?= $a['media_avaliacao'] ? round($a['media_avaliacao'], 1) : 'N/A' ?>/5
                            </span>
                        </div>
                        
                        <?php if ($is_owner): ?>
                            <a href="detalhes_alojamento.php?id=<?= $a['atividade_id'] ?>&tipo=atividade" class="btn-detalhes">Ver Detalhes</a>
                            <a href="feedback_alojamento.php?id=<?= $a['atividade_id'] ?>&tipo=atividade" class="btn-feedback">Dar Feedback</a>
                            <!-- Botão Apagar Atividade -->
                            <form method="post" action="actions/action_delete_aloj_ativ.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $a['atividade_id'] ?>">
                                <input type="hidden" name="tipo" value="atividade">
                                <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Tem a certeza que deseja apagar esta atividade?');">Apagar</button>
                            </form>                           
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php if ($current_user == $viagem['nome_de_utilizador']): ?>
                    
                    <form action="novo_alojamento.php" method="post">
                        <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                        <input type="hidden" name="atividade" value="1">
                        <button type="submit">Adicionar Atividade</button>
                    </form>

                <?php endif; ?>
            <?php endif; ?>
        </section>

    
        <section class="like">
            <?php if ($current_user): ?>
                <form action="actions/action_like.php" method="post" id="like-form">
                    <input type="hidden" name="post_id" value="<?php echo $id_viagem; ?>">
                    <button type="submit" class="<?php echo $user_liked ? 'active' : ''; ?>">
                        <?php echo $user_liked ? 'Liked' : 'Like'; ?>
                    </button>
                    
                </form>
                <span id="like-count"><?php echo $likes_count; ?> likes</span>
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



    <?php include_once 'templates/footer_tpl.php'; ?>

