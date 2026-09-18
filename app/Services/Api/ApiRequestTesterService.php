<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

final class ApiRequestTesterService
{
    public function send(string $method, string $url, array $headers = [], string $body = ''): array
    {
        $this->validateUrl($url);
        $started = microtime(true);

        $request = Http::withHeaders($headers)
            ->timeout(5)
            ->connectTimeout(2)
            ->acceptJson();

        $response = $body !== ''
            ? $request->withBody($body, $headers['Content-Type'] ?? $headers['content-type'] ?? 'application/json')->send(strtoupper($method), $url)
            : $request->send(strtoupper($method), $url);

        $responseBody = (string) $response->body();

        if (strlen($responseBody) > 1024 * 1024) {
            $responseBody = substr($responseBody, 0, 1024 * 1024) . "\n\n[Response truncated]";
        }

        return [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'time_ms' => round((microtime(true) - $started) * 1000, 2),
            'body' => $responseBody,
        ];
    }

    private function validateUrl(string $url): void
    {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '';

        if (! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || $host === '') {
            throw new InvalidArgumentException('Use a valid HTTP or HTTPS URL.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('URLs with embedded credentials are not allowed.');
        }

        $normalizedHost = strtolower($host);
        if ($normalizedHost === 'localhost' || str_ends_with($normalizedHost, '.local')) {
            throw new InvalidArgumentException('Local and internal hosts are not allowed.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false && $this->isPrivateIp($host)) {
            throw new InvalidArgumentException('Private and loopback IP addresses are not allowed.');
        }
    }

    private function isPrivateIp(string $host): bool
    {
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        return filter_var($host, FILTER_VALIDATE_IP, $flags) === false;
    }
}
