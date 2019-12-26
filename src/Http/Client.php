<?php

namespace DataFrost\Http;

class Client
{
    protected static $client;

    /**
     * @return \GuzzleHttp\Client
     */
    public static function getInstance(): \GuzzleHttp\Client
    {
        if (!isset(self::$client)) {
            self::$client = new \GuzzleHttp\Client();
        }

        return self::$client;
    }
}
