<?php

  function getDatabaseConnection() {
    $dbh = new PDO('sqlite:database/triptales.db');
    // Configurações standard para o seu projeto:
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->exec('PRAGMA foreign_keys = ON;'); // Ativar FKs é obrigatório no SQLite!
    return $dbh;
  }

?>