<?php

namespace App\Livewire\Tools\Color;

use App\Services\Color\ColorToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = '#FF0000';

    public string $output = '';

    public string $error = '';

    public int $red = 255;

    public int $green = 0;

    public int $blue = 0;

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['color-hex-rgb', 'color-rgb-hex'], true), 404);

        $this->slug = $slug;
    }

    public function convert(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new ColorToolService();

            if ($this->slug === 'color-hex-rgb') {
                $result = $service->hexToRgb($this->input);
                $this->output = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                return;
            }

            $this->output = $service->rgbToHex($this->red, $this->green, $this->blue);
        } catch (\Throwable) {
            $this->error = 'Unable to process the color.';
        }
    }

    public function clear(): void
    {
        $this->input = '';
        $this->red = 255;
        $this->green = 0;
        $this->blue = 0;
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        $title = $this->slug === 'color-hex-rgb' ? 'HEX to RGB' : 'RGB to HEX';

        return view('livewire.tools.color.workbench', ['title' => $title]);
    }
}
