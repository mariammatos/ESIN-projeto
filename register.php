<?php
  session_start();

  $username = $_POST['username'];
  $password = $_POST['password'];

  function insertUser($username, $password) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Users (username, password) VALUES (?, ?)');
    $stmt->execute(array($username, hash('sha256', $password)));
  }

  if (strlen($username) == 0) {
    $_SESSION['msg'] = 'Invalid username!';
    header('Location: registration.php');
    die();
  }

  if (strlen($password) < 8) {
    $_SESSION['msg'] = 'Password must have at least 8 characters.';
    header('Location: registration.php');
    die();
  }

  try {
    $dbh = new PDO('sqlite:sql/products.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    insertUser($username, $password);
    $_SESSION['msg'] = 'Registration successful!';
    header('Location: list_categories.php');
  } catch (PDOException $e) {
    $error_msg = $e->getMessage();

    if (strpos($error_msg, 'UNIQUE')) {
      $_SESSION['msg'] = 'Username already exists!';
    } else {
      $_SESSION['msg'] = "Registration failed! ($error_msg)";
    }
    header('Location: registration.php');
  }
?>