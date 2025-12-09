<?php
  session_start();

  $msg = $_SESSION['msg'];
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
      <h2>Registration</h2>
      <form action="register.php" method="post">
        <input type="text" name="username" placeholder="username">
        <input type="password" name="password" placeholder="password">
        <input type="password" name="password_confirm" placeholder="Confirmar Palavra-passe" required>
        <input type="email" name="email" placeholder="Endereço de E-mail" required>
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="text" name="pais_de_origem" placeholder="País de Origem" required> 
        <textarea name="preferencia_de_viagem" placeholder="As suas preferências de viagem" required></textarea>
        <input type="text" name="foto_de_perfil" placeholder="Link da Foto de Perfil" required>
        <button>Register</button>
      </form>
    </section>

    <footer>
      <p>Copyright &copy; André Restivo, 2018</p>
    </footer>
  </body>
</html>