<?php

namespace App\Livewire\Tools\Encoding;

use App\Services\Encoding\EncodingToolService;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = '';

    public string $output = '';

    public string $error = '';

    public string $operation = 'encode';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['base64', 'url-encode', 'html-encode'], true), 404);

        $this->slug = $slug;
    }

    public function encode(): void
    {
        $this->operation = 'encode';
        $this->run();
    }

    public function decode(): void
    {
        $this->operation = 'decode';
        $this->run();
    }

    public function clear(): void
    {
        $this->input = '';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.encoding.workbench', ['title' => str($this->slug)->replace('-', ' ')->title()]);
    }

    protected function run(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new EncodingToolService();

            $this->output = match ($this->slug) {
                'base64' => $this->operation === 'encode'
                    ? $service->base64Encode($this->input)
                    : ($service->base64Decode($this->input) ?? throw new InvalidArgumentException('Unable to decode this Base64 input.')),
                'url-encode' => $this->operation === 'encode'
                    ? $service->urlEncode($this->input)
                    : $service->urlDecode($this->input),
                'html-encode' => $this->operation === 'encode'
                    ? $service->htmlEncode($this->input)
                    : $service->htmlDecode($this->input),
                default => throw new InvalidArgumentException('Unsupported encoding tool.'),
            };
        } catch (InvalidArgumentException $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
