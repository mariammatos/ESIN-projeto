<?php
// Nome do ficheiro da base de dados SQLite.
$db_file = 'triptales.db'; 

// Tenta estabelecer a ligação.
try {
    // Cria uma nova instância PDO (PHP Data Objects) para o SQLite.
    $db = new PDO("sqlite:" . $db_file);
    
    // Define o modo de erro para exceções, útil para debugging.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ativa as Foreign Keys, que são desativadas por padrão no SQLite.
    // Isto é CRUCIAL para garantir a integridade dos seus dados!
    $db->exec('PRAGMA foreign_keys = ON;');

    // NOTA: A ligação $db está agora disponível para outros ficheiros PHP que a incluam.

} catch (PDOException $e) {
    // Se a ligação falhar, exibe uma mensagem de erro.
    die("Erro ao ligar à base de dados TripTales: " . $e->getMessage());
}

?>