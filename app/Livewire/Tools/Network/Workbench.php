<?php

namespace App\Livewire\Tools\Network;

use App\Services\Network\NetworkToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = '8.8.8.8';

    public string $output = '';

    public string $error = '';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['ip-validator', 'ip-classifier'], true), 404);

        $this->slug = $slug;
    }

    public function process(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new NetworkToolService();

            if ($this->slug === 'ip-validator') {
                $this->output = $service->isValidIp($this->input) ? 'Valid IP address' : 'Invalid IP address';
                return;
            }

            $this->output = $service->ipClassification($this->input);
        } catch (\Throwable) {
            $this->error = 'Unable to process the IP input.';
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
        $title = $this->slug === 'ip-validator' ? 'IP Validator' : 'IP Classifier';

        return view('livewire.tools.network.workbench', ['title' => $title]);
    }
}
