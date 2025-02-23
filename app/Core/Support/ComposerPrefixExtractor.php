<?php

namespace NikoGin\Core\Support;

use RuntimeException;

class ComposerPrefixExtractor
{
    public static function extractPrefix(string $pluginDir): string
    {
        $composerPath = $pluginDir . '/composer.json';

        if (!file_exists($composerPath)) {
            throw new RuntimeException('composer.json not found.');
        }

        $composerJson = json_decode(file_get_contents($composerPath), true);

        return self::getPrefixFromAutoload($composerJson['autoload']['psr-4'] ?? []);
    }

    private static function getPrefixFromAutoload(array $autoload): string
    {
        foreach ($autoload as $prefix => $path) {
            return rtrim($prefix, '\\');
        }
        return 'Default\\Prefix';
    }
}