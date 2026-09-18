<?php

namespace App\Services\Developer;

final class JwtDecoderService
{
    public function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) < 3) {
            return ['header' => [], 'payload' => [], 'signature' => ''];
        }

        $header = json_decode($this->base64UrlDecode($parts[0]), true, 512, JSON_THROW_ON_ERROR);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true, 512, JSON_THROW_ON_ERROR);

        return [
            'header' => $header,
            'payload' => $payload,
            'signature' => $parts[2],
        ];
    }

    private function base64UrlDecode(string $value): string
    {
        $normalized = str_replace(['-', '_'], ['+', '/'], $value);
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($normalized, true) ?: '';
    }
}
