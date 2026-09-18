<?php

namespace App\Livewire\Tools\Text;

use App\Services\Text\TextToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = 'hello world';

    public string $output = '';

    public string $error = '';

    public string $caseTransform = 'upper';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['text-case', 'text-cleaner', 'slug-generator'], true), 404);

        $this->slug = $slug;
    }

    public function process(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new TextToolService();

            $this->output = match ($this->slug) {
                'text-case' => match ($this->caseTransform) {
                    'upper' => $service->toUpper($this->input),
                    'lower' => $service->toLower($this->input),
                    'title' => $service->toTitle($this->input),
                    default => $this->input,
                },
                'text-cleaner' => $service->cleanWhitespace($this->input),
                'slug-generator' => $service->slugify($this->input),
                default => $this->input,
            };
        } catch (\Throwable) {
            $this->error = 'Unable to process the text.';
        }
    }

    public function clear(): void
    {
        $this->input = '';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        $title = match ($this->slug) {
            'text-case' => 'Text Case Converter',
            'text-cleaner' => 'Whitespace Cleaner',
            'slug-generator' => 'Slug Generator',
            default => 'Text Tool',
        };

        return view('livewire.tools.text.workbench', ['title' => $title]);
    }
}
