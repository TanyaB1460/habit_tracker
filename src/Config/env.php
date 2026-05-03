<?php

function env(string $key, ?string $default = null): ?string
{
    static $vars = null;

    if ($vars === null) {
        $vars = [];
        $path = __DIR__ . '/../.env';

        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                [$k, $v] = array_map('trim', explode('=', $line, 2));
                $vars[$k] = $v;
            }
        }
    }

    return $vars[$key] ?? $default;
}
