<?php

function replaceTranslationParams(string $text, array $translationParams)
{
    foreach ($translationParams as $key => $value) {
        $text = str_replace("{" . $key . "}", $value, $text);
    }

    return $text;
}

function getTranslation(array $translationFile, string $path): ?string
{
    $value = $translationFile;

    foreach (explode('.', $path) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return null;
        }

        $value = $value[$segment];
    }

    return is_string($value) ? $value : null;
}