<?php
  session_start(); 
  require_once '../database/db_connect.php';
  require_once '../database/media.php';

  $db = getDatabaseConnection();
  $viagem_id = $_POST['viagem_id'] ?? null;
  $files = $_FILES['fotos'];

 
    foreach ($files['name'] as $index => $name) {
      $_FILES['media_file'] = [
        'name' => $files['name'][$index],
        'type' => $files['type'][$index],
        'tmp_name' => $files['tmp_name'][$index],
        'error' => $files['error'][$index],
        'size' => $files['size'][$index],
      ];
      saveMediaViagem($db, $viagem_id);
    }
  
    $_SESSION['msg'] = "Fotos adicionadas com sucesso!";
    header("Location: ../viagem.php?id=" . $viagem_id);
    exit();
?>