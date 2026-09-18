<?php

namespace Tests\Feature;

use App\Services\Developer\DummyJsonService;
use App\Services\Developer\JwtDecoderService;
use App\Services\Developer\RegexTesterService;
use InvalidArgumentException;
use Tests\TestCase;

class DeveloperToolsTest extends TestCase
{
    public function test_regex_tester_extracts_matches_and_groups(): void
    {
        $service = new RegexTesterService();

        $result = $service->test('/([A-Z]+)-(\d+)/', 'ABC-123 XYZ-456');

        $this->assertSame(['ABC-123', 'XYZ-456'], $result['matches']);
        $this->assertSame(['ABC', '123'], $result['groups'][0]);
        $this->assertSame(['XYZ', '456'], $result['groups'][1]);
    }

    public function test_regex_tester_rejects_invalid_patterns(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new RegexTesterService())->test('/[/', 'text');
    }

    public function test_jwt_decoder_decodes_header_and_payload(): void
    {
        $service = new JwtDecoderService();

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFkYSJ9.signature';
        $result = $service->decode($token);

        $this->assertSame(['alg' => 'HS256', 'typ' => 'JWT'], $result['header']);
        $this->assertSame(['sub' => '1234567890', 'name' => 'Ada'], $result['payload']);
    }

    public function test_dummy_json_generator_creates_valid_json_payload(): void
    {
        $service = new DummyJsonService();

        $payload = $service->generate();

        $this->assertIsArray($payload);
        $this->assertNotEmpty($payload);
        $this->assertArrayHasKey('id', $payload[0]);
        $this->assertArrayHasKey('name', $payload[0]);
    }
}
