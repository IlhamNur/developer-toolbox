<?php

namespace App\Livewire\Tools\Security;

use App\Services\Security\HashToolService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $input = 'hello world';

    public string $hash = '';

    public string $output = '';

    public string $error = '';

    public string $algorithm = 'sha256';

    public string $mode = 'generate';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['hash-generator', 'hash-compare'], true), 404);

        $this->slug = $slug;
    }

    public function process(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $service = new HashToolService();

            if ($this->slug === 'hash-generator') {
                $this->output = $service->generate($this->input, $this->algorithm);
                return;
            }

            $this->output = $service->compare($this->input, $this->hash) ? 'Match' : 'No match';
        } catch (\Throwable) {
            $this->error = 'Unable to process the hash request.';
        }
    }

    public function clear(): void
    {
        $this->input = '';
        $this->hash = '';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        $title = $this->slug === 'hash-generator' ? 'Hash Generator' : 'Hash Comparator';

        return view('livewire.tools.security.workbench', ['title' => $title]);
    }
}
