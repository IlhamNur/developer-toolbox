<?php

namespace App\Services\Text;

final class TextToolService
{
    public function toUpper(string $value): string
    {
        return strtoupper($value);
    }

    public function toLower(string $value): string
    {
        return strtolower($value);
    }

    public function toTitle(string $value): string
    {
        return ucwords(strtolower($value));
    }

    public function cleanWhitespace(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value)) ?? trim($value);
    }

    public function slugify(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[^a-z0-9\s-]+/', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', '-', $normalized) ?? $normalized;
        $normalized = preg_replace('/-+/', '-', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }
}
