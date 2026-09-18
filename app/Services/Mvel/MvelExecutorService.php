<?php

namespace App\Services\Mvel;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MvelExecutorService
{
    public function execute(array $variables, string $expression): array
    {
        $url = rtrim((string) config('services.mvel_executor.url'), '/') . '/api/mvel/execute';

        if (trim((string) config('services.mvel_executor.url')) === '') {
            throw new RuntimeException('MVEL executor is not configured.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(2)
                ->connectTimeout(1)
                ->post($url, [
                    'variables' => $variables,
                    'expression' => $expression,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('MVEL executor is unavailable.', 0, $exception);
        }

        if ($response->failed()) {
            throw new MvelExecutionException(
                (string) ($response->json('error.type') ?: 'MVEL_EXECUTION_ERROR'),
                (string) ($response->json('error.message') ?: 'MVEL execution failed.'),
                $response->status()
            );
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['success'] ?? false) !== true) {
            throw new RuntimeException('MVEL executor returned an invalid response.');
        }

        return $payload;
    }

    public function health(): array
    {
        $url = rtrim((string) config('services.mvel_executor.url'), '/') . '/api/mvel/health';

        if (trim((string) config('services.mvel_executor.url')) === '') {
            throw new RuntimeException('MVEL executor is not configured.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(2)
                ->connectTimeout(1)
                ->get($url);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('MVEL executor is unavailable.', 0, $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('MVEL executor health check failed.');
        }

        return $response->json();
    }
}

final class MvelExecutionException extends RuntimeException
{
    public function __construct(
        public readonly string $errorType,
        string $message,
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
