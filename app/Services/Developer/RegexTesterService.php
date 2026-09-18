<?php

namespace App\Services\Developer;

use InvalidArgumentException;

final class RegexTesterService
{
    public function test(string $pattern, string $subject): array
    {
        $matches = [];
        $groups = [];

        if (@preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER) === false) {
            throw new InvalidArgumentException('Invalid regular expression.');
        }

        $result = [];
        foreach ($matches as $set) {
            $result[] = $set[0];
            $groups[] = array_slice($set, 1);
        }

        return [
            'pattern' => $pattern,
            'subject' => $subject,
            'matches' => $result,
            'groups' => $groups,
        ];
    }
}
