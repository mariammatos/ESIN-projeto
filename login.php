<?php
  session_start();

  $msg = $_SESSION['msg'];
  unset($_SESSION['msg']);
  $viagem = $_SESSION['viagem'];
  unset($_SESSION['viagem']);
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

  <a href="index.php" class="btn-voltar">← Voltar</a>
  <?php echo htmlspecialchars($viagem); ?>


    <?php if (!empty($msg)): ?>
        <p class="error-message">
            <?php echo htmlspecialchars($msg); ?>
        </p>
    <?php endif; ?>
    
    <section id="login">
      <h2>Login</h2>
      <form action="actions/action_login.php" method="post">
        <input type="hidden" name="viagem" value="<?php echo htmlspecialchars($viagem); ?>">
        <input type="text" name="username" placeholder="username">
        <input type="password" name="password" placeholder="password">
        <button>Login</button>
      </form>
    </section>
    <section id="registar">
        <h1>Ainda não tem uma conta?</h1>
        <a href="registration.php" class="btn-signup">Registe-se agora!</a>
    </section>

<?php include_once 'templates/footer_tpl.php'; ?>