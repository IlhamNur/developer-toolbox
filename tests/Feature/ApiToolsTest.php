<?php

namespace Tests\Feature;

use App\Services\Api\CurlGeneratorService;
use App\Services\Api\HttpRequestBuilderService;
use App\Services\Api\ApiTemplateService;
use App\Services\Api\ApiRequestTesterService;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
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

    public function test_api_templates_resolve_environment_variables(): void
    {
        $service = new ApiTemplateService();

        $this->assertSame(
            'https://api.example.com/items?token=demo',
            $service->resolve('{{BASE_URL}}/items?token={{TOKEN}}', "BASE_URL=https://api.example.com\nTOKEN=demo")
        );
    }

    public function test_api_request_tester_returns_status_time_and_body(): void
    {
        Http::fake([
            'https://example.com/items' => Http::response(['ok' => true], 200),
        ]);

        $response = (new ApiRequestTesterService())->send('GET', 'https://example.com/items');

        $this->assertSame(200, $response['status']);
        $this->assertTrue($response['successful']);
        $this->assertStringContainsString('"ok":true', $response['body']);
    }

    public function test_api_request_tester_rejects_private_urls(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ApiRequestTesterService())->send('GET', 'http://127.0.0.1:8080/health');
    }
}
