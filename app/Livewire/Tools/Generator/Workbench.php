<?php

namespace App\Livewire\Tools\Generator;

use App\Services\Generator\GeneratorToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public int $count = 1;

    public int $length = 32;

    public bool $uppercase = true;

    public bool $lowercase = true;

    public bool $numbers = true;

    public bool $symbols = false;

    public bool $excludeAmbiguous = true;

    public string $output = '';

    public string $error = '';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['uuid-generator', 'random-string-generator', 'password-generator'], true), 404);

        $this->slug = $slug;
    }

    public function generate(): void
    {
        $this->error = '';

        try {
            $service = new GeneratorToolService();

            $this->output = match ($this->slug) {
                'uuid-generator' => implode(PHP_EOL, $service->uuid($this->count)),
                'random-string-generator' => $service->randomString($this->length, $this->uppercase, $this->lowercase, $this->numbers, $this->symbols),
                'password-generator' => $service->password($this->length, $this->uppercase, $this->lowercase, $this->numbers, $this->symbols, $this->excludeAmbiguous),
                default => '',
            };
        } catch (\Throwable $exception) {
            $this->error = 'Unable to generate the requested value.';
        }
    }

    public function clear(): void
    {
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.generator.workbench', ['title' => str($this->slug)->replace('-', ' ')->title()]);
    }
}
