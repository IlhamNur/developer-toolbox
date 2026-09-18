<?php

namespace Tests\Feature;

use App\Livewire\Tools\Text\Workbench;
use App\Services\Text\TextToolService;
use Tests\TestCase;

class TextToolsTest extends TestCase
{
    public function test_case_converter_applies_upper_and_title_transforms(): void
    {
        $service = new TextToolService();

        $this->assertSame('HELLO WORLD', $service->toUpper('hello world'));
        $this->assertSame('Hello World', $service->toTitle('hello world'));
        $this->assertSame('hello world', $service->toLower('HELLO WORLD'));
    }

    public function test_whitespace_cleaner_normalizes_spacing(): void
    {
        $service = new TextToolService();

        $this->assertSame('hello world', $service->cleanWhitespace("hello   \n\r\t world"));
    }

    public function test_slug_generator_creates_clean_url_friendly_value(): void
    {
        $service = new TextToolService();

        $this->assertSame('hello-world-2024', $service->slugify('Hello, World! 2024'));
    }

    public function test_text_case_workbench_processes_the_selected_case(): void
    {
        $workbench = new Workbench();
        $workbench->mount('text-case');
        $workbench->input = 'hello world';

        $workbench->caseTransform = 'upper';
        $workbench->process();
        $this->assertSame('HELLO WORLD', $workbench->output);

        $workbench->caseTransform = 'lower';
        $workbench->process();
        $this->assertSame('hello world', $workbench->output);

        $workbench->caseTransform = 'title';
        $workbench->process();
        $this->assertSame('Hello World', $workbench->output);
    }
}
