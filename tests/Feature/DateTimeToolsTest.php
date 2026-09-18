<?php

namespace Tests\Feature;

use App\Services\DateTime\DateTimeToolService;
use Tests\TestCase;

class DateTimeToolsTest extends TestCase
{
    public function test_timestamp_converter_handles_basic_round_trip(): void
    {
        $service = new DateTimeToolService();

        $timestamp = $service->dateToTimestamp('2024-01-01T00:00:00Z');
        $this->assertSame(1704067200, $timestamp);
        $this->assertSame('2024-01-01 00:00:00', $service->timestampToDate(1704067200, 'UTC', 'Y-m-d H:i:s'));
    }

    public function test_invalid_datetime_is_handled_gracefully(): void
    {
        $service = new DateTimeToolService();

        $this->assertNull($service->dateToTimestamp('not-a-date'));
        $this->assertSame('', $service->timestampToDate('not-a-timestamp', 'UTC'));
    }
}
