<?php

$config = require __DIR__ . '/config.php';
foreach ( $config['databases'] as $databaseKey => $databaseData ) {
    if ( isset($databaseData['create']) && $databaseData['create'] === false ) {
        continue;
    }

    $url = $databaseData['host'] . '/' . $databaseData['dbname'];


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

    curl_setopt($ch, CURLOPT_PUT, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // führe die Aktion aus und gebe die Daten an den Browser weiter
    curl_exec($ch);

    // schließe den cURL-Handle und gebe die Systemresourcen frei
    curl_close($ch);
}