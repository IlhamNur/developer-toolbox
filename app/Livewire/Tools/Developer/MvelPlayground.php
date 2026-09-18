<?php

namespace App\Livewire\Tools\Developer;

use App\Services\Mvel\MvelExecutorService;
use InvalidArgumentException;
use JsonException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MvelPlayground extends Component
{
    public array $variables = [
        ['name' => 'firstName', 'type' => 'String', 'value' => 'Ilham'],
        ['name' => 'age', 'type' => 'Integer', 'value' => '24'],
        ['name' => 'active', 'type' => 'Boolean', 'value' => 'true'],
    ];

    public string $expression = '"Hello " + firstName';

    public string $result = '';

    public string $resultType = '';

    public string $executionTimeMs = '';

    public string $error = '';

    public string $runtimeVersion = 'Unavailable';

    public function mount(): void
    {
        try {
            $health = (new MvelExecutorService())->health();
            $this->runtimeVersion = (string) ($health['mvelVersion'] ?? 'Available');
        } catch (\Throwable) {
            $this->runtimeVersion = 'Unavailable';
        }
    }

    public function addVariable(): void
    {
        if (count($this->variables) >= 100) {
            $this->error = 'You can define up to 100 variables.';
            return;
        }

        $this->variables[] = ['name' => 'value' . (count($this->variables) + 1), 'type' => 'String', 'value' => ''];
        $this->error = '';
    }

    public function removeVariable(int $index): void
    {
        if (isset($this->variables[$index])) {
            unset($this->variables[$index]);
            $this->variables = array_values($this->variables);
        }
    }

    public function run(): void
    {
        $this->error = '';
        $this->result = '';
        $this->resultType = '';
        $this->executionTimeMs = '';

        try {
            $payload = [];
            foreach ($this->variables as $variable) {
                $name = trim((string) ($variable['name'] ?? ''));
                $type = (string) ($variable['type'] ?? 'String');

                if ($name === '' || ! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
                    throw new InvalidArgumentException('Variable names must use letters, numbers, and underscores only.');
                }

                $payload[$name] = $this->castValue((string) ($variable['value'] ?? ''), $type);
            }

            $response = (new MvelExecutorService())->execute($payload, $this->expression);
            $this->result = is_string($response['result'] ?? null)
                ? $response['result']
                : json_encode($response['result'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $this->resultType = (string) ($response['type'] ?? '');
            $this->executionTimeMs = (string) ($response['executionTimeMs'] ?? '');
        } catch (JsonException|InvalidArgumentException $exception) {
            $this->error = $exception->getMessage();
        } catch (\Throwable $exception) {
            $this->error = 'MVEL execution failed. ' . $exception->getMessage();
        }
    }

    public function useExample(string $example): void
    {
        $examples = [
            'hello' => ['expression' => '"Hello " + firstName', 'variables' => [['name' => 'firstName', 'type' => 'String', 'value' => 'Ilham']]],
            'conditional' => ['expression' => 'age >= 18 ? "Adult" : "Minor"', 'variables' => [['name' => 'age', 'type' => 'Integer', 'value' => '24']]],
            'map' => ['expression' => 'data["name"]', 'variables' => [['name' => 'data', 'type' => 'JSON', 'value' => '{"name":"Ilham","age":24}']]],
            'nested' => ['expression' => 'response.data.ticketNumber', 'variables' => [['name' => 'response', 'type' => 'JSON', 'value' => '{"success":true,"data":{"ticketNumber":"SRN123456","status":"OPEN"}}']]],
            'boolean' => ['expression' => 'response.success && response.data.status == "OPEN"', 'variables' => [['name' => 'response', 'type' => 'JSON', 'value' => '{"success":true,"data":{"status":"OPEN"}}']]],
            'null' => ['expression' => 'value != null ? value : ""', 'variables' => [['name' => 'value', 'type' => 'Null', 'value' => '']]],
        ];

        if (isset($examples[$example])) {
            $this->expression = $examples[$example]['expression'];
            $this->variables = $examples[$example]['variables'];
            $this->result = '';
            $this->error = '';
        }
    }

    public function clear(): void
    {
        $this->variables = [];
        $this->expression = '';
        $this->result = '';
        $this->resultType = '';
        $this->executionTimeMs = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.developer.mvel-playground', [
            'title' => 'MVEL Playground',
        ]);
    }

    private function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'String' => $value,
            'Integer', 'Long' => filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidArgumentException("{$type} values must be integers."),
            'Double' => filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidArgumentException('Double values must be numeric.'),
            'Boolean' => match (strtolower(trim($value))) {
                'true', '1' => true,
                'false', '0' => false,
                default => throw new InvalidArgumentException('Boolean values must be true or false.'),
            },
            'Null' => null,
            'Map', 'List', 'JSON' => json_decode($value, true, 512, JSON_THROW_ON_ERROR),
            default => throw new InvalidArgumentException('Unsupported variable type.'),
        };
    }
}
