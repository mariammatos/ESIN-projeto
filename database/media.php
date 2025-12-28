<?php

  // retorna as fotos associadas a uma viagem
  function getFotos($db, $viagem_id) {
    $stmt = $db->prepare(
        'SELECT 
            id, Path, data
        FROM 
            Media
        WHERE 
            Viagem = :viagem_id
        ORDER BY 
            id ASC;'
    
    );

    $stmt->bindParam(':viagem_id', $viagem_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // guarda fotos associadas a uma viagem
  function saveMediaViagem($db, $viagem_id) {
    $file = $_FILES['media_file'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $viagem_id . '_' . uniqid() . '.' . $extension;
    $path = "../media/viagens/$filename";
    $data = date('Y-m-d H:i:s');
    $stmt = $db->prepare('INSERT INTO Media (Viagem, path, data) VALUES (?, ?, ?)');
    $stmt->execute(array($viagem_id, $path, $data));
    move_uploaded_file($file['tmp_name'], $path);
  }

  // guarda media associada a um feedback
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