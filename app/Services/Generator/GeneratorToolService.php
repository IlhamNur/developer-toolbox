<?php

namespace App\Services\Generator;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

final class GeneratorToolService
{
    public function uuid(int $count = 1): array
    {
        $results = [];

        for ($i = 0; $i < max(1, $count); $i++) {
            $results[] = Uuid::uuid4()->toString();
        }

        return $results;
    }

    public function randomString(int $length = 32, bool $uppercase = true, bool $lowercase = true, bool $numbers = true, bool $symbols = false): string
    {
        $length = max(1, $length);

        $chars = [];

        if ($uppercase) {
            $chars[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($lowercase) {
            $chars[] = 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($numbers) {
            $chars[] = '0123456789';
        }

        if ($symbols) {
            $chars[] = '!@#$%^&*()-_=+[]{};:,.?';
        }

        if ($chars === []) {
            throw new InvalidArgumentException('Select at least one character type.');
        }

        $pool = implode('', $chars);
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $output .= $pool[random_int(0, strlen($pool) - 1)];
        }

        return $output;
    }

    public function password(int $length = 16, bool $uppercase = true, bool $lowercase = true, bool $numbers = true, bool $symbols = true, bool $excludeAmbiguous = true): string
    {
        $length = max(8, $length);

        $uppercaseChars = $excludeAmbiguous ? 'ABCDEFGHJKLMNPQRSTUVWXYZ' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercaseChars = $excludeAmbiguous ? 'abcdefghijkmnopqrstuvwxyz' : 'abcdefghijklmnopqrstuvwxyz';
        $numberChars = $excludeAmbiguous ? '23456789' : '0123456789';

        $pool = '';
        $pool .= $uppercase ? $uppercaseChars : '';
        $pool .= $lowercase ? $lowercaseChars : '';
        $pool .= $numbers ? $numberChars : '';
        $pool .= $symbols ? '!@#$%^&*()-_=+[]{};:,.?/' : '';

        if ($pool === '') {
            throw new InvalidArgumentException('Select at least one password character type.');
        }

        $password = '';
        $required = [];

        if ($uppercase) {
            $required[] = $uppercaseChars[random_int(0, strlen($uppercaseChars) - 1)];
        }

        if ($lowercase) {
            $required[] = $lowercaseChars[random_int(0, strlen($lowercaseChars) - 1)];
        }

        if ($numbers) {
            $required[] = $numberChars[random_int(0, strlen($numberChars) - 1)];
        }

        if ($symbols) {
            $required[] = '!@#$%^&*()-_=+[]{};:,.?/'[random_int(0, strlen('!@#$%^&*()-_=+[]{};:,.?/') - 1)];
        }

        for ($i = 0; $i < $length - count($required); $i++) {
            $password .= $pool[random_int(0, strlen($pool) - 1)];
        }

        $password .= implode('', $required);

        return str_shuffle($password);
    }

    public function passwordStrength(string $password): array
    {
        $score = 0;

        if (strlen($password) >= 12) {
            $score++;
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        }

        if (preg_match('/[a-z]/', $password)) {
            $score++;
        }

        if (preg_match('/[0-9]/', $password)) {
            $score++;
        }

        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score++;
        }

        return [
            'score' => min(5, $score),
            'label' => $score >= 4 ? 'Strong' : ($score >= 3 ? 'Good' : ($score >= 2 ? 'Medium' : 'Weak')),
        ];
    }
}
