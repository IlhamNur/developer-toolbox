<?php

namespace Tests\Feature;

use App\Services\Text\TextToolService;
use App\Services\Color\ColorToolService;
use App\Services\Network\NetworkToolService;
use App\Services\Number\NumberToolService;
use Tests\TestCase;

class EncodingToolsExpansionTest extends TestCase
{
    public function test_helper_services_still_return_expected_values(): void
    {
        $text = new TextToolService();
        $color = new ColorToolService();
        $network = new NetworkToolService();
        $number = new NumberToolService();

        $this->assertSame('HELLO WORLD', $text->toUpper('hello world'));
        $this->assertSame('#FF0000', $color->rgbToHex(255, 0, 0));
        $this->assertTrue($network->isValidIp('8.8.8.8'));
        $this->assertSame('1,234.56', $number->formatNumber(1234.56));
    }
}
