<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../functions.php';

class ApiIntegrationTest extends TestCase
{
    private $configuratorUrl = 'http://localhost:8080/api-configurator.php';
    private $sessionId = 'testsession123';

    public function testConfigurator()
    {
        $sessionId = bin2hex(random_bytes(16));

        $res = $this->request('POST', $this->configuratorUrl, $sessionId, getArrayFromJsonFile(__DIR__ . "/resources/configurator-request-1.json"));
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);
        $this->assertEquals(
            getArrayFromJsonFile(__DIR__ . "/resources/configurator-response-1.json"),
            json_decode($res['body'], true)
        );
    }

    /*
    public function testSessionPersistsBetweenRequests()
    {
// 1️⃣ POST → store something in session
        $postRes = $this->request('POST', $this->baseUrl, null, [
            'priceOffer' => [
// minimal valid data
            ]
        ]);

        $this->assertNotFalse($postRes['body']);

// 2️⃣ GET → should read from SAME session
        $getRes = $this->request('GET', $this->baseUrl . '?getApiResponse=1');

        $data = json_decode($getRes['body'], true);

        $this->assertIsArray($data);

// adjust depending on your actual response structure
        $this->assertArrayHasKey('priceOffer', $data);
    }

    public function testInvalidJson()
    {
        $headers = "Cookie: PHPSESSID={$this->sessionId}\r\n";
        $headers .= "Content-Type: application/json\r\n";

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => 'invalid json',
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);

        $response = file_get_contents($this->baseUrl, false, $context);

        $this->assertStringContainsString('Invalid JSON', $response);
    }

    private function request($method, $url, $sessionId = null, $body = null): array
    {
        $cookieSessionId = $sessionId ?: $this->sessionId;
        $headers = "Cookie: PHPSESSID=$cookieSessionId\r\n";

        if ($method === 'POST') {
            $headers .= "Content-Type: application/json\r\n";
        }

        $options = [
            'http' => [
                'method' => $method,
                'header' => $headers,
                'ignore_errors' => true // IMPORTANT: allows reading 4xx/5xx responses
            ]
        ];

        if ($body !== null) {
            $options['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        return [
            'body' => $response,
            'headers' => $http_response_header ?? []
        ];
    }
    */
}