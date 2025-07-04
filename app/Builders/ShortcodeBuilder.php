<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\ShortcodeLogicGenerator;

class ShortcodeBuilder
{
    public function __construct(public ShortcodeLogicGenerator $shortcodeLogicGenerator)
    {}

    public function create(string $name, string $action, string $dir): string
    {
        $currentDir = getcwd();
        $shortcodeDir = sprintf('%s/%s/app/Http/Components/Shortcodes', $currentDir, $dir);

        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        $this->shortcodeLogicGenerator->generate($name, $action, $shortcodeDir, $pluginPrefix);
        return $dir;
    }
}