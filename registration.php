<?php
  session_start();

  $msg = $_SESSION['msg'];
  $msg1 = $_SESSION['msg1'];
  unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TripTales</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css"> 
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin%7CMerriweather" rel="stylesheet"> 
  </head>
  <body>


  <section id="registration">
    <?php echo $msg ?>
    <h2>Registration</h2>

    <form action="register.php" method="post">

      <div class="form-group">
        <label for="username">Nome de utilizador:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_SESSION['form_data']['username'] ?? '') ?>" required>
        <?php echo $msg1 ?>
        <button type="submit" formaction="action_checkusername.php" formnovalidate>Verificar</button>
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
        <label for="pais_de_origem">País de origem:</label>
        <input type="text" id="pais_de_origem" name="pais_de_origem" value="<?= htmlspecialchars($_SESSION['form_data']['pais_de_origem'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="preferencia_de_viagem">Preferências de viagem:</label>
        <textarea id="preferencia_de_viagem" name="preferencia_de_viagem" required></textarea>
      </div>

      <div class="form-group">
        <label for="foto_de_perfil">Link da foto de perfil:</label>
        <input type="text" id="foto_de_perfil" name="foto_de_perfil" value="<?= htmlspecialchars($_SESSION['form_data']['foto_de_perfil'] ?? '') ?>" required>
      </div>

      <button type="submit">Register</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>