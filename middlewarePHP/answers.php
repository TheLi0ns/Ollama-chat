<?php
    // Attiva il CORS
    header("Access-Control-Allow-Origin: http://localhost");
    
    // Per ambienti di produzione, è consigliabile limitare l'origine
    // Esempio: header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    // Oppure utilizzare una variabile d'ambiente: header("Access-Control-Allow-Origin: " . getenv('ALLOWED_ORIGIN'));
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");

    // Includi il file di connessione al database
    include 'connection.php';

    // Verifica se il cookie è stato impostato
    if (isset($_COOKIE['user_id'])) {
        $cookieId = $_COOKIE['user_id'];

        // Prepara la query per trovare le righe con il cookie ID uguale
        $query = "SELECT Prompt, Response FROM logs WHERE cookie_ID = '$cookieId'";

        // Esegue la query
        $result = mysqli_query($conn, $query);

        // Crea un array per memorizzare le righe trovate
        $righe = array();

        // Cicla attraverso le righe trovate
        while ($row = mysqli_fetch_assoc($result)) {
            // Crea un oggetto per ogni riga
            $oggetto = array(
                'prompt' => $row['Prompt'],
                'response' => $row['Response']
            );

            // Aggiungi l'oggetto all'array
            array_push($righe, $oggetto);
        }

        // Converte l'array in JSON
        $json = json_encode($righe);

        // Ritorna il JSON
        echo $json;
    } else {
        // Se il cookie non è stato impostato, ritorna un messaggio di errore
        http_response_code(404);
        echo json_encode(array('error' => 'Cookie non impostato'));
    }

    // Chiudi la connessione al database
    mysqli_close($conn);
?>
