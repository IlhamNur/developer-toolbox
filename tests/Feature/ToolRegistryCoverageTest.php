<?php

namespace Tests\Feature;

use App\Support\ToolRegistry;
use Tests\TestCase;

class ToolRegistryCoverageTest extends TestCase
{
    public function test_registry_tracks_every_implemented_tool_group(): void
    {
        $slugs = collect(ToolRegistry::all())->pluck('slug')->all();

        $this->assertContains('text-case', $slugs);
        $this->assertContains('text-cleaner', $slugs);
        $this->assertContains('slug-generator', $slugs);

        $this->assertContains('hash-generator', $slugs);
        $this->assertContains('hash-compare', $slugs);

        $this->assertContains('number-format', $slugs);
        $this->assertContains('number-base', $slugs);

        $this->assertContains('color-hex-rgb', $slugs);
        $this->assertContains('color-rgb-hex', $slugs);

        $this->assertContains('ip-validator', $slugs);
        $this->assertContains('ip-classifier', $slugs);

        $this->assertContains('Text', ToolRegistry::categories());
        $this->assertContains('Security', ToolRegistry::categories());
        $this->assertContains('Number', ToolRegistry::categories());
        $this->assertContains('Color', ToolRegistry::categories());
        $this->assertContains('Network', ToolRegistry::categories());
    }
}
