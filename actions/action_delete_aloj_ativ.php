<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/alojamentos.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['id'], $_POST['tipo'])) {
    $db = getDatabaseConnection();
    $id = (int)$_POST['id'];
    $tipo = $_POST['tipo'];

    if ($tipo === 'atividade') {
        removerAtividade($db, $id); 
    } elseif ($tipo === 'alojamento') {
        removerAlojamento($db, $id); 
    }
}

header("Location: ../viagem.php?id=" . $_POST['viagem_id']);
exit();
