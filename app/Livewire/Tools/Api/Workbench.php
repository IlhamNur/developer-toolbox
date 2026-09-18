<?php

namespace App\Livewire\Tools\Api;

use App\Services\Api\CurlGeneratorService;
use App\Services\Api\HttpRequestBuilderService;
use App\Services\Api\ApiTemplateService;
use App\Services\Api\ApiRequestTesterService;
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

    public string $environment = "BASE_URL=https://api.example.com\nTOKEN=demo-token";

    public string $query = 'page=2&limit=10';

    public string $output = '';

    public string $error = '';

    public string $responseStatus = '';

    public string $responseTime = '';

    public function mount(string $slug): void
    {
        abort_unless(in_array($slug, ['curl-generator', 'http-request', 'api-request-tester'], true), 404);

        $this->slug = $slug;
    }

    public function generate(): void
    {
        $this->error = '';
        $this->output = '';
        $this->responseStatus = '';
        $this->responseTime = '';

        try {
            $templates = new ApiTemplateService();
            $url = $templates->resolve($this->url, $this->environment);
            $headers = $templates->resolve($this->headers, $this->environment);
            $body = $templates->resolve($this->body, $this->environment);

            if ($this->slug === 'api-request-tester') {
                $response = (new ApiRequestTesterService())->send(
                    $this->method,
                    $url,
                    $this->parseHeaders($headers),
                    $body,
                );

                $this->responseStatus = (string) $response['status'];
                $this->responseTime = (string) $response['time_ms'];
                $this->output = $response['body'];

                return;
            }

            if ($this->slug === 'curl-generator') {
                $service = new CurlGeneratorService();

                $this->output = $service->build([
                    'method' => $this->method,
                    'url' => $url,
                    'headers' => array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $headers) ?: [])),
                    'body' => $body,
                ]);

                return;
            }

            $service = new HttpRequestBuilderService();
            $query = [];

            if (trim($this->query) !== '') {
                parse_str($templates->resolve($this->query, $this->environment), $query);
            }

            $request = $service->build([
                'method' => $this->method,
                'url' => $url,
                'headers' => array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $headers) ?: [])),
                'query' => $query,
                'body' => $body,
            ]);

            $this->output = json_encode($request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable) {
            $this->error = $this->slug === 'api-request-tester'
                ? 'Unable to send the request. Check the URL, network policy, and endpoint response.'
                : 'Unable to generate the request.';
        }
    }

    public function clear(): void
    {
        $this->headers = "Accept: application/json";
        $this->body = '';
        $this->environment = "BASE_URL=https://api.example.com\nTOKEN=demo-token";
        $this->query = 'page=2&limit=10';
        $this->output = '';
        $this->error = '';
        $this->responseStatus = '';
        $this->responseTime = '';
    }

    public function render()
    {
        $title = match ($this->slug) {
            'curl-generator' => 'cURL Generator',
            'api-request-tester' => 'API Request Tester',
            default => 'HTTP Request Builder',
        };

        return view('livewire.tools.api.workbench', ['title' => $title]);
    }

    private function parseHeaders(string $headers): array
    {
        $parsed = [];

        foreach (preg_split('/\r\n|\n|\r/', $headers) ?: [] as $header) {
            [$name, $value] = array_pad(explode(':', $header, 2), 2, null);
            if ($value !== null && trim($name) !== '') {
                $parsed[trim($name)] = trim($value);
            }
        }

        return $parsed;
    }
}
