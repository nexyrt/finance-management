<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TranslationService
{
    /**
     * Translate text using Google Translate API (free endpoint)
     *
     * @param string $text
     * @param string $targetLang 'id' or 'zh'
     * @param string $sourceLang default 'id'
     * @return string
     */
    public function translate(string $text, string $targetLang, string $sourceLang = 'id'): string
    {
        // Don't translate if target is same as source
        if ($targetLang === $sourceLang) {
            return $text;
        }

        // Cache key to avoid repeated API calls
        $cacheKey = "translation.{$sourceLang}.{$targetLang}." . md5($text);

        return Cache::remember($cacheKey, now()->addMonths(6), function () use ($text, $targetLang, $sourceLang) {
            try {
                // Using unofficial Google Translate endpoint (no API key required)
                $response = Http::timeout(5)->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => $sourceLang,
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text,
                ]);

                if ($response->successful()) {
                    $result = $response->json();

                    // Google Translate returns nested array structure
                    if (isset($result[0][0][0])) {
                        return $result[0][0][0];
                    }
                }

                // Fallback to original text if translation fails
                return $text;
            } catch (\Exception $e) {
                // Log error but don't break the app
                \Log::warning("Translation failed for '{$text}': " . $e->getMessage());
                return $text;
            }
        });
    }

    /**
     * Translate category name based on current locale
     *
     * @param string $categoryName
     * @return string
     */
    public function translateCategory(string $categoryName): string
    {
        $currentLocale = app()->getLocale();

        // If locale is Indonesian, no translation needed
        if ($currentLocale === 'id') {
            return $categoryName;
        }

        // Translate to current locale
        return $this->translate($categoryName, $currentLocale, 'id');
    }

    /**
     * Batch translate multiple texts
     *
     * @param array $texts
     * @param string $targetLang
     * @param string $sourceLang
     * @return array
     */
    public function batchTranslate(array $texts, string $targetLang, string $sourceLang = 'id'): array
    {
        $results = [];

        foreach ($texts as $key => $text) {
            $results[$key] = $this->translate($text, $targetLang, $sourceLang);
        }

        return $results;
    }

    /**
     * Clear translation cache for specific text
     *
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     * @return void
     */
    public function clearCache(string $text, string $targetLang, string $sourceLang = 'id'): void
    {
        $cacheKey = "translation.{$sourceLang}.{$targetLang}." . md5($text);
        Cache::forget($cacheKey);
    }

    /**
     * Clear all translation cache
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }
}
