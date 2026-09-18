<?php

namespace App\Services\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

final class DateTimeToolService
{
    public function dateToTimestamp(string $value, ?string $timezone = 'UTC'): ?int
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            $date = new DateTimeImmutable($value, new DateTimeZone($timezone));

            return $date->getTimestamp();
        } catch (Exception) {
            return null;
        }
    }

    public function timestampToDate(int|string $value, ?string $timezone = 'UTC', ?string $format = 'Y-m-d H:i:s'): string
    {
        if (trim((string) $value) === '' || ! is_numeric($value)) {
            return '';
        }

        try {
            $date = new DateTimeImmutable('@' . (int) $value);
            $date = $date->setTimezone(new DateTimeZone($timezone ?? 'UTC'));

            return $date->format($format ?? 'Y-m-d H:i:s');
        } catch (Exception) {
            return '';
        }
    }
}
