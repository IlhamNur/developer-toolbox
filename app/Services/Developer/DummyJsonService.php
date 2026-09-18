<?php

namespace App\Services\Developer;

final class DummyJsonService
{
    public function generate(int $count = 5): array
    {
        $records = [];

        for ($i = 0; $i < max(1, $count); $i++) {
            $records[] = [
                'id' => $i + 1,
                'name' => 'User ' . ($i + 1),
                'email' => 'user' . ($i + 1) . '@example.com',
                'role' => ['admin', 'editor', 'viewer'][($i + 1) % 3],
                'active' => $i % 2 === 0,
                'created_at' => date('c', strtotime("-{$i} days")),
            ];
        }

        return $records;
    }
}
