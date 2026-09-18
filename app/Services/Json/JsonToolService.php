<?php

namespace App\Services\Json;

use InvalidArgumentException;
use JsonException;

final class JsonToolService
{
    public function validate(string $input): ?string
    {
        try {
            json_decode($input, true, 512, JSON_THROW_ON_ERROR);

            return null;
        } catch (JsonException $exception) {
            return $this->humanize($exception);
        }
    }

    public function isValid(string $input): bool
    {
        return $this->validate($input) === null;
    }

    public function format(string $input, string $indentation = '2'): string
    {
        $decoded = $this->decode($input);
        $json = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($indentation === '4') {
            return $json;
        }

        if ($indentation === 'tabs') {
            return preg_replace_callback('/^( {4})+/m', fn ($match) => str_repeat("\t", strlen($match[0]) / 4), $json) ?? $json;
        }

        return preg_replace_callback('/^( {4})+/m', fn ($match) => str_repeat('  ', strlen($match[0]) / 4), $json) ?? $json;
    }

    public function minify(string $input): string
    {
        $decoded = $this->decode($input);

        return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function escape(string $input): string
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Add JSON input to escape.');
        }

        return json_encode($input, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function unescape(string $input): string
    {
        $value = json_decode($input, true);

        if (! is_string($value)) {
            throw new InvalidArgumentException('Paste a JSON-encoded string value to unescape.');
        }

        return $value;
    }

    public function diff(string $original, string $modified): array
    {
        $decodedOriginal = $this->decode($original);
        $decodedModified = $this->decode($modified);

        return $this->compareValues($decodedOriginal, $decodedModified);
    }

    private function decode(string $input): mixed
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Add JSON to the input before processing.');
        }

        try {
            return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException($this->humanize($exception), 0, $exception);
        }
    }

    private function compareValues(mixed $original, mixed $modified): array
    {
        if (is_array($original) && is_array($modified)) {
            if (array_is_list($original) && array_is_list($modified)) {
                return ['changed' => $original !== $modified ? ['from' => $original, 'to' => $modified] : [], 'added' => [], 'removed' => []];
            }

            $keys = array_unique([...array_keys($original), ...array_keys($modified)]);
            $result = ['added' => [], 'removed' => [], 'changed' => [], 'unchanged' => []];

            foreach ($keys as $key) {
                if (! array_key_exists($key, $original)) {
                    $result['added'][$key] = $modified[$key];
                    continue;
                }

                if (! array_key_exists($key, $modified)) {
                    $result['removed'][$key] = $original[$key];
                    continue;
                }

                if ($original[$key] === $modified[$key]) {
                    $result['unchanged'][$key] = $original[$key];
                    continue;
                }

                $result['changed'][$key] = [
                    'from' => $original[$key],
                    'to' => $modified[$key],
                ];
            }

            return $result;
        }

        return $original === $modified ? ['unchanged' => $original] : ['changed' => ['from' => $original, 'to' => $modified]];
    }

    private function humanize(JsonException|string $exception): string
    {
        $message = $exception instanceof JsonException ? $exception->getMessage() : $exception;

        return preg_replace('/Syntax error,\s*/i', '', $message) ?: $message;
    }
}
