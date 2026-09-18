<?php

namespace App\Services\Developer;

use InvalidArgumentException;

final class MvelSnippetService
{
    private const SNIPPETS = [
        'string' => [
            'title' => 'String helpers',
            'code' => "name != null && name.trim().length > 0\nname.toUpperCase()\n'Hello ' + firstName",
        ],
        'json' => [
            'title' => 'JSON and maps',
            'code' => "payload['contactNumber']\npayload.containsKey('category')\npayload['items'].size() > 0",
        ],
        'date' => [
            'title' => 'Date checks',
            'code' => "createdAt != null\ncreatedAt.after(cutoffDate)\nnew java.text.SimpleDateFormat('yyyy-MM-dd').format(createdAt)",
        ],
        'list' => [
            'title' => 'List operations',
            'code' => "items.size() > 0\nitems[0]\nitems.contains('network')",
        ],
        'null' => [
            'title' => 'Null checking',
            'code' => "value != null ? value : 'fallback'\nvalue == null\nvalue != empty",
        ],
        'api' => [
            'title' => 'API response patterns',
            'code' => "response != null && response.status == 200\nresponse.body['data']\nresponse.body['error'] == null",
        ],
    ];

    public function categories(): array
    {
        return array_keys(self::SNIPPETS);
    }

    public function snippet(string $category): array
    {
        if (! isset(self::SNIPPETS[$category])) {
            throw new InvalidArgumentException('Choose a valid MVEL snippet category.');
        }

        return self::SNIPPETS[$category];
    }
}
