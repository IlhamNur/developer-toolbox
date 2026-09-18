<?php

namespace Tests\Feature;

use App\Services\Json\JsonToolService;
use Tests\TestCase;

class JsonToolsTest extends TestCase
{
    public function test_json_formatting_is_valid_and_pretty_printed(): void
    {
        $service = new JsonToolService();

        $this->assertSame(
            "{\n  \"name\": \"Ilham\",\n  \"age\": 24\n}",
            $service->format('{"name":"Ilham","age":24}')
        );
    }

    public function test_invalid_json_is_rejected(): void
    {
        $service = new JsonToolService();

        $this->assertFalse($service->isValid('{"name": 1,}'));
        $this->assertNotNull($service->validate('{"name": 1,}'));
    }

    public function test_json_minification_removes_whitespace(): void
    {
        $service = new JsonToolService();

        $this->assertSame('{"name":"Ilham","age":24}', $service->minify('{"name": "Ilham", "age": 24}'));
    }

    public function test_json_escape_and_unescape_round_trip(): void
    {
        $service = new JsonToolService();

        $escaped = $service->escape("{\n  \"message\": \"hello\\nworld\"\n}");
        $this->assertSame("{\n  \"message\": \"hello\\nworld\"\n}", json_decode($escaped, true));
        $this->assertSame("{\n  \"message\": \"hello\\nworld\"\n}", $service->unescape($escaped));
    }

    public function test_json_diff_ignores_property_ordering(): void
    {
        $service = new JsonToolService();

        $result = $service->diff(
            '{"name":"Ilham","age":24,"active":true}',
            '{"age":24,"active":false,"name":"Ilham"}'
        );

        $this->assertArrayHasKey('changed', $result);
        $this->assertArrayHasKey('active', $result['changed']);
        $this->assertSame(false, $result['changed']['active']['to']);
    }

    public function test_json_can_be_converted_to_java_and_mvel_objects(): void
    {
        $service = new JsonToolService();
        $input = '{"name":"Ilham","active":true,"tags":["api","json"]}';

        $this->assertSame('Map.of("name", "Ilham", "active", true, "tags", ["api", "json"])', $service->toObject($input, 'java'));
        $this->assertSame('["name": "Ilham", "active": true, "tags": ["api", "json"]]', $service->toObject($input, 'mvel'));
    }
}
