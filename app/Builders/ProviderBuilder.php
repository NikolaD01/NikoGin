<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\ProviderLogicGenerator;

class ProviderBuilder
{

    public function __construct()
    {}

    public function create(string $name, string $dir) : string
    {

        $currentDir = getcwd();

        $providerDir = sprintf("%s/%s/app/Http/Providers", $currentDir, $dir);
        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        ProviderLogicGenerator::generate($name, $pluginPrefix, $providerDir);

        return $providerDir;
    }

}