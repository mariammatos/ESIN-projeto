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

                <?php if ($is_owner && count($fotos) < 10): ?>
                    <form action="adicionarfotos.php" method="post" class="editar-fotos">
                        <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                        <button type="submit">Adicionar Fotos</button>
                    </form>
                <?php endif; ?>