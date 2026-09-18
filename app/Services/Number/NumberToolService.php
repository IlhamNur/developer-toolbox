<?php

namespace App\Services\Number;

use InvalidArgumentException;

final class NumberToolService
{
    public function formatNumber(float|int|string $value, int $decimals = 2, string $thousandsSeparator = ',', string $decimalSeparator = '.'): string
    {
        if (! is_numeric(trim((string) $value)) || ! is_finite((float) $value)) {
            throw new InvalidArgumentException('Enter a valid number to format.');
        }

        if ($decimals < 0) {
            throw new InvalidArgumentException('Decimal places cannot be negative.');
        }

        $number = (float) $value;

        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    public function convertBase(string $value, int $fromBase, int $toBase): string
    {
        $normalized = strtoupper(trim($value));

        if ($fromBase < 2 || $fromBase > 36 || $toBase < 2 || $toBase > 36) {
            throw new InvalidArgumentException('Bases must be between 2 and 36.');
        }

        if ($normalized === '' || ! preg_match('/^[0-9A-Z]+$/', $normalized)) {
            throw new InvalidArgumentException('Enter a valid number for the selected base.');
        }

        foreach (str_split($normalized) as $digit) {
            if (strpos('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', $digit) >= $fromBase) {
                throw new InvalidArgumentException('The input contains a digit outside the selected base.');
            }
        }

        $decimal = intval(base_convert($normalized, $fromBase, 10));
        $converted = base_convert((string) $decimal, 10, $toBase);

        return $toBase === 16 ? strtoupper($converted) : $converted;
    }
}
