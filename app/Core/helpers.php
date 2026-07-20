<?php

use App\Services\LanguageService;

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return LanguageService::translate($key, $replace);
    }
}

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                
                // Remove surrounding quotes if present
                if (preg_match('/^["\'](.*)["\']$/', $val, $matches)) {
                    $val = $matches[1];
                }
                
                putenv("$key=$val");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}
