<?php

namespace App\Services\Api;

final class CurlGeneratorService
{
    public function build(array $config): string
    {
        $method = strtoupper((string) ($config['method'] ?? 'GET'));
        $url = (string) ($config['url'] ?? '');
        $headers = $config['headers'] ?? [];
        $body = $config['body'] ?? '';

        $command = ['curl'];
        $command[] = '--request ' . $method;

        if ($url !== '') {
            $command[] = escapeshellarg($url);
        }

        foreach ($headers as $header) {
            $command[] = '--header ' . escapeshellarg((string) $header);
        }

        if ($body !== '') {
            $command[] = '--data ' . escapeshellarg((string) $body);
        }

        return implode(' ', $command);
    }
}
