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

try {
    $filename = $safe->setDirectory($env->dir_files)->getDocument($env->document);
    var_dump($filename);

    $metadata = $safe->getMetadata();
    var_dump($metadata);
} catch (DocumentException $e) {
    echo $e->getMessage();
} catch (ClientException $e) {
    echo $e->getMessage();
}
