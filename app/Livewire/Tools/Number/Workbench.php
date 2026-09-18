<?php

namespace App\Livewire\Tools\Number;

use App\Services\Number\NumberToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = '1234567.89';

    public string $output = '';

    public string $error = '';

    public int $decimals = 2;

    public string $thousandsSeparator = ',';

    public string $decimalSeparator = '.';

    public int $fromBase = 10;

    public int $toBase = 2;

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['number-format', 'number-base'], true), 404);

        $this->slug = $slug;
    }

    public function convert(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new NumberToolService();

            $this->output = $this->slug === 'number-format'
                ? $service->formatNumber($this->input, $this->decimals, $this->thousandsSeparator, $this->decimalSeparator)
                : $service->convertBase($this->input, $this->fromBase, $this->toBase);
        } catch (\Throwable) {
            $this->error = 'Unable to process the number.';
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
        $title = $this->slug === 'number-format' ? 'Number Formatter' : 'Base Converter';

        return view('livewire.tools.number.workbench', ['title' => $title]);
    }
}
