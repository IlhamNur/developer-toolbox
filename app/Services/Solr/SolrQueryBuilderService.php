<?php

namespace App\Services\Solr;

use InvalidArgumentException;

final class SolrQueryBuilderService
{
    public function build(string $query, string $filters = '', string $fields = '*', string $sort = '', int $start = 0, int $rows = 10): string
    {
        $query = trim($query);
        $fields = trim($fields) === '' ? '*' : trim($fields);

        if ($query === '') {
            throw new InvalidArgumentException('Enter a Solr query.');
        }

        if ($start < 0 || $rows < 1) {
            throw new InvalidArgumentException('Start must be zero or greater and rows must be at least one.');
        }

        $parameters = [
            'q' => $query,
            'start' => $start,
            'rows' => $rows,
            'fl' => $fields,
        ];

        $filterQueries = [];
        foreach (preg_split('/\r\n|\n|\r/', $filters) ?: [] as $filter) {
            $filter = trim($filter);
            if ($filter === '') {
                continue;
            }

            if (! preg_match('/^[A-Za-z_][A-Za-z0-9_.-]*\s*:\s*.+$/', $filter)) {
                throw new InvalidArgumentException('Each filter must use the field:value format.');
            }

            $filterQueries[] = preg_replace('/\s*:\s*/', ':', $filter, 1) ?? $filter;
        }

        if ($filterQueries !== []) {
            $parameters['fq'] = $filterQueries;
        }

        if (trim($sort) !== '') {
            $parameters['sort'] = trim($sort);
        }

        return http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}
