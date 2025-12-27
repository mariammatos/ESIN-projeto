<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "Faça login para ver mais!";
    header('Location: login.php'); // Se falhar, volta para a página de login
    exit();
}
// Inclui o ficheiro que faz a ligação à base de dados.
require_once 'database/db_connect.php';
require_once 'database/posts.php';
require_once 'database/alojamentos.php';
require_once 'database/users.php';
require_once 'database/destinos.php';
require_once 'database/traveljournals.php';
require_once 'database/media.php';

// --- 1. LÓGICA DE AUTENTICAÇÃO E BUSCA DE DADOS ---

// SIMULAÇÃO: Aqui, o seu código real iria verificar a sessão para obter o nome de utilizador logado.
// Usamos 'sara' como um utilizador de teste por agora.
$id_viagem = (int)$_GET['id'];


// Consulta SQL para obter as publicações das pessoas que o utilizador segue.
// Esta consulta junta Viagens (V) com Utilizador (U) e Seguir (S).
$db = getDatabaseConnection();
$viagem = getViagemDetalhes($db, $id_viagem);
$fotos = getFotos($db, $id_viagem);
$likes = getViagemLikes($db, $id_viagem);
$likes_count = getViagemLikesCount($db, $id_viagem);
$comentarios = getComentarios($db, $id_viagem);
$alojamentos = getAlojamentosViagem($db, $id_viagem);


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
                    <?php foreach ($fotos as $foto): ?>
                        <div class="foto-item">
                            <img width="200" height="200"
                                src="<?= htmlspecialchars($foto['path']) ?>"
                                alt="Foto da viagem" />
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="editar-fotos">
                    <?php if ($is_owner && count($fotos) < 16): ?>
                        <form action="adicionarfotos.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                            <button type="submit">Adicionar Foto</button>
                        </form>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <?php if ($is_owner): ?>
                    <div class="editar-fotos">
                        <form action="adicionarfotos.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                            <button type="submit">Adicionar Foto</button>
                        </form>
                    </div>
                <?php endif; ?>

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


        <section class="atividades-alojamentos">
            <h2>Atividades e Alojamentos</h2>

            <?php if (count($alojamentos) === 0): ?>
                <p>Sem alojamentos registados nesta viagem.</p>
                <?php if ($current_user == $viagem['nome_de_utilizador']): ?>
                    <a href="novo_alojamento.php?viagem_id=<?= $id_viagem ?>" class="btn-adicionar-alojamento">
                        Adicionar Alojamento
                    </a>
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
                            <a href="feedback_alojamento.php?alojamento_id=<?= $a['alojamento_id'] ?>" class="btn-feedback">Dar Feedback</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
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


    <div class="modal-galeria" id="modalGaleria">
        <span class="modal-close" id="fecharModal">&times;</span>
        <button class="modal-btn modal-prev" id="prevFoto">‹</button>
        <img id="modalImg" src="">
        <button class="modal-btn modal-next" id="nextFoto">›</button>
    </div>
    <footer>
        </footer>

</body>
</html>


<script>
document.getElementById('like-form').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const form = this;
    const btn = form.querySelector('button');
    const countSpan = document.getElementById('like-count');
    const container = document.querySelector('.like'); 

    let currentLikes = parseInt(countSpan.innerText) || 0;

    if (btn.classList.contains('active')) {
        // --- REMOVER LIKE ---
        btn.classList.remove('active');
        btn.innerText = 'Like'; 
        currentLikes--; 
        
    } else {
        // --- DAR LIKE ---
        btn.classList.add('active');
        btn.innerText = 'Liked'; 
        currentLikes++;

        const heart = document.createElement('div');
        heart.innerHTML = '❤️';
        heart.className = 'insta-heart-animation';
        container.appendChild(heart);
        
        setTimeout(() => { heart.remove(); }, 1000);
    }
    
    countSpan.innerText = currentLikes + " likes";

    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalGaleria');
    const modalImg = document.getElementById('modalImg');
    const fecharModal = document.getElementById('fecharModal');
    const prevBtn = document.getElementById('prevFoto');
    const nextBtn = document.getElementById('nextFoto');
    
    const fotos = document.querySelectorAll('.foto-item img');
    let currentIndex = 0;
    
    // Abrir ao clicar em foto
    fotos.forEach((foto, index) => {
        foto.addEventListener('click', () => {
            currentIndex = index;
            modalImg.src = foto.src;
            modal.classList.add('active');
        });
    });
    
    // Fechar 
    fecharModal.addEventListener('click', () => {
        modal.classList.remove('active');
    });
    
    // Fechar ao clicar fora da imagem
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
    
    // Foto anterior
    prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + fotos.length) % fotos.length;
        modalImg.src = fotos[currentIndex].src;
    });
    
    // Próxima foto
    nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % fotos.length;
        modalImg.src = fotos[currentIndex].src;
    });
    
    // Navegação com teclado
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            modal.classList.remove('active');
        } else if (e.key === 'ArrowLeft') {
            prevBtn.click();
        } else if (e.key === 'ArrowRight') {
            nextBtn.click();
        }
    });
});
</script>
