<?php

    function destinoexiste($pais, $local) {
        global $dbh;
        $stmt = $dbh->prepare('SELECT COUNT(*) FROM Destino WHERE pais = ? AND local = ?');
        $stmt->execute(array($pais, $local));
        return $stmt->fetchColumn() > 0;
    }

?>