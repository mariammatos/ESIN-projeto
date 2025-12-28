<?php
  session_start();

  $msg = $_SESSION['msg'];
  $msg1 = $_SESSION['msg1'];
  unset($_SESSION['msg']);
  unset($_SESSION['msg1']);

  if (!isset($_SESSION['pais_matches'])) {
    $_SESSION['pais_matches'] = [];
  }

  $editar = (isset($_POST['editar']) && $_POST['editar'] == '1') || 
            (isset($_GET['editar']) && $_GET['editar'] == '1');


if ($editar) {
      $editar = true; 
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $_SESSION['form_data']['username'] = $_POST['username'] ?? '';
          $_SESSION['form_data']['email'] = $_POST['email'] ?? '';
          $_SESSION['form_data']['nome'] = $_POST['nome'] ?? '';
          $_SESSION['pais'] = $_POST['pais'] ?? '';
          $_SESSION['pref'] = $_POST['pref'] ?? '';
      }
  }

 $prefSelecionadas = [];

  if (!empty($_SESSION['pref'])) {
      // Se já for um array (vem do formulário/POST), usa diretamente
      if (is_array($_SESSION['pref'])) {
          $prefSelecionadas = $_SESSION['pref'];
      } 
      // Se for uma string (vem da Base de Dados), usa o explode
      else {
          $prefSelecionadas = array_map('trim', explode(',', $_SESSION['pref']));
      }
  }
  

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TripTales</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styleregist.css"> 
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin%7CMerriweather" rel="stylesheet"> 
  </head>
  <body>

    <?php if (!isset($editar)): ?>
      <a href="login.php" class="btn-voltar">← Voltar ao Login</a>
    <?php else: ?>
      <a href="perfil.php?user=<?= urlencode($_SESSION['form_data']['username']) ?>" class="btn-voltar">← Voltar ao Perfil</a>
    <?php endif; ?>

  <section id="registration">
    <?php echo $msg ?>
    <?php if ($editar): ?>
        <h2>Editar Perfil</h2>
    <?php else: ?>
        <h2>Registo</h2>
    <?php endif; ?>


    <form action="actions/action_register.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="editar" value="<?= $editar ? '1' : '0' ?>">
      <?php if ($editar): ?>
        <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['form_data']['username'] ?? '') ?>">
      <?php endif; ?>

      <div class="form-group">
        <label for="username">Nome de utilizador:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_SESSION['form_data']['username'] ?? '') ?>" required
         <?php if ($editar) echo 'disabled'; ?>>

        <?php if (!$editar): ?>
          <?php if (!empty($msg1)): ?>
            <div class="validation-message <?= strpos($msg1, 'disponível') !== false ? 'success' : 'error' ?>">
              <?= strip_tags($msg1) ?>
            </div>
          <?php endif; ?>
        
          <button type="submit" formaction="actions/action_checkusername.php" formnovalidate>Verificar</button>
        <?php endif; ?>
      </div>

      <?php if (!$editar): ?>
        <div class="form-group">
          <label for="password">Palavra-passe:</label>
          <input type="password" id="password" name="password">
        </div>

        <div class="form-group">
          <label for="password_confirm">Confirmar palavra-passe:</label>
          <input type="password" id="password_confirm" name="password_confirm" required>
            <div id="password-match-message" class="validation-message" style="display: none;"></div>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="email">Endereço de e-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_SESSION['form_data']['nome'] ?? '') ?>" required>
      </div>

        <div class="form-group">
            <label for="pais">País:</label>
            <input type="text" name="pais" id="pais" value="<?= htmlspecialchars($_SESSION['pais'] ?? '') ?>" required>
            <input type="hidden" name="registar" id="registar" value="1">
            <button type="submit" formaction="actions/action_procurarpais.php" formnovalidate>Procurar país</button>
        

       <?php if (!empty($_SESSION['pais_matches'])): ?>
          <p>Países encontrados:</p>
          <div class="radio-group">
            <?php foreach($_SESSION['pais_matches'] as $m): ?>
              <label class="radio-option">
                <input type="radio" name="pais_selecionado" value="<?= htmlspecialchars($m) ?>" required
                <?= (isset($_SESSION['pais_selecionado']) && $_SESSION['pais_selecionado'] === $m) ? 'checked' : '' ?>>
                <?= htmlspecialchars($m) ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

 <div class="form-group">
        <label for="preferencia_de_viagem">Preferências de viagem:</label>
        <p class="helper-text">Selecione pelo menos uma opção</p>
        <div class="checkbox-group">
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Praia" <?= in_array('Praia', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Praia</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Cidade" <?= in_array('Cidade', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Cidade</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Natureza" <?= in_array('Natureza', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Natureza</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Neve" <?= in_array('Neve', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Neve</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Aventura" <?= in_array('Aventura', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Aventura</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Relaxamento" <?= in_array('Relaxamento', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Relaxamento</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Cultura" <?= in_array('Cultura', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Cultura</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Gastronomia" <?= in_array('Gastronomia', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Gastronomia</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Romântica" <?= in_array('Romântica', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Romântica</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Familiar" <?= in_array('Familiar', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Familiar</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Compras" <?= in_array('Compras', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Compras</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Económica" <?= in_array('Económica', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Económica</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Luxo" <?= in_array('Luxo', $prefSelecionadas) ? 'checked' : '' ?>>
            <span>Luxo</span>
          </label>
        </div>
      </div>
      
      <?php if (!isset($editar)): ?>
        <div class="form-group">
          <label for="profile_pic">Foto de perfil</label>
          <input type="file" id="profile_pic" name="profile_pic" accept="image/png,image/jpeg">
        </div>
      <?php else: ?>
        <div class="form-group">
          <label for="profile_pic">Nova foto de perfil (Opcional)</label>
          <input type="file" id="profile_pic" name="profile_pic" accept="image/png,image/jpeg">
        </div>
      <?php endif; ?>

      <button type="submit">Registar</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>
