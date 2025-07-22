<?php

namespace NikoGin\Builders;

use NikoGin\Core\Support\ComposerPrefixExtractor;
use NikoGin\Services\Logic\CronLogicGenerator;

class CronBuilder
{
    public function __construct()
    {}

    public function create(string $name, string $dir) : string
    {

        $currentDir = getcwd();

        $cronDir = sprintf('%s/%s/app/Http/Crons', $currentDir, $dir);
        $providerDir = sprintf('%s/%s/app/Http/Providers', $currentDir, $dir);

        $pluginPrefix = ComposerPrefixExtractor::extractPrefix($dir);

        CronLogicGenerator::generateProvider($name, $pluginPrefix, $providerDir);
        CronLogicGenerator::generate($name, $pluginPrefix, $cronDir);

        return $dir;
    }

}