<?php

namespace App\Services;

class LanguageService
{
    private static ?array $translations = null;

    public static function getLocale(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['lang'] ?? 'en';
    }

    public static function setLocale(string $locale): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (in_array($locale, ['en', 'ar'], true)) {
            $_SESSION['lang'] = $locale;
            self::$translations = null; // Reset cache
        }
    }

    public static function isRtl(): bool
    {
        return self::getLocale() === 'ar';
    }

    private static function loadTranslations(): void
    {
        if (self::$translations !== null) {
            return;
        }

        $locale = self::getLocale();
        $file = __DIR__ . "/../Lang/{$locale}.php";
        if (is_file($file)) {
            self::$translations = require $file;
        } else {
            self::$translations = [];
        }
    }

    public static function translate(string $key, array $replace = []): string
    {
        self::loadTranslations();

        $value = self::$translations[$key] ?? $key;

        foreach ($replace as $k => $v) {
            $value = str_replace(':' . $k, $v, $value);
        }

        return $value;
    }
}
