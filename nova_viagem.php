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
    <h2>Crie uma nova viagem!</h2>

    <form action="actions/action_criarviagem.php" method="post" enctype="multipart/form-data">

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

        <div class="form-group">
            <label for="pais">País:</label>
            <input type="text" name="pais" id="pais" value="<?= htmlspecialchars($_SESSION['pais'] ?? '') ?>" required>
            <button type="submit" formaction="actions/action_procurarpais.php" formnovalidate>Procurar país</button>
        

            <?php
            if (!empty($_SESSION['pais_matches'])):
                echo '<p>Países encontrados:</p>';
                foreach($_SESSION['pais_matches'] as $m):
            ?>
                <input type="radio" name="pais_selecionado" value="<?= htmlspecialchars($m) ?>" required>
                <?= htmlspecialchars($m) ?><br>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="form-group">
            <label for="local">Cidade/região/local:</label>
            <input type="text" name="local" id="local" value="<?= htmlspecialchars($_SESSION['local'] ?? '') ?>" placeholder="Digite o local" required>
        </div>


      <button type="submit">Publicar</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>