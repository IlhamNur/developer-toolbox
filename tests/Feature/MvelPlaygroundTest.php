<?php

namespace Tests\Feature;

use App\Livewire\Tools\Developer\MvelPlayground;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MvelPlaygroundTest extends TestCase
{
    public function test_playground_casts_variables_and_displays_executor_result(): void
    {
        config(['services.mvel_executor.url' => 'http://mvel-executor.test']);
        Http::fake([
            'http://mvel-executor.test/api/mvel/execute' => Http::response([
                'success' => true,
                'result' => 'SRN123456',
                'type' => 'java.lang.String',
                'executionTimeMs' => 2.31,
            ]),
        ]);

        $playground = new MvelPlayground();
        $playground->variables = [[
            'name' => 'response',
            'type' => 'JSON',
            'value' => '{"data":{"ticketNumber":"SRN123456"}}',
        ]];
        $playground->expression = 'response.data.ticketNumber';
        $playground->run();

        $this->assertSame('SRN123456', $playground->result);
        $this->assertSame('java.lang.String', $playground->resultType);
        Http::assertSent(fn ($request) => $request['variables']['response']['data']['ticketNumber'] === 'SRN123456');
    }

    public function test_playground_rejects_invalid_typed_variables(): void
    {
        $playground = new MvelPlayground();
        $playground->variables = [['name' => 'age', 'type' => 'Integer', 'value' => 'not-a-number']];
        $playground->expression = 'age + 1';
        $playground->run();

        $this->assertStringContainsString('integers', $playground->error);
    }

    public function test_playground_reports_runtime_version_when_executor_is_available(): void
    {
        config(['services.mvel_executor.url' => 'http://mvel-executor.test']);
        Http::fake([
            'http://mvel-executor.test/api/mvel/health' => Http::response([
                'status' => 'UP',
                'mvelVersion' => '2.5.2.Final',
            ]),
        ]);

        $playground = new MvelPlayground();
        $playground->mount();

        $this->assertSame('2.5.2.Final', $playground->runtimeVersion);
    }
}
