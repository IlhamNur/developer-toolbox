<?php

namespace Tests\Feature;

use App\Services\Api\ApiMonitorService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiMonitorTest extends TestCase
{
    public function test_monitor_reports_up_endpoint_status_and_time(): void
    {
        Http::fake(['https://example.com/health' => Http::response('ok', 200)]);

        $result = (new ApiMonitorService())->check('Example', 'https://example.com/health');

        $this->assertSame('UP', $result['status']);
        $this->assertSame(200, $result['code']);
        $this->assertSame('', $result['error']);
    }

    public function test_monitor_reports_blocked_endpoint_as_down(): void
    {
        $result = (new ApiMonitorService())->check('Local', 'http://127.0.0.1/health');

        $this->assertSame('DOWN', $result['status']);
        $this->assertStringContainsString('Private', $result['error']);
    }
}