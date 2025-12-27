<?php
  session_start();

  if (!isset($_SESSION['em_andamento'])) {
        $_SESSION['em_andamento'] = false;
   }

  if (isset($_POST['toggle_andamento'])) {
        $_SESSION['em_andamento'] = !$_SESSION['em_andamento'];
  }

  if (isset($_POST['titulo'])) {
      $_SESSION['form_data']['titulo'] = $_POST['titulo'];
  }

  if (isset($_POST['data_ida'])) {
      $_SESSION['form_data']['data_ida'] = $_POST['data_ida'];
  }

  if (isset($_POST['data_volta'])) {
      $_SESSION['form_data']['data_volta'] = $_POST['data_volta'];
  }
  
  $viagem_id = $_POST['viagem_id'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TripTales</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/stylenova_viagem.css"> 
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin%7CMerriweather" rel="stylesheet"> 
  </head>
  <body>

  <a href="feed.php" class="btn-voltar">← Voltar ao Feed</a>

  <section id="registration">
    <?php echo $msg ?>

    <form action="actions/action_criarviagem.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="editar" value="1">
        <input type="hidden" name="viagem_id" value="<?= htmlspecialchars($viagem_id) ?>">

      <div class="form-group">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($_SESSION['form_data']['titulo'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="data_ida">Início:</label>
        <input type="date" id="data_ida" name="data_ida" value="<?= htmlspecialchars($_SESSION['form_data']['data_ida'] ?? '') ?>" required>
      </div>

        <div class="form-group">
            <label for="data_volta">Fim:</label>
            <input type="date"
                name="data_volta"
                id="data_volta"
                <?php if ($_SESSION['em_andamento']) echo 'disabled'; ?>>

        <button type="submit" formaction="nova_viagem.php" name="toggle_andamento" formnovalidate>
            <?php echo $_SESSION['em_andamento'] ? 'Viagem terminada!' : 'Ainda a decorrer'; ?>
        </button>
        </div>


      <button type="submit">Guardar Alterações</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>