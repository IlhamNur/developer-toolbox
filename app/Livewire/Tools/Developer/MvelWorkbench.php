<?php

namespace App\Livewire\Tools\Developer;

use App\Services\Developer\MvelSnippetService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MvelWorkbench extends Component
{
    public string $category = 'string';

    public string $output = '';

    public string $error = '';

    public function loadSnippet(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $snippet = (new MvelSnippetService())->snippet($this->category);
            $this->output = $snippet['code'];
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function clear(): void
    {
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.developer.mvel-workbench', [
            'title' => 'MVEL Snippet Library',
            'categories' => (new MvelSnippetService())->categories(),
        ]);
    }
}
