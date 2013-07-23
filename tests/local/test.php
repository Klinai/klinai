<?php

use Klinai\Client\Client;
use Klinai\Client\ClientConfig;

chdir(dirname(__DIR__));

require './_autoload.php';
require './_files/initDefaultDatabase.php';


$config = new ClientConfig( require './_files/config.php' );
$client = new Client();
$client->setConfig($config);



$docData1 = array(
        'key1'=>'foo',
        'key2'=>'bar',
);

$response1 = $client->storeDoc('client_test1', $docData1);

echo 'var_dump($response1):' . "\n";
var_dump($response1);

$docData2 = array(
        '_id'=>$response1->id,
        '_rev'=>$response1->rev,
        'key1'=>'foo',
        'key2'=>'test',
);
$response2 = $client->storeDoc('client_test1', $docData2);

echo 'var_dump($response2):' . "\n";
var_dump($response2);

echo 'var_dump($response1->id,$response2->id,$response1->id==$response2->id):' . "\n";
var_dump($response1->id,$response2->id,$response1->id==$response2->id);

echo 'var_dump($response1->rev,$response2->rev,$response1->rev==$response2->rev):' . "\n";
var_dump($response1->rev,$response2->rev,$response1->rev==$response2->rev);
