<?php

namespace Tests\Feature;

use App\Services\Number\NumberToolService;
use InvalidArgumentException;
use Tests\TestCase;

class NumberToolsTest extends TestCase
{
    public function test_number_formatter_formats_values_consistently(): void
    {
        $service = new NumberToolService();

        $this->assertSame('1,234,567.89', $service->formatNumber(1234567.89));
        $this->assertSame('1.234.567,89', $service->formatNumber(1234567.89, 2, '.', ','));
    }

    public function test_base_converter_handles_decimal_binary_and_hex(): void
    {
        $service = new NumberToolService();

        $this->assertSame('11111111', $service->convertBase('255', 10, 2));
        $this->assertSame('FF', $service->convertBase('255', 10, 16));
        $this->assertSame('255', $service->convertBase('FF', 16, 10));
    }

    public function test_base_converter_rejects_invalid_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new NumberToolService())->convertBase('2', 2, 10);
    }

    public function test_number_formatter_rejects_invalid_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new NumberToolService())->formatNumber('not-a-number');
    }
}
