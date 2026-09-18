<?php

namespace App\Services\Color;

use InvalidArgumentException;

final class ColorToolService
{
    public function hexToRgb(string $hex): array
    {
        $normalized = str_starts_with($hex, '#') ? substr($hex, 1) : $hex;

        if (strlen($normalized) === 3) {
            $normalized = preg_replace('/(.)/', '$1$1', $normalized) ?? $normalized;
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $normalized)) {
            throw new InvalidArgumentException('Enter a valid HEX color.');
        }

        $r = hexdec(substr($normalized, 0, 2));
        $g = hexdec(substr($normalized, 2, 2));
        $b = hexdec(substr($normalized, 4, 2));

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    public function rgbToHex(int $r, int $g, int $b): string
    {
        $components = [$r, $g, $b];

        foreach ($components as $index => $value) {
            $components[$index] = max(0, min(255, $value));
        }

        return '#' . strtoupper(sprintf('%02X%02X%02X', ...$components));
    }
}
