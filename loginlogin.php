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
    <link rel="stylesheet" href="css/stylelogin.css"> 
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin%7CMerriweather" rel="stylesheet"> 
  </head>
  <body>

    <?php echo $msg ?>
    <section id="login">
      <h2>Login</h2>
      <form action="login.php" method="post">
        <input type="text" name="username" placeholder="username">
        <input type="password" name="password" placeholder="password">
        <button>Login</button>
      </form>
    </section>
    <section id="registar">
        <h1>Ainda não tem uma conta?</h1>
        <a href="registration.php" class="btn-signup">Registe-se agora!</a>
    </section>
    <footer>
      <p>Copyright &copy; André Restivo, 2018</p>
    </footer>
  </body>
</html>