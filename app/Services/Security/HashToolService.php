<?php

namespace App\Services\Security;

final class HashToolService
{
    public function generate(string $value, string $algorithm = 'sha256'): string
    {
        return hash($algorithm, $value);
    }

    public function compare(string $value, string $hash): bool
    {
        foreach (['sha256', 'sha1', 'md5'] as $algorithm) {
            if (hash_equals(hash($algorithm, $value), trim($hash))) {
                return true;
            }
        }

        return false;
    }
}
