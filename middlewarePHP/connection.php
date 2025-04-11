<?php
    // Parametri connessione MySQL (dovresti usarli da variabili d'ambiente)
    $db_host = $_ENV["DB_HOST"]; // Nome del servizio nel docker-compose
    $db_user = $_ENV["DB_USER"];
    $db_pass = $_ENV["DB_PASSWORD"];
    $db_name = $_ENV["DB_NAME"];

    // Connessione al database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Verifica connessione
    if ($conn->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
?>