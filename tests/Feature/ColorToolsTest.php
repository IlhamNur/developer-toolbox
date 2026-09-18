<?php

namespace Tests\Feature;

use App\Services\Color\ColorToolService;
use InvalidArgumentException;
use Tests\TestCase;

class ColorToolsTest extends TestCase
{
    public function test_hex_to_rgb_conversion_returns_expected_values(): void
    {
        $service = new ColorToolService();

        $this->assertSame(['r' => 255, 'g' => 0, 'b' => 0], $service->hexToRgb('#FF0000'));
        $this->assertSame(['r' => 0, 'g' => 255, 'b' => 0], $service->hexToRgb('#00ff00'));
    }

    public function test_rgb_to_hex_conversion_round_trips(): void
    {
        $service = new ColorToolService();

        $this->assertSame('#FF0000', $service->rgbToHex(255, 0, 0));
        $this->assertSame('#00FF00', $service->rgbToHex(0, 255, 0));
    }

    public function test_hex_to_rgb_rejects_invalid_colors(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ColorToolService())->hexToRgb('not-a-color');
    }
}
