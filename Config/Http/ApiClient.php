<?php
// Libraries/Http/ApiClient.php

class ApiClient
{
    private string $baseUrl;
    private int $timeout;
    private array $defaultHeaders;

    public array $lastRequest = [];
    public array $lastResponse = [];

    public function __construct(string $baseUrl, int $timeout = 25, array $defaultHeaders = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = max(1, $timeout);

        $this->defaultHeaders = array_merge([], $defaultHeaders);
    }

    /** POST application/x-www-form-urlencoded */
    public function postUrlEncoded(string $path, array $fields, array $headers = []): array
    {
        $url  = $this->buildUrl($path);
        $body = http_build_query($fields, '', '&', PHP_QUERY_RFC3986);

        $finalHeaders = array_merge(
            $this->defaultHeaders,
            [
                'Content-Type: application/x-www-form-urlencoded',
                'Expect:',
                'Connection: close',
            ],
            $headers
        );

        $this->lastRequest = [
            'method'  => 'POST',
            'url'     => $url,
            'headers' => $finalHeaders,
            'body'    => $body,
            'body_len' => strlen($body),
        ];

        $respHeaders = [];
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HTTPHEADER     => $finalHeaders,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HEADERFUNCTION => function ($curl, $headerLine) use (&$respHeaders) {
                $len = strlen($headerLine);
                $headerLine = trim($headerLine);
                if ($headerLine === '' || strpos($headerLine, ':') === false) {
                    return $len;
                }
                [$k, $v] = explode(':', $headerLine, 2);
                $respHeaders[trim($k)] = trim($v);
                return $len;
            },
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $raw   = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $code  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $json = null;
        $decoded = json_decode((string)$raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $json = $decoded;
        }

        $result = [
            'ok'      => ($errno === 0 && $code >= 200 && $code < 300),
            'status'  => $code,
            'body'    => $raw,
            'json'    => $json,
            'headers' => $respHeaders,
            'error'   => $errno ? ("cURL {$errno}: {$err}") : null,
        ];

        $this->lastResponse = $result;
        return $result;
    }

    private function buildUrl(string $path): string
    {
        $path = ltrim($path, '/');
        return $this->baseUrl . '/' . $path;
    }
}
