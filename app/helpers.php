<?php

use App\Services\TranslationService;

if (!function_exists('translate_text')) {
    /**
     * Auto-translate text to current locale
     *
     * @param string $text
     * @param string $sourceLang
     * @return string
     */
    function translate_text(string $text, string $sourceLang = 'id'): string
    {
        $translationService = app(TranslationService::class);
        $currentLocale = app()->getLocale();

        if ($currentLocale === $sourceLang) {
            return $text;
        }

        return $translationService->translate($text, $currentLocale, $sourceLang);
    }
}

if (!function_exists('translate_category')) {
    /**
     * Translate category name to current locale
     *
     * @param string $categoryName
     * @return string
     */
    function translate_category(string $categoryName): string
    {
        $translationService = app(TranslationService::class);
        return $translationService->translateCategory($categoryName);
    }
}
