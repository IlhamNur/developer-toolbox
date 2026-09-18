<?php

namespace App\Services\Api;

final class HttpRequestBuilderService
{
    public function build(array $config): array
    {
        $method = strtoupper((string) ($config['method'] ?? 'GET'));
        $url = (string) ($config['url'] ?? '');
        $headers = array_values((array) ($config['headers'] ?? []));
        $query = $config['query'] ?? [];
        $body = $config['body'] ?? null;

        if ($query !== []) {
            $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
            $separator = str_contains($url, '?') ? '&' : '?';
            $url = $url . $separator . $queryString;
        }

        return [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];
    }
}
