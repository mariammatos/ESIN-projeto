<?php
  session_start();

  $msg = $_SESSION['msg'];
  $msg1 = $_SESSION['msg1'];
  unset($_SESSION['msg']);
  unset($_SESSION['msg1']);

  if (!isset($_SESSION['pais_matches'])) {
    $_SESSION['pais_matches'] = [];
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

    <a href="login.php" class="btn-voltar">← Voltar ao Login</a>


  <section id="registration">
    <?php echo $msg ?>
    <h2>Registo</h2>

    <form action="actions/action_register.php" method="post" enctype="multipart/form-data">

      <div class="form-group">
        <label for="username">Nome de utilizador:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_SESSION['form_data']['username'] ?? '') ?>" required>
        
        <?php if (!empty($msg1)): ?>
          <div class="validation-message <?= strpos($msg1, 'disponível') !== false ? 'success' : 'error' ?>">
            <?= strip_tags($msg1) ?>
          </div>
        <?php endif; ?>
        
        <button type="submit" formaction="actions/action_checkusername.php" formnovalidate>Verificar</button>
      </div>

      <div class="form-group">
        <label for="password">Palavra-passe:</label>
        <input type="password" id="password" name="password">
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirmar palavra-passe:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
          <div id="password-match-message" class="validation-message" style="display: none;"></div>
      </div>

      <div class="form-group">
        <label for="email">Endereço de e-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="nome">Nome completo:</label>
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
                <input type="radio" name="pais_selecionado" value="<?= htmlspecialchars($m) ?>" required>
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
            <input type="checkbox" name="pref[]" value="Praia">
            <span>Praia</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Cidade">
            <span>Cidade</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Natureza">
            <span>Natureza</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Neve">
            <span>Neve</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Aventura">
            <span>Aventura</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Relaxamento">
            <span>Relaxamento</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Cultura">
            <span>Cultura</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Gastronomia">
            <span>Gastronomia</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Romântica">
            <span>Romântica</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Familiar">
            <span>Familiar</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Compras">
            <span>Compras</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Económica">
            <span>Económica</span>
          </label>
          <label class="checkbox-option">
            <input type="checkbox" name="pref[]" value="Luxo">
            <span>Luxo</span>
          </label>
        </div>
      </div>
      

      <div class="form-group">
        <label for="profile_pic">Foto de perfil</label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/png,image/jpeg">
      </div>

      <button type="submit">Registar</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>

<script>
    // Validação da palavra-passe em tempo real
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    const matchMessage = document.getElementById('password-match-message');
    
    function checkPasswordMatch() {
      // Só mostrar mensagem se o utilizador começou a escrever na confirmação
      if (passwordConfirm.value.length === 0) {
        matchMessage.style.display = 'none';
        return;
      }
      
      matchMessage.style.display = 'flex';
      
      if (password.value === passwordConfirm.value) {
        matchMessage.className = 'validation-message success';
        matchMessage.textContent = 'As palavras-passe coincidem!';
      } else {
        matchMessage.className = 'validation-message error';
        matchMessage.textContent = 'As palavras-passe não coincidem.';
      }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    passwordConfirm.addEventListener('input', checkPasswordMatch);
    
  </script>