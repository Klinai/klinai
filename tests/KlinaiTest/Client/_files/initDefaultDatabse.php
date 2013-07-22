<?php

$config = require 'config.php';

foreach ( $config['databases'] as $databaseKey => $databaseData ) {
    $url = $databaseData['host'] . '/' . $databaseData['dbname'];


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url );

    curl_setopt($ch, CURLOPT_PUT, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // führe die Aktion aus und gebe die Daten an den Browser weiter
    curl_exec($ch);

    // schließe den cURL-Handle und gebe die Systemresourcen frei
    curl_close($ch);
}