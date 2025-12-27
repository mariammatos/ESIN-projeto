<?php
session_start();
require_once '../database/db_connect.php';
require_once '../database/destinos.php';
$dbh = getDatabaseConnection();

$_SESSION['pais_matches'] = [];

if (!empty($_POST['pais'])) {
    $pais_input = trim($_POST['pais']);
    $_SESSION['pais'] = $pais_input;

    $_SESSION['pais_matches'] = procurarpaises($dbh, $pais_input);

}

if (isset($_POST['registar'])) {
    $_SESSION['form_data']['username'] = $_POST['username'] ?? '';
    $_SESSION['form_data']['email'] = $_POST['email'] ?? '';
    $_SESSION['form_data']['nome'] = $_POST['nome'] ?? '';
    $_SESSION['local'] = $_POST['local'] ?? '';
    header('Location: ../registration.php');
    exit;
}  
else {
    $_SESSION['form_data']['titulo'] = $_POST['titulo'] ?? '';
    $_SESSION['form_data']['data_ida'] = $_POST['data_ida'] ?? '';
    $_SESSION['form_data']['data_volta'] = $_POST['data_volta'] ?? '';
    header('Location: ../nova_viagem.php');
    exit;
}
?>