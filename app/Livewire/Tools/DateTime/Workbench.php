<?php

namespace App\Livewire\Tools\DateTime;

use App\Services\DateTime\DateTimeToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug = 'timestamp';

    public string $input = '';

    public string $output = '';

    public string $error = '';

    public string $timezone = 'UTC';

    public string $format = 'Y-m-d H:i:s';

    public string $mode = 'timestamp-to-date';

    public function mount(string $slug): void
    {
        abort_unless($slug === 'timestamp', 404);

        $this->slug = $slug;
    }

    public function convert(): void
    {
        $this->error = '';
        $this->output = '';

        $service = new DateTimeToolService();

        try {
            if ($this->mode === 'timestamp-to-date') {
                $converted = $service->timestampToDate($this->input, $this->timezone, $this->format);
                $this->output = $converted;

                if ($converted === '') {
                    $this->error = 'Enter a valid Unix timestamp.';
                }

                return;
            }

            $converted = $service->dateToTimestamp($this->input, $this->timezone);
            $this->output = $converted === null ? '' : (string) $converted;

            if ($converted === null) {
                $this->error = 'Enter a valid date or ISO-8601 value.';
            }
        } catch (\Throwable) {
            $this->error = 'Unable to convert the provided value.';
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
        return view('livewire.tools.datetime.workbench', ['title' => 'Timestamp Converter']);
    }
}
