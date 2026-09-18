<?php

namespace App\Services\Api;

final class ApiMonitorService
{
    public function check(string $name, string $url): array
    {
        $started = microtime(true);

        try {
            $response = (new ApiRequestTesterService())->send('GET', trim($url), ['Accept' => 'application/json']);

            return [
                'name' => $name,
                'url' => $url,
                'status' => $response['successful'] ? 'UP' : 'DOWN',
                'code' => $response['status'],
                'time_ms' => $response['time_ms'],
                'error' => '',
            ];
        } catch (\Throwable $exception) {
            return [
                'name' => $name,
                'url' => $url,
                'status' => 'DOWN',
                'code' => null,
                'time_ms' => round((microtime(true) - $started) * 1000, 2),
                'error' => $exception->getMessage(),
            ];
        }
    }
}
