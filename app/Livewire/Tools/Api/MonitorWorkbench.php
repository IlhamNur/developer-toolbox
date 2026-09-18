<?php

namespace App\Livewire\Tools\Api;

use App\Services\Api\ApiMonitorService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MonitorWorkbench extends Component
{
    public string $endpoints = "Public API|https://example.com\nHTTPBin|https://httpbin.org/status/200";

    public array $results = [];

    public string $error = '';

    public function check(): void
    {
        $this->results = [];
        $this->error = '';
        $service = new ApiMonitorService();

        foreach (preg_split('/\r\n|\n|\r/', $this->endpoints) ?: [] as $line) {
            [$name, $url] = array_pad(explode('|', $line, 2), 2, null);
            if (trim((string) $name) === '' && trim((string) $url) === '') {
                continue;
            }

            if ($url === null || trim($url) === '') {
                $this->results[] = [
                    'name' => trim((string) $name),
                    'url' => '',
                    'status' => 'DOWN',
                    'code' => null,
                    'time_ms' => 0,
                    'error' => 'Use the Name|URL format.',
                ];
                continue;
            }

            $this->results[] = $service->check(trim((string) $name) ?: trim($url), trim($url));
        }
    }

    public function clear(): void
    {
        $this->endpoints = '';
        $this->results = [];
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.api.monitor-workbench', ['title' => 'API Monitor']);
    }
}
