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

header('Location: ../nova_viagem.php');
exit;