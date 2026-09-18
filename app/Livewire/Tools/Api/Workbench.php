<?php

namespace App\Livewire\Tools\Api;

use App\Services\Api\CurlGeneratorService;
use App\Services\Api\HttpRequestBuilderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $slug;

    public string $method = 'GET';

    public string $url = 'https://api.example.com/items';

    public string $headers = "Accept: application/json";

    public string $body = '';

    public string $query = 'page=2&limit=10';

    public string $output = '';

    public string $error = '';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['curl-generator', 'http-request'], true), 404);

        $this->slug = $slug;
    }

    public function generate(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            if ($this->slug === 'curl-generator') {
                $service = new CurlGeneratorService();

                $this->output = $service->build([
                    'method' => $this->method,
                    'url' => $this->url,
                    'headers' => array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $this->headers) ?: [])),
                    'body' => $this->body,
                ]);

                return;
            }

            $service = new HttpRequestBuilderService();
            $query = [];

            if (trim($this->query) !== '') {
                parse_str($this->query, $query);
            }

            $request = $service->build([
                'method' => $this->method,
                'url' => $this->url,
                'headers' => array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $this->headers) ?: [])),
                'query' => $query,
                'body' => $this->body,
            ]);

            $this->output = json_encode($request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable) {
            $this->error = 'Unable to generate the request.';
        }
    }

    public function clear(): void
    {
        $this->headers = "Accept: application/json";
        $this->body = '';
        $this->query = 'page=2&limit=10';
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        $title = $this->slug === 'curl-generator' ? 'cURL Generator' : 'HTTP Request Builder';

        return view('livewire.tools.api.workbench', ['title' => $title]);
    }
}
