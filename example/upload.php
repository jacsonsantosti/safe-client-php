<?php

chdir(dirname(__DIR__));
require "vendor/autoload.php";
$env = json_decode(file_get_contents("example/data.json"));

use DataFrost\Core\Safe;
use DataFrost\Exception\DocumentException;
use GuzzleHttp\Exception\ClientException;

$safe = new Safe([
    'host' => 'http://38.128.236.77:9090',
    'headers' => ['X-AUTH-TOKEN' => $env->access_token]
]);

$metadatas = [
    'x-safe-meta-cpf' => 55844774000
];

try {
    $document = fopen($env->dir_files . '/' . $env->document, 'r');
    $response = $safe->setMetadata($metadatas)->setDocument($document, $env->uuid);
    echo $response;
} catch (DocumentException $e) {
    echo $e->getMessage();
} catch (ClientException $e) {
    echo $e->getMessage();
}
