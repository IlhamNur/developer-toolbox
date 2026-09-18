<?php

namespace App\Services\Encoding;

use InvalidArgumentException;

final class EncodingToolService
{
    public function base64Encode(string $input): string
    {
        return base64_encode($input);
    }

    public function base64Decode(string $input): ?string
    {
        $normalized = trim($input);

        if ($normalized === '') {
            return null;
        }

        $decoded = base64_decode($normalized, true);

        if ($decoded === false) {
            return null;
        }

        return $decoded;
    }

    public function urlEncode(string $input): string
    {
        return rawurlencode($input);
    }

    public function urlDecode(string $input): string
    {
        return rawurldecode($input);
    }

    public function htmlEncode(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function htmlDecode(string $input): string
    {
        return html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function newlineEscape(string $input): string
    {
        return str_replace(
            ['\\', "\r\n", "\r", "\n", "\t"],
            ['\\\\', '\\n', '\\n', '\\n', '\\t'],
            $input
        );
    }

    public function newlineUnescape(string $input): string
    {
        return preg_replace_callback('/\\\\([nrt\\\\])/', function (array $matches): string {
            return match ($matches[1]) {
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                '\\' => '\\',
            };
        }, $input) ?? $input;
    }
}
