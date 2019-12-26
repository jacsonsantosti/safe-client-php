<?php

chdir(dirname(__DIR__));
require "vendor/autoload.php";
$env = json_decode(file_get_contents("example/data.json"));

use DataFrost\Core\Safe;
use DataFrost\Exception\BucketException;
use GuzzleHttp\Exception\ClientException;

$safe = new Safe([
    'host' => 'http://38.128.236.77:9090',
    'headers' => ['X-AUTH-TOKEN' => $env->access_token]
]);

try {
    $buckets = $safe->getBuckets();
    var_dump($buckets);
} catch (BucketException $e) {
    echo $e->getMessage();
} catch (ClientException $e) {
    echo $e->getMessage();
}
