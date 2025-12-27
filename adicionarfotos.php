<?php
  session_start(); 
    $msg = $_SESSION['msg'];
    $viagem_id = $_POST['viagem_id'];
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
    <h2>Adicionar fotos</h2>

    <form action="actions/action_adicionarfotos.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="viagem_id" value="<?= $viagem_id ?>">
      
      <div class="form-group">
        <label for="fotos">Fotos</label>
        <input type="file" id="fotos" name="fotos[]" accept="image/png,image/jpeg" multiple>
      </div>

      <button type="submit">Adicionar Fotos</button>
    </form>
  </section>


    <footer>
      <p>&copy; 2025 TripTales. Projeto ESIN.</p>
    </footer>
  </body>
</html>