<article class="post-viagem">
    <div class="post-header">
        <h2><?= htmlspecialchars($viagem['titulo']); ?></h2>
        
        <?php if (!empty($viagem['nome_de_utilizador'])): ?>
            <span class="autor">
                por 
                <a href="perfil.php?user=<?= urlencode($viagem['nome_de_utilizador']) ?>">
                    @<?= htmlspecialchars($viagem['nome_de_utilizador']) ?> 
                    (<?= htmlspecialchars($viagem['nome']) ?>)
                </a>
            </span>
        <?php endif; ?>
    </div>
    
    <div class="post-detalhes">
        <?php
            $fotos_post = array_slice(getFotos($db, $viagem['id']), 0, 4);
            if (!empty($fotos_post)):
        ?>
            <div class="post-fotos">
                <?php foreach ($fotos_post as $foto): ?>
                    <div class="post-foto">
                        <img src="<?= htmlspecialchars($foto['path']); ?>" 
                             alt="Foto da viagem <?= htmlspecialchars($viagem['titulo']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p><strong>Destino:</strong> <?= htmlspecialchars($viagem['cidade_local']); ?>, <?= htmlspecialchars($viagem['pais']); ?></p>
        <p><a href="viagem.php?id=<?= $viagem['id']; ?>">Ver todos os detalhes da viagem...</a></p>
    </div>

    <div class="post-interacoes">
        <?php 
            $likes_count = getViagemLikesCount($db, $viagem['id']);
            $comentarios_count = getViagemComentariosCount($db, $viagem['id']);
        ?>
        <span><?= $likes_count; ?> Likes</span> | <span><?= $comentarios_count; ?> Comentários</span>
    </div>
</article>