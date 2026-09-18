<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MvelExecutorIntegrationTest extends TestCase
{
    public function test_execute_endpoint_forwards_payload_and_returns_executor_result(): void
    {
        config(['services.mvel_executor.url' => 'http://mvel-executor.test']);
        Http::fake([
            'http://mvel-executor.test/api/mvel/execute' => Http::response([
                'success' => true,
                'result' => 'Hello Ilham',
                'type' => 'java.lang.String',
                'executionTimeMs' => 2.31,
            ]),
        ]);

        $response = $this->postJson('/api/mvel/execute', [
            'variables' => ['firstName' => 'Ilham'],
            'expression' => '"Hello " + firstName',
        ]);

        $response->assertOk()->assertJsonPath('result', 'Hello Ilham');
        Http::assertSent(fn ($request) => $request->url() === 'http://mvel-executor.test/api/mvel/execute'
            && $request['variables']['firstName'] === 'Ilham');
    }

    public function test_execute_endpoint_rejects_oversized_expressions(): void
    {
        $response = $this->postJson('/api/mvel/execute', [
            'variables' => [],
            'expression' => str_repeat('x', 51201),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('expression');
    }

    public function test_health_endpoint_reports_executor_status(): void
    {
        config(['services.mvel_executor.url' => 'http://mvel-executor.test']);
        Http::fake([
            'http://mvel-executor.test/api/mvel/health' => Http::response([
                'status' => 'UP',
                'mvelVersion' => '2.5.2.Final',
            ]),
        ]);

        $this->getJson('/api/mvel/health')
            ->assertOk()
            ->assertJsonPath('mvelVersion', '2.5.2.Final');
    }

    public function test_execute_endpoint_preserves_executor_error_contract(): void
    {
        config(['services.mvel_executor.url' => 'http://mvel-executor.test']);
        Http::fake([
            'http://mvel-executor.test/api/mvel/execute' => Http::response([
                'success' => false,
                'error' => [
                    'type' => 'DANGEROUS_EXPRESSION',
                    'message' => 'This expression uses a restricted Java capability.',
                ],
            ], 400),
        ]);

        $this->postJson('/api/mvel/execute', [
            'variables' => ['safe' => true],
            'expression' => 'Runtime.getRuntime()',
        ])->assertStatus(400)
            ->assertJsonPath('error.type', 'DANGEROUS_EXPRESSION');
    }
}