<?php

/**
 * Generate JSON translation files from PHP translation files
 * This script converts PHP translation arrays to JSON format for laravel-vue-i18n
 */

$langPath = __DIR__ . '/lang';
$locales = ['en', 'ar', 'fr', 'lt'];

foreach ($locales as $locale) {
    $translations = [];
    $localePath = $langPath . '/' . $locale;
    
    if (!is_dir($localePath)) {
        continue;
    }
    
    // Get all PHP files in the locale directory
    $files = glob($localePath . '/*.php');
    
    foreach ($files as $file) {
        $namespace = basename($file, '.php');
        $contents = include $file;
        
        if (is_array($contents)) {
            foreach ($contents as $key => $value) {
                $translations[$namespace . '.' . $key] = $value;
            }
        }
    }
    
    // Write to JSON file
    $jsonFile = $langPath . '/php_' . $locale . '.json';
    file_put_contents(
        $jsonFile, 
        json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
    
    echo "Generated translations for {$locale}: {$jsonFile}\n";
}

echo "\nAll translation files have been generated successfully!\n";
