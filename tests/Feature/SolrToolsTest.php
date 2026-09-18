<?php

namespace Tests\Feature;

use App\Services\Solr\SolrQueryBuilderService;
use InvalidArgumentException;
use Tests\TestCase;

class SolrToolsTest extends TestCase
{
    public function test_solr_query_builder_generates_encoded_parameters(): void
    {
        $query = (new SolrQueryBuilderService())->build(
            'name:Ilham',
            "status:active\ncategory:network",
            'id,name',
            'score desc',
            20,
            10,
        );

        $this->assertSame(
            'q=name%3AIlham&start=20&rows=10&fl=id%2Cname&fq%5B0%5D=status%3Aactive&fq%5B1%5D=category%3Anetwork&sort=score%20desc',
            $query
        );
    }

    public function test_solr_query_builder_rejects_invalid_filters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SolrQueryBuilderService())->build('*:*', 'invalid filter');
    }
}