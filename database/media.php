<?php
  function getFotos($db, $viagem_id) {
    $stmt = $db->prepare(
        'SELECT 
            Path, data
        FROM 
            Media
        WHERE 
            Viagem = :viagem_id
        ORDER BY 
            data DESC;'
    );

    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  function saveMediaViagem($db, $viagem_id) {
    $file = $_FILES['media_file'];
    $filename = basename($file['name']);
    $path = "../media/viagens/$filename";
    $data = date('Y-m-d H:i:s');
    $stmt = $db->prepare('INSERT INTO Media (Viagem, path, data) VALUES (?, ?, ?)');
    $stmt->execute(array($viagem_id, $path, $data));
    move_uploaded_file($file['tmp_name'], $path);
  }

  function saveMediaFeedback($db, $feedback_id) {
    $file = $_FILES['media_file'];
    $filename = basename($file['name']);
    $path = "../media/viagens/$filename";
    $data = date('Y-m-d H:i:s');
    $stmt = $db->prepare('INSERT INTO Media (Feedback, path, data) VALUES (?, ?, ?)');
    $stmt->execute(array($feedback_id, $path, $data));
    move_uploaded_file($file['tmp_name'], $path);
  }
  

?>