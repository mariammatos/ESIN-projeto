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
        
        <button type="submit" formaction="actions/action_checkusername.php" formnovalidate>Verificar </button>
      </div>

      <div class="form-group">
        <label for="password">Palavra-passe:</label>
        <input type="password" id="password" name="password">
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirmar palavra-passe:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
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
          <input type="checkbox" name="pref[]" value="Praia">Praia
          <input type="checkbox" name="pref[]" value="Cidade">Cidade
          <input type="checkbox" name="pref[]" value="Natureza">Natureza
          <input type="checkbox" name="pref[]" value="Neve">Neve
          <input type="checkbox" name="pref[]" value="Aventura">Aventura
          <input type="checkbox" name="pref[]" value="Relaxamento">Relaxamento
          <input type="checkbox" name="pref[]" value="Cultura">Cultura
          <input type="checkbox" name="pref[]" value="Gastronomia">Gastronomia
          <input type="checkbox" name="pref[]" value="Romântica">Romântica
          <input type="checkbox" name="pref[]" value="Familiar">Familiar
          <input type="checkbox" name="pref[]" value="Compras">Compras
          <input type="checkbox" name="pref[]" value="Económica">Económica
          <input type="checkbox" name="pref[]" value="Luxo">Luxo
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