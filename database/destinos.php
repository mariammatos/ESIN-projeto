<?php

    function normalize_string($string) {
        $string = mb_strtolower($string, 'UTF-8');
        return preg_replace(
            ['/À|Á|Â|Ã|Ä|Å/', '/à|á|â|ã|ä|å/', '/È|É|Ê|Ë/', '/è|é|ê|ë/',
            '/Ì|Í|Î|Ï/', '/ì|í|î|ï/', '/Ò|Ó|Ô|Õ|Ö/', '/ò|ó|ô|õ|ö/',
            '/Ù|Ú|Û|Ü/', '/ù|ú|û|ü/', '/Ç/', '/ç/'],
            ['A','a','E','e','I','i','O','o','U','u','C','c'],
            $string
        );
    }


    function getDestinoId($db, $pais, $local) {
        $local_normalizado = normalize_string($local);
        $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);

        $stmt = $db->prepare('SELECT id FROM Destino WHERE pais = ? AND LOWER(removeacentos(cidade_local)) = ?');
        $stmt->execute(array($pais, $local_normalizado));
        return $stmt->fetchColumn();
    }

    function insertdestino($db, $pais, $local) {
        $stmt = $db->prepare('INSERT INTO Destino (cidade_local, pais) VALUES (?, ?)');
        $stmt->execute(array($local, $pais));
        return $db->lastInsertId();
    }

    function procurarpaises($db, $pais_input) {
        $pais_normalizado = normalize_string($pais_input);
        $db->sqliteCreateFunction('removeacentos', 'normalize_string', 1);

        $stmt = $db->prepare('SELECT pais FROM Destino WHERE LOWER(removeacentos(pais)) LIKE ?');
        $stmt->execute(array("%$pais_normalizado%"));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

?>