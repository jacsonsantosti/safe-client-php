<?php

namespace DataFrost\Core;

use DataFrost\Exception\BucketException;
use DataFrost\Exception\DocumentException;
use DataFrost\Http\Client;
use DataFrost\Support\Arr;
use DataFrost\Support\Str;

class Safe
{
    /**
     * @var \GuzzleHttp\Client $client 
     */
    protected $client;

    /**
     * @var string $host 
     */
    protected $host;

    /**
     * @var array $headers
     */
    protected $headers = [];

    /**
     * @var string $version
     */
    protected $version;

    /**
     * @var string $directory
     */
    protected $directory = null;

    /**
     * @var array $metadata
     */
    protected $metadata = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->client  = Client::getInstance();
        $this->host    = $options['host'] ?? null;
        $this->headers = $options['headers'] ?? [];
        $this->version = $options['version'] ?? 'v1';
        $this->directory = $options['directory'] ?? null;
    }

    /**
     * @param string $host
     * @return Safe
     */
    public function setHost(string $host): Safe
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param array $headers
     * @return Safe
     */
    public function setHeaders(array $headers): Safe
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $version
     * @return Safe
     */
    public function setVersion(string $version): Safe
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @throws BucketException
     * @return array
     */
    public function getBuckets(): array
    {
        $response = $this->client->request(
            'GET',
            $this->host . '/api/' . $this->version . '/documents',
            [
                'headers' => $this->headers
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new BucketException("Error Processing Request", $response->getStatusCode());
        }

        $response = Str::toArray("\n", $response->getBody());
        return Arr::filter($response);
    }

    /**
     * @param string $uuid
     * @throws DocumentException
     * @return resource|string
     */
    public function getDocument(string $uuid)
    {
        $response = $this->client->request(
            'GET',
            $this->host . '/api/' . $this->version . '/documents/' . $uuid,
            [
                'headers' => $this->headers
            ]
        );

        $this->metadata = Arr::filterRegex($response->getHeaders(), 'x-safe-meta-\w+');

        if ($response->getStatusCode() !== 200) {
            throw new DocumentException("Error Processing Request", $response->getStatusCode());
        }

        if (!is_null($this->directory)) {
            $this->createDirectory($this->directory);
            $filename = $this->directory . '/' . $uuid;
            $this->append($filename, $response->getBody());
            return $filename;
        }

        return $response->getBody();
    }

    /**
     * @param string $directory
     * @return Safe
     */
    public function setDirectory(string $directory): Safe
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @param string $path
     * @return void
     */
    private function createDirectory(string $path): void
    {
        if (!is_dir($path)) mkdir($path, 0777, true);
    }

    /**
     * @param string $filename
     * @param resource|string $content
     * @return void
     */
    private function append(string $filename, $content): void
    {
        file_put_contents($filename, $content);
    }

    /**
     * @param resource|string
     * @throws DocumentException
     */
    public function setDocument($file, $uuid)
    {
        if (!is_resource($file)) {
            $file = $this->resource($file);
        }

        $response = $this->client->request(
            'PUT',
            $this->host . '/api/' . $this->version . '/documents/' . $uuid,
            array_merge(
                [
                    'headers' => $this->headers,
                    'body' => $file
                ],
                $this->metadata
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new DocumentException("Error Processing Request", $response->getStatusCode());
        }

        return $response->getBody();
    }

    /**
     * @param string $file
     * @return resource
     */
    private function resource(string $file)
    {
        return fopen($file, 'r');
    }

    /**
     * @param array $metadata
     * @return Safe
     */
    public function setMetadata(array $metadata): Safe
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }
}
