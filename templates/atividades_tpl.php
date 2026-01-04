                    <li>
                        <strong>
                            <a href="detalhes_alojamento.php?id=<?= $a['detalhe_id'] ?>&tipo=atividade" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($a['nome_atividade']) ?>
                            </a>
                        </strong> 
                        (<?= htmlspecialchars($a['tipo_atividade']) ?>)<br>
                        Local: <?= htmlspecialchars($a['localizacao']) ?><br>

                        Data: <?= htmlspecialchars($a['data_atividade']) ?> 

                        <?php if (verificarFeedback($db, $a['atividade_id'], 'atividade')): ?>
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
                        <?php endif; ?>

                        
                        <?php if ($is_owner): ?>
                            <?php if (!verificarFeedback($db, $a['atividade_id'], 'atividade')): ?>
                                <a href="feedback_alojamento.php?id=<?= $a['atividade_id'] ?>&tipo=atividade" class="btn-feedback">Dar Feedback</a>
                            <?php endif; ?>

                            <form method="post" action="actions/action_delete_aloj_ativ.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $a['atividade_id'] ?>">
                                <input type="hidden" name="tipo" value="atividade">
                                <input type="hidden" name="viagem_id" value="<?= $id_viagem ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Tem a certeza que deseja apagar esta atividade?');">🗑️</button>
                            </form>                           
                        <?php endif; ?>
                    </li>