<?php

namespace Tests\Feature;

use App\Services\Api\CurlGeneratorService;
use App\Services\Api\HttpRequestBuilderService;
use Tests\TestCase;

class ApiToolsTest extends TestCase
{
    public function test_curl_generator_builds_valid_command(): void
    {
        $service = new CurlGeneratorService();

        $curl = $service->build([
            'method' => 'POST',
            'url' => 'https://api.example.com/users',
            'headers' => ['Accept: application/json', 'Authorization: Bearer token'],
            'body' => '{"name":"Ada"}',
        ]);

        $this->assertStringContainsString('curl --request POST', $curl);
        $this->assertStringContainsString('https://api.example.com/users', $curl);
        $this->assertStringContainsString('Accept: application/json', $curl);
        $this->assertStringContainsString('--data', $curl);
    }

    public function test_http_request_builder_generates_valid_payload(): void
    {
        $service = new HttpRequestBuilderService();

        $payload = $service->build([
            'method' => 'GET',
            'url' => 'https://api.example.com/items',
            'headers' => ['X-Trace: abc123'],
            'query' => ['page' => '2', 'limit' => '10'],
        ]);

        $this->assertSame('GET', $payload['method']);
        $this->assertSame('https://api.example.com/items?page=2&limit=10', $payload['url']);
        $this->assertSame(['X-Trace: abc123'], $payload['headers']);
    }
}
