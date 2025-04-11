<?php

    // Attiva il CORS
    header("Access-Control-Allow-Origin: http://localhost");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");
    
    // Per ambienti di produzione, è consigliabile limitare l'origine
    // Esempio: header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    // Oppure utilizzare una variabile d'ambiente: header("Access-Control-Allow-Origin: " . getenv('ALLOWED_ORIGIN'));

    // Connessione
    include "connection.php";

    if (!isset($_COOKIE['user_id'])) {
        // Se il cookie non esiste, genera un ID univoco
        $userId = uniqid("user_", true);
        // Imposta il cookie con una durata di 1 giorno
        setcookie("user_id", $userId, time() + (24 * 60 * 60), "/");
    } else {
        // Recupera l'ID dal cookie
        $userId = $_COOKIE['user_id'];
    }

    // Impostazioni API
    $api_url = $_ENV["OLLAMA_URL"];
    $chiedi = $_POST["prompt"];

    // Preparazione dati per la richiesta API
    $data = [
        'prompt' => $chiedi,
        'model' => "llama3.2:1b",
        'stream' => false
    ];

    // Invio richiesta a Ollama
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);

    // Log della richiesta e risposta nel database
    if (isset($response_data['response'])) {
        // Prepara la query SQL
        $stmt = $conn->prepare("INSERT INTO logs (Prompt, Response, cookie_ID) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $chiedi, $response_data['response'], $userId);
        
        if ($stmt->execute()) {
            // Successo nel logging
            echo $response;
        } else {
            // Errore nel logging
            error_log("Errore nel logging: " . $stmt->error);
            echo $response; // Ritorna comunque la risposta
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Errore nella risposta di Llama']);
    }

    // Chiudi connessione MySQL
    $conn->close();
?>
