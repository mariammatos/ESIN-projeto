<?php
session_start();
require 'db_connect.php';
$pdo = getDatabaseConnection();

$_SESSION['pais_matches'] = [];

if (!empty($_POST['pais'])) {
    $pais_input = trim($_POST['pais']);
    $_SESSION['pais'] = $pais_input;
    function removeacentos($string) {
        return preg_replace(
            ['/À|Á|Â|Ã|Ä|Å/', '/à|á|â|ã|ä|å/', '/È|É|Ê|Ë/', '/è|é|ê|ë/',
             '/Ì|Í|Î|Ï/', '/ì|í|î|ï/', '/Ò|Ó|Ô|Õ|Ö/', '/ò|ó|ô|õ|ö/',
             '/Ù|Ú|Û|Ü/', '/ù|ú|û|ü/', '/Ç/', '/ç/'],
            ['A','a','E','e','I','i','O','o','U','u','C','c'],
            $string
        );
    }

    $pais_normalizado = mb_strtolower(removeacentos($pais_input), 'UTF-8');
    $pdo->sqliteCreateFunction('removeacentos', 'removeacentos', 1); // serve para podermos usar a função em sqlite

    // Pesquisa na base
    $sql = "SELECT pais FROM Destino WHERE LOWER(removeacentos(pais)) LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$pais_normalizado%"]);
    $_SESSION['pais_matches'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

}

// Redireciona de volta para o formulário principal
header('Location: ../nova_viagem.php');
exit;