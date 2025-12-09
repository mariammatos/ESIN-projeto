<?php
  session_start();

  // get username and password from HTTP parameters
  $username = $_POST['username'];
  $password = $_POST['password'];

  // check if username and password are correct
  function loginSuccess($username, $password) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT * FROM Users WHERE username = ? AND password = ?');
    $stmt->execute(array($username, hash('sha256', $password)));
    return $stmt->fetch();
  }

  // if login successful:
  // - create a new session attribute for the user
  // - redirect user to main page
  // else:
  // - set error msg "Login failed!"
  // - redirect user to main page

  try {
    $dbh = new PDO('sqlite:sql/products.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (loginSuccess($username, $password)) {
      $_SESSION['username'] = $username;
    } else {
      $_SESSION['msg'] = 'Invalid username or password!';
    }

  } catch (PDOException $e) {
    $_SESSION['msg'] = 'Error: ' . $e->getMessage();
  }

  header('Location: list_categories.php');
?>