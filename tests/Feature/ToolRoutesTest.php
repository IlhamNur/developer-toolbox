<?php

namespace Tests\Feature;

use App\Support\ToolRegistry;
use Tests\TestCase;

class ToolRoutesTest extends TestCase
{
    public function test_every_registered_tool_page_renders_successfully(): void
    {
        foreach (ToolRegistry::all() as $tool) {
            $response = $this->get('/tools/' . $tool['slug']);

            $response->assertSuccessful();
            $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        }
    }
}
