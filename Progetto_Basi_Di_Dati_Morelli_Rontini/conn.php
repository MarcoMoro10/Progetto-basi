<?php
     // Connessione al DB
    $servername = "127.0.0.1";
    $username = "root";
    $password = "1234";
    $dbname = "EFORM";
    $dsn = "mysql:host=$servername;dbname=$dbname";
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try { $conn = new PDO($dsn, $username, $password, $options);
    } catch(PDOException $e) {
        echo "Errore nella connessione al database: " . $e->getMessage();
    }
        
    ?>