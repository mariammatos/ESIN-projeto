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