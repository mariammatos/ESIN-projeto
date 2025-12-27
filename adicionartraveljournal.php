<?php
  session_start();


  if (isset($_POST['titulo'])) {
      $_SESSION['form_data']['titulo'] = $_POST['titulo'];
  }
  
  if (isset($_POST['descricao'])) {
    $_SESSION['form_data']['descricao'] = $_POST['descricao'];
  }

  if (isset($_POST['avaliacao'])) {
    $_SESSION['form_data']['avaliacao'] = $_POST['avaliacao'];
  }

  if (isset($_POST['editar'])) {
    $editar = $_POST['editar'];
  }

  $id_viagem = $_POST['viagem_id'] ?? '';
  $msg = $_SESSION['error'] ?? $_SESSION['msg'] ?? '';


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TripTales</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styletraveljournal.css"> 
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin%7CMerriweather" rel="stylesheet"> 
  </head>
  <body>


  <section id="registration">
    <?php echo $msg ?>
    <h2>Conte-nos tudo sobre a sua viagem!</h2>

    <form action="actions/action_criartraveljournal.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="viagem_id" value="<?= htmlspecialchars($id_viagem) ?>">
        <input type="hidden" name="editar" value="<?= htmlspecialchars($editar ?? false) ?>">


        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="6" required><?= htmlspecialchars($_SESSION['form_data']['descricao'] ?? '') ?></textarea>
        </div> 

      <div class="form-group">
        <label for="avaliacao">Avaliação:</label>
        <input name="avaliacao" type="number" value="<?= htmlspecialchars($_SESSION['form_data']['avaliacao'] ?? 0) ?>" min="0" max="5" step="1" required>
      </div>

      <button type="submit">Guardar</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>