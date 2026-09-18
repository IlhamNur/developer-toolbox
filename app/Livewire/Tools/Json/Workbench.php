<?php

namespace App\Livewire\Tools\Json;

use App\Services\Json\JsonToolService;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = '';

    public string $modifiedInput = '';

    public string $output = '';

    public string $error = '';

    public string $indentation = '2';

    public function boot(): void
    {
        $this->slug ??= request()->route('slug') ?? 'json-formatter';
    }

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['json-formatter', 'json-validator', 'json-minifier', 'json-escape', 'json-unescape', 'json-diff'], true), 404);

        $this->slug = $slug;
    }

    public function process(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new JsonToolService();

            match ($this->slug) {
                'json-formatter' => $this->output = $service->format($this->input, $this->indentation),
                'json-validator' => $this->output = $service->isValid($this->input) ? 'Valid JSON' : 'Invalid JSON',
                'json-minifier' => $this->output = $service->minify($this->input),
                'json-escape' => $this->output = $service->escape($this->input),
                'json-unescape' => $this->output = $service->unescape($this->input),
                'json-diff' => $this->output = json_encode($service->diff($this->input, $this->modifiedInput), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            };

            if ($this->slug === 'json-validator' && ! $service->isValid($this->input)) {
                $this->error = $service->validate($this->input) ?? 'Unable to validate JSON.';
                $this->output = '';
            }
        } catch (InvalidArgumentException $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function clear(): void
    {
        $this->input = '';
        $this->modifiedInput = '';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.json.workbench', ['title' => str($this->slug)->replace('-', ' ')->title()]);
    }
}