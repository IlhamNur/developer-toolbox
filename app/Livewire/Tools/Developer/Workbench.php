<?php

namespace App\Livewire\Tools\Developer;

use App\Services\Developer\DummyJsonService;
use App\Services\Developer\JwtDecoderService;
use App\Services\Developer\RegexTesterService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $pattern = '/([A-Z]+)-(\d+)/';

    public string $subject = 'ABC-123 XYZ-456';

    public string $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFkYSJ9.signature';

    public string $count = '5';

    public string $output = '';

    public string $error = '';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['regex-tester', 'jwt-decoder', 'dummy-json'], true), 404);

        $this->slug = $slug;
    }

    public function run(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            if ($this->slug === 'regex-tester') {
                $result = (new RegexTesterService())->test($this->pattern, $this->subject);
                $this->output = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                return;
            }

            if ($this->slug === 'jwt-decoder') {
                $result = (new JwtDecoderService())->decode($this->token);
                $this->output = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                return;
            }

            $result = (new DummyJsonService())->generate((int) $this->count);
            $this->output = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable) {
            $this->error = 'Unable to generate the requested developer output.';
        }
    }

    public function clear(): void
    {
        $this->pattern = '/([A-Z]+)-(\d+)/';
        $this->subject = 'ABC-123 XYZ-456';
        $this->token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFkYSJ9.signature';
        $this->count = '5';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        $title = match ($this->slug) {
            'regex-tester' => 'Regex Tester',
            'jwt-decoder' => 'JWT Decoder',
            'dummy-json' => 'Dummy JSON Generator',
            default => 'Developer Tool',
        };

        return view('livewire.tools.developer.workbench', ['title' => $title]);
    }
}
