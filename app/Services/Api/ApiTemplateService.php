<?php

namespace App\Services\Api;

final class ApiTemplateService
{
    public function parse(string $definitions): array
    {
        $variables = [];

        foreach (preg_split('/\r\n|\n|\r/', $definitions) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $line, 2), 2, null);
            if ($value !== null && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', trim($name))) {
                $variables[trim($name)] = trim($value);
            }
        }

        return $variables;
    }

    public function resolve(string $value, string $definitions): string
    {
        $variables = $this->parse($definitions);

        return preg_replace_callback('/\{\{\s*([A-Za-z_][A-Za-z0-9_]*)\s*\}\}/', function (array $matches) use ($variables): string {
            return $variables[$matches[1]] ?? $matches[0];
        }, $value) ?? $value;
    }
}
