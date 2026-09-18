<?php

namespace Tests\Feature;

use App\Services\Developer\MvelSnippetService;
use InvalidArgumentException;
use Tests\TestCase;

class MvelToolsTest extends TestCase
{
    public function test_mvel_snippet_library_returns_common_categories(): void
    {
        $service = new MvelSnippetService();

        $this->assertContains('string', $service->categories());
        $this->assertContains('api', $service->categories());
        $this->assertStringContainsString('response', $service->snippet('api')['code']);
    }

    public function test_mvel_snippet_library_rejects_unknown_categories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MvelSnippetService())->snippet('unknown');
    }
}