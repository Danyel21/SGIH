<?php

// Detalhes da conexão com a base de dados
define('DB_HOST', 'vsgate-s1.dei.isep.ipp.pt'); // Substitua pelo host da sua base de dados
define('DB_NAME', 'db1201707'); // Substitua pelo nome da sua base de dados
define('DB_CHARSET', 'utf8'); // Define o charset para UTF-8
define('DB_PORT', '10464'); // Substitua pela porta da sua base de dados
define('DB_USER', '1201707'); // Substitua pelo seu utilizador da base de dados
define('DB_PASS', 'oliveira_707'); // Substitua pela sua palavra-passe da base de dados
    
try {
    // Cria uma nova instância PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET . ";port=" . DB_PORT, DB_USER, DB_PASS);
    
    // Define o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define o modo de fetch padrão para objetos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Opcional: Define o fuso horário da conexão, se necessário
    // $pdo->exec("SET time_zone = '+00:00';");

} catch (PDOException $e) {
    // Em caso de erro na conexão, exibe uma mensagem e termina o script
    die("Erro de conexão com a base de dados: " . $e->getMessage());
}

?>
