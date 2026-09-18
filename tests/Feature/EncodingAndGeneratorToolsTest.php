<?php

namespace Tests\Feature;

use App\Services\Encoding\EncodingToolService;
use App\Services\Generator\GeneratorToolService;
use InvalidArgumentException;
use Tests\TestCase;

class EncodingAndGeneratorToolsTest extends TestCase
{
    public function test_base64_encode_and_decode_round_trip(): void
    {
        $service = new EncodingToolService();

        $encoded = $service->base64Encode('hello world');
        $this->assertSame('aGVsbG8gd29ybGQ=', $encoded);
        $this->assertSame('hello world', $service->base64Decode($encoded));
    }

    public function test_invalid_base64_is_handled_gracefully(): void
    {
        $service = new EncodingToolService();

        $this->assertNull($service->base64Decode('%%%not-base64%%%'));
    }

    public function test_url_encode_and_decode_round_trip(): void
    {
        $service = new EncodingToolService();

        $encoded = $service->urlEncode('hello world?name=Ilham');
        $this->assertSame('hello%20world%3Fname%3DIlham', $encoded);
        $this->assertSame('hello world?name=Ilham', $service->urlDecode($encoded));
    }

    public function test_newline_converter_escapes_and_restores_line_breaks(): void
    {
        $service = new EncodingToolService();
        $input = "line one\nline two\tvalue";

        $escaped = $service->newlineEscape($input);

        $this->assertSame('line one\\nline two\\tvalue', $escaped);
        $this->assertSame($input, $service->newlineUnescape($escaped));
    }

    public function test_uuid_generator_creates_valid_uuid_v4_values(): void
    {
        $service = new GeneratorToolService();

        $values = $service->uuid(5);

        $this->assertCount(5, $values);
        foreach ($values as $value) {
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
        }
    }

    public function test_random_string_generator_respects_requested_length_and_character_sets(): void
    {
        $service = new GeneratorToolService();

        $value = $service->randomString(24, true, true, true, false);

        $this->assertSame(24, strlen($value));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $value);
    }

    public function test_password_generator_enforces_constraints_and_strength(): void
    {
        $service = new GeneratorToolService();

        $password = $service->password(18, true, true, true, true, true);

        $this->assertSame(18, strlen($password));
        $this->assertTrue($service->passwordStrength($password)['score'] >= 3);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
    }

    public function test_password_generator_honors_ambiguous_character_setting(): void
    {
        $service = new GeneratorToolService();

        $excluded = $service->password(32, true, false, false, false, true);
        $allowed = $service->password(32, true, false, false, false, false);

        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $excluded);
        $this->assertDoesNotMatchRegularExpression('/[OI]/', $excluded);
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $allowed);
    }

    public function test_generators_reject_empty_character_pools(): void
    {
        $service = new GeneratorToolService();

        $this->expectException(InvalidArgumentException::class);
        $service->randomString(16, false, false, false, false);
    }

    public function test_password_generator_rejects_empty_character_pools(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new GeneratorToolService())->password(16, false, false, false, false);
    }
}
